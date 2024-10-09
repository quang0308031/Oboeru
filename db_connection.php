<?php
$host = "localhost";  // Tên host của cơ sở dữ liệu
$user = "root";       // Tên người dùng của cơ sở dữ liệu
$password = "";       // Mật khẩu người dùng (nếu có)
$database = "quiz_db"; // Tên cơ sở dữ liệu

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
