<?php
require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/../../models/UserModel.php';

final class AdminAuthController extends AdminBaseController
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

        unset($_SESSION['login_error']);

        header('Location: index.php?c=dashboard&a=index');
    }

    public function logout(): void
    {
        unset($_SESSION['admin']);
        header('Location: index.php?c=auth&a=login');
    }
}
