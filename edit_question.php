<?php
require 'db_connection.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu ID của câu hỏi đã được gửi
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin câu hỏi hiện tại dựa trên ID
    $sql = "SELECT * FROM quizzes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $question = $result->fetch_assoc();

    if (!$question) {
        echo "Câu hỏi không tồn tại.";
        exit();
    }

    // Lấy các đáp án hiện tại
    $sql_answers = "SELECT * FROM answers WHERE quiz_id = ?";
    $stmt_answers = $conn->prepare($sql_answers);
    $stmt_answers->bind_param("i", $id);
    $stmt_answers->execute();
    $result_answers = $stmt_answers->get_result();
    $answers = [];
    
    while ($row = $result_answers->fetch_assoc()) {
        $answers[] = $row;
    }
} else {
    echo "ID câu hỏi không hợp lệ.";
    exit();
}

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_question = trim($_POST['question']); // Loại bỏ khoảng trắng thừa
    $image_path = $_POST['image_path']; // Đường dẫn hình ảnh
    $answer_texts = $_POST['answer_texts']; // Mảng đáp án
    $correct_answer = $_POST['correct_answer']; // Đáp án đúng

    // Kiểm tra nếu câu hỏi đã được chỉnh sửa
    if ($new_question === $question['question']) {
        // Nếu câu hỏi không thay đổi, hiển thị thông báo lỗi
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('errorModal').style.display = 'flex';
            });
        </script>";
    } else {
        // Cập nhật câu hỏi
        $sql = "UPDATE quizzes SET question = ?, image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $new_question, $image_path, $id);

        if ($stmt->execute()) {
            // Cập nhật các đáp án
            foreach ($answer_texts as $key => $answer_text) {
                $answer_id = $answers[$key]['id']; // Lấy ID của đáp án
                $sql_answer = "UPDATE answers SET answer_text = ?, is_correct = ? WHERE id = ?";
                $stmt_answer = $conn->prepare($sql_answer);
                $is_correct = ($key + 1 == $correct_answer) ? 1 : 0; // Đánh dấu đáp án đúng
                $stmt_answer->bind_param("sii", $answer_text, $is_correct, $answer_id);
                $stmt_answer->execute();
            }

            // Nếu cập nhật thành công, hiển thị thông báo
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('successModal').style.display = 'flex';
                    
                    // Sau 3 giây tự động quay về trang quản lý câu hỏi
                    setTimeout(function() {
                        window.location.href = 'manage_questions.php';
                    }, 3000);
                });
            </script>";
        } else {
            echo "Lỗi khi cập nhật câu hỏi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa câu hỏi</title>
    <style>
        body {
            background-color: #F8F9FA;
            color: #FFFFFF;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #FFFFFF;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007BFF;
        }

        h3 {
            color: #007BFF;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #495057;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #74C0FC;
            background-color: #E9ECEF;
            color: #212529;
        }

        button {
            background-color: #74C0FC;
            color: #1C253A;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            width: 100%;
        }

        button:hover {
            background-color: #5DADE2;
        }

        /* Modal styles */
        .modal {
            display: none; /* Ẩn modal theo mặc định */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Nền tối mờ */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #2F3E5A;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            color: white;
            width: 300px;
        }

        .close-btn {
            background-color: #74C0FC;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .close-btn:hover {
            background-color: #5DADE2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Chỉnh sửa câu hỏi</h2>
        <form method="POST">
            <label for="question">Câu hỏi:</label>
            <textarea name="question" id="question" rows="3" required><?php echo $question['question']; ?></textarea>

            <label for="image_path">Đường dẫn hình ảnh (nếu có):</label>
            <input type="text" name="image_path" id="image_path" value="<?php echo $question['image_path']; ?>">

            <h3>Chỉnh sửa đáp án</h3>
            <?php foreach ($answers as $key => $answer): ?>
                <label for="answer_text_<?php echo $key; ?>">Đáp án <?php echo $key + 1; ?>:</label>
                <input type="text" name="answer_texts[]" id="answer_text_<?php echo $key; ?>" value="<?php echo $answer['answer_text']; ?>" required>
                <label>
                    <input type="radio" name="correct_answer" value="<?php echo $key + 1; ?>" <?php echo $answer['is_correct'] ? 'checked' : ''; ?>> Đáp án đúng
                </label>
            <?php endforeach; ?>

            <button type="submit">Cập nhật</button>
        </form>
    </div>

    <!-- Modal thông báo thành công -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <p>Chỉnh sửa câu hỏi thành công!</p>
            <button class="close-btn" onclick="closeModal()">Đóng</button>
        </div>
    </div>

    <!-- Modal thông báo lỗi -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <p>Bạn chưa chỉnh sửa tên câu hỏi!</p>
            <button class="close-btn" onclick="closeErrorModal()">Đóng</button>
        </div>
    </div>

    <script>
        // Đóng modal thành công và chuyển hướng về trang quản lý câu hỏi
        function closeModal() {
            window.location.href = 'manage_questions.php'; // Chuyển hướng về trang quản lý câu hỏi
        }

        // Đóng modal lỗi
        function closeErrorModal() {
            document.getElementById('errorModal').style.display = 'none';
        }
    </script>
</body>
</html>
