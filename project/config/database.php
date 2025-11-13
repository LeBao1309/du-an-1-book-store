<?php
// Cấu hình kết nối CSDL
$host = 'localhost';
$user = 'root';
$pass = ''; // nếu bạn có mật khẩu thì điền vào
$dbname = 'du_an_1_book_store';

// Kết nối MySQLi
$conn = new mysqli($host, $user, $pass, $dbname);

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    die('Kết nối database thất bại: ' . $conn->connect_error);
}

// Đặt charset để đọc tiếng Việt
$conn->set_charset('utf8mb4');
