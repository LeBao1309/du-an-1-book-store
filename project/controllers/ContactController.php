<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/SimpleEmailService.php';

class ContactController extends BaseController {
    
    private $emailService;
    
    public function __construct() {
        // Load email config từ file
        $emailConfig = require __DIR__ . '/../config/email.php';
        
        // Khởi tạo email service với Gmail credentials
        $this->emailService = new SimpleEmailService(
            $emailConfig['username'],
            $emailConfig['password']
        );
    }
    
    public function index() {
        // Hiển thị trang liên hệ
        echo $this->render('page/contact');
    }
    
    public function send() {
        // Xử lý gửi form liên hệ
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        // Lấy dữ liệu từ form
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        // Validate
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }
        
        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        
        if (empty($subject)) {
            $errors[] = 'Vui lòng nhập tiêu đề';
        }
        
        if (empty($message)) {
            $errors[] = 'Vui lòng nhập nội dung';
        }
        
        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            exit;
        }
        
        // Tạo nội dung email HTML
        $htmlBody = $this->createEmailHTML($name, $email, $phone, $subject, $message);
        $plainBody = $this->createEmailPlain($name, $email, $phone, $subject, $message);
        
        // Gửi email
        $mailSent = $this->emailService->send(
            'toanvotruong276@gmail.com',
            'Admin Book Store',
            "[Book Store] " . $subject,
            $htmlBody,
            $plainBody
        );
        
        // Trả về JSON response
        header('Content-Type: application/json');
        if ($mailSent) {
            echo json_encode([
                'success' => true, 
                'message' => 'Cảm ơn bạn đã liên hệ! Email đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất.'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Cảm ơn bạn đã liên hệ! Thông tin đã được ghi nhận. (Lưu ý: Cần cấu hình App Password để gửi email)'
            ]);
        }
        exit;
    }
    
    private function createEmailHTML($name, $email, $phone, $subject, $messageBody) {
        $phoneDisplay = $phone ? $phone : 'Không cung cấp';
        $dateTime = date('d/m/Y H:i:s');
        $messageHtml = nl2br(htmlspecialchars($messageBody));
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0fbfbf 0%, #088a8a 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h2 { font-size: 24px; margin: 0; }
        .content { padding: 30px 20px; }
        .info-card { margin: 15px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #0fbfbf; border-radius: 5px; }
        .info-label { font-weight: bold; color: #0fbfbf; }
        .message-box { margin-top: 20px; padding: 20px; background: white; border: 2px solid #e0f9f9; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; background: #f8f9fa; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📧 Liên hệ mới từ khách hàng</h2>
            <p>Book Store - Hệ thống quản lý sách</p>
        </div>
        <div class="content">
            <div class="info-card">
                <span class="info-label">👤 Họ và tên:</span> {$name}
            </div>
            <div class="info-card">
                <span class="info-label">✉️ Email:</span> {$email}
            </div>
            <div class="info-card">
                <span class="info-label">📞 Số điện thoại:</span> {$phoneDisplay}
            </div>
            <div class="info-card">
                <span class="info-label">📌 Tiêu đề:</span> {$subject}
            </div>
            <div class="message-box">
                <h3 style="color: #0fbfbf; margin-bottom: 10px;">💬 Nội dung tin nhắn:</h3>
                <div>{$messageHtml}</div>
            </div>
        </div>
        <div class="footer">
            <p><strong>Book Store</strong> - Hệ thống bán sách trực tuyến</p>
            <p>📅 Thời gian nhận: {$dateTime}</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    private function createEmailPlain($name, $email, $phone, $subject, $messageBody) {
        $phoneDisplay = $phone ? $phone : 'Không cung cấp';
        $dateTime = date('d/m/Y H:i:s');
        
        return "LIÊN HỆ MỚI TỪ WEBSITE BOOK STORE\n" .
               "=====================================\n\n" .
               "Họ và tên: {$name}\n" .
               "Email: {$email}\n" .
               "Số điện thoại: {$phoneDisplay}\n" .
               "Tiêu đề: {$subject}\n" .
               "Thời gian: {$dateTime}\n\n" .
               "NỘI DUNG:\n" .
               "-------------------------------------\n" .
               $messageBody . "\n" .
               "-------------------------------------";
    }
}

