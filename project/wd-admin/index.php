<?php
session_start();

require_once __DIR__ . '/../controllers/admin/AdminBaseController.php';
require_once __DIR__ . '/../controllers/admin/AdminAuthController.php';
require_once __DIR__ . '/../controllers/admin/DashboardAdminController.php';
require_once __DIR__ . '/../controllers/admin/UserAdminController.php';
require_once __DIR__ . '/../controllers/admin/CategoryAdminController.php'; 
require_once __DIR__ . '/../controllers/admin/ProductAdminController.php';  
require_once __DIR__ . '/../controllers/admin/AuthorAdminController.php';
require_once __DIR__ . '/../controllers/admin/PublisherAdminController.php';
require_once __DIR__ . '/../controllers/admin/CouponAdminController.php';
require_once __DIR__ . '/../controllers/admin/OrderAdminController.php';

$c = $_GET['c'] ?? 'dashboard';
$a = $_GET['a'] ?? 'index';

$controllerMap = [
    'auth'       => AdminAuthController::class,
    'dashboard'  => DashboardAdminController::class,
    'users'      => UserAdminController::class,
    'catalog'    => CategoryAdminController::class,
    'products'   => ProductAdminController::class,
    'authors'    => AuthorAdminController::class,
    'publishers' => PublisherAdminController::class,
    'coupons'    => CouponAdminController::class,
    'orders'     => OrderAdminController::class,
];

$cls = $controllerMap[$c] ?? null;
if (!$cls) die("Controller not found");

$ctrl = new $cls();

if (!method_exists($ctrl, $a)) {
    http_response_code(404);
    exit('Action not found');
}

echo $ctrl->$a();