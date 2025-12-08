<?php
require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminAuthorModel.php';

final class AuthorAdminController extends BaseAdminController
{
    public function index(): string
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'status'  => $_GET['status'] ?? '',
            'deleted' => (int)($_GET['deleted'] ?? 0),
        ];

        $pagination = AdminAuthorModel::paginate($filters, $page, 10);
        $csrf       = $this->csrfToken();

        return $this->renderAdmin('admin/catalog/authors', [
            'pagination' => $pagination,
            'filters'    => $filters,
            'csrf'       => $csrf,
        ]);
    }

    public function store(): void
    {
        $this->checkCsrf();

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $isActive = isset($_POST['is_active']);

        if ($name === '') {
            $_SESSION['flash_error'] = 'Tên tác giả không được để trống';
            header('Location: index.php?c=authors&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        $base = $slug;
        $i    = 1;
        while (AdminAuthorModel::isSlugExist($slug)) {
            $slug = $base . '-' . $i++;
        }

        AdminAuthorModel::create($name, $slug, $isActive);
        $_SESSION['flash_success'] = 'Thêm tác giả thành công';
        header('Location: index.php?c=authors&a=index');
    }

    public function update(): void
    {
        $this->checkCsrf();

        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $isActive = isset($_POST['is_active']);

        if ($id <= 0 || $name === '') {
            $_SESSION['flash_error'] = 'Dữ liệu không hợp lệ';
            header('Location: index.php?c=authors&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        $base = $slug;
        $i    = 1;
        while (AdminAuthorModel::isSlugExist($slug, $id)) {
            $slug = $base . '-' . $i++;
        }

        AdminAuthorModel::update($id, $name, $slug, $isActive);
        $_SESSION['flash_success'] = 'Cập nhật tác giả thành công';
        header('Location: index.php?c=authors&a=index');
    }

   public function delete(): void
   {
      $this->checkCsrf();
      $id = (int)($_POST['id'] ?? 0);

      if ($id > 0) {
         $author = AdminAuthorModel::find($id);
         if (!$author) {
            $_SESSION['flash_error'] = 'Tác giả không tồn tại hoặc đã bị xóa.';
            header('Location: index.php?c=authors&a=index');
            return;
         }

         $count = AdminAuthorModel::countUsedInBooks($id);

         if ($count > 0) {
               $_SESSION['flash_error'] = "Không thể xoá: tác giả đang được dùng ở {$count} sách.";
         } else {
             if (AdminAuthorModel::softDelete($id)) {
                $_SESSION['flash_success'] = 'Đã xóa tác giả (soft delete).';
             } else {
                $_SESSION['flash_error'] = 'Không thể xoá tác giả (lỗi hệ thống).';
             }
         }
      }

      header('Location: index.php?c=authors&a=index');
   }

   public function restore(): void
   {
      $this->checkCsrf();
      $id = (int)($_POST['id'] ?? 0);

      $row = AdminAuthorModel::findDeleted($id);
      if (!$row) {
         $_SESSION['flash_error'] = 'Tác giả không tồn tại trong thùng rác.';
         header('Location: index.php?c=authors&a=index&deleted=1');
         return;
      }

      AdminAuthorModel::restore($id);
      $_SESSION['flash_success'] = 'Khôi phục tác giả thành công';
      header('Location: index.php?c=authors&a=index&deleted=1');
   }
}
