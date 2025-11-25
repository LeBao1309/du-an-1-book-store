<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/BookModel.php';

final class Wishlist extends BaseModel
{
    /**
     * Thêm sách vào danh sách yêu thích
     * @param int $userId ID của người dùng
     * @param int $bookId ID của cuốn sách
     * @return bool True nếu thành công hoặc sách đã có
     */
    public static function add(int $userId, int $bookId): bool
    {
        $sql = "INSERT IGNORE INTO wishlist (user_id, book_id) 
                VALUES (:userId, :bookId)";
        
        $st = self::db()->prepare($sql);
        return $st->execute([':userId' => $userId, ':bookId' => $bookId]);
    }

    /**
     * Xóa sách khỏi danh sách yêu thích
     * @param int $userId ID của người dùng
     * @param int $bookId ID của cuốn sách
     * @return bool True nếu xóa thành công
     */
    public static function remove(int $userId, int $bookId): bool
    {
        $sql = "DELETE FROM wishlist 
                WHERE user_id = :userId AND book_id = :bookId";
        
        $st = self::db()->prepare($sql);
        return $st->execute([':userId' => $userId, ':bookId' => $bookId]);
    }

    /**
     * Lấy danh sách Yêu thích của người dùng
     * @param int $userId ID của người dùng
     * @return array Danh sách sách
     */
    public static function getWishlist(int $userId): array
    {
        // ĐÃ SỬA: Bỏ điều kiện "AND i.sort_order = 0" ở dòng JOIN book_images
        // Lý do: Dữ liệu mẫu đang để sort_order = 1, nếu lọc = 0 sẽ không ra ảnh.
        $sql = "SELECT b.id, b.title, b.slug,
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM wishlist uw
                JOIN books b ON uw.book_id = b.id
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id 
                WHERE uw.user_id = :userId
                GROUP BY b.id, b.title, b.slug
                ORDER BY uw.id DESC";
        
        $st = self::db()->prepare($sql);
        $st->execute([':userId' => $userId]);
        return $st->fetchAll();
    }
    
    /**
     * Kiểm tra xem một cuốn sách có nằm trong wishlist của user hay không
     * @param int $userId
     * @param int $bookId
     * @return bool
     */
    public static function check(int $userId, int $bookId): bool
    {
        $sql = "SELECT 1 FROM wishlist 
                WHERE user_id = :userId AND book_id = :bookId LIMIT 1";
        $st = self::db()->prepare($sql);
        $st->execute([':userId' => $userId, ':bookId' => $bookId]);
        return (bool)$st->fetch();
    }
}