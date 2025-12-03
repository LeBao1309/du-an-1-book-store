<?php
require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/../../models/UserModel.php';

final class UserAdminController extends AdminBaseController
{
    public function index(): string
    {
        $page = max(1, (int)($_GET['page'] ?? 1));

        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'role'    => $_GET['role']   ?? '',
            'status'  => $_GET['status'] ?? '',
        ];

        $pagination = UserModel::paginateForAdmin($filters, $page, 10);

        $csrf = $this->csrfToken();

        return $this->renderAdmin('admin/users/index', [
            'pagination' => $pagination,
            'filters'    => $filters,
            'csrf'       => $csrf,
        ]);
    }

    // Tạo tài khoản mới (user hoặc admin)
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=users&a=index');
            return;
        }

        $this->checkCsrf();

        $name   = trim($_POST['name'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $pass   = (string)($_POST['password'] ?? '');
        $role   = $_POST['role'] ?? 'user';
        $active = isset($_POST['is_active']);

        if ($name === '' || $email === '' || $pass === '') {
            header('Location: index.php?c=users&a=index');
            return;
        }

        // Chặn role nguy hiểm: chỉ cho 'user' | 'admin'
        if (!in_array($role, ['user','admin'], true)) {
            $role = 'user';
        }

        // Không cho người không phải admin tạo admin (phòng trường hợp bạn sau này thêm role staff)
        $currentAdmin = $_SESSION['admin'] ?? null;
        if ($role === 'admin' && (!$currentAdmin || $currentAdmin['role'] !== 'admin')) {
            $role = 'user';
        }

        try {
            UserModel::adminCreate($name, $email, $pass, $role, $active);
        } catch (\Throwable $e) {
            // có thể thêm flash error nếu muốn; hiện tại bỏ qua
        }

        header('Location: index.php?c=users&a=index');
    }

    // Cập nhật thông tin user / admin
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=users&a=index');
            return;
        }

        $this->checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?c=users&a=index');
            return;
        }

        $name   = trim($_POST['name'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $role   = $_POST['role'] ?? 'user';
        $active = isset($_POST['is_active']);
        $pass   = trim($_POST['password'] ?? '');

        if ($name === '' || $email === '') {
            header('Location: index.php?c=users&a=index');
            return;
        }

        $currentAdmin = $_SESSION['admin'] ?? null;

        if ($currentAdmin && $currentAdmin['id'] == $id && $role !== 'admin') {
            $role = 'admin';
        }


        if (!in_array($role, ['user','admin'], true)) {
            $role = 'user';
        }

        if ($currentAdmin && $currentAdmin['id'] == $id) {
            $active = true;
        }

        $newPassword = $pass === '' ? null : $pass;

        UserModel::adminUpdate($id, $name, $email, $role, $active, $newPassword);

        header('Location: index.php?c=users&a=index');
    }
}
