<?php
// Kết nối cơ sở dữ liệu
include 'db_connection.php'; // Đảm bảo rằng đường dẫn đúng

// Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn lấy các cột term, definition, example và image_path từ bảng flashcards với cùng set_id
$sql_flashcards = "SELECT term, definition, example, image_path FROM flashcards WHERE set_id = $id"; 
$result_flashcards = $conn->query($sql_flashcards);

// Chuyển đổi dữ liệu flashcard sang mảng
$flashcards = [];
if ($result_flashcards && $result_flashcards->num_rows > 0) {
    while ($row = $result_flashcards->fetch_assoc()) {
        $flashcards[] = $row; // Lưu từng flashcard vào mảng
    }
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.css" />
    <title>Flashcards</title>
    <style>
        /* Thiết lập CSS cho nền tối và flashcard lớn */
        body {
            background-color: #f5f7fa; /* Nền sáng hơn */
            color: #333;
            font-family: 'Arial', sans-serif; /* Phông chữ đơn giản */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Chiếm toàn bộ chiều cao màn hình */
            margin: 0;
        }

        .flashcard {
            width: 600px; /* Kích thước lớn hơn */
            height: 400px; /* Kích thước lớn hơn */
            margin: 20px auto;
            perspective: 1000px;
        }

        .card {
            width: 100%;
            height: 100%;
            transition: transform 0.6s;
            transform-style: preserve-3d;
            position: relative;
            border-radius: 15px; /* Bo góc thẻ */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* Bóng đổ */
        }

        .card.flipped {
            transform: rotateY(180deg);
        }

        .card .front, .card .back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-radius: 15px; /* Bo góc thẻ */
            color: black; /* Màu chữ */
        }

        .card .front {
            background-color: white; /* Màu nền thẻ */
            font-size: 32px; /* Tăng kích thước chữ */
        }

        .card .back {
            background-color: white; /* Màu nền thẻ sau */
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column; /* Căn giữa theo cột */
            align-items: center; /* Căn giữa cho định nghĩa và ví dụ */
            justify-content: center; /* Căn giữa theo chiều dọc */
            padding: 20px; /* Thêm khoảng cách cho padding */
            text-align: center; /* Căn giữa nội dung */
        }

        #definition {
            font-size: 36px; /* Kích thước chữ lớn cho định nghĩa */
            margin-bottom: 10px; /* Khoảng cách giữa định nghĩa và ví dụ */
        }

        #example {
            font-size: 24px; /* Kích thước chữ nhỏ hơn cho ví dụ */
        }

        img {
            max-width: 70%; /* Kích thước ảnh nhỏ hơn */
            border-radius: 10px; /* Bo góc hình ảnh */
            margin-top: 10px; /* Khoảng cách với định nghĩa */
        }

        .nav-arrows {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            width: 100%; /* Để mũi tên chiếm toàn bộ chiều rộng */
            max-width: 600px; /* Giới hạn chiều rộng */
        }

        .arrow {
            cursor: pointer;
            font-size: 48px; /* Kích thước mũi tên lớn hơn */
            user-select: none;
            color: #4a69d6; /* Màu mũi tên */
            transition: color 0.3s;
        }

        .arrow:hover {
            color: #50b3a2; /* Thay đổi màu khi hover */
        }

        .counter {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 24px; /* Kích thước chữ cho bộ đếm */
            color: #333; /* Màu chữ */
        }

        .close-button {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #4a69d6;
            transition: color 0.3s;
        }

        .close-button:hover {
            color: #e74c3c; /* Màu khi hover */
        }
    </style>
</head>
<body>
    <div id="navbar" class="bar">
        <!-- Nội dung của thanh điều hướng, nếu có thể giữ nguyên -->
    </div>

    <!-- Nút quay lại thư viện -->
    <div class="close-button" onclick="goToLibrary()">X</div>

    <!-- Bộ đếm thẻ -->
    <div class="counter" id="counter"></div>

    <!-- Flashcard -->
    <div class="flashcard" id="flashcard" onclick="flipCard()">
        <div class="card">
            <div class="front" id="front">Thuật ngữ</div>
            <div class="back">
                <div id="definition">Định nghĩa</div>
                <div id="example">Ví dụ</div>
                <img id="image" src="" alt="Hình ảnh minh họa" style="display:none;" />
            </div>
        </div>
    </div>

    <!-- Mũi tên chuyển thẻ -->
    <div class="nav-arrows">
        <div class="arrow" onclick="showPreviousCard()">&#9664;</div>
        <div class="arrow" onclick="showNextCard()">&#9654;</div>
    </div>

    <script>
        let flashcards = <?php echo json_encode($flashcards); ?>; // Dữ liệu flashcards từ PHP
        let index = 0;
        let cardFlipped = false;

        // Hàm lật thẻ
        function flipCard() {
            const card = document.querySelector('.card');
            cardFlipped = !cardFlipped;
            if (cardFlipped) {
                card.classList.add('flipped');
            } else {
                card.classList.remove('flipped');
            }
        }

        // Cập nhật nội dung thẻ
        function updateCard() {
            document.getElementById('front').innerText = flashcards[index].term;
            document.getElementById('definition').innerText = flashcards[index].definition;
            document.getElementById('example').innerText = flashcards[index].example;

            const imagePath = flashcards[index].image_path;
            const imageElement = document.getElementById('image');

            if (imagePath) {
                imageElement.src = imagePath; // Cập nhật đường dẫn hình ảnh
                imageElement.style.display = 'block'; // Hiển thị hình ảnh
            } else {
                imageElement.style.display = 'none'; // Ẩn hình ảnh nếu không có
            }

            document.getElementById('counter').innerText = (index + 1) + '/' + flashcards.length; // Cập nhật bộ đếm
        }

        // Chuyển đến thẻ tiếp theo
        function showNextCard() {
            index = (index + 1) % flashcards.length; // Chuyển sang thẻ tiếp theo
            updateCard(); // Cập nhật nội dung thẻ
            cardFlipped = false; // Đảm bảo thẻ không bị lật khi chuyển
            document.querySelector('.card').classList.remove('flipped');
        }

        // Chuyển về thẻ trước đó
        function showPreviousCard() {
            index = (index - 1 + flashcards.length) % flashcards.length; // Chuyển về thẻ trước đó
            updateCard(); // Cập nhật nội dung thẻ
            cardFlipped = false; // Đảm bảo thẻ không bị lật khi chuyển
            document.querySelector('.card').classList.remove('flipped');
        }

        // Quay lại thư viện
        function goToLibrary() {
            window.location.href = 'thuvien.php'; // Chuyển hướng đến thuvien.php
        }

        // Khởi tạo thẻ đầu tiên
        document.addEventListener('DOMContentLoaded', () => {
            if (flashcards.length > 0) {
                updateCard(); // Cập nhật nội dung cho thẻ đầu tiên
            }
        });
    </script>
</body>
</html>
