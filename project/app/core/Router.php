<?php
/**
 * Lớp Router (Bộ định tuyến)
 *
 * PHIÊN BẢN ĐÃ SỬA: Đọc URL từ $_GET['url'] (do .htaccess cung cấp)
 * để khắc phục lỗi 404 khi dùng thư mục con.
 */
class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function run() {
        // 1. Lấy phương thức (GET, POST)
        $method = $_SERVER['REQUEST_METHOD'];

        // 2. Lấy URL "sạch" từ biến 'url' của .htaccess
        // (Thay vì dùng $_SERVER['REQUEST_URI'])
        $uri = $_GET['url'] ?? ''; // Mặc định là chuỗi rỗng nếu không có

        // 3. Chuẩn hóa URI:
        // - Xóa dấu gạch chéo (/) ở cuối
        // - Đảm bảo nó luôn bắt đầu bằng một dấu /
        $uri = '/' . rtrim($uri, '/');

        // 4. Xử lý trường hợp đặc biệt cho trang chủ (khi $uri là '//' hoặc '/')
        // (Nếu $_GET['url'] rỗng, $uri sẽ là '/')
        // (Nếu $_GET['url'] là '/', $uri cũng sẽ là '/')

        // 5. Kiểm tra xem route có tồn tại không
        if (isset($this->routes[$method][$uri])) {
            
            $callback = $this->routes[$method][$uri];

            // TRƯỜNG HỢP 1: Callback là [Controller::class, 'method']
            if (is_array($callback)) {
                // (Giả sử bạn đã có Autoloader hoặc require_once ở index.php)
                $controller = new $callback[0]();
                $action = $callback[1];
                $controller->$action();
            
            } else {
                // TRƯỜN HỢP 2: Callback là một hàm (Closure)
                $callback();
            }

        } else {
            // 6. Không tìm thấy route -> Báo lỗi 404
            http_response_code(404);
            echo "404 - Page Not Found (Router.php)";
        }
    }
}
?>