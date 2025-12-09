<?php

require_once __DIR__ . '/../BaseModel.php';

/**
 * AdminCommentModel - quản lý bình luận/đánh giá trong admin.
 */
final class AdminCommentModel extends BaseModel
{
    public static function paginate(array $filters, int $page, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if (!empty($filters['keyword'])) {
            $where[] = "(c.content LIKE :kw OR u.name LIKE :kw OR b.title LIKE :kw)";
            $params[':kw'] = '%' . $filters['keyword'] . '%';
        }
        if (!empty($filters['rating'])) {
            $where[] = "c.rating = :rating";
            $params[':rating'] = (int)$filters['rating'];
        }
        if (!empty($filters['book_id'])) {
            $where[] = "c.book_id = :bid";
            $params[':bid'] = (int)$filters['book_id'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $base = "
            FROM comments c
            JOIN users u ON c.user_id = u.id
            JOIN books b ON c.book_id = b.id
            $whereSql
        ";

        $pdo = self::db();

        $countStmt = $pdo->prepare("SELECT COUNT(*) " . $base);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $lastPage = (int)ceil($total / $perPage);

        $sql = "
            SELECT c.*, u.name AS user_name, b.title AS book_title
            $base
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [],
            'total' => $total,
            'page' => $page,
            'last_page' => max(1, $lastPage),
        ];
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM comments WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
