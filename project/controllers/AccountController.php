<?php
require_once __DIR__ . '/../models/UserModel.php';
// Kiểm tra và nạp các model cần thiết
if (file_exists(__DIR__ . '/../models/OrderModel.php')) {
    require_once __DIR__ . '/../models/OrderModel.php';
}
if (file_exists(__DIR__ . '/../models/WishlistModel.php')) {
    require_once __DIR__ . '/../models/WishlistModel.php';
}

final class AccountController extends BaseController
{
    private function requireLogin(): array
    {
        $u = $_SESSION['user'] ?? null;
        if (!$u) {
            $this->flash('error', 'Vui lòng đăng nhập');
            $this->redirect('?controller=auth&action=login');
        }
        return $u;
    }

    public function profile(): string
    {
        $u = $this->requireLogin();
        $user = UserModel::findById((int)$u['id']);
        $csrf = $this->csrfToken();
        $active = 'profile';

        return $this->render('account/profile', compact('user','csrf','active'));
    }

    public function updateProfile(): string
    {
        $u = $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();

            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            $error = null;
            if ($name === '' || $email === '') {
                $error = 'Tên và email không được rỗng';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ';
            } elseif (UserModel::emailExistsForOther($email, (int)$u['id'])) {
                $error = 'Email đã được sử dụng';
            }

            if ($error) {
                $user = UserModel::findById((int)$u['id']);
                $csrf = $this->csrfToken();
                $active = 'profile';
                return $this->render('account/profile', compact('user','csrf','active','error'));
            }

            UserModel::updateProfile((int)$u['id'], $name, $email);
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;

            $this->flash('success', 'Cập nhật thông tin thành công');
            $this->redirect('?controller=account&action=profile');
        }

        $this->redirect('?controller=account&action=profile');
        return '';
    }
    
    public function password(): string
    {
        $this->requireLogin();
        $csrf = $this->csrfToken();
        $active = 'password';
        return $this->render('account/password', compact('csrf','active')); 
    }

    public function changePassword(): string
    {
        $u = $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=account&action=password');
            return '';
        }
        
        $this->checkCsrf();
        $oldPassword = (string)($_POST['old_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $newPassword2 = (string)($_POST['confirm_password'] ?? '');

        $user = UserModel::findById((int)$u['id']); 
        $error = null;

        if (!password_verify($oldPassword, $user['password'] ?? '')) {
            $error = 'Mật khẩu cũ không đúng.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Mật khẩu mới phải tối thiểu 6 ký tự.';
        } elseif ($newPassword !== $newPassword2) {
            $error = 'Mật khẩu mới và xác nhận không khớp.';
        }

        if ($error) {
            $csrf = $this->csrfToken();
            $active = 'password';
            return $this->render('account/password', compact('csrf', 'active', 'error'));
        }

        UserModel::updatePassword((int)$u['id'], $newPassword);
        $this->flash('success', 'Đổi mật khẩu thành công!');
        $this->redirect('?controller=account&action=password');
        return '';
    }

    public function address(): string
    {
        $u = $this->requireLogin();
        $addresses = UserModel::getAddresses((int)$u['id']);
        $csrf = $this->csrfToken();
        $active = 'address';
        return $this->render('account/address', compact('addresses', 'csrf', 'active'));
    }

    public function addAddress(): string
    {
        $u = $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $address = trim($_POST['full_address'] ?? '');
            $phone = trim($_POST['shipping_phone'] ?? '');
            $isDefault = isset($_POST['is_default']);

            if ($address === '' || $phone === '') {
                $this->flash('error', 'Địa chỉ và số điện thoại không được rỗng.');
            } else {
                UserModel::createAddress((int)$u['id'], $address, $phone, $isDefault);
                $this->flash('success', 'Thêm địa chỉ giao hàng thành công.');
            }
        }
        $this->redirect('?controller=account&action=address');
        return '';
    }

    public function setDefaultAddress(int $id): string
    {
        $u = $this->requireLogin();
        $address = UserModel::findAddressById($id, (int)$u['id']);
        if (!$address) {
            $this->flash('error', 'Địa chỉ không hợp lệ.');
        } else {
            UserModel::setDefaultAddress($id, (int)$u['id']);
            $this->flash('success', 'Đặt địa chỉ mặc định thành công.');
        }
        $this->redirect('?controller=account&action=address');
        return '';
    }

    public function deleteAddress(int $id): string
    {
        $u = $this->requireLogin();
        $deleted = UserModel::deleteAddress($id, (int)$u['id']);

        if ($deleted) {
             $this->flash('success', 'Xóa địa chỉ thành công.');
        } else {
             $this->flash('error', 'Không thể xóa địa chỉ.');
        }
        $this->redirect('?controller=account&action=address');
        return '';
    }

    public function wishlist(): string
    {
        $u = $this->requireLogin();
        require_once __DIR__ . '/../models/WishlistModel.php';
        $books = WishlistModel::getWishlist((int)$u['id']);
        $csrf = $this->csrfToken();
        $active = 'wishlist'; 
        return $this->render('account/wishlist', compact('books', 'csrf', 'active'));
    }

    public function removeWishlist(): string
    {
        $u = $this->requireLogin();
        $bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($bookId > 0) {
            require_once __DIR__ . '/../models/WishlistModel.php';
            WishlistModel::remove((int)$u['id'], $bookId);
            $this->flash('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích');
        }
        $this->redirect('?controller=account&action=wishlist');
        return '';
    }
    
    public function addWishlist(): void
    {
        $u = $this->requireLogin();
        $bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($bookId > 0) {
            require_once __DIR__ . '/../models/WishlistModel.php';
            WishlistModel::add((int)$u['id'], $bookId);
            $this->flash('success', 'Đã thêm sách vào danh sách yêu thích ❤️');
        }
        $backUrl = $_SERVER['HTTP_REFERER'] ?? '?controller=home';
        $this->redirect($backUrl);
    }

    // --- PHẦN ĐƠN HÀNG ---
    public function orders(): string
    {
        $u = $this->requireLogin();
        $orders = OrderModel::getHistory((int)$u['id']);
        $active = 'orders'; 
        return $this->render('account/orders', compact('orders', 'active'));
    }

    public function orderDetail(): string
    {
        $u = $this->requireLogin();
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $order = OrderModel::getOrderById($orderId, (int)$u['id']);
        if (!$order) {
            $this->flash('error', 'Không tìm thấy đơn hàng này.');
            $this->redirect('?controller=account&action=orders');
            return '';
        }

        $items = OrderModel::getOrderItems($orderId);
        $active = 'orders';

        return $this->render('account/order_detail', compact('order', 'items', 'active'));
    }
    
    public function cancelOrder(): void
    {
        $u = $this->requireLogin();
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Lý do mặc định
        $reason = "Khách hàng chủ động hủy"; 

        if (OrderModel::cancelOrder($orderId, (int)$u['id'], $reason)) {
            $this->flash('success', 'Đã hủy đơn hàng thành công.');
        } else {
            $this->flash('error', 'Không thể hủy đơn hàng này (Đơn đã được xử lý hoặc không tồn tại).');
        }
        $this->redirect('?controller=account&action=orders');
    }

    public function confirmReceived(): void
    {
        $u = $this->requireLogin();
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (OrderModel::confirmReceived($orderId, (int)$u['id'])) {
            $this->flash('success', 'Cảm ơn bạn! Đã xác nhận giao hàng thành công.');
        } else {
            $this->flash('error', 'Không thể xác nhận (Đơn chưa được giao hoặc lỗi hệ thống).');
        }
        $this->redirect('?controller=account&action=orders');
    }
}