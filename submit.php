<?php
// Kết nối cơ sở dữ liệu (giả sử bạn đã có file kết nối)
require 'db_connection.php'; // Thay 'db_connection.php' bằng file kết nối thực tế của bạn

// Kiểm tra xem form có được gửi không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy tất cả câu hỏi và đáp án từ form
    $questions = $_POST['questions'];

    // Kiểm tra nếu tồn tại câu hỏi
    if (!empty($questions)) {
        // Lặp qua từng câu hỏi
        foreach ($questions as $questionId => $questionData) {
            $question = $questionData['question']; // Nội dung câu hỏi
            $answer1 = $questionData['answer1'];
            $answer2 = $questionData['answer2'];
            $answer3 = $questionData['answer3'];
            $answer4 = $questionData['answer4'];
            $correctAnswer = $questionData['correct_answer']; // Đáp án đúng (radio button)

            // Xử lý tải lên hình ảnh nếu có cho từng câu hỏi
            $image_path = null;
            if (isset($_FILES['questions']['name'][$questionId]['image']) && $_FILES['questions']['error'][$questionId]['image'] === 0) {
                $image_name = basename($_FILES['questions']['name'][$questionId]['image']);
                $image_tmp = $_FILES['questions']['tmp_name'][$questionId]['image'];
                $upload_dir = 'uploads/';  // Thư mục lưu ảnh
                $image_path = $upload_dir . $image_name;

                // Kiểm tra nếu thư mục uploads không tồn tại, thì tạo mới
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Di chuyển ảnh vào thư mục
                move_uploaded_file($image_tmp, $image_path);
            }

            // Lưu câu hỏi vào bảng `Quizzes`, bao gồm cả đường dẫn ảnh nếu có
            $sql = "INSERT INTO Quizzes (question, image_path) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $question, $image_path);
            $stmt->execute();

            // Lấy ID của câu hỏi vừa thêm vào (quiz_id)
            $quiz_id = $stmt->insert_id;

            // Lưu các đáp án vào bảng `Answers`
            $sql = "INSERT INTO Answers (quiz_id, answer_text, is_correct) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // Lưu đáp án 1
            $is_correct = ($correctAnswer == 1) ? 1 : 0;
            $stmt->bind_param("isi", $quiz_id, $answer1, $is_correct);
            $stmt->execute();

            // Lưu đáp án 2
            $is_correct = ($correctAnswer == 2) ? 1 : 0;
            $stmt->bind_param("isi", $quiz_id, $answer2, $is_correct);
            $stmt->execute();

            // Lưu đáp án 3
            $is_correct = ($correctAnswer == 3) ? 1 : 0;
            $stmt->bind_param("isi", $quiz_id, $answer3, $is_correct);
            $stmt->execute();

            // Lưu đáp án 4
            $is_correct = ($correctAnswer == 4) ? 1 : 0;
            $stmt->bind_param("isi", $quiz_id, $answer4, $is_correct);
            $stmt->execute();
        }

        // Chuyển hướng sau khi lưu thành công
        header("Location: create_question.php?status=success");
        exit();
    } else {
        // Nếu không có câu hỏi nào được nhập
        echo "Không có câu hỏi nào được nhập!";
    }
} else {
    echo "Không có dữ liệu từ form!";
}
