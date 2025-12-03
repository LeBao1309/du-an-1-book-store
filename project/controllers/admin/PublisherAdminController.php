<?php
require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/../../models/admin/AdminPublisherModel.php';

final class PublisherAdminController extends AdminBaseController
{
    public function index(): string
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
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

        if ($name === '') {
            $_SESSION['flash_error'] = 'Tên nhà xuất bản không được để trống';
            header('Location: index.php?c=publishers&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        AdminPublisherModel::create($name, $slug);
        $_SESSION['flash_success'] = 'Thêm nhà xuất bản thành công';
        header('Location: index.php?c=publishers&a=index');
    }

    public function update(): void
    {
        $this->checkCsrf();

        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        if ($id <= 0 || $name === '') {
            $_SESSION['flash_error'] = 'Dữ liệu không hợp lệ';
            header('Location: index.php?c=publishers&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        AdminPublisherModel::update($id, $name, $slug);
        $_SESSION['flash_success'] = 'Cập nhật nhà xuất bản thành công';
        header('Location: index.php?c=publishers&a=index');
    }

   public function delete(): void
   {
      $this->checkCsrf();
      $id = (int)($_POST['id'] ?? 0);

      if ($id > 0) {
         $count = AdminPublisherModel::countUsedInBooks($id);

         if ($count > 0) {
               $_SESSION['flash_error'] = "Không thể xoá: nhà xuất bản đang được dùng ở {$count} sách.";
         } else {
               if (AdminPublisherModel::delete($id)) {
                  $_SESSION['flash_success'] = 'Xóa nhà xuất bản thành công';
               } else {
                  $_SESSION['flash_error'] = 'Không thể xoá nhà xuất bản (lỗi hệ thống).';
               }
         }
      }

      header('Location: index.php?c=publishers&a=index');
   }


    private function slugify(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^\p{L}\p{N}]+/u', '-', $str);
        $str = trim($str, '-');
        return $str ?: 'nha-xuat-ban';
    }
}
