<?php
require 'db_connection.php';
session_start(); // Khởi tạo session
$message = '';

if (isset($_POST['login'])) {
    $username_or_phone = $_POST['username_or_phone'];
    $password = $_POST['password'];

    if (empty($username_or_phone) || empty($password)) {
        $message = "Vui lòng điền đầy đủ các trường thông tin.";
    } else {
        // Kiểm tra tên đăng nhập hoặc số điện thoại trong cơ sở dữ liệu
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR phone = ?");
        $stmt->bind_param("ss", $username_or_phone, $username_or_phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                // Lưu thông tin đăng nhập vào session
                $_SESSION['username'] = $user['username']; // Lưu tên người dùng vào session
                $_SESSION['phone'] = $user['phone']; // Lưu số điện thoại vào session
                $_SESSION['email'] = $user['email']; // Lưu email vào session
                $_SESSION['user_id'] = $user['id']; // Lưu ID người dùng vào session

                // Chuyển hướng đến trang chủ hoặc trang thông tin cá nhân sau khi đăng nhập thành công
                header("Location: trangchu2.php");
                exit(); 
            } else {
                $message = "Mật khẩu không đúng.";
            }
        } else {
            $message = "Tên đăng nhập hoặc số điện thoại không tồn tại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHqT8lDq7POxhh79t5+1dkePZr5kXcN9JKfpEBZXVV4BskIVF5t3oRMP5fEUexvE1zKk3y9g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Thiết lập chung */
        body {
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-card {
            display: flex;
            width: 1000px;
            max-width: 1200px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-left {
            background: url('img/img quizlet.PNG') center center/cover no-repeat; /* Sử dụng hình ảnh nền */
            width: 480px; /* Chỉnh lại chiều rộng của phần hình ảnh */
            height: 600px; /* Điều chỉnh chiều cao phù hợp với nội dung */
            background-position: center right;
        }

        .login-right {
            width: 50%; /* Chỉnh lại chiều rộng của phần form đăng nhập */
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 700;
        }

        .social-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 20px 0;
        }

        .social-buttons a {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .social-google { background-color: #db4437; }
        .social-facebook { background-color: #3b5998; }
        .social-apple { background-color: #333; }

        .social-buttons a:hover {
            opacity: 0.85;
        }

        .social-buttons a i {
            margin-right: 10px;
        }

        .divider {
            text-align: center;
            color: #aaa;
            margin: 20px 0;
            font-size: 14px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .login-form input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .login-form input:focus {
            border-color: #4caf50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
        }

        .login-form button {
            padding: 15px;
            border: none;
            background-color: #4caf50;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .login-form button:hover {
            background-color: #45a049;
        }

        .login-form .forgot-password {
            text-align: right;
            font-size: 14px;
        }

        .login-form .forgot-password a {
            text-decoration: none;
            color: #2575fc;
        }

        .register-link {
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
        }

        .register-link a {
            text-decoration: none;
            color: #2575fc;
            font-weight: 600;
        }

        .message {
            background-color: #ffefc4;
            color: #8a6d3b;
            border: 1px solid #faebcc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }

        .message.success {
            background-color: #dff0d8;
            color: #3c763d;
            border-color: #d6e9c6;
        }
        </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="login-left">
                <!-- Hình nền được đặt trực tiếp trong CSS phần login-left -->
            </div>
            <div class="login-right">
                <h2>Đăng nhập</h2>
                <?php if ($message): ?>
                    <div class="message <?php echo ($message === 'Đăng nhập thành công!') ? 'success' : ''; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <div class="social-buttons">
                    <a href="logingg.php" class="social-google"><i class="fab fa-google"></i> Đăng nhập bằng Google</a>
                </div>
                <div class="divider">hoặc email</div>
                <form action="login.php" method="POST" class="login-form">
                    <input type="text" name="username_or_phone" placeholder="Nhập tên đăng nhập hoặc số điện thoại" required>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                    <div class="forgot-password">
                        <a href="forgot_password.php">Quên mật khẩu</a>
                    </div>
                    <button type="submit" name="login">Đăng nhập</button>
                </form>
                <div class="register-link">
                    Mới sử dụng? <a href="register.php">Tạo tài khoản</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
