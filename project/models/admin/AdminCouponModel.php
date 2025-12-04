<?php
require_once __DIR__ . '/../BaseModel.php';

final class AdminCouponModel extends BaseModel
{
    public static function all(): array
    {
        $sql = "SELECT * FROM coupons ORDER BY id DESC";
        return self::db()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM coupons WHERE id = :id";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(array $d): bool
    {
        $sql = "
            INSERT INTO coupons
                (code, type, value, max_discount, min_order_total,
                 usage_limit, max_uses_per_user,
                 starts_at, ends_at, is_active)
            VALUES
                (:code, :type, :value, :max_discount, :min_order_total,
                 :usage_limit, :max_user,
                 :starts_at, :ends_at, :active)
        ";
        $st = self::db()->prepare($sql);
        return $st->execute([
            ':code'          => trim($d['code']),
            ':type'          => $d['type'],
            ':value'         => (float) $d['value'],
            ':max_discount'  => $d['max_discount'] !== '' ? (float) $d['max_discount'] : null,
            ':min_order_total'=> (float) ($d['min_order_total'] ?? 0),
            ':usage_limit'   => $d['usage_limit'] !== '' ? (int) $d['usage_limit'] : null,
            ':max_user'      => $d['max_uses_per_user'] !== '' ? (int) $d['max_uses_per_user'] : null,
            ':starts_at'     => $d['starts_at'] ?: null,
            ':ends_at'       => $d['ends_at'] ?: null,
            ':active'        => !empty($d['is_active']) ? 1 : 0,
        ]);
    }

    public static function update(int $id, array $d): bool
    {
        $sql = "
            UPDATE coupons
            SET code              = :code,
                type              = :type,
                value             = :value,
                max_discount      = :max_discount,
                min_order_total   = :min_order_total,
                usage_limit       = :usage_limit,
                max_uses_per_user = :max_user,
                starts_at         = :starts_at,
                ends_at           = :ends_at,
                is_active         = :active
            WHERE id = :id
        ";
        $st = self::db()->prepare($sql);
        return $st->execute([
            ':id'             => $id,
            ':code'           => trim($d['code']),
            ':type'           => $d['type'],
            ':value'          => (float) $d['value'],
            ':max_discount'   => $d['max_discount'] !== '' ? (float) $d['max_discount'] : null,
            ':min_order_total'=> (float) ($d['min_order_total'] ?? 0),
            ':usage_limit'    => $d['usage_limit'] !== '' ? (int) $d['usage_limit'] : null,
            ':max_user'       => $d['max_uses_per_user'] !== '' ? (int) $d['max_uses_per_user'] : null,
            ':starts_at'      => $d['starts_at'] ?: null,
            ':ends_at'        => $d['ends_at'] ?: null,
            ':active'         => !empty($d['is_active']) ? 1 : 0,
        ]);
    }

    /**
     * Kiểm tra có order nào đã dùng coupon này chưa.
     */
    public static function isUsed(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM orders WHERE coupon_id = :id";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        return (int) $st->fetchColumn() > 0;
    }

    /**
     * Xóa coupon: chỉ cho phép xóa nếu chưa có order nào dùng.
     */
    public static function deleteIfUnused(int $id): bool
    {
        if (self::isUsed($id)) {
            return false;
        }
        $sql = "DELETE FROM coupons WHERE id = :id";
        $st  = self::db()->prepare($sql);
        return $st->execute([':id' => $id]);
    }

    public static function usageReport(): array
    {
        $sql = "
            SELECT 
                c.id,
                c.code,
                COUNT(o.id)            AS used_orders,
                COALESCE(SUM(o.discount_amount), 0) AS total_discount
            FROM coupons c
            LEFT JOIN orders o ON o.coupon_id = c.id
            GROUP BY c.id, c.code
            ORDER BY c.id DESC
        ";
        return self::db()->query($sql)->fetchAll();
    }
}
