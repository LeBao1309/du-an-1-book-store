<?php
require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel
{
    /**
     * Lấy danh sách toàn bộ đơn hàng của User
     */
    public static function getHistory(int $userId): array
    {
        // JOIN bảng payment để lấy phương thức thanh toán (COD/VNPAY...)
        $sql = "SELECT o.*, p.payment_method 
                FROM orders o
                LEFT JOIN payment p ON o.id = p.order_id
                WHERE o.user_id = :uid
                ORDER BY o.created_at DESC";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết 1 đơn hàng (Kiểm tra đúng chủ sở hữu)
     */
    public static function getOrderById(int $orderId, int $userId): ?array
    {
        $sql = "SELECT o.*, p.payment_method 
                FROM orders o
                LEFT JOIN payment p ON o.id = p.order_id
                WHERE o.id = :id AND o.user_id = :uid";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':id' => $orderId, ':uid' => $userId]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    /**
     * Lấy danh sách sản phẩm trong đơn hàng
     */
    public static function getOrderItems(int $orderId): array
    {
        // JOIN books, book_variants, book_images để lấy tên, ảnh, loại bìa
        $sql = "SELECT oi.*, b.title, bv.format, img.image_url
                FROM order_items oi
                JOIN book_variants bv ON oi.variant_id = bv.id
                JOIN books b ON bv.book_id = b.id
                LEFT JOIN book_images img ON b.id = img.book_id AND img.sort_order = 0
                WHERE oi.order_id = :oid";

        $stmt = self::db()->prepare($sql);
        $stmt->execute([':oid' => $orderId]);
        return $stmt->fetchAll();
    }
}