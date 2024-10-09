<?php
// Kết nối cơ sở dữ liệu
include 'db_connection.php';

// Lấy ID từ tham số URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn để lấy thông tin flashcard tương ứng
$sql = "SELECT * FROM flashcards WHERE id = $id"; // Thay đổi bảng và cột tùy theo cấu trúc cơ sở dữ liệu của bạn
$result = $conn->query($sql);

// Kiểm tra nếu có dữ liệu
if ($result->num_rows > 0) {
    $card = $result->fetch_assoc();
    $jsonData = json_encode(["card" => [$card]]); // Chuyển đổi dữ liệu sang định dạng JSON
} else {
    echo json_encode(["error" => "Không tìm thấy flashcard."]);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="flashcard.css">
    <script>
        // Nhận dữ liệu flashcard từ PHP
        const jsonString = <?php echo json_encode($jsonData); ?>;
        const obj = JSON.parse(jsonString);
        
        // Cập nhật nội dung flashcard
        document.addEventListener("DOMContentLoaded", function() {
            const word = document.getElementById("word");
            const mean = document.getElementById("meaning");
            const ex = document.getElementById("Ex");
            const img = document.getElementById("ex_img");
            
            word.innerHTML = obj.card[0].word;
            mean.innerHTML = obj.card[0].mean;
            ex.innerHTML = obj.card[0].ex;
            img.src = obj.card[0].img;
        });
    </script>
    <script src="flashcard.js" defer></script>
    <title><?php echo htmlspecialchars($card['title']); ?></title>
</head>
<body>
    <div id="flashcard" class="flashcard" onclick="flipCard(event)">
        <div class="front">
            <h2 id="word"></h2>
            <p id="meaning"></p>
            <img id="ex_img" src="" alt="Image">
        </div>
        <div class="back">
            <p id="Ex"></p>
        </div>
    </div>

    <div class="bar">
        <button id="arrow_left">&lt;</button>
        <button id="arrow_right">&gt;</button>
        <button id="hint">Gợi ý</button>
        <button id="speaker">Phát âm</button>
        <button id="star">Yêu thích</button>
    </div>
</body>
</html>
