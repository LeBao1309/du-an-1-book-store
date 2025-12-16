<?php

require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminBookModel.php';
require_once __DIR__ . '/../../models/admin/AdminCategoryModel.php';
require_once __DIR__ . '/../../models/admin/AdminAuthorModel.php';
require_once __DIR__ . '/../../models/admin/AdminPublisherModel.php';
require_once __DIR__ . '/../../models/admin/AdminBookVariantModel.php';

final class BookAdminController extends BaseAdminController
{
    /* ================================================================
       LIST + FILTER + PAGINATION
    ================================================================ */
    public function index()
    {
        $filters = [
            "keyword"     => $_GET["keyword"] ?? "",
            "category_id" => $_GET["category_id"] ?? "",
            "status"      => $_GET["status"] ?? "",
            "deleted"     => (int)($_GET["deleted"] ?? 0),
        ];

        $page = isset($_GET["page"]) ? max(1, (int)$_GET["page"]) : 1;

        $pagination = AdminBookModel::paginate($filters, $page, 10);

        return $this->renderAdmin("admin/catalog/products", [
            "pagination" => $pagination,
            "filters"    => $filters,
            "csrf"       => $this->csrfToken(),
            "categories" => AdminCategoryModel::allActive(),
            "authors"    => AdminAuthorModel::allActive(),
            "publishers" => AdminPublisherModel::allActive(),
        ]);
    }

    /**
     * Quản lý gallery ảnh cho một sách.
     */
    public function images()
    {
        $bookId = (int)($_GET['id'] ?? 0);
        $book   = AdminBookModel::find($bookId);
        if (!$book) {
            $this->flashError("Sách không tồn tại.");
            return $this->redirect("index.php?c=products&a=index");
        }

        return $this->renderAdmin("admin/catalog/product_images", [
            'book'   => $book,
            'images' => AdminBookModel::getImages($bookId),
            'csrf'   => $this->csrfToken(),
        ]);
    }


    /* ================================================================
       CREATE FORM
    ================================================================ */
    public function create()
    {
        // UI đang là one-page, chuyển về danh sách.
        return $this->redirect("index.php?c=products&a=index");
    }


    /* ================================================================
       STORE
    ================================================================ */
    public function store()
    {
        $this->checkCsrf();

        $title       = trim($_POST["title"] ?? "");
        $slugInput   = trim($_POST["slug"] ?? "");
        $categoryId  = (int)($_POST["category_id"] ?? 0);
        $shortDesc   = trim($_POST["short_desc"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $active      = isset($_POST["is_active"]) ? 1 : 0;

        $authorIds   = array_unique(array_map('intval', $_POST["author_ids"] ?? []));
        $publisherIds= array_unique(array_map('intval', $_POST["publisher_ids"] ?? []));
        $publisherId = $publisherIds[0] ?? 0;

        if ($title === "" || $categoryId === 0) {
            $this->flashError("Vui lòng nhập đầy đủ thông tin bắt buộc.");
            return $this->redirect("index.php?c=products&a=index");
        }

        $category = AdminCategoryModel::find($categoryId);
        if (!$category) {
            $this->flashError("Danh mục không hợp lệ.");
            return $this->redirect("index.php?c=products&a=index");
        }

        $validAuthors    = array_column(AdminAuthorModel::allActive(), 'name', 'id');
        $validPublishers = array_column(AdminPublisherModel::allActive(), 'name', 'id');

        foreach ($authorIds as $aid) {
            if (!isset($validAuthors[$aid])) {
                $this->flashError("Tác giả không hợp lệ.");
                return $this->redirect("index.php?c=products&a=index");
            }
        }

        if ($publisherId && !isset($validPublishers[$publisherId])) {
            $this->flashError("Nhà xuất bản không hợp lệ.");
            return $this->redirect("index.php?c=products&a=index");
        }

        $slug = $slugInput !== '' ? $slugInput : $this->slugify($title);
        $base = $slug;
        $i = 1;
        while (AdminBookModel::isSlugExist($slug)) {
            $slug = $base . "-" . $i++;
        }

        $bookId = AdminBookModel::create([
            "title"       => $title,
            "slug"        => $slug,
            "category_id" => $categoryId,
            "short_desc"  => $shortDesc,
            "description" => $description,
            "is_active"   => $active,
        ]);

        if (!empty($authorIds)) {
            AdminBookModel::syncAuthors($bookId, $authorIds);
        }

        if ($publisherId > 0) {
            AdminBookModel::setPublisher($bookId, $publisherId);
        }

        $this->flashSuccess("Tạo sách thành công.");
        return $this->redirect("index.php?c=products&a=index");
    }


    public function edit()
    {
        return $this->redirect("index.php?c=products&a=index");
    }


    public function update()
    {
        $this->checkCsrf();

        $id = (int)($_POST["id"] ?? 0);

        $book = AdminBookModel::find($id);
        if (!$book) {
            $this->flashError("Không tìm thấy sách.");
            return $this->redirect("index.php?c=products&a=index");
        }

        $title       = trim($_POST["title"] ?? "");
        $slugInput   = trim($_POST["slug"] ?? "");
        $categoryId  = (int)($_POST["category_id"] ?? 0);
        $shortDesc   = trim($_POST["short_desc"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $active      = isset($_POST["is_active"]) ? 1 : 0;

        $authorIds    = array_unique(array_map('intval', $_POST["author_ids"] ?? []));
        $publisherIds = array_unique(array_map('intval', $_POST["publisher_ids"] ?? []));
        $publisherId  = $publisherIds[0] ?? 0;

        if ($title === "" || $categoryId === 0) {
            $this->flashError("Vui lòng nhập đầy đủ thông tin bắt buộc.");
            return $this->redirect("index.php?c=products&a=index");
        }

        $category = AdminCategoryModel::find($categoryId);
        if (!$category) {
            $this->flashError("Danh mục không hợp lệ.");
            return $this->redirect("index.php?c=products&a=index");
        }

        $validAuthors    = array_column(AdminAuthorModel::allActive(), 'name', 'id');
        $validPublishers = array_column(AdminPublisherModel::allActive(), 'name', 'id');

        foreach ($authorIds as $aid) {
            if (!isset($validAuthors[$aid])) {
                $this->flashError("Tác giả không hợp lệ.");
                return $this->redirect("index.php?c=products&a=index");
            }
        }

        if ($publisherId && !isset($validPublishers[$publisherId])) {
            $this->flashError("Nhà xuất bản không hợp lệ.");
            return $this->redirect("index.php?c=products&a=index");
        }

        $slug = $slugInput !== '' ? $slugInput : $this->slugify($title);
        $base = $slug;
        $i = 1;
        while (AdminBookModel::isSlugExist($slug, $id)) {
            $slug = $base . "-" . $i++;
        }

        AdminBookModel::updateRecord($id, [
            "title"       => $title,
            "slug"        => $slug,
            "category_id" => $categoryId,
            "short_desc"  => $shortDesc,
            "description" => $description,
            "is_active"   => $active,
        ]);

        AdminBookModel::syncAuthors($id, $authorIds);
        
        if ($publisherId > 0) {
            AdminBookModel::setPublisher($id, $publisherId);
        } else {
            AdminBookModel::setPublisher($id, 0);
        }

        $this->flashSuccess("Cập nhật sách thành công.");
        return $this->redirect("index.php?c=products&a=index");
    }

    public function delete()
    {
        $this->checkCsrf();
        $id = (int)($_POST["id"] ?? 0);

        if (!AdminBookModel::find($id)) {
            $this->flashError("Sách không tồn tại.");
            return $this->redirectBack();
        }

        AdminBookModel::softDelete($id);

        $this->flashSuccess("Đã xoá sách (soft delete).");
        return $this->redirect("index.php?c=products&a=index");
    }

    public function restore()
    {
        $this->checkCsrf();
        $id = (int)($_POST["id"] ?? 0);

        if (!AdminBookModel::findDeleted($id)) {
            $this->flashError("Không tìm thấy sách đã xoá.");
            return $this->redirect("index.php?c=products&a=index&deleted=1");
        }

        AdminBookModel::restore($id);

        $this->flashSuccess("Khôi phục sách thành công.");
        return $this->redirect("index.php?c=products&a=index&deleted=1");
    }


    public function addImage()
    {
        $this->checkCsrf();
        $bookId = (int)($_POST["book_id"] ?? 0);
        $sortInput = trim((string)($_POST["sort_order"] ?? ''));

        // Cho phép nhập nhiều dòng URL (mỗi dòng một ảnh)
        $urlsRaw = $_POST["image_urls"] ?? $_POST["image_url"] ?? '';
        $urls = array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', (string)$urlsRaw)));

        if ($bookId === 0 || empty($urls)) {
            $this->flashError("Thiếu thông tin ảnh.");
            return $this->redirectBack();
        }

        // Nếu không nhập sort, lấy tiếp nối max sort của book
        $sort = is_numeric($sortInput) ? (int)$sortInput : AdminBookModel::nextSortOrder($bookId);
        if ($sort < 0) {
            $sort = AdminBookModel::nextSortOrder($bookId);
        }

        foreach ($urls as $u) {
            if ($u === '') continue;
            AdminBookModel::addImage($bookId, $u, $sort++);
        }

        $this->flashSuccess("Thêm ảnh thành công.");
        return $this->redirect("index.php?c=products&a=images&id={$bookId}");
    }

    public function deleteImage()
    {
        $this->checkCsrf();
        $imgId  = (int)($_POST["id"] ?? 0);
        $bookId = (int)($_POST["book_id"] ?? 0);

        if ($imgId > 0) {
            AdminBookModel::deleteImage($imgId);
            $this->flashSuccess("Xoá ảnh thành công.");
        }

        return $this->redirect("index.php?c=products&a=images&id={$bookId}");
    }
}
