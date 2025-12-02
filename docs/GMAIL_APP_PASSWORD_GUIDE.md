# 🔐 Hướng dẫn lấy Gmail App Password

## Bước 1: Truy cập Google Account
1. Mở trình duyệt, truy cập: https://myaccount.google.com/
2. Đăng nhập bằng tài khoản **toanvotruong276@gmail.com**

## Bước 2: Bật xác minh 2 bước (2-Step Verification)
1. Vào **Security** (Bảo mật)
2. Tìm mục **2-Step Verification** 
3. Nếu chưa bật, click vào và làm theo hướng dẫn để bật

## Bước 3: Tạo App Password
1. Sau khi đã bật 2-Step Verification, quay lại **Security**
2. Tìm mục **App passwords** (Mật khẩu ứng dụng)
3. Click vào **App passwords**
4. Chọn:
   - **Select app**: Mail
   - **Select device**: Windows Computer (hoặc Other)
   - Đặt tên: "Book Store Contact Form"
5. Click **Generate** (Tạo)
6. Copy mật khẩu 16 ký tự (dạng: `xxxx xxxx xxxx xxxx`)

## Bước 4: Cấu hình trong ContactController.php
1. Mở file: `project/controllers/ContactController.php`
2. Tìm dòng:
   ```php
   $this->emailService->setAppPassword('YOUR_APP_PASSWORD');
   ```
3. Thay `YOUR_APP_PASSWORD` bằng mật khẩu vừa copy (bỏ khoảng trắng)
4. Ví dụ:
   ```php
   $this->emailService->setAppPassword('abcdabcdabcdabcd');
   ```

## Bước 5: Test gửi email
1. Truy cập: http://localhost/du-an-1-book-store/project/?controller=contact
2. Điền form và gửi
3. Kiểm tra email **toanvotruong276@gmail.com**
4. Nếu không nhận được email, check:
   - Spam folder
   - Apache error log: `C:\xampp\apache\logs\error.log`

## ⚠️ Lưu ý bảo mật

### KHÔNG ĐƯỢC commit App Password lên GitHub!

Tạo file `.gitignore` trong thư mục gốc:
```
.env
/project/config/email.php
```

### Cách tốt hơn: Dùng file config riêng

Tạo file `project/config/email.php`:
```php
<?php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'username' => 'toanvotruong276@gmail.com',
    'password' => 'your_app_password_here', // App Password
    'from_email' => 'toanvotruong276@gmail.com',
    'from_name' => 'Book Store'
];
```

Sau đó trong ContactController:
```php
$config = require __DIR__ . '/../config/email.php';
$this->emailService->setAppPassword($config['password']);
```

Và thêm `/project/config/email.php` vào `.gitignore`

## Khắc phục sự cố

### Email không gửi được?
- Kiểm tra kết nối internet
- Đảm bảo port 587 không bị firewall chặn
- Xem log: `C:\xampp\apache\logs\error.log`

### Gmail từ chối kết nối?
- Đảm bảo đã bật 2-Step Verification
- App Password phải là 16 ký tự, không có khoảng trắng
- Thử tạo lại App Password mới

### Nhận được email nhưng vào Spam?
- Bình thường, vì gửi từ localhost
- Đánh dấu "Not Spam" để lần sau vào Inbox

---

**Sau khi cấu hình xong, bạn sẽ nhận được email đẹp mắt với đầy đủ thông tin người liên hệ!** 📧✨
