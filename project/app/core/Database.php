<?php
/**
 * Lớp Database (Singleton Pattern)
 *
 * Nhiệm vụ: Đọc hằng số từ file 'config/database.php' [cite: "project/config/database.php"]
 * để tạo ra MỘT kết nối PDO duy nhất và cung cấp
 * kết nối đó cho BaseModel.
 */
class Database {
    
    /**
     * @var Database Biến này lưu trữ 1 instance (thể hiện) DUY NHẤT
     */
    private static $instance = null;
    
    /**
     * @var PDO Biến này lưu trữ kết nối PDO
     */
    private $conn;

    /**
     * Hàm khởi tạo (constructor)
     *
     * Được đặt là "private" để ngăn "new Database()" từ bên ngoài.
     */
    private function __construct() {
        
        // File 'config/database.php' [cite: "project/config/database.php"] đã được nạp ở 'index.php' [cite: "project/public/index.php"],
        // nên chúng ta có thể dùng các hằng số (constants) ngay lập tức.
        
        try {
            // 1. Xây dựng DSN (Chuỗi kết nối)
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            // 2. Tạo kết nối PDO
            $this->conn = new PDO (
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

        } catch (PDOException $e) {
            // Nếu hằng số trong 'config/database.php' [cite: "project/config/database.php"] sai, nó sẽ báo lỗi ở đây
            die("Kết nối CSDL thất bại: " . $e->getMessage());
        }
    }

    /**
     * Lấy 1 instance (thể hiện) DUY NHẤT của lớp Database.
     *
     * Đây là cách chúng ta đảm bảo app chỉ kết nối CSDL 1 lần.
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Lấy đối tượng kết nối PDO (để BaseModel sử dụng)
     */
    public function getConnection() {
        return $this->conn;
    }
}
?>