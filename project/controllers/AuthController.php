<?php
require_once __DIR__ . '/../models/UserModel.php';
final class AuthController extends BaseController
{
    public function login(): string
    {
        $error = null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $email = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');

            $user = UserModel::findByEmail($email);

            if ($user && (int)$user['is_active'] === 1 && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => (int)$user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                ];
                $this->flash('success', 'Đăng nhập thành công');
                $this->redirect('?controller=home&action=index');           
            } else {
                $error = 'Email/Mật khẩu không đúng hoặc tài khoản bị khóa';
            }
        }
        $csrf = $this->csrfToken();
        return $this->render('auth/login', compact('error','success','csrf'));
    }

    public function register(): string
    {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');
            $password2 = (string)($_POST['password2'] ?? $_POST['confirm_password'] ?? '');

            if ($name === '')                      $error = 'Tên không được trống';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Email không hợp lệ';
            elseif (strlen($password) < 6)         $error = 'Mật khẩu tối thiểu 6 ký tự';
            elseif ($password !== $password2)      $error = 'Mật khẩu nhập lại không khớp';
            else {
                $m = new UserModel();
                if ($m->findByEmail($email)) $error = 'Email đã tồn tại';
                else {
                  $id = UserModel::create($name, $email, $password);
                    $_SESSION['user'] = ['id'=>$id,'email'=>$email,'name'=>$name,'role'=>'user'];
                    $this->flash('success', 'Tạo tài khoản thành công. Chào mừng bạn!');
                    $this->redirect('?controller=home&action=index');
                }
            }
        }
        $csrf = $this->csrfToken();
        return $this->render('auth/register', compact('error','csrf'));
    }

    public function logout(): string
    {
        unset($_SESSION['user']);
        $this->flash('success', 'Đã đăng xuất');
        $this->redirect('?controller=home&action=index');
        return '';
    }

    /**
     * Hiển thị / xử lý form quên mật khẩu (user).
     */
    public function forgot(): string
    {
        $message = null;
        $error   = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $email = trim($_POST['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ';
            } else {
                $user = UserModel::findByEmail($email);
                if (!$user || (int)$user['is_active'] !== 1) {
                    // Ẩn thông tin tồn tại để tránh lộ dữ liệu
                    $message = 'Nếu email tồn tại, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu.';
                } else {
                    $token = UserModel::createResetToken((int)$user['id']);
                    if ($token) {
                        $sent = $this->sendResetEmail($user['email'], $user['name'], $token, false);
                        $message = $sent
                            ? 'Nếu email tồn tại, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu.'
                            : 'Không gửi được email đặt lại mật khẩu, vui lòng thử lại sau hoặc liên hệ hỗ trợ.';
                    } else {
                        $message = 'Không thể tạo liên kết đặt lại, thử lại sau.';
                    }
                }
            }
        }

        $csrf = $this->csrfToken();
        return $this->render('auth/forgot_password', compact('message','error','csrf'));
    }

    /**
     * Đặt lại mật khẩu bằng token.
     */
    public function reset(): string
    {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        $error = null;
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $password  = (string)($_POST['password'] ?? '');
            $confirm   = (string)($_POST['password2'] ?? '');

            $record = UserModel::findResetByToken($token);
            if (!$record) {
                $error = 'Liên kết không hợp lệ hoặc đã hết hạn.';
            } elseif (strlen($password) < 6) {
                $error = 'Mật khẩu tối thiểu 6 ký tự.';
            } elseif ($password !== $confirm) {
                $error = 'Mật khẩu nhập lại không khớp.';
            } else {
                UserModel::updatePassword((int)$record['user_id'], $password);
                UserModel::markResetUsed($token);
                $_SESSION['flash_success'] = 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.';
                $this->redirect('?controller=auth&action=login');
            }
        } else {
            // GET: kiểm tra token hợp lệ trước khi hiển thị form
            if (!UserModel::findResetByToken($token)) {
                $error = 'Liên kết không hợp lệ hoặc đã hết hạn.';
            }
        }

        $csrf = $this->csrfToken();
        return $this->render('auth/reset_password', [
            'error' => $error,
            'message' => $message,
            'csrf' => $csrf,
            'token' => $token,
        ]);
    }

    private function sendResetEmail(string $email, string $name, string $token, bool $isAdmin): bool
    {
        $link = $isAdmin
            ? 'index.php?c=auth&a=reset&token=' . urlencode($token)
            : 'index.php?controller=auth&action=reset&token=' . urlencode($token);

        $subject = '[BookStore] Đặt lại mật khẩu';
        $body = "Xin chào {$name},\n\n"
              . "Bạn (hoặc ai đó) đã yêu cầu đặt lại mật khẩu. "
              . "Nhấp liên kết dưới đây để đặt lại mật khẩu:\n{$link}\n\n"
              . "Liên kết có hiệu lực 60 phút. Nếu không phải bạn, hãy bỏ qua email này.";

        require_once __DIR__ . '/../services/EmailService.php';
        $mailer = new EmailService();
        $sent = $mailer->sendPlain($email, $name, $subject, $body);

        return $sent;
    }
}
