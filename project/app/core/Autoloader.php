<?php
/**
 * Lớp Autoloader (Tự động tải)
 *
 * Mục đích: Tự động "require_once" một file
 * khi một class được gọi lần đầu tiên.
 */
class Autoloader {
    
    /**
     * Đăng ký hàm tự động tải
     */
    public static function register() {
        // spl_autoload_register là hàm của PHP
        // Nó nói: "Khi nào PHP không tìm thấy 1 class,
        // hãy gọi hàm 'self::autoload'"
        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * Hàm tự động tải
     *
     * @param string $className Tên class mà PHP đang tìm
     * Ví dụ: "HomeController" hoặc "BaseModel"
     */
    public static function autoload($className) {
        
        // Chuyển đổi namespace (nếu bạn dùng)
        // (Hiện tại chúng ta không dùng, nhưng đây là best practice)
        // $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

        // 1. Xác định đường dẫn gốc của 'app'
        // (Đi lên 2 cấp từ app/core -> project/)
        $baseDir = __DIR__ . '/../../'; 

        // 2. Tạo danh sách các thư mục có thể chứa class
        // (Chúng ta cần tìm trong 'controllers', 'models', 'core'...)
        $directories = [
            'app/controllers/',
            'app/models/',
            'app/core/'
        ];

        // 3. Vòng lặp để tìm file
        foreach ($directories as $dir) {
            $file = $baseDir . $dir . $className . '.php';

            // Nếu tìm thấy file, require nó và dừng lại
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
        
    }
}
?>