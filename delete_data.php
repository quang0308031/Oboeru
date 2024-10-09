<?php
require 'db_connection.php'; // Kết nối cơ sở dữ liệu

// Xóa tất cả các câu hỏi và đáp án từ bảng Quizzes và Answers
$sql_answers = "DELETE FROM Answers";
$sql_questions = "DELETE FROM Quizzes";

if ($conn->query($sql_answers) === TRUE && $conn->query($sql_questions) === TRUE) {
    // Nếu xóa thành công, chuyển hướng về trang chính với thông báo thành công
    header("Location: index.php?status=deleted");
} else {
    // Nếu có lỗi xảy ra, hiển thị thông báo lỗi
    echo "Lỗi khi xóa dữ liệu: " . $conn->error;
}
?>
