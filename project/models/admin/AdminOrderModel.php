<?php
require_once __DIR__ . '/../BaseModel.php';

final class AdminOrderModel extends BaseModel
{
    public static function filter(array $filters, int $page = 1, int $perPage = 20): array
    {
        $where  = [];
        $params = [];

        if ($filters['keyword'] !== '') {
            $where[]          = "(o.id LIKE :kw OR u.email LIKE :kw)";
            $params[':kw']    = '%' . $filters['keyword'] . '%';
        }

        if ($filters['shipping_status'] !== '') {
            $where[]              = "o.shipping_status = :ship";
            $params[':ship']      = $filters['shipping_status'];
        }

        if ($filters['payment_status'] !== '') {
            $where[]              = "o.payment_status = :pstat";
            $params[':pstat']     = $filters['payment_status'];
        }

        if ($filters['payment_method'] !== '') {
            $where[]              = "p.payment_method = :pmethod";
            $params[':pmethod']   = $filters['payment_method'];
        }

        if ($filters['channel'] !== '') {
            $where[]              = "p.provider = :provider";
            $params[':provider']  = $filters['channel'];
        }

        if ($filters['from_date'] !== '') {
            $where[]              = "DATE(o.created_at) >= :fromd";
            $params[':fromd']     = $filters['from_date'];
        }

        if ($filters['to_date'] !== '') {
            $where[]              = "DATE(o.created_at) <= :tod";
            $params[':tod']       = $filters['to_date'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Đếm tổng
        $sqlCount = "
            SELECT COUNT(*)
            FROM orders o
            LEFT JOIN users u   ON u.id = o.user_id
            LEFT JOIN payment p ON p.order_id = o.id
            $whereSql
        ";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int) $st->fetchColumn();

        $offset = ($page - 1) * $perPage;

        // Lấy danh sách
        $sql = "
            SELECT 
                o.*,
                u.name  AS user_name,
                u.email AS user_email,
                p.payment_method,
                p.status AS payment_gateway_status
            FROM orders o
            LEFT JOIN users u   ON u.id = o.user_id
            LEFT JOIN payment p ON p.order_id = o.id
            $whereSql
            ORDER BY o.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $st = self::db()->prepare($sql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset,  \PDO::PARAM_INT);
        $st->execute();

        return [
            'items'     => $st->fetchAll(),
            'total'     => $total,
            'page'      => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public static function find(int $id): ?array
    {
        $sql = "
            SELECT 
                o.*,
                u.name  AS user_name,
                u.email AS user_email
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            WHERE o.id = :id
            LIMIT 1
        ";
        $st = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function items(int $orderId): array
    {
        $sql = "
            SELECT 
                oi.*,
                b.title  AS book_title,
                v.format AS variant_format
            FROM order_items oi
            JOIN book_variants v ON v.id = oi.variant_id
            JOIN books b         ON b.id = v.book_id
            WHERE oi.order_id = :oid
        ";
        $st = self::db()->prepare($sql);
        $st->execute([':oid' => $orderId]);
        return $st->fetchAll();
    }

    /**
     * Cập nhật trạng thái vận chuyển.
     * Trả về true nếu cập nhật thành công, false nếu không hợp lệ.
     */
    public static function updateShippingStatus(
        int $id,
        string $toStatus,
        ?int $adminId,
        ?string $reason
    ): bool {
        $db = self::db();

        // Lấy trạng thái hiện tại
        $cur = self::find($id);
        if (!$cur) {
            return false;
        }

        $from = $cur['shipping_status'];

        if ($from === $toStatus) {
            return true; // không có gì để làm
        }

        // Không cho sửa đơn đã shipped hoặc đã cancelled
        if (in_array($from, ['shipped', 'cancelled'], true)) {
            return false;
        }

        // Chỉ cho phép chuyển theo các rule đơn giản
        $allowed = [
            'pending'    => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
        ];

        if (!isset($allowed[$from]) || !in_array($toStatus, $allowed[$from], true)) {
            return false;
        }

        // Nếu huỷ → cần lý do (tối thiểu chuỗi non-empty)
        $cancelReason = null;
        $cancelUserId = null;

        if ($toStatus === 'cancelled') {
            $cancelReason = trim((string) $reason);
            if ($cancelReason === '') {
                $cancelReason = 'Đơn bị hủy bởi quản trị viên';
            }
            $cancelUserId = $adminId;
        }

        $sql = "
            UPDATE orders
            SET shipping_status       = :st,
                cancel_reason         = :reason,
                cancelled_by_user_id  = :uid
            WHERE id = :id
        ";
        $st = $db->prepare($sql);
        return $st->execute([
            ':st'    => $toStatus,
            ':reason'=> $cancelReason,
            ':uid'   => $cancelUserId,
            ':id'    => $id,
        ]);
    }
}
