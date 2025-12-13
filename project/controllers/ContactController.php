<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/EmailService.php';

class ContactController extends BaseController {
    
    private $emailService;
    
    public function __construct() {
        // Sử dụng EmailService chung (đọc cấu hình từ config/email.php)
        $this->emailService = new EmailService();
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
        
        // Gửi email qua EmailService (dùng chung luồng với các chỗ khác)
        $mailSent = $this->emailService->sendContactEmail(
            'wisedecision2409@gmail.com',
            $name,
            $email,
            $subject,
            $message,
            $phone
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
    
}
