<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/BookModel.php';

final class WishlistModel extends BaseModel
{
    /**
     * Thêm sách vào danh sách yêu thích
     * Sửa: Bỏ created_at vì bảng wishlist trong DB không có cột này
     */
    public static function add(int $userId, int $bookId): bool
    {
        try {
            // Chỉ insert user_id và book_id
            $sql = "INSERT IGNORE INTO wishlist (user_id, book_id) 
                    VALUES (:userId, :bookId)";
            
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([':userId' => $userId, ':bookId' => $bookId]);
        } catch (PDOException $e) {
            error_log("Error adding to wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa sách khỏi danh sách yêu thích
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
     * Sửa: Bỏ sắp xếp theo created_at
     */
    public static function getWishlist(int $userId): array
    {
        try {
            // Join bảng để lấy thông tin sách, tác giả, giá, ảnh
            // Không dùng created_at nữa
            $sql = "SELECT 
                        b.id, 
                        b.title, 
                        b.slug,
                        GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as author_names,
                        MIN(IFNULL(v.sale_price, v.price)) as display_price,
                        MIN(i.image_url) as image_url
                    FROM wishlist uw
                    JOIN books b ON uw.book_id = b.id
                    LEFT JOIN book_variants v ON v.book_id = b.id
                    LEFT JOIN book_images i ON i.book_id = b.id AND i.sort_order = 0
                    -- Join bảng tác giả
                    LEFT JOIN book_authors ba ON b.id = ba.book_id
                    LEFT JOIN authors a ON ba.author_id = a.id
                    
                    WHERE uw.user_id = :userId
                    GROUP BY b.id, b.title, b.slug
                    ORDER BY b.id DESC"; // Sắp xếp theo ID sách giảm dần (mới thêm sẽ có ID cao nếu logic DB khác, hoặc đơn giản là theo thứ tự sách)
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            // Ghi lỗi cụ thể ra file log hoặc màn hình để debug nếu cần
            error_log("Error getting wishlist: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kiểm tra xem một cuốn sách có nằm trong wishlist hay không
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