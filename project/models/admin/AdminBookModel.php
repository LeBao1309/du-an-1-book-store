<?php

require_once __DIR__ . '/../BaseModel.php';

final class AdminBookModel extends BaseModel
{
    public static function paginate(array $filters, int $page = 1, int $perPage = 10): array
    {
        $where  = [];
        $params = [];

        if ($filters['keyword'] !== '') {
            $where[]       = 'b.title LIKE :kw';
            $params[':kw'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $where[]          = 'b.category_id = :cid';
            $params[':cid']   = (int)$filters['category_id'];
        }

        if ($filters['status'] !== '') {
            $where[]            = 'b.is_active = :active';
            $params[':active']  = (int)$filters['status'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        // Đếm tổng
        $sqlCount = "SELECT COUNT(*) FROM books b {$whereSql}";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int)$st->fetchColumn();

        $offset = ($page - 1) * $perPage;

        // Lấy list + join N tác giả + N NXB
        $sql = "
            SELECT 
                b.id,
                b.title,
                b.slug,
                b.category_id,
                b.short_desc,
                b.description,
                b.is_active,
                b.created_at,
                c.name AS category_name,

                GROUP_CONCAT(
                    DISTINCT a.name ORDER BY a.name SEPARATOR ', '
                ) AS author_names,
                GROUP_CONCAT(
                    DISTINCT ba.author_id ORDER BY ba.author_id
                ) AS author_ids,

                GROUP_CONCAT(
                    DISTINCT p.name ORDER BY p.name SEPARATOR ', '
                ) AS publisher_names,
                GROUP_CONCAT(
                    DISTINCT bp.publisher_id ORDER BY bp.publisher_id
                ) AS publisher_ids

            FROM books b
            LEFT JOIN categories c      ON c.id  = b.category_id
            LEFT JOIN book_authors ba   ON ba.book_id = b.id
            LEFT JOIN authors a         ON a.id  = ba.author_id
            LEFT JOIN book_publisher bp ON bp.book_id = b.id
            LEFT JOIN publisher p       ON p.id  = bp.publisher_id
            {$whereSql}
            GROUP BY 
                b.id, b.title, b.slug, b.category_id, b.short_desc,
                b.description, b.is_active, b.created_at, c.name
            ORDER BY b.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $st = self::db()->prepare($sql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset,  \PDO::PARAM_INT);
        $st->execute();

        return [
            'items'     => $st->fetchAll(),
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM books WHERE id = :id LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO books 
                    (title, slug, category_id, short_desc, description, is_active)
                VALUES 
                    (:title, :slug, :category_id, :short_desc, :description, :is_active)";
        $st = self::db()->prepare($sql);
        $st->execute([
            ':title'       => $data['title'],
            ':slug'        => $data['slug'],
            ':category_id' => $data['category_id'],
            ':short_desc'  => $data['short_desc'],
            ':description' => $data['description'],
            ':is_active'   => $data['is_active'] ? 1 : 0,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE books
                SET title       = :title,
                    slug        = :slug,
                    category_id = :category_id,
                    short_desc  = :short_desc,
                    description = :description,
                    is_active   = :is_active
                WHERE id = :id";
        $st = self::db()->prepare($sql);
        return $st->execute([
            ':id'          => $id,
            ':title'       => $data['title'],
            ':slug'        => $data['slug'],
            ':category_id' => $data['category_id'],
            ':short_desc'  => $data['short_desc'],
            ':description' => $data['description'],
            ':is_active'   => $data['is_active'] ? 1 : 0,
        ]);
    }

    public static function delete(int $id): bool
    {
        // Giả sử book_authors & book_publisher ON DELETE CASCADE
        $sql = "DELETE FROM books WHERE id = :id";
        $st  = self::db()->prepare($sql);
        return $st->execute([':id' => $id]);
    }

    // ===================== QUAN HỆ AUTHORS / PUBLISHERS =====================

    public static function syncAuthors(int $bookId, array $authorIds): void
    {
        $db = self::db();

        // Xóa liên kết cũ
        $st = $db->prepare("DELETE FROM book_authors WHERE book_id = :bid");
        $st->execute([':bid' => $bookId]);

        $authorIds = array_unique(array_filter(array_map('intval', $authorIds)));
        if (!$authorIds) {
            return;
        }

        $sql = "INSERT INTO book_authors (book_id, author_id) VALUES (:bid, :aid)";
        $st  = $db->prepare($sql);
        foreach ($authorIds as $aid) {
            $st->execute([':bid' => $bookId, ':aid' => $aid]);
        }
    }

    public static function syncPublishers(int $bookId, array $publisherIds): void
    {
        $db = self::db();

        // Xóa liên kết cũ
        $st = $db->prepare("DELETE FROM book_publisher WHERE book_id = :bid");
        $st->execute([':bid' => $bookId]);

        $publisherIds = array_unique(array_filter(array_map('intval', $publisherIds)));
        if (!$publisherIds) {
            return;
        }

        $sql = "INSERT INTO book_publisher (book_id, publisher_id)
                VALUES (:bid, :pid)";
        $st  = $db->prepare($sql);
        foreach ($publisherIds as $pid) {
            $st->execute([':bid' => $bookId, ':pid' => $pid]);
        }
    }
}
