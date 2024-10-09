<?php
// Kiểm tra nếu có yêu cầu POST từ biểu mẫu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Kết nối đến cơ sở dữ liệu
    require 'db_connection.php'; // Thay đổi theo cách bạn cấu hình kết nối

    // Kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Tạo token hoặc mã xác thực
        $token = bin2hex(random_bytes(50)); // Tạo mã token ngẫu nhiên

        // Lưu token vào cơ sở dữ liệu để xác thực (có thể lưu vào bảng khác hoặc trường khác trong bảng người dùng)
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Tạo liên kết đổi mật khẩu
        $link = "http://yourdomain.com/reset_password_form.php?token=" . $token; // Thay đổi thành đường dẫn thực tế

        // Gửi email
        $subject = "Yêu cầu đặt lại mật khẩu";
        $message = "Nhấp vào liên kết sau để đặt lại mật khẩu của bạn: " . $link;
        $headers = "From: no-reply@yourdomain.com\r\n"; // Thay đổi theo yêu cầu của bạn

        if (mail($email, $subject, $message, $headers)) {
            // Chuyển hướng về trang quên mật khẩu với thông báo thành công
            header("Location: forgot_password.php?success=true");
            exit();
        } else {
            echo "Có lỗi trong việc gửi email.";
        }
    } else {
        echo "Email không tồn tại trong hệ thống.";
    }
}
?>
