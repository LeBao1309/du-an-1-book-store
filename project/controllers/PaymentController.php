<?php
require_once __DIR__ . '/../models/OrderModel.php';

/**
 * PaymentController
 *
 * Xử lý các bước thanh toán:
 * - Trang checkout: chọn phương thức thanh toán
 * - Thanh toán VNPay
 * - Thanh toán MoMo
 * - Thanh toán khi nhận hàng (COD)
 */
class PaymentController extends BaseController
{
    /**
     * Tính tổng tiền giỏ hàng hiện tại (VND)
     */
    private function getCartAmount(): int
    {
        $cart = $_SESSION['cart'] ?? [];
        $totalAmount = 0;

        foreach ($cart as $item) {
            $qty   = (int)($item['quantity'] ?? 0);
            $price = (int)($item['price'] ?? 0);
            $totalAmount += $qty * $price;
        }

        return $totalAmount;
    }

    /**
     * Trang checkout: hiển thị tóm tắt đơn + chọn phương thức thanh toán
     * URL: GET index.php?controller=payment&action=checkout
     */
public function checkout(): string
{
    // 🔐 BẮT BUỘC ĐĂNG NHẬP (nếu bạn chưa thêm)
    if (empty($_SESSION['user'])) {
        $this->flash('error', 'Vui lòng đăng nhập trước khi thanh toán');
        $this->redirect('index.php?controller=auth&action=login');
        return '';
    }

    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        $this->flash('error', 'Giỏ hàng đang trống');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    $totalAmount   = $this->getCartAmount();
    $totalQuantity = 0;
    foreach ($cart as $item) {
        $totalQuantity += (int)($item['quantity'] ?? 0);
    }

    // Lấy user hiện tại
    $currentUser = $_SESSION['user'] ?? null;

    // 🟢 LẤY ĐỊA CHỈ GIAO HÀNG MẶC ĐỊNH
    $shipping = null;
    if ($currentUser && !empty($currentUser['id'])) {
        $userId    = (int)$currentUser['id'];
        $addresses = UserModel::getAddresses($userId);
        if (!empty($addresses)) {
            $shipping = $addresses[0];
        }
    }

    // ✅ KIỂM TRA ĐÃ CÓ SĐT + ĐỊA CHỈ CHƯA
    $canCheckout = false;
    if (is_array($shipping)
        && !empty($shipping['shipping_phone'])
        && !empty($shipping['full_address'])) {
        $canCheckout = true;
    }

    // CSRF cho form lựa chọn phương thức thanh toán
    $csrf = $this->csrfToken();

    return $this->render('payment/checkout', [
        'cart'          => $cart,
        'totalAmount'   => $totalAmount,
        'totalQuantity' => $totalQuantity,
        'csrf'          => $csrf,
        'shipping'      => $shipping,      // địa chỉ mặc định
        'currentUser'   => $currentUser,   // nếu cần dùng tên, email
        'canCheckout'   => $canCheckout,   // ✅ QUYỀN ĐƯỢC THANH TOÁN
    ]);
}


    /**
     * Xử lý form từ trang checkout: đọc phương thức và chuyển sang luồng tương ứng
     * URL: POST index.php?controller=payment&action=process
     */
    public function process(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=payment&action=checkout');
            return '';
        }

        $this->checkCsrf();

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->flash('error', 'Giỏ hàng đang trống');
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $totalAmount = $this->getCartAmount();
        if ($totalAmount <= 0) {
            $this->flash('error', 'Số tiền thanh toán không hợp lệ');
            $this->redirect('index.php?controller=cart&action=index');
            return '';
        }

        $method = $_POST['payment_method'] ?? '';

        switch ($method) {
            case 'vnpay':
                $this->payWithVnpay($totalAmount);
                return ''; // payWithVnpay sẽ redirect + exit

            case 'momo':
                $this->payWithMomo($totalAmount);
                return ''; // payWithMomo sẽ redirect + exit

            case 'cod':
                return $this->payWithCod($totalAmount);

            default:
                $this->flash('error', 'Phương thức thanh toán không hợp lệ');
                $this->redirect('index.php?controller=payment&action=checkout');
                return '';
        }
    }

    /* ======================================================
     *   CÁC HÀM RIÊNG CHO TỪNG PHƯƠNG THỨC THANH TOÁN
     * ====================================================== */

    /**
     * Thanh toán bằng VNPay: build URL và redirect
     */
    private function payWithVnpay(int $totalAmount): void
    {
        // ================== CẤU HÌNH VNPAY (SANDBOX) ==================
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

        // TODO: Sửa URL này cho đúng domain/thư mục thực tế của bạn
        $vnp_Returnurl = "http://localhost/du-an-1-book-store/project/index.php?controller=payment&action=vnpayReturn";

        // TODO: Thay 2 thông tin này bằng mã thật VNPAY cấp
        $vnp_TmnCode    = "YOUR_TMNCODE";
        $vnp_HashSecret = "YOUR_HASHSECRET";

        $vnp_TxnRef    = time();
        $vnp_OrderInfo = "Thanh toán đơn hàng #" . $vnp_TxnRef;
        $vnp_OrderType = "other";
        $vnp_Amount    = $totalAmount * 100; // VNPAY yêu cầu nhân 100
        $vnp_Locale    = "vn";
        $vnp_BankCode  = ""; // để trống: cho khách tự chọn
        $vnp_IpAddr    = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $inputData = [
            "vnp_Version"   => "2.1.0",
            "vnp_TmnCode"   => $vnp_TmnCode,
            "vnp_Amount"    => $vnp_Amount,
            "vnp_Command"   => "pay",
            "vnp_CreateDate"=> date('YmdHis'),
            "vnp_CurrCode"  => "VND",
            "vnp_IpAddr"    => $vnp_IpAddr,
            "vnp_Locale"    => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef"    => $vnp_TxnRef,
        ];

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);

        $query    = "";
        $hashData = "";
        $i        = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;

        if (!empty($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $vnp_Url      .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Thực tế: nên lưu đơn hàng trạng thái "pending" ở đây

        header('Location: ' . $vnp_Url);
        exit;
    }

    /**
     * VNPay redirect user về đây sau khi thanh toán xong
     * URL: GET index.php?controller=payment&action=vnpayReturn
     */
    public function vnpayReturn(): string
    {
        $vnp_HashSecret = "YOUR_HASHSECRET"; // phải trùng với payWithVnpay()

        $inputData = [];
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = "";
        $i        = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHashCheck = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $isValid = ($secureHashCheck === $vnp_SecureHash);
        $rspCode = $inputData['vnp_ResponseCode'] ?? null; // "00" = thành công
        $orderId = $inputData['vnp_TxnRef'] ?? null;
        $amount  = $inputData['vnp_Amount'] ?? null;

        if ($isValid && $rspCode === '00') {

        if (!empty($_SESSION['user']) && !empty($_SESSION['cart'])) {
            $currentUser = $_SESSION['user'];
            $userId      = (int)$currentUser['id'];
            $cart        = $_SESSION['cart'];

            $addresses = UserModel::getAddresses($userId);
            $shipping  = $addresses[0] ?? null;

            try {
                $savedOrderId = OrderModel::createFromCart(
                    $userId,
                    $shipping,
                    $cart,
                    'pending',
                    'Thanh toán VNPay thành công'
                );
            } catch (\Throwable $e) {
                $this->flash('error', 'Thanh toán thành công nhưng lỗi lưu đơn: ' . $e->getMessage());
                $savedOrderId = null;
            }
        }

        unset($_SESSION['cart']);
        $message = "Thanh toán VNPay thành công. Mã đơn: " 
                . htmlspecialchars((string)($savedOrderId ?? $orderId));
        $success = true;
    } else {
            $message = "Thanh toán VNPay thất bại hoặc dữ liệu không hợp lệ.";
            $success = false;
        }

        return $this->render('payment/vnpay_return', [
            'success' => $success,
            'message' => $message,
            'orderId' => $orderId,
            'amount'  => $amount,
        ]);
    }

    /**
     * Thanh toán bằng MoMo: gọi API create và redirect payUrl
     */
    private function payWithMomo(int $totalAmount): void
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        // TODO: Thay 3 thông tin này bằng thông tin thật từ MoMo Partner
        $partnerCode = "MOMOXXXX";
        $accessKey   = "ACCESS_KEY_DEMO";
        $secretKey   = "SECRET_KEY_DEMO";

        $orderId   = time() . "";
        $requestId = time() . "";
        $orderInfo = "Thanh toán đơn hàng #" . $orderId;

        // TODO: Sửa đúng URL project của bạn
        $redirectUrl = "http://localhost/du-an-1-book-store/project/index.php?controller=payment&action=momoReturn";
        $ipnUrl      = "http://localhost/du-an-1-book-store/project/index.php?controller=payment&action=momoIpn";

        $amount    = (string)$totalAmount;
        $extraData = "";

        $requestType = "captureWallet";

        $rawHash = "accessKey=" . $accessKey
            . "&amount=" . $amount
            . "&extraData=" . $extraData
            . "&ipnUrl=" . $ipnUrl
            . "&orderId=" . $orderId
            . "&orderInfo=" . $orderInfo
            . "&partnerCode=" . $partnerCode
            . "&redirectUrl=" . $redirectUrl
            . "&requestId=" . $requestId
            . "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'BookStore',
            'storeId'     => 'BookStore',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data)),
        ]);

        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($result === false) {
            $this->flash('error', 'Không kết nối được tới MoMo: ' . $curlError);
            $this->redirect('index.php?controller=payment&action=checkout');
            return;
        }

        $jsonResult = json_decode($result, true);
        if (!empty($jsonResult['payUrl'])) {
            header('Location: ' . $jsonResult['payUrl']);
            exit;
        }

        $message = $jsonResult['message'] ?? 'Không tạo được link thanh toán MoMo';
        $this->flash('error', 'MoMo lỗi: ' . $message);
        $this->redirect('index.php?controller=payment&action=checkout');
    }

    /**
     * MoMo redirect về sau khi thanh toán xong
     * URL: GET index.php?controller=payment&action=momoReturn
     */
    public function momoReturn(): string
    {
        $resultCode = $_GET['resultCode'] ?? null; // 0 = thành công
        $orderId    = $_GET['orderId']    ?? null;
        $amount     = $_GET['amount']     ?? null;
        $message    = $_GET['message']    ?? '';

        if ($resultCode === '0') {
    // Lưu đơn hàng
    if (!empty($_SESSION['user']) && !empty($_SESSION['cart'])) {
            $currentUser = $_SESSION['user'];
            $userId      = (int)$currentUser['id'];
            $cart        = $_SESSION['cart'];

            $addresses = UserModel::getAddresses($userId);
            $shipping  = $addresses[0] ?? null;

            try {
                $savedOrderId = OrderModel::createFromCart(
                    $userId,
                    $shipping,
                    $cart,
                    'pending',     // trạng thái giao hàng
                    'Thanh toán MoMo thành công'
                );
            } catch (\Throwable $e) {
                // Nếu fail thì vẫn nên log, nhưng tạm thời chỉ flash
                $this->flash('error', 'Thanh toán thành công nhưng lỗi lưu đơn: ' . $e->getMessage());
                $savedOrderId = null;
            }
        }

        unset($_SESSION['cart']);
        $success = true;
        $msg     = "Thanh toán MoMo thành công. Mã đơn: " 
                . htmlspecialchars((string)($savedOrderId ?? $orderId));
    } else {
            $success = false;
            $msg     = "Thanh toán MoMo thất bại: " . htmlspecialchars($message);
        }

        return $this->render('payment/momo_return', [
            'success' => $success,
            'message' => $msg,
            'orderId' => $orderId,
            'amount'  => $amount,
        ]);
    }

    /**
     * Thanh toán khi nhận hàng (COD)
     */
private function payWithCod(int $totalAmount): string
{
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        $this->flash('error', 'Giỏ hàng đang trống');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    if (empty($_SESSION['user'])) {
        $this->flash('error', 'Vui lòng đăng nhập trước khi thanh toán');
        $this->redirect('index.php?controller=auth&action=login');
        return '';
    }

    $currentUser = $_SESSION['user'];
    $userId      = (int)$currentUser['id'];

    // Lấy địa chỉ giao hàng mặc định
    $addresses = UserModel::getAddresses($userId);
    $shipping  = $addresses[0] ?? null;

    if (!$shipping) {
        $this->flash('error', 'Vui lòng cập nhật địa chỉ giao hàng trước khi đặt hàng');
        $this->redirect('index.php?controller=account&action=address');
        return '';
    }

    // 👉 LƯU ORDER XUỐNG DB
    try {
        $orderId = OrderModel::createFromCart(
            $userId,
            $shipping,
            $cart,
            'pending',   // COD: trạng thái giao hàng ban đầu
            null        // note: tạm thời để null, sau có thể thêm ghi chú từ form
        );
    } catch (\Throwable $e) {
        // Có lỗi thì báo cho user
        $this->flash('error', 'Không tạo được đơn hàng: ' . $e->getMessage());
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    // XÓA GIỎ HÀNG SAU KHI LƯU ORDER
    unset($_SESSION['cart']);

    return $this->render('payment/cod_success', [
        'orderId' => $orderId,
        'amount'  => $totalAmount,
    ]);
}

}
