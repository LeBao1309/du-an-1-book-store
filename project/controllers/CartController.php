<?php

require_once __DIR__ . '/../models/BookModel.php';

class CartController extends BaseController
{
    /**
     * Chuẩn hoá giỏ hàng trong session:
     * - NEW: $_SESSION['cart'][variant_id] = ['variant_id'=>int,'quantity'=>int]
     * - OLD: $_SESSION['cart'][book_id] = ['id','title','price','image_url','quantity']
     * => tự migrate OLD -> NEW (chọn variant rẻ nhất của book)
     */
    private function normalizeCartSession(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart) || !is_array($cart)) {
            $_SESSION['cart'] = [];
            return;
        }

        // Nếu phần tử đầu tiên có 'variant_id' => đã là giỏ mới
        $first = reset($cart);
        if (is_array($first) && isset($first['variant_id'])) {
            // đảm bảo format đúng
            $fixed = [];
            foreach ($cart as $k => $item) {
                $vid = (int)($item['variant_id'] ?? $k);
                $qty = (int)($item['quantity'] ?? 0);
                if ($vid > 0 && $qty > 0) {
                    $fixed[$vid] = ['variant_id' => $vid, 'quantity' => $qty];
                }
            }
            $_SESSION['cart'] = $fixed;
            return;
        }

        // Nếu không có 'variant_id' => giỏ cũ, migrate
        $new = [];
        foreach ($cart as $bookId => $item) {
            $bookId = (int)$bookId;
            $qty = (int)($item['quantity'] ?? 0);
            if ($bookId <= 0 || $qty <= 0) continue;

            $variantId = BookModel::getCheapestVariantId($bookId);
            if ($variantId <= 0) continue;

            if (isset($new[$variantId])) $new[$variantId]['quantity'] += $qty;
            else $new[$variantId] = ['variant_id' => $variantId, 'quantity' => $qty];
        }

        $_SESSION['cart'] = $new;
    }

    /**
     * Trang giỏ hàng
     * URL: index.php?controller=cart&action=index
     */
    public function index(): string
    {
        $this->normalizeCartSession();

        $cartRef = $_SESSION['cart'] ?? []; // [variant_id => ['variant_id','quantity']]
        $cartItems = [];
        $totalQuantity = 0;
        $totalPrice = 0;

        if (!empty($cartRef)) {
            $variantIds = array_map('intval', array_keys($cartRef));
            $dbItems = BookModel::getCartItemsByVariantIds($variantIds);

            // index theo variant_id
            $indexed = [];
            foreach ($dbItems as $row) {
                $indexed[(int)$row['variant_id']] = $row;
            }

            foreach ($cartRef as $vid => $ref) {
                $vid = (int)$vid;
                $qty = (int)($ref['quantity'] ?? 0);
                if ($qty <= 0) continue;
                if (!isset($indexed[$vid])) continue; // variant bị xoá

                $unit = (int)$indexed[$vid]['unit_price'];
                $sub  = $unit * $qty;

                $cartItems[] = [
                    'variant_id' => $vid,
                    'book_id'    => (int)$indexed[$vid]['book_id'],
                    'title'      => (string)$indexed[$vid]['title'],
                    'format'     => (string)($indexed[$vid]['format'] ?? ''),
                    'price'      => $unit,
                    'quantity'   => $qty,
                    'subtotal'   => $sub,
                    'image_url'  => (string)($indexed[$vid]['image_url'] ?? ''),
                ];

                $totalQuantity += $qty;
                $totalPrice    += $sub;
            }
        }

        $csrf = $this->csrfToken();

        return $this->render('cart/index', [
            // ✅ view nên dùng cartItems (hydrate DB)
            'cartItems'     => $cartItems,
            'totalQuantity' => $totalQuantity,
            'totalPrice'    => $totalPrice,
            'csrf'          => $csrf,
        ]);
    }

    /**
     * Thêm sách vào giỏ
     * URL: index.php?controller=cart&action=add&id=BOOK_ID&variant_id=VARIANT_ID (khuyến nghị)
     */
    public function add(int $bookId): string
    {
        if ($bookId <= 0) {
            $this->flash('error', 'Sách không hợp lệ');
            $this->redirect('index.php');
            return '';
        }

        $book = BookModel::findById($bookId);
        if (!$book) {
            $this->flash('error', 'Không tìm thấy sách');
            $this->redirect('index.php');
            return '';
        }

        $this->normalizeCartSession();

        // ✅ ưu tiên variant_id truyền vào, nếu không có -> lấy variant rẻ nhất
        $variantId = (int)($_GET['variant_id'] ?? 0);
        if ($variantId > 0) {
            if (!BookModel::isVariantOfBook($variantId, $bookId)) {
                $this->flash('error', 'Biến thể không hợp lệ');
                $this->redirect('index.php');
                return '';
            }
        } else {
            $variantId = BookModel::getCheapestVariantId($bookId);
        }

        if ($variantId <= 0) {
            $this->flash('error', 'Sách chưa có biến thể (variant)');
            $this->redirect('index.php');
            return '';
        }

        if (!isset($_SESSION['cart'][$variantId])) {
            $_SESSION['cart'][$variantId] = [
                'variant_id' => $variantId,
                'quantity'   => 1,
            ];
        } else {
            $_SESSION['cart'][$variantId]['quantity'] = (int)$_SESSION['cart'][$variantId]['quantity'] + 1;
        }

        $this->flash('success', 'Đã thêm sách vào giỏ hàng');

        $redirectParam = $_GET['redirect'] ?? '';
        if ($redirectParam === 'cart') {
            $this->redirect('index.php?controller=cart&action=index');
        } else {
            $backUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php?controller=cart&action=index';
            $this->redirect($backUrl);
        }

        return '';
    }

    /**
     * Cập nhật số lượng trong giỏ
     * POST quantities[variant_id] = qty
     */
    public function update(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $this->checkCsrf();
        $this->normalizeCartSession();

        if (empty($_SESSION['cart'])) {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $quantities = $_POST['quantities'] ?? [];
        if (!is_array($quantities)) {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        foreach ($quantities as $variantId => $qty) {
            $variantId = (int)$variantId;
            $qty = (int)$qty;

            if (!isset($_SESSION['cart'][$variantId])) continue;

            if ($qty <= 0) unset($_SESSION['cart'][$variantId]);
            else $_SESSION['cart'][$variantId]['quantity'] = $qty;
        }

        $this->flash('success', 'Cập nhật giỏ hàng thành công');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ
     * URL: index.php?controller=cart&action=remove&id=VARIANT_ID
     */
    public function remove(int $variantId): string
    {
        $this->normalizeCartSession();

        if (isset($_SESSION['cart'][$variantId])) {
            unset($_SESSION['cart'][$variantId]);
            $this->flash('success', 'Đã xóa sản phẩm khỏi giỏ');
        }

        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    public function clear(): string
    {
        unset($_SESSION['cart']);
        $this->flash('success', 'Đã xóa toàn bộ giỏ hàng');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    /**
     * Tăng/giảm 1 sản phẩm (theo variant_id)
     * POST: id=variant_id, direction=inc|dec
     */
    public function updateSingle(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $this->checkCsrf();
        $this->normalizeCartSession();

        if (empty($_SESSION['cart'])) {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $variantId = (int)($_POST['id'] ?? 0);
        $direction = $_POST['direction'] ?? 'inc';

        if ($variantId <= 0 || !isset($_SESSION['cart'][$variantId])) {
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $qty = (int)($_SESSION['cart'][$variantId]['quantity'] ?? 1);

        if ($direction === 'inc') $qty++;
        elseif ($direction === 'dec') $qty--;

        if ($qty <= 0) unset($_SESSION['cart'][$variantId]);
        else $_SESSION['cart'][$variantId]['quantity'] = $qty;

        $this->flash('success', 'Đã cập nhật số lượng sản phẩm');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }
}
?>