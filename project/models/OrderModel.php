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
     */
    public static function createFromCart(
        int $userId,
        ?array $shipping,
        array $cart,
        string $shippingStatus = 'pending',
        ?string $note = null,
        ?int $couponId = null,
        float $discountAmount = 0.0,
        string $paymentStatus = 'pending',
        ?string $paymentMethod = null,
        ?string $paymentProvider = null,
        ?string $transactionCode = null
    ): int {
        if (empty($cart)) {
            throw new \RuntimeException('Giỏ hàng trống, không thể tạo đơn.');
        }

        // Chuẩn hoá input cart => variant_id => qty
        // chấp nhận cả trường hợp cart đang là: [variant_id => ['variant_id','quantity']]
        $variantQty = [];
        foreach ($cart as $k => $item) {
            if (is_array($item) && isset($item['variant_id'])) {
                $vid = (int)$item['variant_id'];
                $qty = (int)($item['quantity'] ?? 0);
            } else {
                // fallback: nếu ai đó truyền kiểu lạ
                $vid = (int)$k;
                $qty = is_array($item) ? (int)($item['quantity'] ?? 0) : 0;
            }
            if ($vid > 0 && $qty > 0) $variantQty[$vid] = $qty;
        }

        if (empty($variantQty)) {
            throw new \RuntimeException('Giỏ hàng thiếu variant_id hợp lệ.');
        }

        // Địa chỉ
        $userAddressId   = $shipping['id'] ?? null;
        $shippingPhone   = $shipping['shipping_phone'] ?? null;
        $shippingAddress = $shipping['full_address']   ?? null;

        if (empty($shippingAddress)) {
            throw new \RuntimeException('Thiếu địa chỉ giao hàng (shipping_address).');
        }

        $pdo = self::db();
        $pdo->beginTransaction();

        try {
            // 1) LẤY GIÁ THỰC TỪ DB THEO VARIANT
            $variantIds = array_keys($variantQty);
            $placeholders = implode(',', array_fill(0, count($variantIds), '?'));

            $sqlPrice = "
                SELECT
                    id AS variant_id,
                    CAST(COALESCE(NULLIF(sale_price, 0), price) AS DECIMAL(18,2)) AS unit_price
                FROM book_variants
                WHERE id IN ($placeholders)
            ";
            $stmtPrice = $pdo->prepare($sqlPrice);
            $stmtPrice->execute($variantIds);
            $rows = $stmtPrice->fetchAll();

            $priceMap = [];
            foreach ($rows as $r) {
                $priceMap[(int)$r['variant_id']] = (float)$r['unit_price'];
            }

            // 2) TÍNH TỔNG
            $totalAmount = 0.0;
            foreach ($variantQty as $vid => $qty) {
                if (!isset($priceMap[$vid])) {
                    throw new \RuntimeException('Variant không tồn tại: ' . $vid);
                }
                $totalAmount += $priceMap[$vid] * $qty;
            }
            $finalTotal = max(0, $totalAmount - $discountAmount);

            // 3) INSERT orders
            $sqlOrder = "
                INSERT INTO orders (
                    user_id,
                    user_address_id,
                    total,
                    coupon_id,
                    discount_amount,
                    payment_status,
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
                    :coupon_id,
                    :discount_amount,
                    :payment_status,
                    :shipping_status,
                    :shipping_address,
                    :shipping_phone,
                    :note,
                    NOW()
                )
            ";
            $stmt = $pdo->prepare($sqlOrder);
            $stmt->execute([
                ':user_id'          => $userId,
                ':user_address_id'  => $userAddressId,
                ':total'            => $totalAmount,
                ':coupon_id'        => $couponId,
                ':discount_amount'  => $discountAmount,
                ':payment_status'   => $paymentStatus,
                ':shipping_status'  => $shippingStatus,
                ':shipping_address' => $shippingAddress,
                ':shipping_phone'   => $shippingPhone,
                ':note'             => $note,
            ]);

            $orderId = (int)$pdo->lastInsertId();

            // 4) INSERT order_items
            $sqlItem = "
                INSERT INTO order_items (
                    order_id, variant_id, quantity, price, subtotal
                ) VALUES (
                    :order_id, :variant_id, :quantity, :price, :subtotal
                )
            ";
            $stmtItem = $pdo->prepare($sqlItem);

            foreach ($variantQty as $vid => $qty) {
                $price = (float)$priceMap[$vid];
                $sub   = $price * $qty;

                $stmtItem->execute([
                    ':order_id'   => $orderId,
                    ':variant_id' => $vid,
                    ':quantity'   => $qty,
                    ':price'      => $price,
                    ':subtotal'   => $sub,
                ]);
            }

            // 5) Ghi bảng payment (để admin xem phương thức & trạng thái)
            $payStatus = ($paymentStatus === 'paid') ? 'success' : 'pending';
            // An toàn với enum DB (đa số chỉ có cod/card); fallback về cod nếu không khớp
            $allowedMethods = ['cod','card'];
            $payMethod = in_array($paymentMethod, $allowedMethods, true) ? $paymentMethod : 'cod';
            $payProvider = $paymentProvider ?: $payMethod;
            $stmtPay = $pdo->prepare("
                INSERT INTO payment (order_id, payment_method, amount, status, provider, transaction_code, paid_at)
                VALUES (:order_id, :method, :amount, :status, :provider, :txn, :paid_at)
                ON DUPLICATE KEY UPDATE
                    payment_method = VALUES(payment_method),
                    amount = VALUES(amount),
                    status = VALUES(status),
                    provider = VALUES(provider),
                    transaction_code = VALUES(transaction_code),
                    paid_at = VALUES(paid_at)
            ");
            $stmtPay->execute([
                ':order_id' => $orderId,
                ':method'   => $payMethod,
                ':amount'   => $finalTotal,
                ':status'   => $payStatus,
                ':provider' => $payProvider,
                ':txn'      => $transactionCode,
                ':paid_at'  => ($payStatus === 'success') ? date('Y-m-d H:i:s') : null,
            ]);

            $pdo->commit();
            return $orderId;

        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    
    /**
     * Kiểm tra user đã mua sản phẩm (book_id) chưa
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

    /**
     * Hủy đơn hàng (Chỉ áp dụng khi đơn đang chờ xử lý - pending)
     * Đã sửa lỗi trùng tên tham số :uid
     */
    public static function cancelOrder(int $orderId, int $userId, string $reason = ''): bool
    {
        $sql = "UPDATE orders 
                SET shipping_status = 'cancelled', 
                    cancelled_by_user_id = :uid_update, 
                    cancel_reason = :reason,
                    updated_at = NOW()
                WHERE id = :id 
                  AND user_id = :uid_check 
                  AND shipping_status = 'pending'";
        
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([
            ':id'         => $orderId, 
            ':uid_update' => $userId,
            ':uid_check'  => $userId,
            ':reason'     => $reason
        ]);
    }

    /**
     * Xác nhận đã nhận hàng (Chuyển từ shipped -> delivered)
     * Đồng thời cập nhật payment_status thành 'paid' nếu đang là COD (pending)
     */
    public static function confirmReceived(int $orderId, int $userId): bool
    {
        $sql = "UPDATE orders 
                SET shipping_status = 'delivered',
                    payment_status = CASE 
                        WHEN payment_status = 'pending' THEN 'paid' 
                        ELSE payment_status 
                    END,
                    updated_at = NOW()
                WHERE id = :id 
                  AND user_id = :uid 
                  AND shipping_status = 'shipped'";
        
        $stmt = self::db()->prepare($sql); 
        return $stmt->execute([
            ':id'  => $orderId, 
            ':uid' => $userId
        ]);
    }

        /**
     * Lấy danh sách sản phẩm trong đơn theo order_id (dùng cho COD/VNPay success page)
     */
/**
 * Lấy danh sách sản phẩm trong đơn theo order_id (dùng cho COD/VNPay success page)
 */
    public static function getItemsByOrderId(int $orderId): array
    {
        if ($orderId <= 0) return [];

        $sql = "
            SELECT
                oi.quantity,
                oi.price AS price,
                oi.subtotal AS subtotal,
                b.title,
                bv.format,
                img.image_url
            FROM order_items oi
            JOIN book_variants bv ON oi.variant_id = bv.id
            JOIN books b ON bv.book_id = b.id
            LEFT JOIN book_images img 
                ON b.id = img.book_id AND img.sort_order = 0
            WHERE oi.order_id = :oid
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->execute([':oid' => $orderId]);
        return $stmt->fetchAll();
    }

}


 
