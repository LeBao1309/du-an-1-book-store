<?php
// project/controllers/admin/ProductAdminController.php
require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/../../models/admin/AdminBookModel.php';
require_once __DIR__ . '/../../models/admin/AdminCategoryModel.php';
require_once __DIR__ . '/../../models/admin/AdminAuthorModel.php';
require_once __DIR__ . '/../../models/admin/AdminPublisherModel.php';

final class ProductAdminController extends AdminBaseController
{
    public function index(): string
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'keyword'     => trim($_GET['keyword'] ?? ''),
            'category_id' => (int)($_GET['category_id'] ?? 0),
            'status'      => $_GET['status'] ?? '',
        ];

        $pagination = AdminBookModel::paginate($filters, $page, 10);
        $csrf       = $this->csrfToken();

        $categories = AdminCategoryModel::all();
        $authors    = AdminAuthorModel::all();
        $publishers = AdminPublisherModel::all();

        return $this->renderAdmin(
            'admin/catalog/products',
            [
                'pagination' => $pagination,
                'filters'    => $filters,
                'csrf'       => $csrf,
                'categories' => $categories,
                'authors'    => $authors,
                'publishers' => $publishers,
            ]
        );
    }

    public function store(): void
    {
        $this->checkCsrf();

        $title       = trim($_POST['title'] ?? '');
        $slug        = trim($_POST['slug'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $short_desc  = trim($_POST['short_desc'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $is_active   = isset($_POST['is_active']);

        $authorIds    = $_POST['author_ids']    ?? [];
        $publisherIds = $_POST['publisher_ids'] ?? [];

        if (!is_array($authorIds))    $authorIds    = [];
        if (!is_array($publisherIds)) $publisherIds = [];

        if ($title === '' || $category_id <= 0) {
            $_SESSION['flash_error'] = 'Vui lòng nhập tiêu đề và chọn danh mục';
            header('Location: index.php?c=products&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($title);
        }

        $bookId = AdminBookModel::create([
            'title'       => $title,
            'slug'        => $slug,
            'category_id' => $category_id,
            'short_desc'  => $short_desc,
            'description' => $description,
            'is_active'   => $is_active,
        ]);

        AdminBookModel::syncAuthors($bookId, $authorIds);
        AdminBookModel::syncPublishers($bookId, $publisherIds);

        $_SESSION['flash_success'] = 'Thêm sách thành công';
        header('Location: index.php?c=products&a=index');
    }

    public function update(): void
    {
        $this->checkCsrf();

        $id          = (int)($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $slug        = trim($_POST['slug'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $short_desc  = trim($_POST['short_desc'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $is_active   = isset($_POST['is_active']);

        $authorIds    = $_POST['author_ids']    ?? [];
        $publisherIds = $_POST['publisher_ids'] ?? [];

        if (!is_array($authorIds))    $authorIds    = [];
        if (!is_array($publisherIds)) $publisherIds = [];

        if ($id <= 0 || $title === '' || $category_id <= 0) {
            $_SESSION['flash_error'] = 'Dữ liệu không hợp lệ';
            header('Location: index.php?c=products&a=index');
            return;
        }

        if ($slug === '') {
            $slug = $this->slugify($title);
        }

        AdminBookModel::update($id, [
            'title'       => $title,
            'slug'        => $slug,
            'category_id' => $category_id,
            'short_desc'  => $short_desc,
            'description' => $description,
            'is_active'   => $is_active,
        ]);

        AdminBookModel::syncAuthors($id, $authorIds);
        AdminBookModel::syncPublishers($id, $publisherIds);

        $_SESSION['flash_success'] = 'Cập nhật sách thành công';
        header('Location: index.php?c=products&a=index');
    }

    public function delete(): void
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            AdminBookModel::delete($id);
            $_SESSION['flash_success'] = 'Xóa sách thành công';
        }
        header('Location: index.php?c=products&a=index');
    }

    private function slugify(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^\p{L}\p{N}]+/u', '-', $str);
        $str = trim($str, '-');
        return $str ?: 'sach';
    }
}
