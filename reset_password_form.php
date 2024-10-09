<?php
require 'db_connection.php';
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Kiểm tra token trong cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Token không hợp lệ.");
    }
} else {
    die("Không có token được cung cấp.");
}

// Xử lý đổi mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'];

    // Mã hóa mật khẩu (nên sử dụng password_hash để bảo mật)
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Cập nhật mật khẩu và xóa token
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $hashed_password, $token);
    $stmt->execute();

    echo "Mật khẩu đã được đặt lại thành công.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
</head>
<body>
    <form action="" method="POST">
        <label for="password">Mật khẩu mới:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Đặt lại mật khẩu</button>
    </form>
</body>
</html>
