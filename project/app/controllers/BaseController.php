<?php
/**
 * LỚP CONTROLLER CHA (BaseController)
 *
 * Mục đích: Đây là lớp "Core" mà TẤT CẢ các Controller khác
 * (như AuthController, CategoryController) sẽ kế thừa.
 *
 * Nó cung cấp một "bộ công cụ" (toolkit) các hàm trợ giúp (helpers)
 * để xử lý các tác vụ lặp đi lặp lại như:
 * - Lấy dữ liệu (input)
 * - Bảo mật (CSRF, Auth)
 * - Gửi phản hồi (redirect, flash message)
 *
 * CÁCH DÙNG:
 * 1. Tạo file controller con, ví dụ: `CategoryController.php`
 * 2. Khai báo: `class CategoryController extends BaseController { ... }`
 * 3. Bên trong class con, bạn có thể gọi các hàm này bằng:
 * $this->isPost(), $this->param('name'), $this->setFlash(...), v.v.
 */
abstract class BaseController {
    
    // ===============================================================
    // 1. View & Redirect Helpers (Xử lý Phản hồi Cơ bản)
    // ===============================================================

    /**
     * Render một file View (và truyền dữ liệu cho nó).
     *
     * @param string $viewPath Đường dẫn file view (tính từ 'app/views/'). Ví dụ: 'auth/login'
     * @param array $data Mảng dữ liệu truyền cho view. Ví dụ: ['name' => 'John']
     *
     * @example $this->view('home/index', ['user' => $user]);
     */
    protected function view($viewPath, $data = []) {
        // Biến mảng $data['name'] thành biến $name
        extract($data); 
        
        // Sửa đường dẫn: Giả sử file index.php ở public/, 
        // thì BaseController ở app/core/
        $viewFile = __DIR__ . "/../views/{$viewPath}.php";
        if (!file_exists($viewFile)) {
            die("View not found: {$viewFile}");
        }
        
        // (Nâng cao: Bạn có thể 'require' header/footer chung ở đây)
        require_once $viewFile;
    }

    /**
     * Chuyển hướng người dùng đến một URL khác.
     *
     * @param string $url Đường dẫn cần chuyển đến (vd: '/login' hoặc 'http://...')
     *
     * @example $this->redirect('/admin/dashboard');
     */
  protected function redirect($url) {
        
        // Chúng ta phải đảm bảo BASE_URL (từ file config [cite: "project/config/database.php"])
        // được thêm vào ĐẦU của URL.
        //
        // CŨ (Bị lỗi): header("Location: {$url}"); 
        // -> /login -> http://localhost/login
        //
        // MỚI (Đã sửa):
        // -> /login -> http://localhost/du-an-1-book-store/project/public/login
        
        header("Location: " . BASE_URL . $url);
        exit; // Luôn 'exit' ngay sau khi 'header'
    }

    /**
     * Quay lại trang trước đó (trang đã gửi request).
     * Rất hữu ích khi xử lý form lỗi và muốn quay lại.
     *
     * @example $this->back();
     */
    protected function back(): void {
        $ref = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/');
        // Không dùng $this->redirect() ở đây vì $ref đã là URL đầy đủ
        header("Location: {$ref}");
        exit;
    }

    
    // ===============================================================
    // 2. Flash Message Helpers (Gửi thông báo)
    // ===============================================================

    /**
     * Đặt một "tin nhắn flash" vào Session.
     * (Tin nhắn này chỉ tồn tại cho đến khi nó được đọc 1 lần).
     *
     * @param string $type Loại thông báo (vd: 'error', 'success')
     * @param string $message Nội dung tin nhắn
     *
     * @example $this->setFlash('error', 'Email hoặc mật khẩu sai!');
     */
    protected function setFlash($type, $message) {
        // Giả sử session_start() đã được gọi ở index.php
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Lấy một "tin nhắn flash" (và xóa nó ngay sau đó).
     * (Dùng hàm này trong View để hiển thị thông báo)
     *
     * @param string $type Loại thông báo (vd: 'error', 'success')
     * @return string|null Trả về nội dung tin nhắn, hoặc null nếu không có.
     *
     * @example $error = $this->getFlash('error'); if($error) { echo $error; }
     */
    protected function getFlash($type) {
        if (isset($_SESSION['flash'][$type])) {
            $m = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]); // Xóa ngay sau khi đọc
            return $m;
        }
        return null;
    }

    // ===============================================================
    // 3. Auth Helpers (Xử lý Xác thực & Phân quyền)
    // ===============================================================

    /**
     * Kiểm tra xem người dùng đã đăng nhập hay chưa.
     * @return bool
     */
    protected function isLoggedIn() { 
        return isset($_SESSION['user_id']); 
    }

    /**
     * Lấy thông tin của người dùng đang đăng nhập.
     * @return array|null
     */
    protected function currentUser() {
        if (!$this->isLoggedIn()) return null;
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['name'],
            'role' => $_SESSION['role']
        ];
    }

    /**
     * "Vệ sĩ": Yêu cầu người dùng phải đăng nhập.
     * Nếu chưa, tự động chuyển hướng về trang /login.
     * (Đặt ở đầu các hàm cần bảo vệ)
     *
     * @example public function dashboard() { $this->requireLogin(); ... }
     */
    protected function requireLogin(): void {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Bạn cần đăng nhập để xem trang này.');
            $this->redirect('/login'); // Cần đảm bảo bạn có route '/login'
        }
    }

    /**
     * "Vệ sĩ": Yêu cầu người dùng phải có 1 quyền (role) nhất định.
     * Tự động gọi 'requireLogin()' trước.
     *
     * @param string|array $roles Quyền yêu cầu (vd: 'admin' hoặc ['admin', 'editor'])
     *
     * @example public function manageUsers() { $this->requireRole('admin'); ... }
     */
    protected function requireRole($roles): void {
        $this->requireLogin(); // Đảm bảo đã đăng nhập
        
        $role = $_SESSION['role'] ?? 'user';
        $allowed = is_array($roles) ? $roles : [$roles]; // Chuyển 'admin' thành ['admin']

        // Nếu quyền của user không nằm trong danh sách cho phép
        if (!in_array($role, $allowed, true)) {
            $this->setFlash('error', 'Bạn không có quyền truy cập trang này.');
            $this->redirect('/'); // Chuyển về trang chủ
        }
    }

    // ===============================================================
    // 4. Input Helpers (Lấy dữ liệu an toàn)
    // ===============================================================

    /**
     * Kiểm tra đây có phải là request POST hay không.
     * @return bool
     */
    protected function isPost(): bool { 
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; 
    }

    /**
     * Lấy 1 tham số từ $_POST hoặc $_GET (ưu tiên $_POST).
     * Tự động làm sạch (sanitize) dữ liệu.
     *
     * @param string $key Tên tham số (vd: 'email', 'id')
     * @param mixed $default Giá trị trả về nếu không tìm thấy key
     * @return mixed
     *
     * @example $email = $this->param('email');
     */
    protected function param(string $key, $default = null) {
        if (isset($_POST[$key])) return $this->sanitize($_POST[$key]);
        if (isset($_GET[$key]))  return $this->sanitize($_GET[$key]);
        return $default;
    }

    /**
     * Lấy một danh sách các tham số được chỉ định (whitelist).
     * Rất hữu ích để lấy dữ liệu form cho vào Model.
     *
     * @param array $keys Danh sách các key (vd: ['name', 'email', 'password'])
     * @return array Mảng kết quả (vd: ['name' => 'John', 'email' => 'a@b.com', 'password' => null])
     *
     * @example $data = $this->only(['name', 'email']); $model->insert($data);
     */
    protected function only(array $keys): array {
        $out = [];
        foreach ($keys as $k) {
            $out[$k] = $this->param($k);
        }
        return $out;
    }

    /**
     * Hàm nội bộ: Làm sạch (sanitize) dữ liệu đầu vào.
     * (Cắt khoảng trắng thừa)
     */
    protected function sanitize($value) {
        if (is_array($value)) {
            // Nếu là mảng, làm sạch từng phần tử
            return array_map([$this, 'sanitize'], $value);
        }
        if (is_string($value)) {
            // Nếu là chuỗi, cắt khoảng trắng
            return trim($value);
        }
        return $value;
    }

    // ===============================================================
    // 5. CSRF Helpers (Bảo mật Form)
    // ===============================================================

    /**
     * Lấy (hoặc tạo mới) một CSRF token.
     * (Dùng trong Controller để truyền cho View)
     *
     * @return string
     * @example $data['csrf'] = $this->csrfToken(); $this->view('form', $data);
     */
    protected function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * "Vệ sĩ": Kiểm tra CSRF token khi xử lý POST.
     * Tự động báo lỗi và quay lại (redirect back) nếu token sai.
     *
     * @example public function handleLogin() { $this->verifyCsrf(); ... }
     */
    protected function verifyCsrf(): void {
        if (!$this->isPost()) return; // Chỉ kiểm tra POST

        $token = $_POST['_csrf'] ?? ''; // Tên token bạn đặt trong form
        
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            // Token không khớp hoặc không tồn tại
            $this->setFlash('error', 'Phiên làm việc không hợp lệ hoặc đã hết hạn.');
            $this->back(); // Quay lại trang chứa form
        }
    }

    // ===============================================================
    // 6. Paging Helper (Trợ giúp Phân trang)
    // ===============================================================

    /**
     * Lấy thông tin phân trang từ URL (khớp với BaseModel::paginate).
     *
     * @param int $defaultLimit Số lượng item/trang mặc định
     * @return array [trang hiện tại, số lượng/trang, offset]
     *
     * @example list($page, $limit) = $this->paging(10);
     * $data = $model->paginate($limit, $page);
     */
    protected function paging(int $defaultLimit = 12): array {
        $page  = max(1, (int)($this->param('page', 1)));
        $limit = max(1, (int)($this->param('limit', $defaultLimit)));
        $offset = ($page - 1) * $limit;
        
        // Trả về một mảng chứa 3 giá trị
        return [$page, $limit, $offset];
    }
}