<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/BookModel.php';

final class WishlistModel extends BaseModel
{
    /**
     * Thêm sách vào danh sách yêu thích
     * @param int $userId ID của người dùng
     * @param int $bookId ID của cuốn sách
     * @return bool True nếu thành công hoặc sách đã có
     */
    public static function add(int $userId, int $bookId): bool
    {
        try {
            $sql = "INSERT IGNORE INTO wishlist (user_id, book_id, created_at) 
                    VALUES (:userId, :bookId, NOW())";
            
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([':userId' => $userId, ':bookId' => $bookId]);
        } catch (PDOException $e) {
            error_log("Error adding to wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa sách khỏi danh sách yêu thích
     * @param int $userId ID của người dùng
     * @param int $bookId ID của cuốn sách
     * @return bool True nếu xóa thành công
     */
    public static function remove(int $userId, int $bookId): bool
    {
        try {
            $sql = "DELETE FROM wishlist 
                    WHERE user_id = :userId AND book_id = :bookId";
            
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([':userId' => $userId, ':bookId' => $bookId]);
        } catch (PDOException $e) {
            error_log("Error removing from wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách Yêu thích của người dùng
     * @param int $userId ID của người dùng
     * @return array Danh sách sách
     */
    public static function getWishlist(int $userId): array
    {
        try {
            // Lấy ảnh đầu tiên của sách (sort_order thấp nhất)
            $sql = "SELECT 
                        b.id, 
                        b.title, 
                        b.slug,
                        b.author_name,
                        MIN(IFNULL(v.sale_price, v.price)) as display_price,
                        (
                            SELECT image_url 
                            FROM book_images 
                            WHERE book_id = b.id 
                            ORDER BY sort_order ASC 
                            LIMIT 1
                        ) as image_url,
                        uw.created_at
                    FROM wishlist uw
                    JOIN books b ON uw.book_id = b.id
                    LEFT JOIN book_variants v ON v.book_id = b.id
                    WHERE uw.user_id = :userId
                    GROUP BY b.id, b.title, b.slug, b.author_name, uw.created_at
                    ORDER BY uw.created_at DESC";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error getting wishlist: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kiểm tra xem một cuốn sách có nằm trong wishlist của user hay không
     * @param int $userId ID của người dùng
     * @param int $bookId ID của cuốn sách
     * @return bool True nếu sách có trong wishlist
     */
    public static function check(int $userId, int $bookId): bool
    {
        try {
            $sql = "SELECT 1 FROM wishlist 
                    WHERE user_id = :userId AND book_id = :bookId 
                    LIMIT 1";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':userId' => $userId, ':bookId' => $bookId]);
            return (bool)$stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error checking wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số lượng sách trong wishlist của người dùng
     * @param int $userId ID của người dùng
     * @return int Số lượng sách
     */
    public static function count(int $userId): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM wishlist WHERE user_id = :userId";
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':userId' => $userId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting wishlist: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Xóa toàn bộ wishlist của người dùng
     * @param int $userId ID của người dùng
     * @return bool True nếu thành công
     */
    public static function clearAll(int $userId): bool
    {
        try {
            $sql = "DELETE FROM wishlist WHERE user_id = :userId";
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([':userId' => $userId]);
        } catch (PDOException $e) {
            error_log("Error clearing wishlist: " . $e->getMessage());
            return false;
        }
    }
}
