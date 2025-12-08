<?php

/**
 * EmailService - Dịch vụ gửi email qua SMTP (Gmail App Password).
 * Đọc cấu hình từ config/email.php, có thể override qua tham số truyền vào constructor.
 */
class EmailService
{
    private string $smtpHost = 'smtp.gmail.com';
    private int $smtpPort = 587;
    private string $username = '';
    private string $password = '';
    private string $fromEmail = '';
    private string $fromName = 'Book Store';

    public function __construct(array $override = [])
    {
        $config = [];
        $file = __DIR__ . '/../config/email.php';
        if (file_exists($file)) {
            $cfg = include $file;
            if (is_array($cfg)) {
                $config = $cfg;
            }
        }
        $config = array_merge($config, $override);

        $this->smtpHost  = $config['smtp_host']  ?? $this->smtpHost;
        $this->smtpPort  = (int)($config['smtp_port'] ?? $this->smtpPort);
        $this->username  = $config['username']   ?? $this->username;
        $this->password  = $config['password']   ?? $this->password;
        $this->fromEmail = $config['from_email'] ?? $this->username;
        $this->fromName  = $config['from_name']  ?? $this->fromName;
    }

    /**
     * Gửi email text đơn giản (dùng cho reset password).
     */
    public function sendPlain(string $toEmail, string $toName, string $subject, string $body): bool
    {
        if (empty($this->username) || empty($this->password)) {
            error_log("EmailService: SMTP chưa cấu hình, bỏ qua gửi.");
            return false;
        }

        $headers = [];
        $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";
        $headers[] = "To: {$toName} <{$toEmail}>";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: 8bit";

        return $this->sendViaSMTP($toEmail, $subject, $body, $headers);
    }

    /**
     * Gửi email liên hệ (giữ lại để tương thích).
     */
    public function sendContactEmail($toEmail, $fromName, $fromEmail, $subject, $messageBody, $phone = '')
    {
        if (empty($this->password)) {
            error_log("EmailService: App Password chưa được cấu hình.");
            return false;
        }

        $htmlContent = $this->createEmailTemplate($fromName, $fromEmail, $subject, $messageBody, $phone);
        $boundary = md5(uniqid(time()));

        $headers = [];
        $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";
        $headers[] = "Reply-To: {$fromName} <{$fromEmail}>";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";
        $headers[] = "X-Mailer: PHP/" . phpversion();

        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $this->createPlainTextEmail($fromName, $fromEmail, $messageBody, $phone);
        $body .= "\r\n\r\n--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $htmlContent;
        $body .= "\r\n\r\n--{$boundary}--";

        return $this->sendViaSMTP($toEmail, "[Book Store] " . $subject, $body, $headers);
    }

    private function createEmailTemplate($fromName, $fromEmail, $subject, $messageBody, $phone)
    {
        $phoneDisplay = $phone ? $phone : 'Không cung cấp';
        $dateTime = date('d/m/Y H:i:s');
        $messageHtml = htmlspecialchars($messageBody);

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
        .info-label { font-weight: bold; color: #0fbfbf; display: inline-block; min-width: 120px; }
        .message-box { margin-top: 20px; padding: 20px; background: white; border: 2px solid #e0f9f9; border-radius: 5px; }
        .message-box h3 { color: #0fbfbf; margin-bottom: 10px; font-size: 16px; }
        .message-text { color: #555; line-height: 1.8; white-space: pre-wrap; }
        .footer { text-align: center; padding: 20px; background: #f8f9fa; color: #666; font-size: 12px; border-top: 1px solid #e0e0e0; }
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
                <span class="info-label">👤 Họ và tên:</span> {$fromName}
            </div>
            <div class="info-card">
                <span class="info-label">✉️ Email:</span> {$fromEmail}
            </div>
            <div class="info-card">
                <span class="info-label">📞 Số điện thoại:</span> {$phoneDisplay}
            </div>
            <div class="info-card">
                <span class="info-label">📌 Tiêu đề:</span> {$subject}
            </div>
            <div class="message-box">
                <h3>💬 Nội dung tin nhắn:</h3>
                <div class="message-text">{$messageHtml}</div>
            </div>
        </div>
        <div class="footer">
            <p><strong>Book Store</strong> - Hệ thống bán sách trực tuyến</p>
            <p>📅 Thời gian nhận: {$dateTime}</p>
            <p>Email này được gửi tự động từ form liên hệ</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function createPlainTextEmail($fromName, $fromEmail, $messageBody, $phone)
    {
        $phoneDisplay = $phone ? $phone : 'Không cung cấp';
        $dateTime = date('d/m/Y H:i:s');

        return "LIÊN HỆ MỚI TỪ WEBSITE BOOK STORE\n" .
               "=====================================\n\n" .
               "Họ và tên: {$fromName}\n" .
               "Email: {$fromEmail}\n" .
               "Số điện thoại: {$phoneDisplay}\n" .
               "Thời gian: {$dateTime}\n\n" .
               "NỘI DUNG:\n" .
               "-------------------------------------\n" .
               $messageBody . "\n" .
               "-------------------------------------\n\n" .
               "Email này được gửi từ form liên hệ trên website Book Store.";
    }

    /**
     * Gửi email qua SMTP socket
     */
    private function sendViaSMTP($to, $subject, $body, $headers)
    {
        $socket = @fsockopen($this->smtpHost, $this->smtpPort, $errno, $errstr, 30);
        if (!$socket) {
            error_log("SMTP Connection FAILED: {$errno} - {$errstr}");
            return false;
        }

        $resp = fgets($socket, 515);
        if (substr($resp, 0, 3) !== '220') {
            fclose($socket);
            return false;
        }

        fputs($socket, "EHLO {$this->smtpHost}\r\n");
        fgets($socket, 515);

        fputs($socket, "STARTTLS\r\n");
        $resp = fgets($socket, 515);
        if (substr($resp, 0, 3) !== '220') {
            fclose($socket);
            return false;
        }
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        fputs($socket, "EHLO {$this->smtpHost}\r\n");
        fgets($socket, 515);

        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket, 515);
        fputs($socket, base64_encode($this->username) . "\r\n");
        fgets($socket, 515);
        fputs($socket, base64_encode($this->password) . "\r\n");
        $resp = fgets($socket, 515);
        if (substr($resp, 0, 3) !== '235') {
            fclose($socket);
            error_log("SMTP Auth Failed");
            return false;
        }

        fputs($socket, "MAIL FROM: <{$this->fromEmail}>\r\n"); fgets($socket, 515);
        fputs($socket, "RCPT TO: <{$to}>\r\n"); fgets($socket, 515);
        fputs($socket, "DATA\r\n"); fgets($socket, 515);

        fputs($socket, "To: {$to}\r\n");
        fputs($socket, "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n");
        foreach ($headers as $h) {
            fputs($socket, $h . "\r\n");
        }
        fputs($socket, "\r\n");
        fputs($socket, $body);
        fputs($socket, "\r\n.\r\n");
        $resp = fgets($socket, 515);
        fputs($socket, "QUIT\r\n");
        fclose($socket);

        return substr($resp, 0, 3) === '250';
    }
}
