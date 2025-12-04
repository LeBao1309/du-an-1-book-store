<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Comment Model - Quản lý bình luận và đánh giá sách
 */
final class CommentModel extends BaseModel
{
    /**
     * Lấy tất cả bình luận của 1 cuốn sách
     * @param int $bookId ID của cuốn sách
     * @return array Danh sách bình luận
     */
    public static function getByBookId(int $bookId): array
    {
        try {
            $sql = "SELECT 
                        c.*, 
                        u.name as user_name 
                    FROM comments c
                    INNER JOIN users u ON c.user_id = u.id
                    WHERE c.book_id = :bookId
                    ORDER BY c.created_at DESC";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':bookId' => $bookId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error getting comments: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tính rating trung bình của 1 cuốn sách
     * @param int $bookId ID của cuốn sách
     * @return array Mảng chứa avg_rating và total
     */
    public static function getAverageRating(int $bookId): array
    {
        try {
            $sql = "SELECT 
                        AVG(rating) as avg_rating, 
                        COUNT(*) as total
                    FROM comments 
                    WHERE book_id = :bookId AND rating IS NOT NULL";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':bookId' => $bookId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: ['avg_rating' => 0, 'total' => 0];
        } catch (PDOException $e) {
            error_log("Error getting average rating: " . $e->getMessage());
            return ['avg_rating' => 0, 'total' => 0];
        }
    }

    /**
     * Thêm bình luận mới
     * @param int $bookId ID của cuốn sách
     * @param int $userId ID của người dùng
     * @param string $content Nội dung bình luận
     * @param int|null $rating Điểm đánh giá (1-5)
     * @return bool True nếu thành công
     */
    public static function create(int $bookId, int $userId, string $content, ?int $rating = null): bool
    {
        try {
            $sql = "INSERT INTO comments (book_id, user_id, content, rating, created_at)
                    VALUES (:bookId, :userId, :content, :rating, NOW())";
            
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([
                ':bookId' => $bookId,
                ':userId' => $userId,
                ':content' => $content,
                ':rating' => $rating
            ]);
        } catch (PDOException $e) {
            error_log("Error creating comment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra xem người dùng đã bình luận cho sách này chưa
     * @param int $userId ID người dùng
     * @param int $bookId ID cuốn sách
     * @return bool True nếu đã bình luận
     */
    public static function hasUserCommented(int $userId, int $bookId): bool
    {
        try {
            $sql = "SELECT 1 FROM comments 
                    WHERE user_id = :userId AND book_id = :bookId 
                    LIMIT 1";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':userId' => $userId, ':bookId' => $bookId]);
            return (bool)$stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error checking user comment: " . $e->getMessage());
            return false;
        }
    }
}
