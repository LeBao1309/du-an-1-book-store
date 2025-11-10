<?php
// 1. Khởi động Session
// (Luôn là dòng đầu tiên TRƯỚC KHI có bất kỳ output HTML nào)
session_start();

// 2. Tải file cấu hình (chứa DB, BASE_URL...)
require_once '../config/database.php';
require_once '../config/app.php';

// 3. Tải và Đăng ký Autoloader
// Đây là file DUY NHẤT chúng ta cần require từ 'core'
require_once '../app/core/Autoloader.php';
Autoloader::register();

// 4. Khởi tạo Router
// Autoloader sẽ tự động tìm và tải file 'app/core/Router.php'
$router = new Router();

// 5. Định nghĩa Routes
// Autoloader sẽ tự động tải 'HomeController' và 'AuthController'
// khi $router->run() cần đến chúng.
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// (Sau này bạn chỉ cần thêm route, không cần require file)
// $router->get('/categories', [CategoryController::class, 'index']);
// $router->post('/categories/store', [CategoryController::class, 'store']);

// 6. Chạy ứng dụng
$router->run();
?>