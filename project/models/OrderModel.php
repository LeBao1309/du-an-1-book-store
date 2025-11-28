<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/BookModel.php';

final class OrderModel extends BaseModel
{
    /**
     * Tạo đơn hàng + chi tiết đơn hàng từ giỏ
     * @param int   $userId
     * @param array $shipping  (mảng lấy từ UserModel::getAddresses(), ví dụ phần tử $addresses[0])
     * @param array $cart      (mảng $_SESSION['cart'])
     * @param string $shippingStatus  (pending/processing/...)
     * @param string|null $note
     * @return int  $orderId
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
            // TÍNH TỔNG TIỀN
            $total = 0;
            foreach ($cart as $item) {
                $qty   = (int)($item['quantity'] ?? 0);
                $price = (int)($item['price'] ?? 0);
                $total += $qty * $price;
            }

            // Snapshot địa chỉ giao hàng
            $userAddressId = $shipping['id'] ?? null;
            $fullAddress   = $shipping['full_address'] ?? '';
            $phone         = $shipping['shipping_phone'] ?? '';

            // INSERT vào orders
            $sqlOrder = "INSERT INTO orders (
                              user_id,
                              user_address_id,
                              total,
                              shipping_status,
                              shipping_address,
                              shipping_phone,
                              note
                         ) VALUES (
                              :user_id,
                              :user_address_id,
                              :total,
                              :shipping_status,
                              :shipping_address,
                              :shipping_phone,
                              :note
                         )";
            $st = $pdo->prepare($sqlOrder);
            $st->execute([
                ':user_id'         => $userId,
                ':user_address_id' => $userAddressId,
                ':total'           => $total,
                ':shipping_status' => $shippingStatus,
                ':shipping_address'=> $fullAddress,
                ':shipping_phone'  => $phone,
                ':note'            => $note,
            ]);

            $orderId = (int)$pdo->lastInsertId();

            // INSERT chi tiết từng sản phẩm
            $sqlItem = "INSERT INTO order_items (
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
                        )";
            $stItem = $pdo->prepare($sqlItem);

            foreach ($cart as $item) {
                $bookId   = (int)($item['id'] ?? 0);
                $qty      = (int)($item['quantity'] ?? 0);
                $price    = (int)($item['price'] ?? 0);
                $subtotal = $qty * $price;

                // LẤY VARIANT CHO SÁCH NÀY
                $variants = Book::getVariants($bookId);
                if (empty($variants)) {
                    throw new \RuntimeException("Sách ID {$bookId} chưa có biến thể (book_variants).");
                }

                // Ưu tiên variant có giá trùng với giá trong giỏ, không có thì lấy cái đầu
                $variantId = $variants[0]['id'];
                foreach ($variants as $v) {
                    $vPrice = (int)($v['sale_price'] ?? $v['price'] ?? 0);
                    if ($vPrice === $price) {
                        $variantId = $v['id'];
                        break;
                    }
                }

                $stItem->execute([
                    ':order_id'  => $orderId,
                    ':variant_id'=> $variantId,
                    ':quantity'  => $qty,
                    ':price'     => $price,
                    ':subtotal'  => $subtotal,
                ]);
            }

            $pdo->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
