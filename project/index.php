<?php
session_start();

// NẠP BASECONTROLLER TRƯỚC
require_once __DIR__ . '/controllers/BaseController.php';

// NẠP CÁC CONTROLLER ĐANG CÓ
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/CategoryController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/BookController.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/AccountController.php';



$controllerName = isset($_GET['controller']) ? strtolower($_GET['controller']) : 'home';
$action         = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';
$id             = isset($_GET['id']) ? (int)$_GET['id'] : null;
$slug           = isset($_GET['slug']) ? $_GET['slug'] : null;

$controllerMap = [
    'home'     => 'HomeController',
    'category' => 'CategoryController',
    'auth'     => 'AuthController',
    'cart'     => 'CartController', 
    'book'     => 'BookController',
    'product'  => 'ProductController',
    'account'  => 'AccountController',
];

if (!array_key_exists($controllerName, $controllerMap)) {
    http_response_code(404);
    echo "Controller không tồn tại";
    exit;
}

$className = $controllerMap[$controllerName];

if (!class_exists($className)) {
    http_response_code(404);
    echo "Class {$className} không tồn tại";
    exit;
}

$controller = new $className();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo "Action {$action} không tồn tại";
    exit;
}

if ($slug !== null && $slug !== '') {
    echo $controller->$action($slug);
} elseif ($id !== null) {
    echo $controller->$action($id);
} else {
    echo $controller->$action();
}



