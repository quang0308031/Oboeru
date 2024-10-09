<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo câu hỏi trắc nghiệm</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: auto;
            background-color: #F8F9FA; /* Màu nền xám nhạt */
        }

        .container {
            width: 100%;
            max-width: 900px;
            background-color: #ffffff; /* Nền trắng cho form */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            color: #333333; /* Màu văn bản */
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            color: #007BFF; /* Màu xanh dương nhạt */
        }

        .question-block {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: #495057; /* Màu xám tối cho nhãn */
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #CED4DA; /* Màu xám nhạt cho viền */
            font-size: 16px;
            margin-bottom: 20px;
            resize: none;
            background-color: #E9ECEF; /* Màu nền nhạt */
            color: #212529; /* Màu chữ đen nhạt */
        }

        .answer-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .answer {
            background-color: #E9ECEF; /* Nền nhạt cho câu trả lời */
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #212529; /* Màu chữ đen nhạt */
        }

        input[type="radio"] {
            margin-right: 15px;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        button {
            padding: 15px 30px;
            border: none;
            background-color: #007BFF; /* Màu xanh dương nhạt */
            color: #FFFFFF; /* Màu trắng cho chữ */
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3; /* Màu xanh dương đậm khi hover */
        }

        .add-question {
            background-color: #28A745; /* Màu xanh lá cho nút thêm câu hỏi */
        }

        .add-question:hover {
            background-color: #218838; /* Màu xanh lá đậm hơn khi hover */
        }

        /* Thêm class này để áp dụng cho cả 2 nhóm nút */
        .action-button {
            padding: 15px 30px;
            font-size: 16px;
            background-color: #17A2B8; /* Màu xanh ngọc */
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
        }

        .action-button:hover {
            background-color: #138496; /* Màu xanh ngọc đậm khi hover */
        }

        .navigation-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tạo câu hỏi trắc nghiệm</h2>
        <form action="submit.php" method="POST" enctype="multipart/form-data" id="question-form" onsubmit="return handleSubmit()">
            <div id="question-container">
                <!-- Câu hỏi 1 ban đầu -->
                <div class="question-block" id="question-1">
                    <label for="question">Câu hỏi 1:</label>
                    <textarea id="question" name="questions[1][question]" rows="3" required></textarea>

                    <label for="image">Thêm ảnh (nếu có):</label>
                    <input type="file" name="questions[1][image]" accept="image/*"><br><br>

                    <div class="answer-group">
                        <div class="answer">
                            <label><input type="radio" name="questions[1][correct_answer]" value="1"> Đáp án 1</label>
                            <input type="text" name="questions[1][answer1]" placeholder="Nhập đáp án 1" style="width: 75%; padding: 10px;" required>
                        </div>
                        <div class="answer">
                            <label><input type="radio" name="questions[1][correct_answer]" value="2"> Đáp án 2</label>
                            <input type="text" name="questions[1][answer2]" placeholder="Nhập đáp án 2" style="width: 75%; padding: 10px;" required>
                        </div>
                        <div class="answer">
                            <label><input type="radio" name="questions[1][correct_answer]" value="3"> Đáp án 3</label>
                            <input type="text" name="questions[1][answer3]" placeholder="Nhập đáp án 3" style="width: 75%; padding: 10px;" required>
                        </div>
                        <div class="answer">
                            <label><input type="radio" name="questions[1][correct_answer]" value="4"> Đáp án 4</label>
                            <input type="text" name="questions[1][answer4]" placeholder="Nhập đáp án 4" style="width: 75%; padding: 10px;" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="button-group">
                <button type="button" class="add-question" onclick="addMoreQuestions()">Thêm câu hỏi</button>
                <button type="submit">Lưu tất cả câu hỏi</button>
            </div>
        </form>

        <!-- Nút quay lại trang chủ -->
        <div class="navigation-group">
            <button class="action-button" onclick="location.href='trangchu2.php'">Quay về trang chủ</button>
            <button class="action-button" onclick="location.href='take_quiz.php'">Làm bài kiểm tra</button>
            <button class="action-button" onclick="location.href='manage_questions.php'">Sửa câu hỏi</button>
        </div>
    </div>

    <script>
        let questionCount = 1; // Đếm số lượng câu hỏi

        function addMoreQuestions() {
            questionCount++;
            const container = document.getElementById('question-container');

            const newQuestionBlock = document.createElement('div');
            newQuestionBlock.classList.add('question-block');
            newQuestionBlock.id = `question-${questionCount}`;
            newQuestionBlock.innerHTML = `
                <label for="question">Câu hỏi ${questionCount}:</label>
                <textarea id="question" name="questions[${questionCount}][question]" rows="3" required></textarea>

                <label for="image">Thêm ảnh (nếu có):</label>
                <input type="file" name="questions[${questionCount}][image]" accept="image/*"><br><br>

                <div class="answer-group">
                    <div class="answer">
                        <label><input type="radio" name="questions[${questionCount}][correct_answer]" value="1"> Đáp án 1</label>
                        <input type="text" name="questions[${questionCount}][answer1]" placeholder="Nhập đáp án 1" style="width: 75%; padding: 10px;" required>
                    </div>
                    <div class="answer">
                        <label><input type="radio" name="questions[${questionCount}][correct_answer]" value="2"> Đáp án 2</label>
                        <input type="text" name="questions[${questionCount}][answer2]" placeholder="Nhập đáp án 2" style="width: 75%; padding: 10px;" required>
                    </div>
                    <div class="answer">
                        <label><input type="radio" name="questions[${questionCount}][correct_answer]" value="3"> Đáp án 3</label>
                        <input type="text" name="questions[${questionCount}][answer3]" placeholder="Nhập đáp án 3" style="width: 75%; padding: 10px;" required>
                    </div>
                    <div class="answer">
                        <label><input type="radio" name="questions[${questionCount}][correct_answer]" value="4"> Đáp án 4</label>
                        <input type="text" name="questions[${questionCount}][answer4]" placeholder="Nhập đáp án 4" style="width: 75%; padding: 10px;" required>
                    </div>
                </div>
            `;

            container.appendChild(newQuestionBlock);

            // Cập nhật thanh cuộn
            setTimeout(() => {
                // Cuộn về câu hỏi mới thêm
                newQuestionBlock.scrollIntoView({ behavior: 'smooth' });
            }, 100);

            // Di chuyển phần nút xuống cuối cùng
            const buttonGroup = document.querySelector('.button-group');
            container.appendChild(buttonGroup);
        }

        // Kiểm tra xem các đáp án có trùng nhau không
        function validateForm() {
            const questionBlocks = document.querySelectorAll('.question-block');
            for (const block of questionBlocks) {
                const answers = [];
                block.querySelectorAll('input[type="text"]').forEach(input => {
                    answers.push(input.value.trim().toLowerCase());
                });

                // Kiểm tra xem có đáp án nào trùng nhau không
                const hasDuplicate = answers.some((item, index) => answers.indexOf(item) !== index);
                if (hasDuplicate) {
                    alert('Các đáp án không được trùng nhau trong một câu hỏi!');
                    return false; // Ngăn chặn form submit nếu trùng đáp án
                }
            }
            return true; // Cho phép submit nếu không có lỗi
        }

        // Xử lý khi submit form, hiển thị thông báo thành công
        function handleSubmit() {
            if (validateForm()) {
                alert("Tạo câu hỏi mới thành công!"); // Hiển thị thông báo khi lưu thành công
                return true; // Tiếp tục gửi form
            }
            return false; // Ngăn gửi form nếu có lỗi
        }
    </script>
</body>
</html>
