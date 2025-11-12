<?php
declare(strict_types=1);
ini_set('display_errors','1'); error_reporting(E_ALL); session_start();

/** BASE_URL khi đặt ở /du-an-1-book-store/project */
if (!defined('BASE_URL')) {
  $base = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
  define('BASE_URL', ($base === '' || $base === '/') ? '' : $base);
}

/** Autoload controllers + models TRONG chính thư mục project/ */
spl_autoload_register(function (string $class): void {
  $class = str_replace('\\','/',$class);
  foreach ([
    __DIR__ . '/controllers/' . $class . '.php',
    __DIR__ . '/models/'      . $class . '.php',
  ] as $f) { if (is_file($f)) { require_once $f; return; } }
});

$c = isset($_GET['c']) ? strtolower($_GET['c']) : 'home';
$a = isset($_GET['a']) ? strtolower($_GET['a']) : 'index';
if (!preg_match('/^[a-z][a-z0-9_]*$/',$c)) { http_response_code(400); exit('Invalid controller'); }
if (!preg_match('/^[a-z][a-z0-9_]*$/',$a)) { http_response_code(400); exit('Invalid action'); }

$controllerClass = ucfirst($c) . 'Controller';
if (!class_exists($controllerClass)) { http_response_code(404); exit("Controller not found: {$controllerClass}"); }

$controller = new $controllerClass();
if (!method_exists($controller,$a)) { http_response_code(404); exit("Action not found: {$a}"); }
$out = $controller->$a(); if (is_string($out)) echo $out;
