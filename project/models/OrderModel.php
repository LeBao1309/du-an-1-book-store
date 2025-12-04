<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Order Model - Quản lý đơn hàng
 */
final class OrderModel extends BaseModel
{
    /**
     * Lấy danh sách toàn bộ đơn hàng của User
     */
    public static function getHistory(int $userId): array
    {
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

    /**
     * Tạo đơn hàng từ giỏ hàng
     * - GHI ĐÚNG VÀO BẢNG orders & order_items THEO SCHEMA HIỆN TẠI
     */
    public static function createFromCart(
        int $userId,
        ?array $shipping,
        array $cart,
        string $shippingStatus = 'pending',
        ?string $note = null
    ): int {
        if (empty($cart)) {
            throw new \RuntimeException('Giỏ hàng trống, không thể tạo đơn.');
        }

        $pdo = self::db();
        $pdo->beginTransaction();

        try {
            // 1. TÍNH TỔNG TIỀN (theo schema: cột 'total')
            $totalAmount = 0;

            foreach ($cart as $item) {
                $qty   = (int)($item['quantity'] ?? 0);
                $price = (float)($item['price'] ?? 0);
                if ($qty > 0 && $price >= 0) {
                    $totalAmount += $qty * $price;
                }
            }

            // 2. LẤY THÔNG TIN ĐỊA CHỈ (NẾU CÓ)
            // user_address_id: id trong bảng user_address
            $userAddressId   = $shipping['id'] ?? null;
            $shippingPhone   = $shipping['shipping_phone'] ?? null;
            $shippingAddress = $shipping['full_address']   ?? null;

            if (empty($shippingAddress)) {
                // tuỳ anh: có thể throw hoặc để NULL và DB báo lỗi NOT NULL
                throw new \RuntimeException('Thiếu địa chỉ giao hàng (shipping_address).');
            }

            // 3. INSERT VÀO BẢNG orders
            // LƯU Ý: cột trong DB là: user_id, user_address_id, total,
            //        shipping_status, shipping_address, shipping_phone, note
            $sqlOrder = "
                INSERT INTO orders (
                    user_id,
                    user_address_id,
                    total,
                    shipping_status,
                    shipping_address,
                    shipping_phone,
                    note,
                    created_at
                )
                VALUES (
                    :user_id,
                    :user_address_id,
                    :total,
                    :shipping_status,
                    :shipping_address,
                    :shipping_phone,
                    :note,
                    NOW()
                )
            ";

            $stmt = $pdo->prepare($sqlOrder);
            $stmt->execute([
                ':user_id'         => $userId,
                ':user_address_id' => $userAddressId,
                ':total'           => $totalAmount,
                ':shipping_status' => $shippingStatus,
                ':shipping_address'=> $shippingAddress,
                ':shipping_phone'  => $shippingPhone,
                ':note'            => $note,
            ]);

            $orderId = (int)$pdo->lastInsertId();

            // 4. INSERT CÁC DÒNG order_items
            // CỘT TRONG DB: order_id, variant_id, quantity, price, subtotal
            $sqlItem = "
                INSERT INTO order_items (
                    order_id,
                    variant_id,
                    quantity,
                    price,
                    subtotal
                ) VALUES (
                    :order_id,
                    :variant_id,
                    :quantity,
                    :price,
                    :subtotal
                )
            ";
            $stmtItem = $pdo->prepare($sqlItem);

            foreach ($cart as $item) {
                $qty   = (int)($item['quantity'] ?? 0);
                $price = (float)($item['price']    ?? 0);
                $sub   = $qty * $price;

                // KEY NÀY PHẢI ĐÚNG VỚI GIỎ HÀNG (anh đang dùng variant_id trong cart)
                $variantId = (int)($item['variant_id'] ?? 0);

                if ($variantId <= 0 || $qty <= 0) {
                    continue;
                }

                $stmtItem->execute([
                    ':order_id'   => $orderId,
                    ':variant_id' => $variantId,
                    ':quantity'   => $qty,
                    ':price'      => $price,
                    ':subtotal'   => $sub,
                ]);
            }

            $pdo->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
    /**
     * Kiểm tra user đã mua sản phẩm (book_id) chưa
     * Chỉ tính đơn hàng đã giao (delivered)
     */
    public static function hasUserPurchasedBook(int $userId, int $bookId): bool
    {
        $sql = "SELECT COUNT(*) as count
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN book_variants bv ON oi.variant_id = bv.id
                WHERE o.user_id = :user_id 
                  AND bv.book_id = :book_id
                  AND o.shipping_status = 'delivered'";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':book_id' => $bookId
        ]);
        
        $result = $stmt->fetch();
        return $result && $result['count'] > 0;
    }
}
