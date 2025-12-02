# Hướng dẫn cấu hình Email cho XAMPP

## Vấn đề
XAMPP mặc định không thể gửi email qua hàm `mail()` của PHP trên localhost.

## Giải pháp

### Phương án 1: Cấu hình Gmail SMTP trên XAMPP (Đơn giản nhất)

#### Bước 1: Cấu hình php.ini
1. Mở file `C:\xampp\php\php.ini`
2. Tìm và sửa các dòng sau:

```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from=toanvotruong276@gmail.com
sendmail_path="\"C:\xampp\sendmail\sendmail.exe\" -t"
```

#### Bước 2: Cấu hình sendmail.ini
1. Mở file `C:\xampp\sendmail\sendmail.ini`
2. Sửa các dòng sau:

```ini
[sendmail]

smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=toanvotruong276@gmail.com
auth_password=YOUR_APP_PASSWORD_HERE
force_sender=toanvotruong276@gmail.com
```

#### Bước 3: Tạo App Password cho Gmail
1. Truy cập: https://myaccount.google.com/security
2. Bật "2-Step Verification" (xác minh 2 bước) nếu chưa bật
3. Vào "App passwords" (Mật khẩu ứng dụng)
4. Chọn "Mail" và "Windows Computer"
5. Copy mật khẩu 16 ký tự được tạo
6. Dán vào `auth_password` trong file `sendmail.ini`

#### Bước 4: Restart Apache
- Tắt và mở lại Apache trong XAMPP Control Panel

---

### Phương án 2: Sử dụng PHPMailer (Chuyên nghiệp hơn)

#### Bước 1: Cài đặt PHPMailer
Mở Terminal trong thư mục project và chạy:

```bash
cd C:\xampp\htdocs\du-an-1-book-store\project
composer require phpmailer/phpmailer
```

#### Bước 2: Tạo EmailService.php
Tạo file `project/services/EmailService.php`:

```php
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailService {
    
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Cấu hình SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'toanvotruong276@gmail.com';
        $this->mailer->Password = 'YOUR_APP_PASSWORD_HERE'; // Thay bằng App Password
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->CharSet = 'UTF-8';
        
        // Người gửi mặc định
        $this->mailer->setFrom('toanvotruong276@gmail.com', 'Book Store');
    }
    
    public function sendContactEmail($fromName, $fromEmail, $subject, $message, $phone = '') {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress('toanvotruong276@gmail.com', 'Admin Book Store');
            $this->mailer->addReplyTo($fromEmail, $fromName);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "[Book Store] " . $subject;
            
            // Tạo HTML body
            $htmlBody = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
                    .header { background: #0fbfbf; color: white; padding: 15px; text-align: center; }
                    .content { padding: 20px; background: #f9f9f9; }
                    .info-row { margin: 10px 0; padding: 10px; background: white; border-left: 4px solid #0fbfbf; }
                    .label { font-weight: bold; color: #0fbfbf; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>📧 Liên hệ từ Website Book Store</h2>
                    </div>
                    <div class='content'>
                        <div class='info-row'>
                            <span class='label'>👤 Họ tên:</span> {$fromName}
                        </div>
                        <div class='info-row'>
                            <span class='label'>✉️ Email:</span> {$fromEmail}
                        </div>
                        <div class='info-row'>
                            <span class='label'>📞 Số điện thoại:</span> " . ($phone ?: 'Không cung cấp') . "
                        </div>
                        <div class='info-row'>
                            <span class='label'>💬 Nội dung:</span><br>
                            <div style='margin-top: 10px; padding: 10px; background: #fff;'>
                                " . nl2br(htmlspecialchars($message)) . "
                            </div>
                        </div>
                    </div>
                    <div style='text-align: center; padding: 15px; color: #666; font-size: 12px;'>
                        <p>Thời gian: " . date('d/m/Y H:i:s') . "</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = strip_tags($message);
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Email error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
```

#### Bước 3: Sử dụng trong ContactController
```php
require_once __DIR__ . '/../services/EmailService.php';

// Trong method send()
$emailService = new EmailService();
$mailSent = $emailService->sendContactEmail($name, $email, $subject, $message, $phone);
```

---

## Test Email

Sau khi cấu hình xong, test bằng cách:
1. Truy cập: http://localhost/du-an-1-book-store/project/?controller=contact
2. Điền form và gửi
3. Kiểm tra email toanvotruong276@gmail.com

## Lưu ý bảo mật

⚠️ **QUAN TRỌNG**: Không commit App Password lên GitHub!

Tạo file `.env` để lưu thông tin nhạy cảm:
```
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=toanvotruong276@gmail.com
SMTP_PASSWORD=your_app_password_here
SMTP_FROM_EMAIL=toanvotruong276@gmail.com
SMTP_FROM_NAME=Book Store
```

Thêm `.env` vào file `.gitignore`:
```
.env
vendor/
```

---

## Khắc phục sự cố

### Email không gửi được?
1. Kiểm tra Apache error log: `C:\xampp\apache\logs\error.log`
2. Kiểm tra sendmail debug log: `C:\xampp\sendmail\debug.log`
3. Đảm bảo đã bật 2-Step Verification và tạo App Password
4. Kiểm tra firewall không chặn port 587

### Gmail chặn gửi email?
- Vào https://www.google.com/settings/security/lesssecureapps
- Hoặc sử dụng App Password (khuyến nghị)

### Test nhanh sendmail
```bash
echo "Test email" | sendmail -v toanvotruong276@gmail.com
```

---

## Khuyến nghị

- ✅ Dùng **Phương án 1** cho development (đơn giản, nhanh)
- ✅ Dùng **Phương án 2** cho production (chuyên nghiệp, dễ quản lý)
- ✅ Luôn dùng App Password thay vì mật khẩu Gmail thật
- ✅ Lưu credentials trong `.env`, không commit lên Git
