<?php
/**
 * Simple SMTP Email Sender using fsockopen
 * Version 2 - Với error handling tốt hơn
 */
class SimpleEmailService {
    
    private $host = 'smtp.gmail.com';
    private $port = 587;
    private $username;
    private $password;
    private $debug = true;
    
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    
    public function send($to, $toName, $subject, $htmlBody, $plainBody = '') {
        $from = $this->username;
        $fromName = 'Book Store';
        
        try {
            // Kết nối
            $this->log("Connecting to {$this->host}:{$this->port}");
            $socket = fsockopen($this->host, $this->port, $errno, $errstr, 10);
            
            if (!$socket) {
                throw new Exception("Connection failed: $errstr ($errno)");
            }
            
            $this->log("Connected!");
            
            // Đọc greeting
            $response = fgets($socket, 515);
            $this->log("Server: " . trim($response));
            
            if (substr($response, 0, 3) != '220') {
                throw new Exception("Invalid greeting: $response");
            }
            
            // EHLO
            $this->sendCommand($socket, "EHLO localhost\r\n", '250');
            
            // STARTTLS
            $this->sendCommand($socket, "STARTTLS\r\n", '220');
            
            // Enable TLS
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception("TLS encryption failed");
            }
            
            $this->log("TLS enabled");
            
            // EHLO again after TLS
            $this->sendCommand($socket, "EHLO localhost\r\n", '250');
            
            // AUTH LOGIN
            $this->sendCommand($socket, "AUTH LOGIN\r\n", '334');
            $this->sendCommand($socket, base64_encode($this->username) . "\r\n", '334');
            $this->sendCommand($socket, base64_encode($this->password) . "\r\n", '235');
            
            $this->log("Authentication successful!");
            
            // MAIL FROM
            $this->sendCommand($socket, "MAIL FROM: <{$from}>\r\n", '250');
            
            // RCPT TO
            $this->sendCommand($socket, "RCPT TO: <{$to}>\r\n", '250');
            
            // DATA
            $this->sendCommand($socket, "DATA\r\n", '354');
            
            // Email headers and body
            $boundary = md5(time());
            $message = "";
            $message .= "From: {$fromName} <{$from}>\r\n";
            $message .= "To: {$toName} <{$to}>\r\n";
            $message .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $message .= "\r\n";
            
            // Plain text part
            if ($plainBody) {
                $message .= "--{$boundary}\r\n";
                $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                $message .= $plainBody . "\r\n";
            }
            
            // HTML part
            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $message .= $htmlBody . "\r\n";
            $message .= "--{$boundary}--\r\n";
            
            // End with .
            $message .= ".\r\n";
            
            fputs($socket, $message);
            $response = fgets($socket, 515);
            $this->log("DATA response: " . trim($response));
            
            if (substr($response, 0, 3) != '250') {
                throw new Exception("Email send failed: $response");
            }
            
            // QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            $this->log("Email sent successfully!");
            return true;
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendCommand($socket, $command, $expectedCode) {
        fputs($socket, $command);
        $this->log("< " . trim($command));
        
        // Đọc tất cả response lines (multi-line response)
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            $this->log("> " . trim($line));
            
            // Nếu dòng không bắt đầu bằng code-dấu gạch ngang thì đã hết
            if (substr($line, 3, 1) != '-') {
                break;
            }
        }
        
        // Kiểm tra code cuối cùng
        if (substr($response, 0, 3) != $expectedCode) {
            throw new Exception("Expected $expectedCode, got: $response");
        }
    }
    
    private function log($message) {
        if ($this->debug) {
            error_log("SimpleEmailService: " . $message);
        }
    }
}
