<?php
// project/models/admin/AdminBookModel.php
require_once __DIR__ . '/../BaseModel.php';

final class AdminBookModel extends BaseModel
{
    /* ============================================================
       PAGINATION + FILTER
    ============================================================ */
    public static function paginate(array $filters, int $page, int $perPage): array
    {
        $where  = ["b.is_deleted = :deleted"];
        $params = [":deleted" => (int)($filters["deleted"] ?? 0)];

        if (($filters["keyword"] ?? "") !== "") {
            $where[]         = "(b.title LIKE :kw OR b.slug LIKE :kw)";
            $params[":kw"]   = "%" . $filters["keyword"] . "%";
        }

        if (!empty($filters["category_id"])) {
            $where[]           = "b.category_id = :cat";
            $params[":cat"]    = (int)$filters["category_id"];
        }

        if (($filters["status"] ?? "") !== "") {
            $where[]            = "b.is_active = :active";
            $params[":active"]  = (int)$filters["status"];
        }

        $whereSQL = "WHERE " . implode(" AND ", $where);

        /* Count */
        $sqlCount = "
            SELECT COUNT(*)
            FROM books b
            {$whereSQL}
        ";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int)$st->fetchColumn();

        /* Pagination */
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
                b.*,
                c.name AS category_name,
                GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR ', ') AS author_names,
                GROUP_CONCAT(DISTINCT a.id   ORDER BY a.id   SEPARATOR ',')   AS author_ids,
                GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ') AS publisher_names,
                GROUP_CONCAT(DISTINCT p.id   ORDER BY p.id   SEPARATOR ',')   AS publisher_ids
            FROM books b
            LEFT JOIN categories c ON c.id = b.category_id
            LEFT JOIN book_authors ba ON ba.book_id = b.id
            LEFT JOIN authors a ON a.id = ba.author_id AND a.is_deleted = 0
            LEFT JOIN book_publisher bp ON bp.book_id = b.id
            LEFT JOIN publisher p ON p.id = bp.publisher_id AND p.is_deleted = 0
            {$whereSQL}
            GROUP BY b.id
            ORDER BY b.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $st = self::db()->prepare($sql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(":limit",  $perPage, \PDO::PARAM_INT);
        $st->bindValue(":offset", $offset,  \PDO::PARAM_INT);
        $st->execute();

        return [
            "items"     => $st->fetchAll(),
            "total"     => $total,
            "page"      => $page,
            "last_page" => max(1, ceil($total / $perPage)),
        ];
    }


    /* ============================================================
       GET ALL ACTIVE (cho dropdown)
    ============================================================ */
    public static function allActive(): array
    {
        $sql = "SELECT id, title FROM books WHERE is_deleted=0 AND is_active=1 ORDER BY title ASC";
        return self::db()->query($sql)->fetchAll();
    }


    /* ============================================================
       FIND BOOK
    ============================================================ */
    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM books WHERE id=:id AND is_deleted=0 LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([":id" => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findDeleted(int $id): ?array
    {
        $sql = "SELECT * FROM books WHERE id=:id AND is_deleted=1 LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([":id" => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }


    /* ============================================================
       SLUG EXIST CHECK
    ============================================================ */
    public static function isSlugExist(string $slug, int $ignoreId = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM books WHERE slug=:slug AND id != :id AND is_deleted = 0";
        $st  = self::db()->prepare($sql);
        $st->execute([
            ":slug" => $slug,
            ":id"   => $ignoreId,
        ]);
        return (bool)$st->fetchColumn();
    }


    /* ============================================================
       CREATE BOOK
    ============================================================ */
    public static function create(array $d): int
    {
        $sql = "
            INSERT INTO books
            (title, slug, category_id, description, short_desc, is_active)
            VALUES (:title, :slug, :category_id, :description, :short_desc, :active)
        ";

        $st = self::db()->prepare($sql);
        $st->execute([
            ":title"       => $d["title"],
            ":slug"        => $d["slug"],
            ":category_id" => $d["category_id"],
            ":description" => $d["description"],
            ":short_desc"  => $d["short_desc"],
            ":active"      => $d["is_active"],
        ]);

        return (int) self::db()->lastInsertId();
    }


    /* ============================================================
       UPDATE BOOK
    ============================================================ */
    public static function updateRecord(int $id, array $d): bool
    {
        $sql = "
            UPDATE books
            SET title=:title,
                slug=:slug,
                category_id=:category_id,
                description=:description,
                short_desc=:short_desc,
                is_active=:active
            WHERE id=:id AND is_deleted=0
        ";

        return self::db()->prepare($sql)->execute([
            ":id"          => $id,
            ":title"       => $d["title"],
            ":slug"        => $d["slug"],
            ":category_id" => $d["category_id"],
            ":description" => $d["description"],
            ":short_desc"  => $d["short_desc"],
            ":active"      => $d["is_active"],
        ]);
    }


    /* ============================================================
       SOFT DELETE / RESTORE
    ============================================================ */
    public static function softDelete(int $id): bool
    {
        $sql = "
            UPDATE books
            SET is_deleted=1,
                deleted_at=NOW(),
                is_active=0
            WHERE id=:id
        ";
        return self::db()->prepare($sql)->execute([":id" => $id]);
    }

    public static function restore(int $id): bool
    {
        $sql = "
            UPDATE books
            SET is_deleted=0,
                deleted_at=NULL
            WHERE id=:id
        ";
        return self::db()->prepare($sql)->execute([":id" => $id]);
    }


    /* ============================================================
       RELATIONS: IMAGES
    ============================================================ */
    public static function getImages(int $bookId): array
    {
        $sql = "SELECT * FROM book_images WHERE book_id = :id AND is_deleted = 0 ORDER BY sort_order ASC, id ASC";
        $st  = self::db()->prepare($sql);
        $st->execute([":id" => $bookId]);
        return $st->fetchAll();
    }

    public static function nextSortOrder(int $bookId): int
    {
        $sql = "SELECT COALESCE(MAX(sort_order), -1) + 1 AS next_sort FROM book_images WHERE book_id = :bid AND is_deleted = 0";
        $st  = self::db()->prepare($sql);
        $st->execute([":bid" => $bookId]);
        $row = $st->fetch();
        return (int)($row['next_sort'] ?? 0);
    }

    /* Add image */
    public static function addImage(int $bookId, string $url, int $sort = 0): bool
    {
        $sql = "INSERT INTO book_images (book_id, image_url, sort_order)
                VALUES (:bid, :url, :sort)";
        return self::db()->prepare($sql)->execute([
            ":bid"  => $bookId,
            ":url"  => $url,
            ":sort" => $sort,
        ]);
    }

    /* Remove image */
    public static function deleteImage(int $id): bool
    {
        $sql = "UPDATE book_images 
                SET is_deleted = 1, deleted_at = NOW()
                WHERE id = :id AND is_deleted = 0";
        return self::db()->prepare($sql)->execute([":id" => $id]);
    }


    /* ============================================================
       RELATIONS: AUTHORS (N-N)
    ============================================================ */
    public static function getAuthors(int $bookId): array
    {
        $sql = "
            SELECT a.*
            FROM authors a
            JOIN book_authors ba ON ba.author_id = a.id
            WHERE ba.book_id = :id AND a.is_deleted = 0
        ";
        $st = self::db()->prepare($sql);
        $st->execute([":id" => $bookId]);
        return $st->fetchAll();
    }

    public static function syncAuthors(int $bookId, array $authorIds): void
    {
        // Xoá cũ
        self::db()->prepare("DELETE FROM book_authors WHERE book_id=:id")
            ->execute([":id" => $bookId]);

        // Insert mới
        $sql = "INSERT INTO book_authors (book_id, author_id) VALUES (:bid, :aid)";
        $st  = self::db()->prepare($sql);

        foreach ($authorIds as $aid) {
            $aid = (int)$aid;
            if ($aid <= 0) {
                continue;
            }
            $st->execute([
                ":bid" => $bookId,
                ":aid" => $aid,
            ]);
        }
    }


    /* ============================================================
       RELATIONS: PUBLISHER (1-N)
    ============================================================ */
    public static function getPublisher(int $bookId): ?array
    {
        $sql = "
            SELECT p.*
            FROM publisher p
            JOIN book_publisher bp ON bp.publisher_id = p.id
            WHERE bp.book_id = :id AND p.is_deleted = 0
            LIMIT 1
        ";
        $st = self::db()->prepare($sql);
        $st->execute([":id" => $bookId]);
        return $st->fetch() ?: null;
    }

    public static function setPublisher(int $bookId, int $publisherId): bool
    {
        // Xoá cũ
        self::db()->prepare("DELETE FROM book_publisher WHERE book_id=:id")
            ->execute([":id" => $bookId]);

        if ($publisherId <= 0) {
            return true;
        }

        // Set mới
        $sql = "INSERT INTO book_publisher (book_id, publisher_id)
                VALUES (:bid, :pid)";
        return self::db()->prepare($sql)->execute([
            ":bid" => $bookId,
            ":pid" => $publisherId,
        ]);
    }


    /* ============================================================
       RELATIONS: VARIANTS
    ============================================================ */
    public static function getVariants(int $bookId): array
    {
        $sql = "
            SELECT *
            FROM book_variants
            WHERE book_id = :id AND is_deleted=0
            ORDER BY id ASC
        ";
        $st = self::db()->prepare($sql);
        $st->execute([":id" => $bookId]);
        return $st->fetchAll();
    }
}
