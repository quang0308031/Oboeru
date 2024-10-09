<?php
// Kết nối cơ sở dữ liệu
include 'db_connection.php'; // Đảm bảo đường dẫn đúng

// Lấy ID của bộ flashcard từ URL
$set_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn lấy tất cả các từ (term) từ bảng flashcards với set_id tương ứng
$sql_questions = "SELECT id, term, definition FROM flashcards WHERE set_id = ?"; // Câu hỏi
$stmt = $conn->prepare($sql_questions);

if ($stmt) {
    $stmt->bind_param("i", $set_id);
    $stmt->execute();
    $result_questions = $stmt->get_result();
} else {
    die("Lỗi chuẩn bị câu truy vấn: " . $conn->error);
}

// Kiểm tra xem có câu hỏi nào không
if ($result_questions->num_rows == 0) {
    echo "<p>Không có câu hỏi nào trong bộ học phần này.</p>";
    exit;
}

// Lưu trữ câu hỏi và đáp án
$questions = [];
while ($row = $result_questions->fetch_assoc()) {
    $questions[] = $row;
}

// Khởi tạo các đáp án
$quiz_data = [];
foreach ($questions as $question) {
    $term_id = $question['id'];
    $term = $question['term'];
    $correct_answer = $question['definition'];

    // Lấy 3 đáp án sai ngẫu nhiên
    $sql_wrong_answers = "SELECT definition FROM flashcards WHERE set_id = ? AND definition != ? ORDER BY RAND() LIMIT 3";
    $stmt_wrong = $conn->prepare($sql_wrong_answers);
    $stmt_wrong->bind_param("is", $set_id, $correct_answer);
    $stmt_wrong->execute();
    $result_wrong = $stmt_wrong->get_result();

    $wrong_answers = [];
    while ($row_wrong = $result_wrong->fetch_assoc()) {
        $wrong_answers[] = $row_wrong['definition'];
    }

    // Trộn các đáp án đúng và sai
    $options = array_merge([$correct_answer], $wrong_answers);
    shuffle($options); // Trộn đáp án để ngẫu nhiên

    // Lưu dữ liệu câu hỏi
    $quiz_data[] = [
        'term' => $term,
        'options' => $options,
        'correct_answer' => $correct_answer,
    ];
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra</title>
    <link rel="stylesheet" href="test.css">
    <style>
        .correct-answer {
            background-color: #ffcccc; /* Nền đỏ nhạt */
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            font-size: 20px; /* Kích thước chữ cho đáp án đúng */
        }
        .close-button {
            position: fixed; /* Đặt lại thành fixed để có thể nhấn vào bất kỳ lúc nào */
            top: 10px;
            right: 10px; /* Đưa nút sang bên phải */
            font-size: 32px; /* Tăng kích thước chữ */
            color: red;
            cursor: pointer;
            z-index: 1000; /* Đảm bảo nó nằm trên tất cả các phần khác */
        }
        .question p {
            font-size: 24px; /* Kích thước chữ cho câu hỏi */
        }
        label {
            font-size: 20px; /* Kích thước chữ cho đáp án */
        }
    </style>
</head>
<body>
    <h1>Kiểm tra</h1>

    <div class="close-button" onclick="window.location.href='thuvien.php'">&times;</div> <!-- Nút quay lại thư viện -->

    <?php
    // Xử lý khi người dùng nộp bài
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $score = 0;
        $user_answers = $_POST['answers'];
        
        foreach ($quiz_data as $index => $question) {
            $is_correct = $user_answers[$index] === $question['correct_answer'];
            if ($is_correct) {
                $score++;
            }

            echo '<div class="question">';
            echo '<p>' . htmlspecialchars($index + 1) . ". " . htmlspecialchars($question['term']) . '</p>';
            echo '<p class="correct-answer">Đáp án đúng: ' . htmlspecialchars($question['correct_answer']) . '</p>';
            echo '</div>';
        }
        
        echo "<h2>Điểm của bạn: " . htmlspecialchars($score) . "/" . count($quiz_data) . "</h2>";
    } else {
        // Hiển thị câu hỏi nếu chưa nộp bài
        echo '<form method="POST" id="quiz-form">';
        foreach ($quiz_data as $index => $question): ?>
            <div class="question">
                <p><?php echo htmlspecialchars($index + 1) . ". " . htmlspecialchars($question['term']); ?></p>
                <?php foreach ($question['options'] as $option): ?>
                    <label>
                        <input type="radio" name="answers[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($option); ?>" required>
                        <?php echo htmlspecialchars($option); ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit">Nộp bài</button>
        </form>
    <?php } ?>

    <script>
        document.getElementById('quiz-form').onsubmit = function() {
            return confirm('Bạn có chắc chắn muốn nộp bài không?');
        };
    </script>
</body>
</html>
