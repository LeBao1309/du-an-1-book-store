<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Publisher Model - Quản lý thông tin nhà xuất bản
 */
final class PublisherModel extends BaseModel
{
    /**
     * Lấy danh sách tất cả nhà xuất bản có sách
     * @return array Danh sách nhà xuất bản
     */
    public static function getAll(): array
    {
        try {
            $sql = "SELECT DISTINCT p.* 
                    FROM publisher p
                    JOIN book_publisher bp ON p.id = bp.publisher_id
                    ORDER BY p.name ASC";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error getting publishers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm nhà xuất bản theo ID
     * @param int $id ID của nhà xuất bản
     * @return array|null Thông tin nhà xuất bản
     */
    public static function findById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM publisher WHERE id = :id LIMIT 1";
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("Error finding publisher: " . $e->getMessage());
            return null;
        }
    }
}
