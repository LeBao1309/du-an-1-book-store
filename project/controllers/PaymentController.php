<?php

require_once __DIR__ . '/../models/UserModel.php';   // vì bạn dùng UserModel::getAddresses
require_once __DIR__ . '/../models/OrderModel.php'; // để dùng OrderModel::createFromCart
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
    $this->requireAuth();

    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        $this->flash('warning', 'Giỏ hàng đang trống.');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    $currentUser = $_SESSION['user'] ?? null;
    $userId = (int)($currentUser['id'] ?? 0);

    // 🔹 LẤY DANH SÁCH ĐỊA CHỈ CỦA USER
    $addresses = UserModel::getAddresses($userId);

    // 🔹 ƯU TIÊN ĐỊA CHỈ ĐÃ CHỌN Ở SESSION, NẾU CHƯA CÓ THÌ LẤY ĐỊA CHỈ ĐẦU TIÊN
    $shipping = $_SESSION['checkout_shipping'] ?? ($addresses[0] ?? null);

    // 🔹 TÍNH COI CÓ ĐỦ ĐIỀU KIỆN CHECKOUT HAY CHƯA
    $canCheckout = is_array($shipping)
        && !empty($shipping['shipping_phone'])
        && !empty($shipping['full_address']);

    $totalAmount   = $this->getCartAmount(); // dùng hàm em đã có sẵn
    $totalQuantity = 0;
    foreach ($cart as $item) {
        $totalQuantity += (int)($item['quantity'] ?? 0);
    }

    $csrf = $this->csrfToken();

    return $this->render('payment/checkout', [
        'cart'          => $cart,
        'currentUser'   => $currentUser,
        'shipping'      => $shipping,
        'addresses'     => $addresses,   // 🔹 TRUYỀN XUỐNG VIEW
        'totalAmount'   => $totalAmount,
        'totalQuantity' => $totalQuantity,
        'canCheckout'   => $canCheckout,
        'csrf'          => $csrf,
    ]);
}
//
public function selectShipping(): void
{
    $this->requireAuth();
    $this->checkCsrf();

    $currentUser = $_SESSION['user'] ?? null;
    $userId = (int)($currentUser['id'] ?? 0);

    $shippingId = (int)($_POST['shipping_id'] ?? 0);

    if ($shippingId <= 0 || $userId <= 0) {
        $this->flash('danger', 'Không chọn được địa chỉ giao hàng.');
        $this->redirect('index.php?controller=payment&action=checkout');
        return;
    }

    // 🔹 LẤY ĐỊA CHỈ THEO ID + USER, ĐẢM BẢO KHÔNG LẤY NHẦM CỦA NGƯỜI KHÁC
    $shipping = UserModel::getAddressByIdAndUser($shippingId, $userId);

    if (!$shipping) {
        $this->flash('danger', 'Địa chỉ không hợp lệ.');
        $this->redirect('index.php?controller=payment&action=checkout');
        return;
    }

    // 🔹 LƯU ĐỊA CHỈ NÀY CHO LẦN CHECKOUT HIỆN TẠI
    $_SESSION['checkout_shipping'] = $shipping;

    $this->flash('success', 'Đã chọn địa chỉ giao hàng.');
    $this->redirect('index.php?controller=payment&action=checkout');
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
        $vnp_TmnCode    = "7DN3KMIT";
        $vnp_HashSecret = "Y0OZ4UPLC8J4RSHAOVZO1SMZV066HF7C";

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
     * Tạo đơn hàng trong DB từ giỏ hiện tại sau khi thanh toán thành công
     * Trả về ID đơn hàng trong DB, hoặc null nếu không tạo được
     */
    private function createOrderAfterPayment(string $note = ''): ?int
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            // Không còn giỏ => không tạo đơn được
            return null;
        }

        // Lấy user hiện tại
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || empty($currentUser['id'])) {
            return null;
        }

        $userId = (int)$currentUser['id'];

        // Lấy địa chỉ giao hàng (mặc định: phần tử đầu tiên)
        $addresses = UserModel::getAddresses($userId);
        $shipping  = $addresses[0] ?? null;   // nếu hàm createFromCart cho phép null thì vẫn OK

        // Nếu anh muốn bắt buộc phải có shipping thì có thể check ở đây
        // if (!$shipping) { return null; }

        // 🔹 TẠO ĐƠN HÀNG TRONG DB
        // Giả sử OrderModel::createFromCart:
        // createFromCart(int $userId, ?array $shipping, array $cart, string $shippingStatus = 'pending', ?string $note = null): int
        $orderId = OrderModel::createFromCart(
            $userId,
            $shipping,
            $cart,
            'pending',          // shipping_status ban đầu
            $note               // ghi chú: "Thanh toán VNPay/MoMo ..."
        );

        // Xoá giỏ sau khi tạo đơn
        unset($_SESSION['cart']);

        return $orderId;
    }


    /**
     * VNPay redirect user về đây sau khi thanh toán xong
     * URL: GET index.php?controller=payment&action=vnpayReturn
     */
    public function vnpayReturn(): string
    {
        $vnp_HashSecret = "Y0OZ4UPLC8J4RSHAOVZO1SMZV066HF7C"; // phải trùng với payWithVnpay()

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

        $isValid   = ($secureHashCheck === $vnp_SecureHash);
        $rspCode   = $inputData['vnp_ResponseCode'] ?? null; // "00" = thành công
        $txnRef    = $inputData['vnp_TxnRef']       ?? null; // mã giao dịch bên VNPay
        $amount    = $inputData['vnp_Amount']       ?? null;

        $dbOrderId = null;

        if ($isValid && $rspCode === '00') {
            // 🔹 Tạo đơn hàng thật trong DB từ giỏ
            $dbOrderId = $this->createOrderAfterPayment(
                'Thanh toán VNPay thành công - mã giao dịch: ' . $txnRef
            );

            $message = "Thanh toán VNPay thành công.";
            if ($dbOrderId) {
                $message .= " Mã đơn hàng của bạn: #" . htmlspecialchars((string)$dbOrderId);
            }
            $success = true;
        } else {
            $message = "Thanh toán VNPay thất bại hoặc dữ liệu không hợp lệ.";
            $success = false;
        }

        return $this->render('payment/vnpay_return', [
            'success'   => $success,
            'message'   => $message,
            'orderId'   => $dbOrderId ?: $txnRef, // ưu tiên ID DB, fallback mã giao dịch
            'amount'    => $amount,
            'txnRef'    => $txnRef,
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
        $orderIdGw  = $_GET['orderId']    ?? null; // mã order bên MoMo (anh gửi từ payWithMomo)
        $amount     = $_GET['amount']     ?? null;
        $message    = $_GET['message']    ?? '';

        $dbOrderId = null;

        if ($resultCode === '0') {
            // 🔹 Tạo đơn hàng thật trong DB từ giỏ
            $dbOrderId = $this->createOrderAfterPayment(
                'Thanh toán MoMo thành công - mã giao dịch: ' . $orderIdGw
            );

            $success = true;
            $msg     = "Thanh toán MoMo thành công.";
            if ($dbOrderId) {
                $msg .= " Mã đơn hàng của bạn: #" . htmlspecialchars((string)$dbOrderId);
            }
        } else {
            $success = false;
            $msg     = "Thanh toán MoMo thất bại: " . htmlspecialchars($message);
        }

        return $this->render('payment/momo_return', [
            'success'  => $success,
            'message'  => $msg,
            'orderId'  => $dbOrderId ?: $orderIdGw, // ưu tiên ID DB
            'amount'   => $amount,
            'orderIdGw'=> $orderIdGw,
        ]);
    }


    /**
     * Thanh toán khi nhận hàng (COD)
     */
/**
 * Thanh toán khi nhận hàng (COD)
 */
private function payWithCod(int $totalAmount): string
{
    // 1. LẤY GIỎ HÀNG
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        $this->flash('error', 'Giỏ hàng đang trống');
        $this->redirect('index.php?controller=cart&action=index');
        return '';
    }

    // 2. LẤY USER HIỆN TẠI
    $currentUser = $_SESSION['user'] ?? null;
    if (!$currentUser || empty($currentUser['id'])) {
        $this->flash('error', 'Vui lòng đăng nhập trước khi thanh toán');
        $this->redirect('index.php?controller=auth&action=login');
        return '';
    }

    $userId = (int)$currentUser['id'];

    // 3. LẤY ĐỊA CHỈ GIAO HÀNG
    // 👉 ƯU TIÊN ĐỊA CHỈ ĐÃ CHỌN Ở CHECKOUT (checkout_shipping)
    $addresses = UserModel::getAddresses($userId);
    $shipping  = $_SESSION['checkout_shipping'] ?? ($addresses[0] ?? null);

    if (!$shipping) {
        $this->flash('error', 'Vui lòng cập nhật địa chỉ giao hàng trước khi thanh toán');
        // Lưu ý: action là "address" cho đúng với route anh đang dùng
        $this->redirect('index.php?controller=account&action=address');
        return '';
    }

    // 4. TẠO ĐƠN HÀNG TRONG DB
    $orderId = OrderModel::createFromCart(
        $userId,
        $shipping,
        $cart,
        'pending',              // shipping_status ban đầu
        'Thanh toán COD'        // ghi chú
    );

    // 5. LẤY DANH SÁCH SẢN PHẨM TRONG ĐƠN VỪA TẠO
    $orderItems = OrderModel::getOrderItems($orderId);

    // 6. XOÁ GIỎ HÀNG + ĐỊA CHỈ TẠM TRONG SESSION
    unset($_SESSION['cart']);
    unset($_SESSION['checkout_shipping']);

    // 7. RENDER VIEW THÀNH CÔNG
    return $this->render('payment/cod_success', [
        'orderId'    => $orderId,
        'amount'     => $totalAmount,
        'orderItems' => $orderItems,
    ]);
}



}
