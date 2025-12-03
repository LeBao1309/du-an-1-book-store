<?php
// project/controllers/admin/CategoryAdminController.php
require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/../../models/admin/AdminCategoryModel.php';

final class CategoryAdminController extends AdminBaseController
{
    public function index(): string
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'status'  => $_GET['status'] ?? '',
        ];

        $pagination    = AdminCategoryModel::paginate($filters, $page, 10);
        $csrf          = $this->csrfToken();
        $allCategories = AdminCategoryModel::all();

        return $this->renderAdmin('admin/catalog/categories', [
            'pagination'    => $pagination,
            'filters'       => $filters,
            'csrf'          => $csrf,
            'allCategories' => $allCategories,
        ]);
    }

    public function store(): void
    {
        $this->checkCsrf();

        $name   = trim($_POST['name'] ?? '');
        $slug   = trim($_POST['slug'] ?? '');
        $parent = (int)($_POST['parent_id'] ?? 0) ?: null;
        $active = isset($_POST['is_active']);

        if ($name === '') {
            $_SESSION['flash_error'] = 'Tên danh mục không được để trống';
        } else {
            if ($slug === '') {
                $slug = self::slugify($name);
            }
            AdminCategoryModel::create($name, $slug, $parent, $active);
            $_SESSION['flash_success'] = 'Thêm danh mục thành công';
        }

        header('Location: index.php?c=catalog&a=index');
    }

    public function update(): void
    {
        $this->checkCsrf();

        $id     = (int)($_POST['id'] ?? 0);
        $name   = trim($_POST['name'] ?? '');
        $slug   = trim($_POST['slug'] ?? '');
        $parent = (int)($_POST['parent_id'] ?? 0) ?: null;
        $active = isset($_POST['is_active']);

        if ($id <= 0 || $name === '') {
            $_SESSION['flash_error'] = 'Dữ liệu không hợp lệ';
            header('Location: index.php?c=catalog&a=index');
            return;
        }

        if ($slug === '') {
            $slug = self::slugify($name);
        }

        AdminCategoryModel::update($id, $name, $slug, $parent, $active);
        $_SESSION['flash_success'] = 'Cập nhật danh mục thành công';

        header('Location: index.php?c=catalog&a=index');
    }

    public function delete(): void
    {
        $this->checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            AdminCategoryModel::delete($id);
            $_SESSION['flash_success'] = 'Xóa danh mục thành công';
        }

        header('Location: index.php?c=catalog&a=index');
    }

    private static function slugify(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^\p{L}\p{N}]+/u', '-', $str);
        $str = trim($str, '-');
        return $str ?: 'danh-muc';
    }
}
