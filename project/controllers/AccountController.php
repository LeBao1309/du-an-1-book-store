<?php
require_once __DIR__ . '/../models/UserModel.php';

final class AccountController extends BaseController
{
    private function requireLogin(): array
    {
        $u = $_SESSION['user'] ?? null;
        if (!$u) {
            $this->flash('error', 'Vui lòng đăng nhập');
            $this->redirect('?controller=auth&action=login');
        }
        return $u;
    }

    public function profile(): string
    {
        $u = $this->requireLogin();
        $user = UserModel::findById((int)$u['id']);
        $csrf = $this->csrfToken();
        $active = 'profile';

        return $this->render('account/profile', compact('user','csrf','active'));
    }

    public function updateProfile(): string
    {
        $u = $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();

            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            $error = null;
            if ($name === '' || $email === '') {
                $error = 'Tên và email không được rỗng';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ';
            } elseif (UserModel::emailExistsForOther($email, (int)$u['id'])) {
                $error = 'Email đã được sử dụng';
            }

            if ($error) {
                $user = UserModel::findById((int)$u['id']);
                $csrf = $this->csrfToken();
                $active = 'profile';
                return $this->render('account/profile', compact('user','csrf','active','error'));
            }

            UserModel::updateProfile((int)$u['id'], $name, $email);

            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;

            $this->flash('success', 'Cập nhật thông tin thành công');
            $this->redirect('?controller=account&action=profile');
        }

        $this->redirect('?controller=account&action=profile');
        return '';
    }
}
