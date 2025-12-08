<?php
// project/controllers/admin/DashboardAdminController.php

require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminDashboardModel.php';

final class DashboardAdminController extends BaseAdminController
{
    public function index(): string
    {
        $range = $_GET['range'] ?? 'last_12_months';

        $stats        = AdminDashboardModel::kpiStats($range);
        $monthly      = AdminDashboardModel::monthlyRevenue($range, 8);
        $statusStats  = AdminDashboardModel::orderStatusStats($range);
        $topCategories= AdminDashboardModel::topCategories(5);
        $topBooks     = AdminDashboardModel::topBooksByRevenue(5);
        $worstBooks   = AdminDashboardModel::bottomBooksByRevenue(5);
        $recentOrders = AdminDashboardModel::recentOrders(6);

        return $this->renderAdmin('admin/dashboard/index', [
            'stats'       => $stats,
            'chartMonths' => $monthly,
            'statusStats' => $statusStats,
            'range'       => $range,
            'topCategories' => $topCategories,
            'topBooks'      => $topBooks,
            'worstBooks'    => $worstBooks,
            'recentOrders'  => $recentOrders,
        ]);
    }
}
