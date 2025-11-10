<?php
/**
 * AuthController
 *
 * Quản lý tất cả logic liên quan đến Đăng nhập, Đăng ký, Đăng xuất.
 * Kế thừa BaseController để sử dụng các hàm trợ giúp (helpers)
 * như: $this->redirect(), $this->param(), $this->setFlash(), v.v.
 */
class AuthController extends BaseController {
    
    /**
     * @var User Model để tương tác với bảng 'users'
     */
    private $userModel;

    /**
     * Hàm khởi tạo (Constructor)
     *
     * Tự động chạy khi "new AuthController()" được gọi.
     * Chúng ta dùng nó để khởi tạo Model User.
     */
    public function __construct() {
        // (Nếu bạn chưa có autoloader, bạn cần require_once file User.php)
        // require_once __DIR__ . '/../models/User.php';
        
        $this->userModel = new User();
    }

    /**
     * Hiển thị trang (form) đăng nhập.
     * URL: (GET) /login
     */
    public function showLogin() {
        // "Vệ sĩ": Nếu đã đăng nhập, ném về trang chủ.
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }

        // Lấy (hoặc tạo) CSRF token để truyền cho View
        $csrfToken = $this->csrfToken();

        // Render view, truyền token và các tin nhắn flash (nếu có)
        // View sẽ dùng $this->getFlash() để hiển thị.
        $this->view('auth/login', [
            'error'     => $this->getFlash('error'),
            'success'   => $this->getFlash('success'),
            'csrfToken' => $csrfToken
        ]);
    }

    /**
     * Xử lý (logic) form đăng nhập.
     * URL: (POST) /login
     */
    public function login() {
        // 1. "Vệ sĩ": Kiểm tra CSRF token và đảm bảo đây là POST.
        // Hàm này sẽ tự động redirect nếu token sai.
        $this->verifyCsrf();

        // 2. Lấy input an toàn (từ BaseController)
        // $this->param() tự động 'trim' và ưu tiên lấy từ POST
        $email = $this->param('email');
        $password = $this->param('password');

        // 3. Validate (Xác thực)
        if (empty($email) || empty($password)) {
            // Dùng helper $this->setFlash() để gửi thông báo
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            // Dùng helper $this->redirect() để chuyển hướng
            $this->redirect('/login');
        }

        // 4. Kiểm tra logic nghiệp vụ
        $user = $this->userModel->findByEmail($email);

        // Kiểm tra user có tồn tại VÀ mật khẩu có khớp không
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $this->setFlash('error', 'Email hoặc mật khẩu không đúng');
            $this->redirect('/login');
        }

        // 5. Đăng nhập thành công: Lưu vào Session
        // (Session đã được start() ở file index.php)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Chuyển về trang chủ
        $this->redirect('/');
    }

    /**
     * Hiển thị trang (form) đăng ký.
     * URL: (GET) /register
     */
    public function showRegister() {
        // "Vệ sĩ": Nếu đã đăng nhập, ném về trang chủ.
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }

        // Lấy token để truyền cho view
        $csrfToken = $this->csrfToken();

        $this->view('auth/register', [
            'error'     => $this->getFlash('error'),
            'csrfToken' => $csrfToken
        ]);
    }

    /**
     * Xử lý (logic) form đăng ký.
     * URL: (POST) /register
     */
    public function register() {
        // 1. "Vệ sĩ": Kiểm tra CSRF token.
        $this->verifyCsrf();

        // 2. Lấy input an toàn.
        // Dùng only() để chỉ lấy các key được phép (tránh Mass Assignment)
        // $this->param() sẽ tự động trim()
        $data = $this->only(['name', 'email', 'password', 'confirm_password']);

        // 3. Validate (Xác thực)
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/register');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Email không hợp lệ');
            $this->redirect('/register');
        }

        if (strlen($data['password']) < 6) {
            $this->setFlash('error', 'Mật khẩu phải có ít nhất 6 ký tự');
            $this->redirect('/register');
        }

        if ($data['password'] !== $data['confirm_password']) {
            $this->setFlash('error', 'Mật khẩu xác nhận không khớp');
            $this->redirect('/register');
        }

        // Kiểm tra nghiệp vụ (email đã tồn tại?)
        if ($this->userModel->findByEmail($data['email'])) {
            $this->setFlash('error', 'Email đã tồn tại');
            $this->redirect('/register');
        }

        // 4. Tạo user
        // (Hàm create() trong Model sẽ tự động hash mật khẩu)
        $this->userModel->create($data['name'], $data['email'], $data['password']);
        
        // 5. Gửi thông báo thành công và chuyển hướng về trang login
        $this->setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        $this->redirect('/login');
    }

    /**
     * Đăng xuất người dùng.
     * URL: (GET hoặc POST) /logout
     */
    public function logout() {
        // Xóa tất cả dữ liệu session
        session_destroy();
        
        // Chuyển về trang đăng nhập
        $this->redirect('/login');
    }
}