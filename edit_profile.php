<?php
require 'db_connection.php';
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['username']) && !isset($_SESSION['user_email'])) {
    header("Location: login.php"); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Kiểm tra nếu người dùng đăng nhập bằng Google (kiểm tra session thông tin từ Google)
if (isset($_SESSION['user_email'])) {
    $username = $_SESSION['user_given_name']; // Lấy tên từ thông tin Google
    $email = $_SESSION['user_email']; // Lấy email từ thông tin Google
    $phone = ""; // Số điện thoại không có sẵn từ Google OAuth, để trống hoặc yêu cầu người dùng thêm vào
} else {
    // Lấy tên người dùng hiện tại từ session (nếu đăng nhập bằng tài khoản thông thường)
    $username = $_SESSION['username'];
    $message = '';
    $error_message = ''; // Thêm biến lưu trữ lỗi

    // Lấy thông tin người dùng từ CSDL
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // Lưu thông tin người dùng hiện tại vào biến $user

    // Lấy thông tin người dùng từ CSDL để điền vào form
    $email = $user['email'];
    $phone = $user['phone'];
}

// Xử lý khi người dùng cập nhật thông tin
if (isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];

    // Kiểm tra định dạng tên người dùng, email và số điện thoại
    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $new_username)) {
        $error_message = "Nhập sai định dạng tên người dùng!";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Nhập sai định dạng email!";
    } elseif (!preg_match("/^[0-9]{10}$/", $new_phone)) { // Kiểm tra định dạng số điện thoại (10 số)
        $error_message = "Số điện thoại phải đủ 10 chữ số!";
    } else {
        if (isset($_SESSION['user_email'])) {
            // Nếu người dùng đăng nhập bằng Google, chỉ cập nhật số điện thoại vào CSDL hoặc session
            $_SESSION['user_phone'] = $new_phone;
            $message = "Cập nhật số điện thoại thành công!";
        } else {
            // Cập nhật thông tin vào CSDL cho người dùng thông thường
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE username = ?");
            $stmt->bind_param("ssss", $new_username, $new_email, $new_phone, $username);

            if ($stmt->execute()) {
                $message = "Cập nhật thông tin thành công!";
                $_SESSION['username'] = $new_username; // Cập nhật lại session với tên người dùng mới
            } else {
                $error_message = "Cập nhật thông tin không thành công!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thông tin cá nhân</title>
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
        <h2>Quản lý thông tin cá nhân</h2>

        <!-- Bảng thông báo -->
        <div class="notification <?php echo $message ? 'success show' : ''; ?>">
            <?php echo $message; ?>
        </div>
        <div class="notification <?php echo $error_message ? 'error show' : ''; ?>">
            <?php echo $error_message; ?>
        </div>

        <!-- Thẻ chứa thông tin người dùng -->
        <div class="card">
            <h3>Thông tin cá nhân</h3>
            <form action="edit_profile.php" method="POST">
                <div class="form-group">
                    <label for="username">Tên người dùng:</label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>" required>
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
