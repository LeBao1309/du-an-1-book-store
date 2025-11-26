<?php

require_once __DIR__ . '/../models/BookModel.php';

class CartController extends BaseController
{
    /**
     * Trang giỏ hàng
     * URL: index.php?controller=cart&action=index
     */
    public function index(): string
    {
        $cart = $_SESSION['cart'] ?? [];

        $totalQuantity = 0;
        $totalPrice    = 0;

        foreach ($cart as $item) {
            $qty   = (int)($item['quantity'] ?? 0);
            $price = (int)($item['price'] ?? 0);

            $totalQuantity += $qty;
            $totalPrice    += $qty * $price;
        }

        // CSRF cho form cập nhật giỏ
        $csrf = $this->csrfToken();

        return $this->render('cart/index', [
            'cart'          => $cart,
            'totalQuantity' => $totalQuantity,
            'totalPrice'    => $totalPrice,
            'csrf'          => $csrf,
        ]);
    }

    /**
     * Thêm sách vào giỏ
     * URL: index.php?controller=cart&action=add&id=BOOK_ID
     */
    public function add(int $bookId): string
    {
        if ($bookId <= 0) {
            $this->flash('error', 'Sách không hợp lệ');
            $this->redirect('index.php');
            return '';
        }

        // Lấy thông tin sách
        $book = Book::findById($bookId);
        if (!$book) {
            $this->flash('error', 'Không tìm thấy sách');
            $this->redirect('index.php');
            return '';
        }

        // Lấy biến thể để xác định giá (min(sale_price, price))
        $variants = Book::getVariants($bookId);
        $price    = 0;

        if (!empty($variants)) {
            $prices = [];
            foreach ($variants as $v) {
                $p = $v['sale_price'] ?? $v['price'] ?? 0;
                $p = (int)$p;
                if ($p > 0) {
                    $prices[] = $p;
                }
            }
            if (!empty($prices)) {
                $price = min($prices);
            }
        }

        // Nếu vẫn chưa có giá thì cho về 0 (tránh lỗi)
        $price = (int)$price;

        // Lấy ảnh đại diện
        $images   = Book::getImages($bookId);
        $imageUrl = '';
        if (!empty($images)) {
            $imageUrl = $images[0]['image_url'] ?? '';
        }

        // Khởi tạo giỏ nếu chưa có
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu đã có thì tăng số lượng, chưa có thì thêm mới
        if (isset($_SESSION['cart'][$bookId])) {
            $_SESSION['cart'][$bookId]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$bookId] = [
                'id'        => (int)$book['id'],
                'title'     => $book['title'] ?? 'Không tên',
                'price'     => $price,
                'image_url' => $imageUrl,
                'quantity'  => 1,
            ];
        }

        $this->flash('success', 'Đã thêm sách vào giỏ hàng');

        // Quay lại trang trước (nếu có), không có thì về trang giỏ hàng
        $backUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php?controller=cart&action=index';
        $this->redirect($backUrl);
        return '';
    }

    /**
     * Cập nhật số lượng trong giỏ
     * URL: POST index.php?controller=cart&action=update
     */
    public function update(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $this->checkCsrf();

        if (empty($_SESSION['cart'])) {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $quantities = $_POST['quantities'] ?? [];
        if (!is_array($quantities)) {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        foreach ($quantities as $id => $qty) {
            $id  = (int)$id;
            $qty = (int)$qty;

            if (!isset($_SESSION['cart'][$id])) {
                continue;
            }

            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id]['quantity'] = $qty;
            }
        }

        $this->flash('success', 'Cập nhật giỏ hàng thành công');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ
     * URL: index.php?controller=cart&action=remove&id=BOOK_ID
     */
    public function remove(int $bookId): string
    {
        if (isset($_SESSION['cart'][$bookId])) {
            unset($_SESSION['cart'][$bookId]);
            $this->flash('success', 'Đã xóa sản phẩm khỏi giỏ');
        }

        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    /**
     * Xóa toàn bộ giỏ
     * URL: index.php?controller=cart&action=clear
     */
    public function clear(): string
    {
        unset($_SESSION['cart']);
        $this->flash('success', 'Đã xóa toàn bộ giỏ hàng');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }
    public function updateSingle(): string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    // Kiểm tra CSRF
    $this->checkCsrf();

    if (empty($_SESSION['cart'])) {
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    $id        = (int)($_POST['id'] ?? 0);
    $direction = $_POST['direction'] ?? 'inc';

    if ($id <= 0 || !isset($_SESSION['cart'][$id])) {
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    $qty = (int)($_SESSION['cart'][$id]['quantity'] ?? 1);

    if ($direction === 'inc') {
        $qty++;
    } elseif ($direction === 'dec') {
        $qty--;
    }

    if ($qty <= 0) {
        // nếu về 0 thì xóa luôn khỏi giỏ
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id]['quantity'] = $qty;
    }

    $this->flash('success', 'Đã cập nhật số lượng sản phẩm');
    $this->redirect('index.php?controller=cart&action=index');
    return '';
}

}
?>