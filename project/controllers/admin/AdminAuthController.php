<?php
require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/UserModel.php';

final class AdminAuthController extends BaseAdminController
{
    public function login(): string
    {
        if (!empty($_SESSION['admin'])) {
            header('Location: index.php?c=dashboard&a=index');
            exit;
        }

        $csrf = $this->csrfToken();
        return $this->render('admin/auth/login', [
            'csrf' => $csrf,
            'error' => $_SESSION['login_error'] ?? null
        ]);
    }

    public function doLogin(): void
    {
        $this->checkCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $admin = UserModel::findByEmail($email);

        if (!$admin || $admin['role'] !== 'admin') {
            $_SESSION['login_error'] = 'Email hoặc mật khẩu không đúng';
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        if (!password_verify($password, $admin['password'])) {
            $_SESSION['login_error'] = 'Email hoặc mật khẩu không đúng';
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        $_SESSION['admin'] = [
            'id'    => $admin['id'],
            'name'  => $admin['name'],
            'email' => $admin['email'],
            'role'  => 'admin'
        ];
        $_SESSION['admin_fingerprint'] = $this->fingerprint();
        session_regenerate_id(true);

        unset($_SESSION['login_error']);

        header('Location: index.php?c=dashboard&a=index');
    }

    public function logout(): void
    {
        unset($_SESSION['admin']);
        unset($_SESSION['admin_fingerprint']);
        session_regenerate_id(true);
        header('Location: index.php?c=auth&a=login');
    }

    public function forgot(): string
    {
        $message = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $email = trim($_POST['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ';
            } else {
                $user = UserModel::findByEmail($email);
                if ($user && $user['role'] === 'admin' && (int)$user['is_active'] === 1) {
                    $token = UserModel::createResetToken((int)$user['id']);
                    if ($token) {
                        $sent = $this->sendResetEmail($user['email'], $user['name'], $token);
                        $message = $sent
                            ? 'Nếu email tồn tại, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu.'
                            : 'Không gửi được email đặt lại mật khẩu, vui lòng thử lại sau hoặc liên hệ hỗ trợ.';
                    }
                }
                if (!$message) {
                    $message = 'Nếu email tồn tại, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu.';
                }
            }
        }

        $csrf = $this->csrfToken();
        return $this->render('admin/auth/forgot', compact('csrf','message','error'));
    }

    public function reset(): string
    {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $password = (string)($_POST['password'] ?? '');
            $confirm  = (string)($_POST['password2'] ?? '');

            $record = UserModel::findResetByToken($token);
            if (!$record || ($record['role'] ?? '') !== 'admin') {
                $error = 'Liên kết không hợp lệ hoặc đã hết hạn.';
            } elseif (strlen($password) < 6) {
                $error = 'Mật khẩu tối thiểu 6 ký tự.';
            } elseif ($password !== $confirm) {
                $error = 'Mật khẩu nhập lại không khớp.';
            } else {
                UserModel::updatePassword((int)$record['user_id'], $password);
                UserModel::markResetUsed($token);
                $_SESSION['flash_success'] = 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.';
                header('Location: index.php?c=auth&a=login');
                exit;
            }
        } else {
            $record = UserModel::findResetByToken($token);
            if (!$record || ($record['role'] ?? '') !== 'admin') {
                $error = 'Liên kết không hợp lệ hoặc đã hết hạn.';
            }
        }

        $csrf = $this->csrfToken();
        return $this->render('admin/auth/reset', [
            'csrf' => $csrf,
            'token'=> $token,
            'error'=> $error,
        ]);
    }

    private function sendResetEmail(string $email, string $name, string $token): bool
    {
        $link = 'index.php?c=auth&a=reset&token=' . urlencode($token);
        $subject = '[Admin] Đặt lại mật khẩu';
        $body = "Xin chào {$name},\n\n"
              . "Nhấp liên kết để đặt lại mật khẩu admin:\n{$link}\n\n"
              . "Liên kết có hiệu lực 60 phút.";
        require_once __DIR__ . '/../../services/EmailService.php';
        $mailer = new EmailService();
        $sent = $mailer->sendPlain($email, $name, $subject, $body);
        return $sent;
    }
}
