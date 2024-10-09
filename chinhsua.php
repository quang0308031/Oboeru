<?php
// Kết nối đến cơ sở dữ liệu MySQL
include 'db_connection.php'; // Bao gồm file db_connection.php

// Biến thông báo
$message = "";
$setId = null; // Khởi tạo biến setId

// Kiểm tra xem có id trong URL không
if (isset($_GET['id'])) {
    $setId = intval($_GET['id']); // Lấy ID từ URL
}

// Nếu form đã được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra xem người dùng đã bấm nút "Lưu" hay "Huỷ"
    if (isset($_POST['save'])) {
        // Lấy dữ liệu từ form
        $title = $_POST['title'];
        $description = $_POST['description'];
        $terms = $_POST['terms'];
        $definitions = $_POST['definitions'];
        $examples = $_POST['examples']; // Lấy dữ liệu của ví dụ
        $images = $_FILES['images']; // Lấy dữ liệu hình ảnh

        // Cập nhật thông tin vào bảng "sets"
        $sql = "UPDATE sets SET title='$title', description='$description' WHERE id='$setId'";

        if ($conn->query($sql) === TRUE) {
            // Xóa tất cả flashcards cũ
            $sql_delete_flashcards = "DELETE FROM flashcards WHERE set_id='$setId'";
            $conn->query($sql_delete_flashcards);

            // Cập nhật flashcards mới
            foreach ($terms as $index => $term) {
                $definition = $definitions[$index];
                $example = $examples[$index];
                $imagePath = null;

                // Xử lý tải hình ảnh
                if (!empty($images['name'][$index])) {
                    $targetDir = "uploads/"; // Thư mục để lưu hình ảnh
                    $imageFileName = uniqid() . "_" . basename($images['name'][$index]); // Tạo tên file duy nhất
                    $imagePath = $targetDir . $imageFileName; // Đường dẫn lưu hình ảnh
                    // Di chuyển hình ảnh vào thư mục uploads
                    if (move_uploaded_file($images['tmp_name'][$index], $imagePath)) {
                        // Chuyển thành công
                    } else {
                        $message = "Lỗi khi tải hình ảnh lên.";
                    }
                }

                // Thêm flashcard mới vào bảng "flashcards"
                $sql_flashcard = "INSERT INTO flashcards (set_id, term, definition, example, image_path) VALUES ('$setId', '$term', '$definition', '$example', '$imagePath')";
                $conn->query($sql_flashcard);
            }

            // Thông báo thành công và chuyển trang
            $message = "Học phần đã được cập nhật thành công!";
            echo "<script>
                    alert('$message');
                    window.location.href='thuvien.php';
                  </script>";
            exit;
        } else {
            $message = "Lỗi: " . $conn->error;
        }
    } else if (isset($_POST['cancel'])) {
        // Nếu bấm nút "Huỷ", chuyển hướng về thư viện
        header("Location: thuvien.php");
        exit;
    }
}

// Truy vấn để lấy dữ liệu học phần
$sql_set = "SELECT title, description FROM sets WHERE id='$setId'";
$result_set = $conn->query($sql_set);
$set_data = $result_set->fetch_assoc();

// Truy vấn để lấy dữ liệu flashcards
$sql_flashcards = "SELECT term, definition, example, image_path FROM flashcards WHERE set_id='$setId'";
$result_flashcards = $conn->query($sql_flashcards);
$flashcards = [];
if ($result_flashcards->num_rows > 0) {
    while ($row = $result_flashcards->fetch_assoc()) {
        $flashcards[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizlet - Chỉnh sửa học phần</title>
    <link rel="stylesheet" href="gdtaohocphanmoi.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }
        /* Các nút chỉnh sửa (Huỷ, Lưu) */
        .edit-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .cancel-btn, .save-btn {
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            width: 150px;
            text-align: center;
        }

        .cancel-btn {
            background-color: #e74c3c;
        }

        .save-btn {
            background-color: #27ae60;
        }

        .cancel-btn:hover {
            background-color: #c0392b;
        }

        .save-btn:hover {
            background-color: #229954;
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
                <h1>Chỉnh sửa học phần</h1>
            </header>

            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <!-- Form to edit the set -->
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Tiêu đề</label>
                    <input type="text" id="title" name="title" placeholder="Nhập tiêu đề học phần" value="<?= htmlspecialchars($set_data['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" placeholder="Nhập mô tả học phần" required><?= htmlspecialchars($set_data['description']) ?></textarea>
                </div>

                <h2>Chỉnh sửa Flashcard</h2>
                <div class="flashcard-group">
                    <?php foreach ($flashcards as $index => $card): ?>
                        <div class="flashcard">
                            <span class="card-number"><?= $index + 1 ?></span>
                            <input type="text" name="terms[]" value="<?= htmlspecialchars($card['term']) ?>" placeholder="Thuật ngữ" required>
                            <input type="text" name="definitions[]" value="<?= htmlspecialchars($card['definition']) ?>" placeholder="Định nghĩa" required>
                            <input type="text" name="examples[]" value="<?= htmlspecialchars($card['example']) ?>" placeholder="Ví dụ">
                            <label class="image-upload">
                                <input type="file" name="images[]" accept="image/*" onchange="showImageName(this)">
                                <span class="file-name hidden"></span>
                            </label>
                            <button type="button" class="delete-btn" onclick="deleteFlashcard(this)">🗑️</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Add Card Button -->
                <div class="add-card">
                    <button type="button" class="add-card-btn" onclick="addFlashcard()">
                        <span class="plus-icon">➕</span>
                        <span>Thêm thẻ</span>
                    </button>
                </div>

                <!-- Save and Cancel Buttons -->
                <div class="edit-buttons">
                    <button type="submit" name="cancel" class="cancel-btn">Huỷ</button>
                    <button type="submit" name="save" class="save-btn">Lưu</button>
                </div>
            </form>
        </main>
    </div>
    <script src="themxoataohocphanmoi.js"></script>
</body>
</html>
