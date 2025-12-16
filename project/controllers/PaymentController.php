<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/CouponModel.php';
require_once __DIR__ . '/../models/BookModel.php';

/**
 * PaymentController
 *
 * Xử lý các bước thanh toán:
 * - Trang checkout: chọn phương thức thanh toán
 * - Thanh toán VNPay
 * - Thanh toán khi nhận hàng (COD)
 */
class PaymentController extends BaseController
{
    /**
     * Chuẩn hoá giỏ hàng trong session:
     * - NEW: $_SESSION['cart'][variant_id] = ['variant_id'=>int,'quantity'=>int]
     * - OLD: $_SESSION['cart'][book_id]    = ['id','title','price','image_url','quantity']
     * => tự migrate OLD -> NEW (chọn variant rẻ nhất của book)
     */
    private function normalizeCartSession(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart) || !is_array($cart)) {
            $_SESSION['cart'] = [];
            return;
        }

        // NEW cart
        $first = reset($cart);
        if (is_array($first) && isset($first['variant_id'])) {
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

        // OLD cart -> migrate
        $new = [];
        foreach ($cart as $bookId => $item) {
            $bookId = (int)$bookId;
            $qty    = (int)($item['quantity'] ?? 0);
            if ($bookId <= 0 || $qty <= 0) continue;

            $variantId = BookModel::getCheapestVariantId($bookId);
            if ($variantId <= 0) continue;

            if (isset($new[$variantId])) $new[$variantId]['quantity'] += $qty;
            else $new[$variantId] = ['variant_id' => $variantId, 'quantity' => $qty];
        }

        $_SESSION['cart'] = $new;
    }

    /**
     * Hydrate giỏ hàng từ DB theo variant_id
     * Trả về: items, totalAmount, totalQuantity
     */
    private function hydrateCartFromSession(): array
    {
        $this->normalizeCartSession();

        $cartRef = $_SESSION['cart'] ?? [];
        if (empty($cartRef)) {
            return ['items' => [], 'totalAmount' => 0, 'totalQuantity' => 0];
        }

        $variantIds = array_map('intval', array_keys($cartRef));
        $dbItems    = BookModel::getCartItemsByVariantIds($variantIds);

        $indexed = [];
        foreach ($dbItems as $row) {
            $indexed[(int)$row['variant_id']] = $row;
        }

        $items = [];
        $totalAmount = 0;
        $totalQuantity = 0;

        foreach ($cartRef as $vid => $ref) {
            $vid = (int)$vid;
            $qty = (int)($ref['quantity'] ?? 0);
            if ($qty <= 0) continue;
            if (!isset($indexed[$vid])) continue;

            $unit = (int)$indexed[$vid]['unit_price'];
            $sub  = $unit * $qty;

            $items[] = [
                'variant_id' => $vid,
                'book_id'    => (int)$indexed[$vid]['book_id'],
                'title'      => (string)$indexed[$vid]['title'],
                'format'     => (string)($indexed[$vid]['format'] ?? ''),
                'price'      => $unit,
                'quantity'   => $qty,
                'subtotal'   => $sub,
                'image_url'  => (string)($indexed[$vid]['image_url'] ?? ''),
            ];

            $totalAmount   += $sub;
            $totalQuantity += $qty;
        }

        return ['items' => $items, 'totalAmount' => $totalAmount, 'totalQuantity' => $totalQuantity];
    }

    /**
     * Trang checkout
     * GET index.php?controller=payment&action=checkout
     */
    public function checkout(): string
    {
        $this->requireAuth();

        $hydrated  = $this->hydrateCartFromSession();
        $cartItems = $hydrated['items'];

        if (empty($cartItems)) {
            $this->flash('warning', 'Giỏ hàng đang trống.');
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $currentUser = $_SESSION['user'] ?? null;
        $userId      = (int)($currentUser['id'] ?? 0);

        $addresses = UserModel::getAddresses($userId);
        $shipping  = $_SESSION['checkout_shipping'] ?? ($addresses[0] ?? null);

        $canCheckout = is_array($shipping)
            && !empty($shipping['shipping_phone'])
            && !empty($shipping['full_address']);

        $totalAmount   = (int)$hydrated['totalAmount'];
        $totalQuantity = (int)$hydrated['totalQuantity'];

        $appliedCoupon  = null;
        $discountAmount = 0.0;

        if (!empty($_SESSION['checkout_coupon']['code'])) {
            $code = $_SESSION['checkout_coupon']['code'];
            $res  = CouponModel::validateForOrder($code, $userId, $totalAmount);

            if ($res['ok']) {
                $appliedCoupon  = $res['coupon'];
                $discountAmount = (float)$res['discount'];

                $_SESSION['checkout_coupon']['coupon_id'] = (int)$appliedCoupon['id'];
                $_SESSION['checkout_coupon']['discount']  = (float)$discountAmount;
            } else {
                unset($_SESSION['checkout_coupon']);
                $this->flash('warning', $res['error']);
            }
        }

        // ✅ (2) checkout() truyền finalTotal
        $finalTotal = (int)max(0, $totalAmount - (int)$discountAmount);

        $csrf = $this->csrfToken();

        return $this->render('payment/checkout', [
            'cartItems'      => $cartItems,
            'user'           => $currentUser,
            'shipping'       => $shipping,
            'addresses'      => $addresses,
            'totalAmount'    => $totalAmount,
            'discountAmount' => $discountAmount,
            'finalTotal'     => $finalTotal, // ✅
            'appliedCoupon'  => $appliedCoupon,
            'totalQuantity'  => $totalQuantity,
            'canCheckout'    => $canCheckout,
            'csrf'           => $csrf,
        ]);
    }

    public function selectShipping(): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $currentUser = $_SESSION['user'] ?? null;
        $userId      = (int)($currentUser['id'] ?? 0);
        $shippingId  = (int)($_POST['shipping_id'] ?? 0);

        if ($shippingId <= 0 || $userId <= 0) {
            $this->flash('danger', 'Không chọn được địa chỉ giao hàng.');
            $this->redirect('index.php?controller=payment&action=checkout');
            return;
        }

        $shipping = UserModel::getAddressByIdAndUser($shippingId, $userId);
        if (!$shipping) {
            $this->flash('danger', 'Địa chỉ không hợp lệ.');
            $this->redirect('index.php?controller=payment&action=checkout');
            return;
        }

        $_SESSION['checkout_shipping'] = $shipping;
        $this->flash('success', 'Đã chọn địa chỉ giao hàng.');
        $this->redirect('index.php?controller=payment&action=checkout');
    }

    /**
     * Áp dụng mã giảm giá
     * POST index.php?controller=payment&action=applyCoupon
     */
    public function applyCoupon(): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $code = trim($_POST['coupon_code'] ?? '');
        if ($code === '') {
            $this->flash('warning', 'Vui lòng nhập mã giảm giá.');
            $this->redirect('index.php?controller=payment&action=checkout');
            return;
        }

        $currentUser = $_SESSION['user'] ?? null;
        $userId      = (int)($currentUser['id'] ?? 0);

        // ✅ (1) applyCoupon() lấy total từ DB hydrate
        $hydrated = $this->hydrateCartFromSession();
        $total    = (int)($hydrated['totalAmount'] ?? 0);

        if ($total <= 0) {
            $this->flash('warning', 'Giỏ hàng đang trống hoặc không hợp lệ.');
            $this->redirect('index.php?controller=cart&action=index');
            return;
        }

        $res = CouponModel::validateForOrder($code, $userId, $total);

        if ($res['ok']) {
            $_SESSION['checkout_coupon'] = [
                'code'      => $code,
                'coupon_id' => (int)$res['coupon']['id'],
                'discount'  => (float)$res['discount'],
            ];
            $this->flash('success', 'Đã áp dụng mã giảm giá.');
        } else {
            unset($_SESSION['checkout_coupon']);
            $this->flash('warning', $res['error']);
        }

        $this->redirect('index.php?controller=payment&action=checkout');
    }

    public function removeCoupon(): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        unset($_SESSION['checkout_coupon']);
        $this->flash('success', 'Đã bỏ mã giảm giá.');
        $this->redirect('index.php?controller=payment&action=checkout');
    }

    /**
     * Xử lý form checkout
     * POST index.php?controller=payment&action=process
     */
    public function process(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=payment&action=checkout');
            return '';
        }

        $this->checkCsrf();

        $hydrated  = $this->hydrateCartFromSession();
        $cartItems = $hydrated['items'];

        if (empty($cartItems)) {
            $this->flash('error', 'Giỏ hàng đang trống');
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $totalAmount = (int)($hydrated['totalAmount'] ?? 0);
        if ($totalAmount <= 0) {
            $this->flash('error', 'Số tiền thanh toán không hợp lệ');
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $currentUser = $_SESSION['user'] ?? null;
        $userId      = (int)($currentUser['id'] ?? 0);

        // ✅ (3) chặn cứng shipping trước VNPay/COD
        $addresses = UserModel::getAddresses($userId);
        $shipping  = $_SESSION['checkout_shipping'] ?? ($addresses[0] ?? null);

        if (
            !$shipping
            || empty($shipping['shipping_phone'])
            || empty($shipping['full_address'])
        ) {
            $this->flash('error', 'Vui lòng chọn/cập nhật địa chỉ & số điện thoại nhận hàng trước khi thanh toán.');
            $this->redirect('index.php?controller=payment&action=checkout');
            return '';
        }

        // Re-validate coupon theo tổng tiền DB
        $discountAmount = 0.0;
        $couponId = null;

        if (!empty($_SESSION['checkout_coupon']['code'])) {
            $code = $_SESSION['checkout_coupon']['code'];
            $res  = CouponModel::validateForOrder($code, $userId, $totalAmount);

            if ($res['ok']) {
                $discountAmount = (float)$res['discount'];
                $couponId       = (int)$res['coupon']['id'];

                $_SESSION['checkout_coupon']['coupon_id'] = $couponId;
                $_SESSION['checkout_coupon']['discount']  = $discountAmount;
            } else {
                unset($_SESSION['checkout_coupon']);
                $this->flash('warning', $res['error']);
            }
        }

        $payable = (int)max(0, $totalAmount - $discountAmount);

        $method = $_POST['payment_method'] ?? '';

        switch ($method) {
            case 'vnpay':
                $this->payWithVnpay($payable);
                return ''; // redirect + exit

            case 'cod':
                return $this->payWithCod($payable, $couponId, $discountAmount);

            default:
                // ✅ (4) đã xoá momo => nếu client gửi momo thì báo lỗi
                $this->flash('error', 'Phương thức thanh toán không hợp lệ');
                $this->redirect('index.php?controller=payment&action=checkout');
                return '';
        }
    }

    /**
     * VNPay: build URL và redirect
     */
    private function payWithVnpay(int $totalAmount): void
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

        // TODO: sửa theo domain thật
        $vnp_Returnurl = "http://localhost/du-an-1-book-store/project/index.php?controller=payment&action=vnpayReturn";

        // TODO: thay thông tin thật
        $vnp_TmnCode    = "7DN3KMIT";
        $vnp_HashSecret = "Y0OZ4UPLC8J4RSHAOVZO1SMZV066HF7C";

        $vnp_TxnRef    = time();
        $vnp_OrderInfo = "Thanh toán đơn hàng #" . $vnp_TxnRef;
        $vnp_OrderType = "other";
        $vnp_Amount    = $totalAmount * 100;
        $vnp_Locale    = "vn";
        $vnp_BankCode  = "NCB";
        $vnp_IpAddr    = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => $vnp_Amount,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $vnp_IpAddr,
            "vnp_Locale"     => $vnp_Locale,
            "vnp_OrderInfo"  => $vnp_OrderInfo,
            "vnp_OrderType"  => $vnp_OrderType,
            "vnp_ReturnUrl"  => $vnp_Returnurl,
            "vnp_TxnRef"     => $vnp_TxnRef,
        ];

        if (!empty($vnp_BankCode)) $inputData['vnp_BankCode'] = $vnp_BankCode;

        ksort($inputData);

        $query = "";
        $hashData = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            else { $hashData .= urlencode($key) . "=" . urlencode($value); $i = 1; }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;

        if (!empty($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        header('Location: ' . $vnp_Url);
        exit;
    }

    /**
     * Tạo đơn hàng sau khi VNPay success
     */
    private function createOrderAfterPayment(string $note = ''): ?int
    {
        $this->normalizeCartSession();

        $cartRef = $_SESSION['cart'] ?? [];
        if (empty($cartRef)) return null;

        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || empty($currentUser['id'])) return null;
        $userId = (int)$currentUser['id'];

        $addresses = UserModel::getAddresses($userId);
        $shipping  = $_SESSION['checkout_shipping'] ?? ($addresses[0] ?? null);

        if (!$shipping || empty($shipping['full_address'])) return null;

        $couponId = !empty($_SESSION['checkout_coupon']['coupon_id'])
            ? (int)$_SESSION['checkout_coupon']['coupon_id']
            : null;

        $discount = !empty($_SESSION['checkout_coupon']['discount'])
            ? (float)$_SESSION['checkout_coupon']['discount']
            : 0.0;

        $orderId = OrderModel::createFromCart(
            $userId,
            $shipping,
            $cartRef,
            'pending',
            $note,
            $couponId,
            $discount
        );

        if ($orderId && $couponId) {
            CouponModel::incrementUsage($couponId);
        }

        unset($_SESSION['cart'], $_SESSION['checkout_coupon'], $_SESSION['checkout_shipping']);

        return $orderId ?: null;
    }

    /**
     * VNPay return
     */
    public function vnpayReturn(): string
    {
        $vnp_HashSecret = "Y0OZ4UPLC8J4RSHAOVZO1SMZV066HF7C";

        $inputData = [];
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") $inputData[$key] = $value;
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            else { $hashData .= urlencode($key) . "=" . urlencode($value); $i = 1; }
        }

        $secureHashCheck = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $isValid = ($secureHashCheck === $vnp_SecureHash);
        $rspCode = $inputData['vnp_ResponseCode'] ?? null;
        $txnRef  = $inputData['vnp_TxnRef'] ?? null;
        $amount  = $inputData['vnp_Amount'] ?? null;

        $dbOrderId  = null;
        $orderItems = [];

        if ($isValid && $rspCode === '00') {
            $dbOrderId = $this->createOrderAfterPayment(
                'Thanh toán VNPay thành công - mã giao dịch: ' . $txnRef
            );

            if ($dbOrderId) {
                $orderItems = OrderModel::getItemsByOrderId((int)$dbOrderId);
            }

            $message = "Thanh toán VNPay thành công.";
            if ($dbOrderId) $message .= " Mã đơn hàng của bạn: #" . (int)$dbOrderId;

            return $this->render('payment/vnpay_return', [
                'success'    => true,
                'message'    => $message,
                'orderId'    => $dbOrderId ?: $txnRef,
                'amount'     => $amount,
                'txnRef'     => $txnRef,
                'orderItems' => $orderItems,
            ]);
        }

        return $this->render('payment/vnpay_return', [
            'success'    => false,
            'message'    => "Thanh toán VNPay thất bại hoặc dữ liệu không hợp lệ.",
            'orderId'    => $txnRef,
            'amount'     => $amount,
            'txnRef'     => $txnRef,
            'orderItems' => [],
        ]);
    }

    /**
     * COD
     */
    private function payWithCod(int $totalAmount, ?int $couponId = null, float $discountAmount = 0.0): string
    {
        $this->requireAuth();

        $hydrated  = $this->hydrateCartFromSession();
        $cartItems = $hydrated['items'];

        if (empty($cartItems)) {
            $this->flash('error', 'Giỏ hàng đang trống');
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $currentUser = $_SESSION['user'] ?? null;
        $userId = (int)($currentUser['id'] ?? 0);
        if ($userId <= 0) {
            $this->flash('error', 'Vui lòng đăng nhập trước khi thanh toán');
            $this->redirect('index.php?controller=auth&action=login');
            return '';
        }

        $addresses = UserModel::getAddresses($userId);
        $shipping  = $_SESSION['checkout_shipping'] ?? ($addresses[0] ?? null);

        if (!$shipping || empty($shipping['full_address'])) {
            $this->flash('error', 'Vui lòng cập nhật địa chỉ giao hàng trước khi thanh toán');
            $this->redirect('index.php?controller=account&action=address');
            return '';
        }

        $this->normalizeCartSession();
        $cartRef = $_SESSION['cart'] ?? [];

        $orderId = OrderModel::createFromCart(
            $userId,
            $shipping,
            $cartRef,
            'pending',
            'Thanh toán COD',
            $couponId,
            $discountAmount
        );

        if ($orderId && $couponId) {
            CouponModel::incrementUsage((int)$couponId);
        }

        unset($_SESSION['cart'], $_SESSION['checkout_shipping'], $_SESSION['checkout_coupon']);

        return $this->render('payment/cod_success', [
            'orderId'    => $orderId,
            'amount'     => $totalAmount,
            'orderItems' => $cartItems,
            'shipping'   => $shipping,
            'user'       => $currentUser,
        ]);
    }
}
