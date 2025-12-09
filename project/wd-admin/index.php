<?php
session_start();

require_once __DIR__ . '/../controllers/admin/BaseAdminController.php';
require_once __DIR__ . '/../controllers/admin/AdminAuthController.php';
require_once __DIR__ . '/../controllers/admin/DashboardAdminController.php';
require_once __DIR__ . '/../controllers/admin/UserAdminController.php';
require_once __DIR__ . '/../controllers/admin/CategoryAdminController.php';
require_once __DIR__ . '/../controllers/admin/BookAdminController.php';
require_once __DIR__ . '/../controllers/admin/BookVariantAdminController.php';
require_once __DIR__ . '/../controllers/admin/AuthorAdminController.php';
require_once __DIR__ . '/../controllers/admin/PublisherAdminController.php';
require_once __DIR__ . '/../controllers/admin/CouponAdminController.php';
require_once __DIR__ . '/../controllers/admin/OrderAdminController.php';
require_once __DIR__ . '/../controllers/admin/CommentAdminController.php';
require_once __DIR__ . '/../controllers/admin/QuestionAdminController.php';

$c = $_GET['c'] ?? 'dashboard';
$a = $_GET['a'] ?? 'index';

// Hỗ trợ cả key cũ (catalog/products) và key mới (categories/books) để tránh vỡ link.
$controllerMap = [
    'auth'          => AdminAuthController::class,
    'dashboard'     => DashboardAdminController::class,
    'users'         => UserAdminController::class,
    'catalog'       => CategoryAdminController::class,
    'categories'    => CategoryAdminController::class,
    'products'      => BookAdminController::class,
    'books'         => BookAdminController::class,
    'book_variants' => BookVariantAdminController::class,
    'authors'       => AuthorAdminController::class,
    'publishers'    => PublisherAdminController::class,
    'coupons'       => CouponAdminController::class,
    'orders'        => OrderAdminController::class,
    'comments'      => CommentAdminController::class,
    'qa'            => QuestionAdminController::class,
];

$cls = $controllerMap[$c] ?? null;
if (!$cls) {
    http_response_code(404);
    exit('Controller not found');
}

$ctrl = new $cls();

if (!method_exists($ctrl, $a)) {
    http_response_code(404);
    exit('Action not found');
}

echo $ctrl->$a();
