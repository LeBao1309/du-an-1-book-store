<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * CouponModel - xử lý mã giảm giá cho frontend.
 */
final class CouponModel extends BaseModel
{
    public static function findByCode(string $code): ?array
    {
        $sql = "SELECT * FROM coupons
                WHERE UPPER(code) = UPPER(:code)
                  AND is_deleted = 0";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Validate coupon cho đơn hàng và tính mức giảm.
     */
    public static function validateForOrder(string $code, int $userId, float $orderTotal): array
    {
        $coupon = self::findByCode($code);
        if (!$coupon) {
            return ['ok' => false, 'error' => 'Mã không tồn tại hoặc đã bị xóa.'];
        }
        if ((int)$coupon['is_active'] !== 1) {
            return ['ok' => false, 'error' => 'Mã đã bị khóa.'];
        }

        $now = date('Y-m-d H:i:s');
        if (!empty($coupon['starts_at']) && $now < $coupon['starts_at']) {
            return ['ok' => false, 'error' => 'Mã giảm giá chưa bắt đầu.'];
        }
        if (!empty($coupon['ends_at']) && $now > $coupon['ends_at']) {
            return ['ok' => false, 'error' => 'Mã giảm giá đã hết hạn.'];
        }

        if (!empty($coupon['usage_limit']) && (int)$coupon['used_count'] >= (int)$coupon['usage_limit']) {
            return ['ok' => false, 'error' => 'Mã giảm giá đã hết lượt sử dụng.'];
        }

        if (!empty($coupon['max_uses_per_user'])) {
            $stmt = self::db()->prepare("SELECT COUNT(*) FROM orders WHERE user_id = :uid AND coupon_id = :cid");
            $stmt->execute([':uid' => $userId, ':cid' => $coupon['id']]);
            $userCount = (int)$stmt->fetchColumn();
            if ($userCount >= (int)$coupon['max_uses_per_user']) {
                return ['ok' => false, 'error' => 'Bạn đã dùng mã này tối đa số lần cho phép.'];
            }
        }

        if (!empty($coupon['min_order_total']) && $orderTotal < (float)$coupon['min_order_total']) {
            return ['ok' => false, 'error' => 'Đơn hàng chưa đủ giá trị tối thiểu.'];
        }

        // Tính giảm
        $discount = 0.0;
        if ($coupon['type'] === 'percent') {
            $discount = $orderTotal * (float)$coupon['value'] / 100;
            if (!empty($coupon['max_discount'])) {
                $discount = min($discount, (float)$coupon['max_discount']);
            }
        } else { // fixed
            $discount = (float)$coupon['value'];
        }
        $discount = max(0.0, min($discount, $orderTotal));

        return [
            'ok' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'final_total' => $orderTotal - $discount,
        ];
    }

    public static function incrementUsage(int $couponId): void
    {
        $sql = "UPDATE coupons SET used_count = used_count + 1 WHERE id = :id";
        self::db()->prepare($sql)->execute([':id' => $couponId]);
    }
}
