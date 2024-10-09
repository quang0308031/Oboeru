<?php
// Kết nối đến cơ sở dữ liệu MySQL
include 'db_connection.php'; // Bao gồm file db_connection.php

// Biến thông báo
$message = "";

// Kiểm tra xem form đã được gửi hay chưa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $terms = $_POST['terms'];
    $definitions = $_POST['definitions'];
    $examples = $_POST['examples']; // Lấy dữ liệu của ví dụ
    $images = $_FILES['images']; // Lấy dữ liệu hình ảnh

    // Chuẩn bị câu lệnh SQL để lưu thông tin vào bảng "sets"
    $sql = "INSERT INTO sets (title, description) VALUES ('$title', '$description')";

    if ($conn->query($sql) === TRUE) {
        $setId = $conn->insert_id; // Lấy ID của bản ghi mới tạo

        // Lưu từng flashcard
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $definition = $definitions[$i];
            $example = $examples[$i]; // Lấy ví dụ tương ứng
            
            // Xử lý tải hình ảnh
            $imagePath = null;
            if (!empty($images['name'][$i])) {
                $targetDir = "uploads/"; // Thư mục để lưu hình ảnh
                $imageFileName = uniqid() . "_" . basename($images['name'][$i]); // Tạo tên file duy nhất
                $imagePath = $targetDir . $imageFileName; // Đường dẫn lưu hình ảnh
                // Di chuyển hình ảnh vào thư mục uploads
                if (move_uploaded_file($images['tmp_name'][$i], $imagePath)) {
                    // Chuyển thành công
                } else {
                    $message = "Lỗi khi tải hình ảnh lên.";
                }
            }

            // Lưu thông tin flashcard vào bảng "flashcards"
            $sql_flashcard = "INSERT INTO flashcards (set_id, term, definition, example, image_path) VALUES ('$setId', '$term', '$definition', '$example', '$imagePath')";
            $conn->query($sql_flashcard);
        }

        // Thông báo thành công và chuyển trang
        $message = "Học phần mới đã được tạo thành công!";
        echo "<script>
                alert('$message');
                window.location.href='thuvien.php';
              </script>";
        exit;
    } else {
        $message = "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizlet - Tạo học phần mới</title>
    <link rel="stylesheet" href="gdtaohocphanmoi.css"> <!-- Kết nối với CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo">
                <img src="https://via.placeholder.com/40" alt="Quizlet Logo">
                <h2>Quizlet</h2>
            </div>
            <ul>
                <li><a href="trangchu2.php">Trang chủ</a></li>
                <li><a href="thuvien.php">Thư viện của bạn</a></li>
                <li><a href="#">Lớp của bạn</a></li>
                <li><a href="#">+ Lớp mới</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Tạo một học phần mới</h1>
            </header>

            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <!-- Form to create a new set -->
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Tiêu đề</label>
                    <input type="text" id="title" name="title" placeholder="Nhập tiêu đề học phần" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" placeholder="Nhập mô tả học phần" required></textarea>
                </div>

                <h2>Tạo Flashcard</h2>
                <div class="flashcard-group">
                    <!-- Flashcard 1 -->
                    <div class="flashcard">
                        <span class="card-number">1</span>
                        <input type="text" name="terms[]" placeholder="Thuật ngữ" required>
                        <input type="text" name="definitions[]" placeholder="Định nghĩa" required>
                        <input type="text" name="examples[]" placeholder="Ví dụ"> <!-- Xóa thuộc tính required -->
                        <label class="image-upload">
                            <input type="file" name="images[]" accept="image/*" onchange="showImageName(this)"> <!-- Xóa thuộc tính required -->
                            <span class="file-name hidden"></span> <!-- Thẻ span để hiển thị tên tệp -->
                        </label>
                        <button type="button" class="delete-btn" onclick="deleteFlashcard(this)">🗑️</button>
                    </div>

                    <!-- Flashcard 2 -->
                    <div class="flashcard">
                        <span class="card-number">2</span>
                        <input type="text" name="terms[]" placeholder="Thuật ngữ" required>
                        <input type="text" name="definitions[]" placeholder="Định nghĩa" required>
                        <input type="text" name="examples[]" placeholder="Ví dụ"> <!-- Xóa thuộc tính required -->
                        <label class="image-upload">
                            <input type="file" name="images[]" accept="image/*" onchange="showImageName(this)"> <!-- Xóa thuộc tính required -->
                            <span class="file-name hidden"></span> <!-- Thẻ span để hiển thị tên tệp -->
                        </label>
                        <button type="button" class="delete-btn" onclick="deleteFlashcard(this)">🗑️</button>
                    </div>
                </div>


                <!-- Add Card Button -->
                <div class="add-card">
                    <button type="button" class="add-card-btn" onclick="addFlashcard()">
                        <span class="plus-icon">➕</span>
                        <span>Thêm thẻ</span>
                    </button>
                </div>

                <!-- Create Button -->
                <div class="create-button">
                    <button type="submit" class="create-set-btn">Tạo</button>
                </div>
            </form>
        </main>
    </div>
    <script src="themxoataohocphanmoi.js"></script>
</body>
</html>
