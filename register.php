<?php
require 'db_connection.php';
$message = '';

if (isset($_POST['register'])) {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';

    // Kiểm tra các trường thông tin có trống không
    if (empty($username) || empty($password) || empty($email) || empty($phone)) {
        $message = "Vui lòng điền đầy đủ các trường thông tin.";
    } 
    // Kiểm tra định dạng email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email không đúng định dạng. Vui lòng nhập theo định dạng abc@abc.abc.";
    } 
    // Kiểm tra định dạng số điện thoại (phải đủ 10 chữ số)
    elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Số điện thoại phải đủ 10 chữ số.";
    } 
    else {
        // Kiểm tra tên đăng nhập hoặc email đã tồn tại
        $checkUser = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $checkUser->bind_param("ss", $username, $email);
        $checkUser->execute();
        $result = $checkUser->get_result();

        if ($result->num_rows > 0) {
            $message = "Tên đăng nhập hoặc email đã tồn tại.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $password, $email, $phone);
            if ($stmt->execute()) {
                $message = "Đăng ký thành công!";
            } else {
                $message = "Lỗi đăng ký, vui lòng thử lại.";
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
    <title>Đăng ký tài khoản</title>
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

        .register-card {
            display: flex;
            width: 1000px;
            max-width: 1200px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .register-left {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            width: 50%; /* Chỉnh lại chiều rộng của phần hình ảnh */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            position: relative;
        }

        .register-left img {
            width: 100%; /* Đặt chiều rộng của hình ảnh bằng 100% */
            height: auto; /* Để tự động điều chỉnh chiều cao */
            max-height: 100vh; /* Giới hạn chiều cao của hình ảnh */
            object-fit: cover; /* Giúp hình ảnh không bị biến dạng */
        }

        .register-left h3 {
            font-size: 24px;
            margin-top: 15px;
            font-weight: 600;
            position: absolute;
            bottom: 10px;
            left: 20px;
        }

        .register-right {
            width: 50%; /* Chỉnh lại chiều rộng của phần form đăng ký */
            padding: 60px;
        }

        .register-right h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 700;
        }

        .social-buttons {
            display: flex;
            margin: 20px 0;
        }

        .social-buttons a {
            width: 100%;
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

        .social-buttons a:hover {
            opacity: 0.85;
        }

        .social-buttons a i {
            margin-right: 10px;
        }

        .divider {
            text-align: center;
            color: #aaa;
            margin: 30px 0;
            font-size: 14px;
        }

        .register-right .input-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .register-right .input-group select {
            width: 30%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .register-right .input-group select:focus {
            border-color: #4caf50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
        }

        .register-right input,
        .register-right select {
            width: 100%;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .register-right input:focus,
        .register-right select:focus {
            border-color: #4caf50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
        }

        .register-right button {
            width: 100%;
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

        .register-right button:hover {
            background-color: #45a049;
        }

        .register-right .terms {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .register-right .terms input {
            margin-right: 10px;
        }

        .register-right .terms span {
            font-size: 14px;
        }

        .register-right .terms a {
            text-decoration: none;
            color: #2575fc;
            font-weight: 600;
        }

        .terms input {
            margin-top: 3%;
            width: 10%;
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
        <div class="register-card">
            <div class="register-left">
                <img src="img/img quizlet.PNG" alt="Image"> <!-- Cập nhật đường dẫn đến ảnh của bạn -->
            </div>
            <div class="register-right">
                <h2>Đăng ký</h2>
                <?php if ($message): ?>
                    <div class="message <?php echo ($message === 'Đăng ký thành công!') ? 'success' : ''; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <div class="social-buttons">
                    <a href="logingg.php" class="social-google"><i class="fab fa-google"></i> Tiếp tục với Google</a>
                </div>
                <div class="divider">hoặc email</div>
                <form action="register.php" method="POST">
                    <div class="input-group">
                        <select name="day" required>
                            <option value="">Ngày</option>
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="month" required>
                            <option value="">Tháng</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>">Tháng <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="year" required>
                            <option value="">Năm</option>
                            <?php for ($i = 1980; $i <= date("Y"); $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <input type="email" name="email" placeholder="E-mail" required>
                    <input type="text" name="username" placeholder="Tên người dùng" required>
                    <input type="text" name="phone" placeholder="Số điện thoại" required> <!-- Trường Số điện thoại -->
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                    <div class="terms">
                        <input type="checkbox" required>
                        <span>Tôi chấp nhận <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a></span>
                    </div>
                    <button type="submit" name="register">Đăng ký</button>
                </form>
                <div class="divider">Đã có tài khoản? <a href="login.php">Đăng nhập</a></div>
            </div>
        </div>
    </div>
</body>
</html>
