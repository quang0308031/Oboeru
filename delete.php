<?php
// Kết nối cơ sở dữ liệu
include 'db_connection.php'; // Đảm bảo đường dẫn đúng

// Kiểm tra xem đã nhận được tham số 'id' chưa
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Tạo truy vấn xóa từ bảng sets
    $sql_delete = "DELETE FROM sets WHERE id = ?";
    
    // Chuẩn bị và thực thi truy vấn
    if ($stmt = $conn->prepare($sql_delete)) {
        $stmt->bind_param("i", $id); // Gắn giá trị 'id' vào câu lệnh SQL
        if ($stmt->execute()) {
            // Sau khi xóa thành công, chuyển hướng về trang thuvien.php
            header("Location: thuvien.php?message=deleted");
            exit();
        } else {
            echo "Lỗi khi xóa học phần: " . $stmt->error;
        }
    } else {
        echo "Lỗi trong việc chuẩn bị truy vấn: " . $conn->error;
    }

    // Đóng statement và kết nối
    $stmt->close();
} else {
    echo "Không tìm thấy ID học phần cần xóa.";
}

$conn->close();
?>
