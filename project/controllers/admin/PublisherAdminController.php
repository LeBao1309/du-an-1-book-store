<?php
require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminPublisherModel.php';

final class PublisherAdminController extends BaseAdminController
{
    public function index(): string
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'status'  => $_GET['status'] ?? '',
            'deleted' => (int)($_GET['deleted'] ?? 0),
        ];

        $pagination = AdminPublisherModel::paginate($filters, $page, 10);
        $csrf       = $this->csrfToken();

        return $this->renderAdmin('admin/catalog/publishers', [
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
            $_SESSION['flash_error'] = 'Tên nhà xuất bản không được để trống';
            header('Location: index.php?c=publishers&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        $base = $slug;
        $i    = 1;
        while (AdminPublisherModel::isSlugExist($slug)) {
            $slug = $base . '-' . $i++;
        }

        AdminPublisherModel::create($name, $slug, $isActive);
        $_SESSION['flash_success'] = 'Thêm nhà xuất bản thành công';
        header('Location: index.php?c=publishers&a=index');
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
            header('Location: index.php?c=publishers&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        $base = $slug;
        $i    = 1;
        while (AdminPublisherModel::isSlugExist($slug, $id)) {
            $slug = $base . '-' . $i++;
        }

        AdminPublisherModel::update($id, $name, $slug, $isActive);
        $_SESSION['flash_success'] = 'Cập nhật nhà xuất bản thành công';
        header('Location: index.php?c=publishers&a=index');
    }

   public function delete(): void
   {
      $this->checkCsrf();
      $id = (int)($_POST['id'] ?? 0);

      if ($id > 0) {
         $publisher = AdminPublisherModel::find($id);
         if (!$publisher) {
            $_SESSION['flash_error'] = 'Nhà xuất bản không tồn tại hoặc đã bị xóa.';
            header('Location: index.php?c=publishers&a=index');
            return;
         }

         $count = AdminPublisherModel::countUsedInBooks($id);

         if ($count > 0) {
               $_SESSION['flash_error'] = "Không thể xoá: nhà xuất bản đang được dùng ở {$count} sách.";
         } else {
             if (AdminPublisherModel::softDelete($id)) {
                $_SESSION['flash_success'] = 'Đã xoá nhà xuất bản (soft delete).';
             } else {
                $_SESSION['flash_error'] = 'Không thể xoá nhà xuất bản (lỗi hệ thống).';
             }
         }
      }

      header('Location: index.php?c=publishers&a=index');
   }


   public function restore(): void
   {
      $this->checkCsrf();
      $id = (int)($_POST['id'] ?? 0);

      $row = AdminPublisherModel::findDeleted($id);
      if (!$row) {
         $_SESSION['flash_error'] = 'Nhà xuất bản không tồn tại trong thùng rác.';
         header('Location: index.php?c=publishers&a=index&deleted=1');
         return;
      }

      AdminPublisherModel::restore($id);
      $_SESSION['flash_success'] = 'Khôi phục nhà xuất bản thành công';
      header('Location: index.php?c=publishers&a=index&deleted=1');
   }
}
