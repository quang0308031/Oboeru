<?php
require 'db_connection.php'; // Kết nối cơ sở dữ liệu

// Lấy các câu hỏi và trộn ngẫu nhiên
$sql = "SELECT * FROM Quizzes";
$result = $conn->query($sql);
$questions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    // Trộn ngẫu nhiên các câu hỏi và đảm bảo không trùng lặp
    shuffle($questions);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Làm Bài Kiểm Tra</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            background-color: #F8F9FA;
            color: #495057;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 800px;
            background-color: #FFFFFF;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007BFF;
        }

    
        .question-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .question-row img {
            max-width: 150px;
            height: auto;
            margin-left: 15px;
        }

        .options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 50px;
        }

        .option {
            background-color: #E9ECEF;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
            color: #212529;
        }

        .option:hover {
            background-color: #bdbfcc;
        }

        .option input[type="radio"] {
            display: none;
        }

        .option.selected {
            background-color: #555;
            color: white;
        }

        .submit-btn {
            text-align: center;
            margin-top: 30px;
        }

        button {
            padding: 10px 30px;
            font-size: 16px;
            background-color: #74C0FC;
            color: #1C253A;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #5DADE2;
        }

        #scoreModal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            color: #1C253A;
        }

        .modal-content h3 {
            margin-bottom: 20px;
        }

        .error {
            color: red;
            font-weight: bold;
            font-size: 18px;
        }

        .score {
            margin-top: 20px;
            text-align: center;
            font-size: 20px;
            color: blue;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Làm bài kiểm tra</h2>
        <form id="quizForm" onsubmit="return submitQuiz()">

            <?php 
            $question_number = 1; 
            foreach ($questions as $question): ?>
                <div class="question-block">
                    <div class="question-row">
                        <div>
                            Câu hỏi <?php echo $question_number++; ?>: <?php echo $question['question']; ?>
                            <span class="error" id="error-<?php echo $question['id']; ?>"></span>
                        </div>

                        <?php if (!empty($question['image_path'])): ?>
                            <img src="<?php echo $question['image_path']; ?>" alt="Image for question">
                        <?php endif; ?>
                    </div>

                    <?php
                    $quiz_id = $question['id'];
                    $sql_answers = "SELECT * FROM Answers WHERE quiz_id = ?";
                    $stmt = $conn->prepare($sql_answers);
                    $stmt->bind_param('i', $quiz_id);
                    $stmt->execute();
                    $result_answers = $stmt->get_result();
                    $answers = [];

                    while ($answer = $result_answers->fetch_assoc()) {
                        $answers[] = $answer;
                    }

                    shuffle($answers);
                    ?>

                    <div class="options">
                        <?php foreach ($answers as $answer): ?>
                            <label class="option">
                                <input type="radio" name="answers[<?php echo $quiz_id; ?>]" value="<?php echo $answer['id']; ?>">
                                <span><?php echo $answer['answer_text']; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div> 
            <?php endforeach; ?>

            <div class="submit-btn">
                <button type="submit">Nộp bài</button>
            </div>
        </form>

        <div id="scoreModal">
            <div class="modal-content">
                <h3>Điểm của bạn: <span id="scoreDisplay"></span></h3>
                <button onclick="window.location.href='trangchu2.php'">Quay về trang chủ</button>
            </div>
        </div>

    </div>

    <script>
        document.querySelectorAll('.option input[type="radio"]').forEach((radio) => {
            radio.addEventListener('click', (event) => {
                const option = event.target.parentElement;
                if (option.classList.contains('selected')) {
                    option.classList.remove('selected');
                    radio.checked = false;
                } else {
                    const allOptions = option.parentElement.querySelectorAll('.option');
                    allOptions.forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');
                }
            });
        });

        function submitQuiz() {
            let valid = true;
            let score = 0;
            const questions = document.querySelectorAll('.question-block');

            questions.forEach(question => {
                const radios = question.querySelectorAll('input[type="radio"]');
                const errorSpan = question.querySelector('.error');
                let selected = false;

                radios.forEach(radio => {
                    if (radio.checked) {
                        selected = true;
                        if (radio.value == radios[0].value) {
                            score++;
                        }
                    }
                });

                if (!selected) {
                    errorSpan.textContent = ' !';
                    valid = false;
                } else {
                    errorSpan.textContent = '';
                }
            });

            if (valid) {
                showScoreModal(score);
            }

            return false;
        }

        function showScoreModal(score) {
            document.getElementById('scoreDisplay').textContent = score;
            document.getElementById('scoreModal').style.display = 'flex';
        }
    </script>
</body>
</html>
