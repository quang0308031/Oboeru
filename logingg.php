<?php
require __DIR__ . "/vendor/autoload.php";

session_start();

// Bật hiển thị lỗi để dễ dàng debug (bạn có thể tắt sau khi hoàn thành)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tạo một đối tượng Google Client
$client = new Google\Client();
$client->setClientId("358203597193-t6dt3a3trhphu50m1jr1lp75gqbdit1v.apps.googleusercontent.com"); // Thay bằng Client ID của bạn
$client->setClientSecret("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"); // Thay bằng Client Secret của bạn
$client->setRedirectUri("http://localhost/doragon4/trangchu2.php");
$client->addScope("email"); // Yêu cầu quyền truy cập email
$client->addScope("profile"); // Yêu cầu quyền truy cập hồ sơ

// Kiểm tra nếu chưa có code trả về thì chuyển người dùng đến trang đăng nhập Google
if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl(); // Tạo URL xác thực
    echo "<p>Redirecting to Google for authentication...</p>";
    header("Location: $authUrl"); // Chuyển hướng người dùng đến Google
    exit();
} else {
    // Lấy mã truy cập (access token) với mã code trả về từ Google
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // Kiểm tra lỗi trong khi lấy access token
    if (isset($token['error'])) {
        exit('Failed to get access token: ' . $token['error']);
    }

    // Lưu token vào session để sử dụng trong tương lai
    $_SESSION['access_token'] = $token;

    // Thiết lập token cho client
    $client->setAccessToken($token['access_token']);

    // Tạo một đối tượng dịch vụ OAuth 2 để lấy thông tin người dùng
    $oauth2 = new Google\Service\Oauth2($client);
    $userinfo = $oauth2->userinfo->get();

    // Lưu thông tin người dùng vào session
    $_SESSION['user_email'] = $userinfo->email;
    $_SESSION['user_family_name'] = $userinfo->familyName;
    $_SESSION['user_given_name'] = $userinfo->givenName;
    $_SESSION['user_name'] = $userinfo->name;

    // Kiểm tra xem thông tin người dùng đã lấy được hay chưa
    if ($userinfo) {
        // Hiển thị thông báo đăng nhập thành công
        echo "<h1>Login Successful!</h1>";
        echo "<p>Welcome, " . htmlspecialchars($userinfo->name) . "!</p>";
        echo "<p>Your email: " . htmlspecialchars($userinfo->email) . "</p>";
        echo "<p>Family Name: " . htmlspecialchars($userinfo->familyName) . "</p>";
        echo "<p>Given Name: " . htmlspecialchars($userinfo->givenName) . "</p>";
        echo "<p>You will be redirected to the home page in 3 seconds...</p>";

        // Tự động chuyển hướng đến trang chủ sau 3 giây
        echo "<script>
            setTimeout(function() {
                window.location.href = 'trangchu2.php';
            }, 3000);
        </script>";
    } else {
        // Nếu không lấy được thông tin người dùng, hiển thị thông báo lỗi
        echo "<h1>Failed to retrieve user information.</h1>";
    }

    exit();
}
?>
