<?php
// project/controllers/admin/DashboardAdminController.php

require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/../../models/admin/AdminDashboardModel.php';

final class DashboardAdminController extends AdminBaseController
{
    public function index(): string
    {
        $range = $_GET['range'] ?? 'last_12_months';

        $stats        = AdminDashboardModel::kpiStats($range);
        $monthly      = AdminDashboardModel::monthlyRevenue($range, 8);
        $statusStats  = AdminDashboardModel::orderStatusStats($range);

        return $this->renderAdmin('admin/dashboard/index', [
            'stats'       => $stats,
            'chartMonths' => $monthly,
            'statusStats' => $statusStats,
            'range'       => $range,
        ]);
    }
}
