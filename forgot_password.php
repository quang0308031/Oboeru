<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <style>
        /* Thiết lập chung */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9fc;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .forgot-password-card {
            width: 50%;
            max-width: 600px;
            background-color: #fff;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .forgot-password-card h1 {
            color: #1f1f41;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .forgot-password-card p {
            color: #5f5f77;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .forgot-password-card label {
            display: block;
            text-align: left;
            color: #333;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .forgot-password-card input[type="email"] {
            width: 100%;
            padding: 15px;
            border: 1px solid #e6e6eb;
            border-radius: 5px;
            margin-bottom: 20px;
            background-color: #f9f9fc;
            font-size: 16px;
        }

        .forgot-password-card input[type="email"]::placeholder {
            color: #b2b2c0;
        }

        .forgot-password-card input[type="email"]:focus {
            border-color: #5c6ac4;
            outline: none;
            background-color: #f4f4fc;
        }

        .forgot-password-card button {
            width: 100%;
            padding: 15px;
            border: none;
            background-color: #5c6ac4;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .forgot-password-card button:hover {
            background-color: #4a56a3;
        }

        .forgot-password-card button:active {
            background-color: #3b447c;
        }

        /* CSS cho bảng thông báo */
        .notification {
            background-color: #d4edda; /* Màu nền xanh nhạt */
            color: #155724; /* Màu chữ xanh đậm */
            border: 1px solid #c3e6cb; /* Đường viền xanh nhạt */
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            text-align: left; /* Căn lề trái */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-password-card">
            <h1>Đặt lại mật khẩu của bạn</h1>
            <p>Nhập email bạn đã đăng ký. Chúng tôi sẽ gửi cho bạn liên kết để đăng nhập và đặt lại mật khẩu. Nếu bạn đăng ký bằng email của phụ huynh, chúng tôi sẽ gửi cho họ liên kết.</p>
            <?php
            // Kiểm tra nếu có thông báo từ việc gửi liên kết
            if (isset($_GET['success']) && $_GET['success'] == 'true') {
                echo '<div class="notification">';
                echo '<strong>Thông báo:</strong> Liên kết đã được gửi tới email của bạn!';
                echo '</div>';
            }
            ?>
            <form action="reset_password.php" method="POST">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="ten@email.com" required>
                <button type="submit">Gửi liên kết</button>
            </form>
        </div>
    </div>
</body>
</html>
