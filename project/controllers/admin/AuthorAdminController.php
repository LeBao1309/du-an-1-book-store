<?php
require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/../../models/admin/AdminAuthorModel.php';

final class AuthorAdminController extends AdminBaseController
{
    public function index(): string
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
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

        if ($name === '') {
            $_SESSION['flash_error'] = 'Tên tác giả không được để trống';
            header('Location: index.php?c=authors&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        AdminAuthorModel::create($name, $slug);
        $_SESSION['flash_success'] = 'Thêm tác giả thành công';
        header('Location: index.php?c=authors&a=index');
    }

    public function update(): void
    {
        $this->checkCsrf();

        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        if ($id <= 0 || $name === '') {
            $_SESSION['flash_error'] = 'Dữ liệu không hợp lệ';
            header('Location: index.php?c=authors&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        AdminAuthorModel::update($id, $name, $slug);
        $_SESSION['flash_success'] = 'Cập nhật tác giả thành công';
        header('Location: index.php?c=authors&a=index');
    }

   public function delete(): void
   {
      $this->checkCsrf();
      $id = (int)($_POST['id'] ?? 0);

      if ($id > 0) {
         $count = AdminAuthorModel::countUsedInBooks($id);

         if ($count > 0) {
               $_SESSION['flash_error'] = "Không thể xoá: tác giả đang được dùng ở {$count} sách.";
         } else {
               if (AdminAuthorModel::delete($id)) {
                  $_SESSION['flash_success'] = 'Xóa tác giả thành công';
               } else {
                  $_SESSION['flash_error'] = 'Không thể xoá tác giả (lỗi hệ thống).';
               }
         }
      }

      header('Location: index.php?c=authors&a=index');
   }


    private function slugify(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^\p{L}\p{N}]+/u', '-', $str);
        $str = trim($str, '-');
        return $str ?: 'tac-gia';
    }
}
