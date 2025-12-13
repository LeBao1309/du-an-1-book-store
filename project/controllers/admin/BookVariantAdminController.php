<?php
require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminBookModel.php';
require_once __DIR__ . '/../../models/admin/AdminBookVariantModel.php';

final class BookVariantAdminController extends BaseAdminController {
    public function index()
    {
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'book_id' => $_GET['book_id'] ?? '',
            'status'  => $_GET['status'] ?? '',
            'deleted' => $_GET['deleted'] ?? 0,
        ];

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $data = AdminBookVariantModel::paginate($filters, $page, 10);

        return $this->renderAdmin('admin/book_variants/index', [
            'items'     => $data['items'],
            'filters'   => $filters,
            'page'      => $page,
            'last_page' => $data['last_page'],
            'books'     => AdminBookModel::allActive(),
            'csrf'      => $this->csrfToken(),
        ]);
    }

    public function create()
    {
        return $this->renderAdmin('admin/book_variants/create', [
            'books' => AdminBookModel::allActive(),
            'csrf'  => $this->csrfToken(),
        ]);
    }

    public function store()
    {
        $this->checkCsrf();
        $bookId    = (int)($_POST['book_id'] ?? 0);
        $format    = trim($_POST['format'] ?? '');
        $price     = (float)($_POST['price'] ?? 0);
        $salePrice = $_POST['sale_price'] !== '' ? (float)$_POST['sale_price'] : null;
        $stock     = (int)($_POST['stock'] ?? 0);
        $active    = isset($_POST['is_active']) ? 1 : 0;

        if ($bookId === 0 || $format === '') {
            $this->flashError("Thiếu dữ liệu bắt buộc.");
            return $this->redirectBack();
        }

        if (!AdminBookModel::find($bookId)) {
            $this->flashError("Sách không tồn tại.");
            return $this->redirectBack();
        }

        if ($price < 0 || ($salePrice !== null && $salePrice < 0)) {
            $this->flashError("Giá không hợp lệ.");
            return $this->redirectBack();
        }

        if ($salePrice !== null && $salePrice > $price) {
            $this->flashError("Giá khuyến mãi không được lớn hơn giá gốc.");
            return $this->redirectBack();
        }

        if ($stock < 0) {
            $this->flashError("Tồn kho phải >= 0.");
            return $this->redirectBack();
        }

        AdminBookVariantModel::create([
            'book_id'    => $bookId,
            'format'     => $format,
            'price'      => $price,
            'sale_price' => $salePrice,
            'stock'      => $stock,
            'is_active'  => $active,
        ]);

        $this->flashSuccess("Tạo biến thể thành công.");
        return $this->redirect('index.php?c=book_variants&a=index');
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);

        $variant = AdminBookVariantModel::find($id);
        if (!$variant) {
            $this->flashError("Biến thể không tồn tại.");
            return $this->redirectBack();
        }

        return $this->renderAdmin('admin/book_variants/edit', [
            'variant' => $variant,
            'books'   => AdminBookModel::allActive(),
            'csrf'    => $this->csrfToken(),
        ]);
    }

    public function update()
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);

        $variant = AdminBookVariantModel::find($id);
        if (!$variant) {
            $this->flashError("Biến thể không tồn tại.");
            return $this->redirectBack();
        }

        $bookId    = (int)$_POST['book_id'];
        $format    = trim($_POST['format']);
        $price     = (float)$_POST['price'];
        $salePrice = $_POST['sale_price'] !== '' ? (float)$_POST['sale_price'] : null;
        $stock     = (int)$_POST['stock'];
        $active    = isset($_POST['is_active']) ? 1 : 0;

        if (!AdminBookModel::find($bookId)) {
            $this->flashError("Sách không tồn tại.");
            return $this->redirectBack();
        }

        if ($format === '' || $price < 0 || ($salePrice !== null && $salePrice < 0) || $stock < 0) {
            $this->flashError("Dữ liệu không hợp lệ.");
            return $this->redirectBack();
        }

        if ($salePrice !== null && $salePrice > $price) {
            $this->flashError("Giá khuyến mãi không được lớn hơn giá gốc.");
            return $this->redirectBack();
        }

        AdminBookVariantModel::updateRecord($id, [
            'book_id'    => $bookId,
            'format'     => $format,
            'price'      => $price,
            'sale_price' => $salePrice,
            'stock'      => $stock,
            'is_active'  => $active,
        ]);

        $this->flashSuccess("Cập nhật biến thể thành công.");
        return $this->redirect('index.php?c=book_variants&a=index');
    }


    public function delete()
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);

        if (!AdminBookVariantModel::find($id)) {
            $this->flashError("Biến thể không tồn tại.");
            return $this->redirectBack();
        }

        AdminBookVariantModel::softDelete($id);

        $this->flashSuccess("Đã xoá biến thể (soft delete).");
        return $this->redirect('index.php?c=book_variants&a=index');
    }


    public function restore()
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);

        if (!AdminBookVariantModel::findDeleted($id)) {
            $this->flashError("Không tìm thấy biến thể bị xoá.");
            return $this->redirectBack();
        }

        AdminBookVariantModel::restore($id);

        $this->flashSuccess("Khôi phục thành công.");
        return $this->redirect('index.php?c=book_variants&a=index&deleted=1');
    }
}
