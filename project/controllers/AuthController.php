<?php
require_once __DIR__ . '/../models/UserModel.php';
final class AuthController extends BaseController
{
    public function login(): string
    {
        $error = null;
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
        return $this->render('auth/login', compact('error','csrf'));
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
                    // auto login
                    $_SESSION['user'] = ['id'=>$id,'email'=>$email,'name'=>$name,'role'=>'user'];
                    $this->flash('success', 'Tạo tài khoản thành công. Chào mừng bạn!');
                    $this->redirect('?controller=home&action=index');       // ← về trang chủ sau đăng ký
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
}
