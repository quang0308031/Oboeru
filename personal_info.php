<?php
require 'db_connection.php'; // Kết nối cơ sở dữ liệu (nếu cần)
session_start();

// Kiểm tra xem người dùng đã đăng nhập bằng Google hay chưa
if (!isset($_SESSION['user_email'])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: login.php");
    exit();
}

// Lấy thông tin người dùng từ session (do đăng nhập bằng Google)
$username = $_SESSION['user_given_name'] ?? '';
$email = $_SESSION['user_email'] ?? '';
$phone = $_SESSION['user_phone'] ?? ''; // Số điện thoại có thể để trống nếu không có từ Google

// Xử lý cập nhật thông tin khi người dùng gửi form
if (isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];

    // Kiểm tra định dạng của tên người dùng, email và số điện thoại
    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $new_username)) {
        $error_message = "Nhập sai định dạng tên người dùng!";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Nhập sai định dạng email!";
    } elseif (!empty($new_phone) && !preg_match("/^[0-9]{10}$/", $new_phone)) { // Kiểm tra định dạng số điện thoại (10 số)
        $error_message = "Số điện thoại phải đủ 10 chữ số!";
    } else {
        // Cập nhật thông tin trong session
        $_SESSION['user_given_name'] = $new_username;
        $_SESSION['user_email'] = $new_email;
        $_SESSION['user_phone'] = $new_phone;

        $message = "Cập nhật thông tin thành công!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            width: 60%;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-group label {
            width: 25%;
            text-align: right;
            font-weight: 600;
            color: #666;
        }

        .form-group input {
            width: 70%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.1);
            outline: none;
        }

        .notification {
            display: none;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 16px;
        }

        .notification.show {
            display: block;
        }

        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .update-button {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .update-button button {
            padding: 15px 30px;
            background-color: #28a745;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .update-button button:hover {
            background-color: #218838;
        }

        .update-button button:active {
            background-color: #1e7e34;
        }

        .card {
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin: 0;
            margin-bottom: 20px;
            font-size: 20px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .form-group input, .update-button button {
            font-size: 16px;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button a {
            padding: 12px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thông tin cá nhân</h2>

        <!-- Bảng thông báo -->
        <div class="notification <?php echo isset($message) ? 'success show' : ''; ?>">
            <?php echo isset($message) ? $message : ''; ?>
        </div>
        <div class="notification <?php echo isset($error_message) ? 'error show' : ''; ?>">
            <?php echo isset($error_message) ? $error_message : ''; ?>
        </div>

        <!-- Thẻ chứa thông tin người dùng -->
        <div class="card">
            <h3>Thông tin cá nhân</h3>
            <form action="personal_info.php" method="POST">
                <div class="form-group">
                    <label for="username">Tên người dùng:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Nhập số điện thoại của bạn">
                </div>
                <div class="update-button">
                    <button type="submit" name="update_profile">Cập nhật thông tin</button>
                </div>
            </form>
        </div>

        <!-- Nút quay về trang chủ -->
        <div class="back-button">
            <a href="trangchu2.php">Quay về trang chủ</a>
        </div>
    </div>
</body>
</html>
