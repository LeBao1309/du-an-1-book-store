<?php
// project/models/admin/AdminDashboardModel.php

require_once __DIR__ . '/../BaseModel.php';

final class AdminDashboardModel extends BaseModel
{
    /**
     * Trả về [start, end] theo range.
     * $range: all | this_month | this_year | last_12_months
     */
    public static function resolveDateRange(string $range): array
    {
        $range = $range ?: 'last_12_months';

        $now = new DateTimeImmutable('now');
        $end = $now->setTime(23, 59, 59);

        switch ($range) {
            case 'this_month':
                $start = $now->modify('first day of this month')->setTime(0, 0, 0);
                break;
            case 'this_year':
                $start = $now->setDate((int)$now->format('Y'), 1, 1)->setTime(0, 0, 0);
                break;
            case 'last_12_months':
                // 11 tháng trước + tháng hiện tại
                $start = $now->modify('first day of -11 month')->setTime(0, 0, 0);
                break;
            case 'all':
            default:
                $start = null;
                $end   = null;
                break;
        }

        return [$start, $end];
    }

    /**
     * KPI chính cho dashboard, theo khoảng thời gian $range.
     */
    public static function kpiStats(string $range): array
    {
        $db = self::db();

        // Tổng sách, danh mục, user, admin (không filter theo range)
        $totalBooks      = (int) $db->query("SELECT COUNT(*) FROM books WHERE is_deleted = 0")->fetchColumn();
        $totalCategories = (int) $db->query("SELECT COUNT(*) FROM categories WHERE is_deleted = 0")->fetchColumn();
        $totalUsers      = (int) $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
        $totalAdmins     = (int) $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

        // Range cho KPI đơn hàng & doanh thu
        [$start, $end] = self::resolveDateRange($range);

        $whereRange = '';
        $params = [];
        if ($start && $end) {
            $whereRange = " AND created_at BETWEEN :start AND :end";
            $params[':start'] = $start->format('Y-m-d H:i:s');
            $params[':end']   = $end->format('Y-m-d H:i:s');
        }

        // Tổng đơn theo range (mọi trạng thái)
        $sqlTotalOrders = "SELECT COUNT(*) FROM orders WHERE 1=1 {$whereRange}";
        $st = $db->prepare($sqlTotalOrders);
        $st->execute($params);
        $totalOrders = (int)$st->fetchColumn();

        // Doanh thu (chỉ tính đơn đã giao thành công - dùng trạng thái shipped như delivered)
        $sqlRevenue = "
            SELECT COALESCE(SUM(total),0)
            FROM orders
            WHERE shipping_status IN ('shipped','delivered') {$whereRange}
        ";
        $st = $db->prepare($sqlRevenue);
        $st->execute($params);
        $totalRevenue = (float)$st->fetchColumn();

        // Đơn tuần này vs tuần trước (không áp range, cho đẹp KPI)
        $sqlWeek = "
            SELECT 
              SUM(CASE 
                      WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
                           THEN 1 ELSE 0 END) AS orders_this_week,
              SUM(CASE 
                      WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) - 1
                           THEN 1 ELSE 0 END) AS orders_last_week
            FROM orders
            WHERE shipping_status IN ('processing','shipped','delivered')
        ";
        $weekRow = $db->query($sqlWeek)->fetch() ?: ['orders_this_week' => 0, 'orders_last_week' => 0];
        $ordersThisWeek = (int)$weekRow['orders_this_week'];
        $ordersLastWeek = (int)$weekRow['orders_last_week'];
        $growthOrders   = self::percentChange($ordersLastWeek, $ordersThisWeek);

        // Doanh thu tuần này vs tuần trước (chỉ delivered)
        $sqlRevenueWeek = "
            SELECT
              COALESCE(SUM(CASE 
                              WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
                                   AND shipping_status IN ('shipped','delivered')
                           THEN total ELSE 0 END),0) AS revenue_this,
              COALESCE(SUM(CASE 
                              WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) - 1
                                   AND shipping_status IN ('shipped','delivered')
                           THEN total ELSE 0 END),0) AS revenue_last
            FROM orders
        ";
        $revRow = $db->query($sqlRevenueWeek)->fetch() ?: ['revenue_this' => 0, 'revenue_last' => 0];
        $revenueThisWeek = (float)$revRow['revenue_this'];
        $revenueLastWeek = (float)$revRow['revenue_last'];
        $growthRevenue   = self::percentChange($revenueLastWeek, $revenueThisWeek);

        return [
            'totalBooks'      => $totalBooks,
            'totalCategories' => $totalCategories,
            'totalUsers'      => $totalUsers,
            'totalAdmins'     => $totalAdmins,
            'totalOrders'     => $totalOrders,
            'totalRevenue'    => $totalRevenue,
            'ordersThisWeek'  => $ordersThisWeek,
            'revenueThisWeek' => $revenueThisWeek,
            'growthOrders'    => $growthOrders,
            'growthRevenue'   => $growthRevenue,
            'range'           => $range,
        ];
    }

    /**
     * Doanh thu theo tháng (bar chart), tự fill tháng trống = 0.
     * Luôn lấy 8 tháng gần nhất, nhưng tôn trọng range nếu có.
     */
    public static function monthlyRevenue(string $range, int $months = 8): array
    {
        $db = self::db();

        // Giao diện: luôn hiển thị tối đa $months tháng trở lại
        $now   = new DateTimeImmutable('now');
        $end   = $now->modify('last day of this month')->setTime(23,59,59);
        $start = $now->modify('first day of -' . ($months-1) . ' month')->setTime(0,0,0);

        // Nếu user chọn range hẹp hơn, siết lại
        [$rStart, $rEnd] = self::resolveDateRange($range);
        if ($rStart && $rStart > $start) $start = $rStart;
        if ($rEnd   && $rEnd   < $end)   $end   = $rEnd;

        // Bucket tháng trống
        $buckets = [];
        $cursor  = $start->modify('first day of this month');
        while ($cursor <= $end) {
            $key = $cursor->format('Y-m');
            $buckets[$key] = [
                'ym'    => $key,
                'label' => $cursor->format('M'),
                'total' => 0.0,
            ];
            $cursor = $cursor->modify('+1 month');
        }

        $sql = "
            SELECT 
              DATE_FORMAT(created_at, '%Y-%m') AS ym,
              DATE_FORMAT(created_at, '%b')    AS label,
              COALESCE(SUM(total),0)           AS total
            FROM orders
            WHERE shipping_status IN ('shipped','delivered')
              AND created_at BETWEEN :start AND :end
            GROUP BY ym, label
        ";
        $st = $db->prepare($sql);
        $st->execute([
            ':start' => $start->format('Y-m-d H:i:s'),
            ':end'   => $end->format('Y-m-d H:i:s'),
        ]);
        foreach ($st->fetchAll() as $row) {
            $key = $row['ym'];
            if (isset($buckets[$key])) {
                $buckets[$key]['total'] = (float)$row['total'];
            }
        }

        // Trả về mảng theo thứ tự thời gian
        return array_values($buckets);
    }

    /**
     * Thống kê trạng thái đơn hàng cho donut chart.
     */
    public static function orderStatusStats(string $range): array
    {
        $db = self::db();
        [$start, $end] = self::resolveDateRange($range);

        $whereRange = '';
        $params = [];
        if ($start && $end) {
            $whereRange = " AND created_at BETWEEN :start AND :end";
            $params[':start'] = $start->format('Y-m-d H:i:s');
            $params[':end']   = $end->format('Y-m-d H:i:s');
        }

        $sql = "
            SELECT shipping_status, COUNT(*) AS cnt
            FROM orders
            WHERE 1=1 {$whereRange}
            GROUP BY shipping_status
        ";
        $st = $db->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll();

        // Default đủ 5 trạng thái, tránh thiếu key
        $statuses = [
            'pending'    => 0,
            'processing' => 0,
            'shipped'    => 0,
            'delivered'  => 0,
            'cancelled'  => 0,
        ];
        foreach ($rows as $row) {
            $status = $row['shipping_status'];
            if (isset($statuses[$status])) {
                $statuses[$status] = (int)$row['cnt'];
            }
        }

        return $statuses;
    }

    /**
     * Top danh mục theo số sách.
     */
    public static function topCategories(int $limit = 5): array
    {
        $sql = "
            SELECT c.id, c.name, c.slug, COUNT(b.id) AS book_count
            FROM categories c
            LEFT JOIN books b ON b.category_id = c.id AND b.is_deleted = 0
            WHERE c.is_deleted = 0
            GROUP BY c.id, c.name, c.slug
            ORDER BY book_count DESC, c.id ASC
            LIMIT :limit
        ";
        $st = self::db()->prepare($sql);
        $st->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    /**
     * Top sách theo doanh thu (từ order_items của đơn đã giao).
     */
    public static function topBooksByRevenue(int $limit = 5): array
    {
        $sql = "
            SELECT 
                b.id,
                b.title,
                b.slug,
                SUM(oi.subtotal) AS revenue,
                SUM(oi.quantity) AS qty
            FROM order_items oi
            JOIN orders o        ON o.id = oi.order_id AND o.shipping_status IN ('shipped','delivered')
            JOIN book_variants v ON v.id = oi.variant_id
            JOIN books b         ON b.id = v.book_id
            GROUP BY b.id, b.title, b.slug
            ORDER BY revenue DESC
            LIMIT :limit
        ";
        $st = self::db()->prepare($sql);
        $st->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    /**
     * Sách ít doanh thu nhất (kể cả chưa bán lần nào).
     */
    public static function bottomBooksByRevenue(int $limit = 5): array
    {
        $sql = "
            SELECT 
                b.id,
                b.title,
                b.slug,
                COALESCE(SUM(oi.subtotal),0) AS revenue,
                COALESCE(SUM(oi.quantity),0) AS qty
            FROM books b
            LEFT JOIN book_variants v ON v.book_id = b.id AND v.is_deleted = 0
            LEFT JOIN order_items oi ON oi.variant_id = v.id
            LEFT JOIN orders o ON o.id = oi.order_id AND o.shipping_status IN ('shipped','delivered')
            WHERE b.is_deleted = 0
            GROUP BY b.id, b.title, b.slug
            ORDER BY revenue ASC, qty ASC, b.id ASC
            LIMIT :limit
        ";
        $st = self::db()->prepare($sql);
        $st->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    /**
     * Đơn hàng gần đây.
     */
    public static function recentOrders(int $limit = 6): array
    {
        $sql = "
            SELECT 
                o.id,
                o.total,
                o.shipping_status,
                o.payment_status,
                o.created_at,
                u.name  AS user_name,
                u.email AS user_email
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            ORDER BY o.created_at DESC
            LIMIT :limit
        ";
        $st = self::db()->prepare($sql);
        $st->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    private static function percentChange(float $old, float $new): float
    {
        if ($old <= 0 && $new <= 0) return 0.0;
        if ($old <= 0) return 100.0;
        return round(($new - $old) / $old * 100, 1);
    }
}
