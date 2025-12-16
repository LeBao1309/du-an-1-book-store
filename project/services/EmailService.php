<?php

/**
 * EmailService - Dịch vụ gửi email qua SMTP (Gmail App Password).
 * Đọc cấu hình từ config/email.php, có thể override qua tham số truyền vào constructor.
 */
class EmailService
{
    private string $smtpHost = 'smtp.gmail.com';
    private int $smtpPort = 587;
    private string $smtpSecure = 'tls'; // tls|ssl
    private string $username = '';
    private string $password = '';
    private string $fromEmail = '';
    private string $fromName = 'Wise Decision bookstore';

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
        $this->smtpSecure = $config['smtp_secure'] ?? $this->smtpSecure;
        $this->username  = $config['username']   ?? $this->username;
        $this->password  = $config['password']   ?? $this->password;
        $this->fromEmail = $config['from_email'] ?? $this->username;
        $this->fromName  = $config['from_name']  ?? $this->fromName;
    }

    public function sendPlain(string $toEmail, string $toName, string $subject, string $body): bool
    {
        if (empty($this->username) || empty($this->password)) {
            error_log("EmailService: SMTP chưa cấu hình, bỏ qua gửi.");
            $this->saveLocalCopy($toEmail, $subject, $body);
            return false;
        }

        $headers = [];
        $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";
        $headers[] = "To: {$toName} <{$toEmail}>";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: 8bit";

        if ($this->canUsePHPMailer()) {
            $sent = $this->sendViaPHPMailer($toEmail, $toName, $subject, nl2br($body), false);
            if (!$sent) {
                $this->saveLocalCopy($toEmail, $subject, $body);
            }
            return $sent;
        }

        return $this->sendViaSMTP($toEmail, $subject, $body, $headers);
    }


    public function sendContactEmail($toEmail, $fromName, $fromEmail, $subject, $messageBody, $phone = '')
    {
        if (empty($this->username) || empty($this->password)) {
            error_log("EmailService: App Password chưa được cấu hình.");
            $this->saveLocalCopy($toEmail, $subject, $messageBody);
            return false;
        }

        $htmlContent = $this->createEmailTemplate($fromName, $fromEmail, $subject, $messageBody, $phone);
        $plainContent = $this->createPlainTextEmail($fromName, $fromEmail, $messageBody, $phone);
        $boundary = md5(uniqid(time()));

        $headers = [];
        $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";
        $headers[] = "Reply-To: {$fromName} <{$fromEmail}>";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";
        $headers[] = "X-Mailer: PHP/" . phpversion();

        // Ưu tiên PHPMailer nếu có (ổn định hơn, tự lo TLS)
        if ($this->canUsePHPMailer()) {
            $sent = $this->sendViaPHPMailer(
                $toEmail,
                $fromName,
                "[Book Store] " . $subject,
                $htmlContent,
                true,
                $plainContent,
                $fromEmail,
                $fromName
            );
            if (!$sent) {
                $this->saveLocalCopy($toEmail, $subject, $htmlContent);
            }
            return $sent;
        }

        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $plainContent;
        $body .= "\r\n\r\n--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $htmlContent;
        $body .= "\r\n\r\n--{$boundary}--";

        return $this->sendViaSMTP($toEmail, "[Book Store] " . $subject, $body, $headers);
    }

    /**
     * Gửi email đặt lại mật khẩu với template HTML và fallback text.
     */
    public function sendResetTemplate(string $toEmail, string $toName, string $subject, string $resetLink): bool
    {
        $safeName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');
        $safeLink = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');

        $plain = "Xin chào {$toName},\n\n"
               . "Bạn (hoặc ai đó) đã yêu cầu đặt lại mật khẩu cho tài khoản Book Store.\n"
               . "Nhấp vào liên kết dưới đây để đặt lại mật khẩu:\n{$resetLink}\n\n"
               . "Liên kết có hiệu lực 60 phút. Nếu không phải bạn, hãy bỏ qua email này.\n"
               . "Sau khi đặt lại, hãy đăng nhập bằng mật khẩu mới.";

        // Template HTML gọn gàng, có nút CTA
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; background:#f5f7fb; margin:0; padding:20px; color:#0f172a; }
    .wrap { max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 20px 60px rgba(15,23,42,0.12); }
    .hero { background: linear-gradient(135deg, #0ea5a5 0%, #0fbf9b 100%); color:#fff; padding:28px 24px; }
    .hero h2 { margin:0 0 8px; font-size:22px; }
    .hero p { margin:0; opacity:0.9; }
    .body { padding:24px; }
    .card { background:#f8fafc; border:1px solid #e5e7eb; border-radius:12px; padding:18px; margin:16px 0; }
    .btn { display:inline-block; padding:12px 18px; border-radius:10px; background:#0ea5a5; color:#fff; text-decoration:none; font-weight:700; box-shadow:0 12px 30px rgba(14,165,165,0.25); }
    .btn:hover { background:#0b8c8c; }
    .note { font-size:13px; color:#475569; margin-top:12px; }
    .footer { padding:14px 24px 20px; font-size:12px; color:#64748b; background:#f8fafc; border-top:1px solid #e2e8f0; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="hero">
      <h2>Đặt lại mật khẩu</h2>
      <p>Xin chào {$safeName}, chúng tôi nhận được yêu cầu đặt lại mật khẩu của bạn.</p>
    </div>
    <div class="body">
      <p>Nhấp nút bên dưới để mở trang đổi mật khẩu. Liên kết chỉ có hiệu lực trong 60 phút.</p>
      <div class="card">
        <a class="btn" href="{$safeLink}" target="_blank" rel="noopener">Đặt lại mật khẩu</a>
        <div class="note">
          Nếu nút không hoạt động, hãy sao chép đường dẫn này vào trình duyệt:<br>
          <span style="word-break:break-all;color:#0ea5a5;">{$safeLink}</span>
        </div>
      </div>
      <p class="note">Sau khi đổi thành công, hãy đăng nhập bằng mật khẩu mới để tiếp tục sử dụng.</p>
    </div>
    <div class="footer">
      Email tự động từ Book Store. Nếu không phải bạn, có thể bỏ qua email này.
    </div>
  </div>
</body>
</html>
HTML;

        if (empty($this->username) || empty($this->password)) {
            error_log("EmailService: SMTP chưa cấu hình, lưu nội dung reset local.");
            $this->saveLocalCopy($toEmail, $subject, $plain);
            return false;
        }

        if ($this->canUsePHPMailer()) {
            $sent = $this->sendViaPHPMailer($toEmail, $toName, $subject, $html, true, $plain);
            if (!$sent) {
                $this->saveLocalCopy($toEmail, $subject, $plain);
            }
            return $sent;
        }

        // Fallback: gửi HTML qua SMTP tay, nếu lỗi thì lưu local
        $headers = [];
        $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";
        $headers[] = "To: {$toName} <{$toEmail}>";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: 8bit";

        $sent = $this->sendViaSMTP($toEmail, $subject, $html, $headers);
        if (!$sent) {
            $this->saveLocalCopy($toEmail, $subject, $plain);
        }
        return $sent;
    }

    private function canUsePHPMailer(): bool
    {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        }
        return class_exists('\\PHPMailer\\PHPMailer\\PHPMailer');
    }
    private function sendViaPHPMailer(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        bool $isHtml = true,
        string $altBody = '',
        ?string $replyToEmail = null,
        ?string $replyToName = null
    ): bool
    {
        if (!class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
            return false;
        }
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->Port       = $this->smtpPort;
            $mail->SMTPSecure = strtolower($this->smtpSecure) === 'ssl'
                ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);
            if ($replyToEmail) {
                $mail->addReplyTo($replyToEmail, $replyToName ?: $replyToEmail);
            }
            $mail->Subject = $subject;
            if ($isHtml) {
                $mail->isHTML(true);
                $mail->Body    = $htmlBody;
                $mail->AltBody = $altBody !== ''
                    ? $altBody
                    : strip_tags(str_replace("<br>", "\n", $htmlBody));
            } else {
                $mail->isHTML(false);
                $mail->Body = $htmlBody;
            }
            return $mail->send();
        } catch (\Throwable $e) {
            error_log('PHPMailer send failed: ' . $e->getMessage());
            return false;
        }
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
        $host = (strtolower($this->smtpSecure) === 'ssl')
            ? "ssl://{$this->smtpHost}"
            : $this->smtpHost;

        $socket = @fsockopen($host, $this->smtpPort, $errno, $errstr, 30);
        if (!$socket) {
            error_log("SMTP Connection FAILED: {$errno} - {$errstr}");
            $this->saveLocalCopy($to, $subject, $body);
            return false;
        }

        $resp = fgets($socket, 515);
        if (substr($resp, 0, 3) !== '220') {
            fclose($socket);
            $this->saveLocalCopy($to, $subject, $body);
            return false;
        }

        fputs($socket, "EHLO {$this->smtpHost}\r\n");
        fgets($socket, 515);

        if (strtolower($this->smtpSecure) !== 'ssl') {
            fputs($socket, "STARTTLS\r\n");
            $resp = fgets($socket, 515);
            if (substr($resp, 0, 3) !== '220') {
                fclose($socket);
                $this->saveLocalCopy($to, $subject, $body);
                return false;
            }
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            fputs($socket, "EHLO {$this->smtpHost}\r\n");
            fgets($socket, 515);
        }

        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket, 515);
        fputs($socket, base64_encode($this->username) . "\r\n");
        fgets($socket, 515);
        fputs($socket, base64_encode($this->password) . "\r\n");
        $resp = fgets($socket, 515);
        if (substr($resp, 0, 3) !== '235') {
            fclose($socket);
            error_log("SMTP Auth Failed");
            $this->saveLocalCopy($to, $subject, $body);
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

        $ok = substr($resp, 0, 3) === '250';
        if (!$ok) {
            $this->saveLocalCopy($to, $subject, $body);
        }
        return $ok;
    }

    private function saveLocalCopy(string $to, string $subject, string $body): void
    {
        $dir = __DIR__ . '/../storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $file = $dir . '/dev_mail.log';
        $content = "==== " . date('Y-m-d H:i:s') . " ====\nTo: {$to}\nSubject: {$subject}\nBody:\n{$body}\n\n";
        @file_put_contents($file, $content, FILE_APPEND);
        error_log("EmailService: Lưu email local tại {$file}");
    }
}
