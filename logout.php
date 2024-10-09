<?php
session_start(); // Bắt đầu session

// Xóa tất cả các biến session
$_SESSION = array();

// Nếu muốn xóa hoàn toàn session, bạn có thể xóa cả cookie phiên
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy bỏ session
session_destroy();

// Chuyển hướng người dùng về trang đăng nhập hoặc trang chủ
header("Location: login.php"); // Thay 'login.php' bằng đường dẫn của trang bạn muốn chuyển hướng tới sau khi đăng xuất
exit();
?>
