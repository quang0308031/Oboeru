<?php
// Kết nối cơ sở dữ liệu
include 'db_connection.php'; // Đảm bảo đường dẫn đúng

// Truy vấn lấy tiêu đề học phần từ bảng sets
$sql_sets = "SELECT id, title, description FROM sets"; // Thay đổi bảng và cột tùy theo cấu trúc cơ sở dữ liệu
$result_sets = $conn->query($sql_sets);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư viện của bạn</title>
    <link rel="stylesheet" href="thuvien.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: #fff;
            width: 250px;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .sidebar .logo h2 {
            color: #0056a6;
            font-weight: 700;
            margin-left: 10px;
            font-size: 24px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            padding: 10px;
            display: block;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .sidebar ul li a:hover {
            background-color: #f0f0f0;
            color: #0056a6;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 40px;
            background-color: #fafafa;
            overflow-y: scroll;
        }

        header {
            margin-bottom: 30px;
        }

        h1 {
            font-size: 32px;
            color: #333;
        }

        h2 {
            font-size: 24px;
            color: #0056a6;
            margin-bottom: 20px;
        }

       /* Set Grid Styles */
.set-grid {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.set-card {
    position: relative; /* Keep this to allow positioning for child elements */
    background-color: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 30%;
    margin: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e0e0e0;
    z-index: 1; /* Lower z-index to keep below dropdown */
}

.set-card h3 {
    margin: 0;
    font-size: 18px;
    color: #0056a6;
    font-weight: 600;
}

.set-card p {
    font-size: 14px;
    color: #666;
}

.set-card:hover {
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    transform: translateY(-5px);
}

/* Dropdown menu styles */
.dropdown {
    position: absolute; /* Keep it absolute to position correctly within the card */
    top: 10px; /* Adjusts the dropdown position */
    right: 10px;
    z-index: 1000; /* Ensure this is high enough to be on top */
}

.more-options {
    font-size: 20px;
    cursor: pointer;
    font-weight: bold;
}

.dropdown-content {
    display: none; /* Hidden by default */
    position: absolute; /* Keep absolute for dropdown positioning */
    right: 0; /* Aligns the dropdown with the button */
    top: 30px; /* Adjusts position relative to the "more-options" */
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 2000; /* Set a very high z-index */
}

.dropdown-content a {
    color: black;
    padding: 3px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

/* Show menu when hovering over more-options */
.dropdown:hover .dropdown-content {
    display: block;
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
                <li><a href="#">Thư viện của bạn</a></li>
                <li><a href="#">Lớp của bạn</a></li>
                <li><a href="#">+ Lớp mới</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Thư viện của bạn</h1>
            </header>

            <!-- Flashcard Sets -->
            <section class="flashcard-sets">
                <h2>Các học phần đã tạo</h2>
                <div class="set-grid">
                    <?php
                    if ($result_sets) {
                        // Kiểm tra số lượng kết quả
                        if ($result_sets->num_rows > 0) {
                            // Hiển thị mỗi học phần
                            while ($row = $result_sets->fetch_assoc()) {
                                echo "<div class='set-card' onclick=\"window.location.href='flashcard.php?id=" . $row['id'] . "'\">"; // Thêm id vào đường dẫn
                                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                                echo "<p>" . htmlspecialchars($row['description']) . "</p>";

                                // Thêm dấu ba chấm và menu thả xuống
                                echo "<div class='dropdown'>";
                                echo "<span class='more-options'>...</span>";
                                echo "<div class='dropdown-content'>";
                                echo "<a href='test.php?id=" . $row['id'] . "'>Kiểm tra</a>";
                                echo "<a href='chinhsua.php?id=" . $row['id'] . "'>Sửa</a>"; // Đường dẫn đến trang sửa
                                echo "<a href='delete.php?id=" . $row['id'] . "' onclick=\"return confirm('Bạn có chắc chắn muốn xóa?');\">Xoá</a>"; // Đường dẫn đến trang xóa
                                echo "</div>";
                                echo "</div>";

                                echo "</div>"; // Đóng thẻ set-card
                            }
                        } else {
                            echo "<p>Chưa có học phần nào được tạo.</p>";
                        }
                    } else {
                        echo "<p>Lỗi truy vấn: " . htmlspecialchars($conn->error) . "</p>";
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
