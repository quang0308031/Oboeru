<?php
require 'db_connection.php'; // Kết nối cơ sở dữ liệu

// Lấy tất cả các câu hỏi từ cơ sở dữ liệu
$sql = "SELECT * FROM quizzes";
$result = $conn->query($sql);
$questions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý câu hỏi</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        }

        body {
            background-color: #F8F9FA; /* Nền tối màu navy */
            color: #FFFFFF; /* Màu chữ trắng */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

                /* Đảm bảo các hàng có chiều cao phù hợp và các nội dung trong cột đều căn giữa */
td {
    vertical-align: middle; /* Đảm bảo nội dung trong ô được căn giữa */
    text-align: center; /* Đảm bảo các nút "Chỉnh sửa" và "Xóa" được căn giữa */
}

/* Căn chỉnh lại phần hình ảnh và đảm bảo kích thước cố định */
td img {
    width: 150px; /* Đặt chiều rộng cố định cho hình ảnh */
    height: auto;
    border-radius: 5px;
    display: block; /* Hiển thị ảnh như một phần tử block */
    margin: 0 auto; /* Căn giữa ảnh trong cột */
}

/* Các nút "Chỉnh sửa" và "Xóa" với chiều cao cố định và căn giữa */
.action-buttons {
    display: flex;
    justify-content: center; /* Căn giữa các nút theo chiều ngang */
    align-items: center; /* Căn giữa các nút theo chiều dọc */
    gap: 10px;
    height: 100%; /* Đảm bảo các nút kéo dài toàn bộ chiều cao của ô */
}

/* Nút "Chỉnh sửa" */
.btn-edit {
    padding: 8px 16px;
    background-color: #74C0FC; /* Màu xanh nhạt */
    color: #1C253A; /* Màu chữ tối */
    border-radius: 8px;
    border: none;
    cursor: pointer;
}

/* Nút "Xóa" */
.btn-delete {
    padding: 8px 16px;
    background-color: #FF6B6B; /* Màu đỏ cho nút xóa */
    color: white;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}

/* Khi hover, thay đổi màu nền */
.btn-edit:hover {
    background-color: #5DADE2; /* Màu xanh nhạt hơn khi hover */
}

.btn-delete:hover {
    background-color: #FF4D4D; /* Màu đỏ đậm hơn khi hover */
}

/* Loại bỏ highlight khi nhấn vào các nút */
.btn-edit:focus, .btn-delete:focus {
    outline: none;
    box-shadow: none; /* Loại bỏ highlight màu xanh khi focus */
}

/* Đảm bảo các hàng có chiều cao đồng đều với phần hành động */
th, td {
    padding: 12px;
    text-align: left;
    /* border-bottom: 1px solid #74C0FC; Đường viền dưới màu xanh nhạt */
    vertical-align: middle; /* Căn giữa theo chiều dọc */
}


        .container {
            width: 90%;
            max-width: 1000px;
            background-color: #FFFFFF; /* Màu navy cho thẻ */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007BFF; /* Màu trắng cho tiêu đề */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            color: #495057; /* Màu chữ trắng */
        }

        tr {
            border-bottom: 1px solid #74C0FC;
        }

        th, td {
            padding: 12px;
            text-align: left;
            /* border-bottom: 1px solid #74C0FC; Đường viền dưới màu xanh nhạt */
        }

        th {
            background-color: #394867; /* Màu navy đậm cho tiêu đề bảng */
            color: #FFFFFF;
        }

        td img {
            width: 150px; /* Đặt chiều rộng cố định cho hình ảnh */
            height: auto;
            border-radius: 5px;
        }

        .btn-edit, .btn-delete {
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-block;
            outline: none;
        }

        .btn-edit {
            background-color: #74C0FC; /* Màu xanh nhạt */
            color: #495057; /* Màu chữ tối */
        }

        .btn-edit:hover {
            background-color: #5DADE2; /* Màu xanh nhạt hơn khi hover */
        }

        .btn-delete {
            background-color: #FF6B6B; /* Màu đỏ cho nút xóa */
            color: #FFFFFF;
        }

        .btn-delete:hover {
            background-color: #FF4D4D; /* Màu đỏ đậm hơn khi hover */
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center; /* Căn giữa các nút */
        }

        .action-buttons a {
            text-decoration: none; /* Bỏ gạch chân trong liên kết */
        }

        .add-question {
            display: block;
            background-color: #85c9ff;
            color: #1C253A;
            padding: 10px 20px;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .add-question:hover {
            background-color: #5DADE2;
        }

        /* Đảm bảo các hàng có chiều cao phù hợp */
        td {
            vertical-align: middle; /* Đảm bảo nội dung trong ô được căn giữa */
        }

        /* Loại bỏ highlight khi nhấn vào các nút */
        .btn-edit:focus, .btn-delete:focus {
            outline: none;
            box-shadow: none; /* Loại bỏ highlight màu xanh khi focus */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quản lý câu hỏi</h2>

        <a href="create_question.php" class="add-question">Tạo câu hỏi mới</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Câu hỏi</th>
                    <th>Hình ảnh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $question): ?>
                    <tr>
                        <td><?php echo $question['id']; ?></td>
                        <td><?php echo $question['question']; ?></td>
                        <td>
                            <?php if ($question['image_path']): ?>
                                <img src="<?php echo $question['image_path']; ?>" alt="Question Image">
                            <?php else: ?>
                                Không có ảnh
                            <?php endif; ?>
                        </td>
                        <td class="action-buttons">
                            <a href="edit_question.php?id=<?php echo $question['id']; ?>" class="btn-edit">Chỉnh sửa</a>
                            <a href="delete_question.php?id=<?php echo $question['id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
