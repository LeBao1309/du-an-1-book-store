<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Author Model - Quản lý thông tin tác giả
 */
final class AuthorModel extends BaseModel
{
    /**
     * Lấy danh sách tất cả tác giả có sách
     * @return array Danh sách tác giả
     */
    public static function getAll(): array
    {
        try {
            $sql = "SELECT DISTINCT a.* 
                    FROM authors a 
                    JOIN book_authors ba ON a.id = ba.author_id
                    ORDER BY a.name ASC";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error getting authors: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm tác giả theo ID
     * @param int $id ID của tác giả
     * @return array|null Thông tin tác giả
     */
    public static function findById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM authors WHERE id = :id LIMIT 1";
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("Error finding author: " . $e->getMessage());
            return null;
        }
    }
}
