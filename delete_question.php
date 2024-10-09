<?php
require 'db_connection.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu ID của câu hỏi đã được gửi
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Xóa câu hỏi dựa trên ID
    $sql = "DELETE FROM quizzes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Nếu xóa thành công, chuyển về trang quản lý câu hỏi
        header("Location: manage_questions.php");
        exit();
    } else {
        echo "Lỗi khi xóa câu hỏi.";
    }
} else {
    echo "ID không hợp lệ.";
}
?>
