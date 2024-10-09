<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizlet Clone</title>
    <link rel="stylesheet" href="trangchu2.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Thêm CSS cho avatar và dropdown */
        .avatar-container {
            position: relative;
            display: inline-block;
            margin-left: 20px;
        }

        .avatar-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f0f0f0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        .avatar-dropdown {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 10;
            border-radius: 5px;
            min-width: 200px;
        }

        .avatar-dropdown ul {
            list-style-type: none;
            padding: 10px 0;
            margin: 0;
        }

        .avatar-dropdown ul li {
            padding: 10px 20px;
            cursor: pointer;
        }

        .avatar-dropdown ul li:hover {
            background-color: #f1f1f1;
        }

        .avatar-dropdown ul li a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
        }

        .avatar-dropdown ul li a:hover {
            color: #007bff;
        }

        .avatar-dropdown ul li a .icon {
            margin-right: 10px;
        }

        /* Hiển thị dropdown khi bấm vào avatar */
        .avatar-container.active .avatar-dropdown {
            display: block;
        }

        /* Dropdown menu cho plus sign */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 10;
            border-radius: 5px;
            min-width: 200px;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu ul {
            list-style-type: none;
            padding: 10px;
            margin: 0;
        }

        .dropdown-menu ul li {
            padding: 10px 20px;
            cursor: pointer;
        }

        .dropdown-menu ul li:hover {
            background-color: #f1f1f1;
        }

        .search-results {
            margin-top: 20px;
        }

        .search-results h4 {
            margin: 10px 0;
        }

        .search-results .set-card {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo">
                <a href="#"><img style="height:40px;width:40px;" src="./img/avt2.png" alt="Quizlet Logo"></a>
                <h2>Quiztask</h2>
            </div>
            <ul>
                <li><a href="#">Trang chủ</a></li>
                <li><a href="thuvien.php">Thư viện của bạn</a></li>
                <li><a href="#">Lớp của bạn</a></li>
                <li><a href="create_question.php"> Tạo câu hỏi </a></li>
                <li><a href="#">Lớp mới</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Tìm thẻ ghi nhớ" onkeyup="searchSets()">
                </div>
                <!-- Plus Sign and Free Trial Button -->
                <div class="premium-container">
                    <button class="plus-btn" id="plusButton">
                        <span class="plus-sign">+</span>
                    </button>
                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu" id="dropdownMenu">
                        <ul>
                            <li><a href="#">Lớp</a></li>
                            <li><a href="taohocphanmoi.php">Học phần</a></li>
                            <li><a href="#">Thư mục</a></li>
                        </ul>
                    </div>

                    <button class="premium-btn">
                        Dùng thử miễn phí
                    </button>
                    
                    <!-- Avatar container -->
                    <div class="avatar-container" id="avatarContainer">
                        <button class="avatar-btn" id="avatarButton">
                            <img src="./img/avt1.png" alt="User Avatar" class="avatar-img">
                        </button>

                        <!-- Dropdown Menu cho avatar -->
                        <div class="avatar-dropdown" id="avatarDropdown">
                            <ul>
                                <li><a href="#"><span class="icon">🏫</span>Lớp</a></li>
                                <li><a href="edit_profile.php"><span class="icon">👤</span>Cá nhân</a></li>
                                <li><a href="logout.php"><span class="icon">🔓</span>Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Recently Viewed -->
            <section class="recent-sets">
                <h3>Gần đây</h3>
                <div class="set-grid" id="setGrid">
                    <div class="set-card">
                        <h4>Hán tự N4</h4>
                        <p>24 thuật ngữ • Tác giả: quizlette1936634</p>
                    </div>
                    <div class="set-card">
                        <h4>Kanji 7-8-10</h4>
                        <p>71 thuật ngữ • Tác giả: quizlette1936634</p>
                    </div>
                    <div class="set-card">
                        <h4>Hán tự 9-11-12-13</h4>
                        <p>74 thuật ngữ • Tác giả: quizlette1936634</p>
                    </div>
                </div>
            </section>

            <!-- Search Results -->
            <div class="search-results" id="searchResults">
                <h4>Kết quả tìm kiếm:</h4>
                <div class="set-grid" id="searchSetGrid"></div>
            </div>

            <!-- Popular Sets -->
            <section class="popular-sets">
                <h3>Bộ thẻ ghi nhớ phổ biến</h3>
                <div class="set-grid">
                    <div class="set-card">
                        <h4>27 sửa đổi</h4>
                        <p>27 thuật ngữ</p>
                    </div>
                    <div class="set-card">
                        <h4>Wordly Wise 3000</h4>
                        <p>15 thuật ngữ</p>
                    </div>
                    <div class="set-card">
                        <h4>Family (1 урок)</h4>
                        <p>28 thuật ngữ</p>
                    </div>
                </div>
            </section>

            <!-- Popular Books -->
            <section class="popular-books">
                <h3>Sách giáo khoa phổ biến</h3>
                <div class="book-grid">
                    <div class="book-card">
                        <h4>Calculus: Early Transcendentals</h4>
                        <p>11,050 lời giải</p>
                    </div>
                    <div class="book-card">
                        <h4>Advanced Engineering Mathematics</h4>
                        <p>4,134 lời giải</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script>
        const sets = [
            { name: "Hán tự N4", author: "quizlette1936634", terms: 24 },
            { name: "Kanji 7-8-10", author: "quizlette1936634", terms: 71 },
            { name: "Hán tự 9-11-12-13", author: "quizlette1936634", terms: 74 },
            { name: "27 sửa đổi", author: "quizlette1936634", terms: 27 },
            { name: "Wordly Wise 3000", author: "quizlette1936634", terms: 15 },
            { name: "Family (1 урок)", author: "quizlette1936634", terms: 28 }
        ];

        function searchSets() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const results = sets.filter(set => set.name.toLowerCase().includes(input));
            displayResults(results);
        }

        function displayResults(results) {
            const searchSetGrid = document.getElementById("searchSetGrid");
            searchSetGrid.innerHTML = ""; // Xóa kết quả trước đó

            if (results.length === 0) {
                searchSetGrid.innerHTML = "<p>Không tìm thấy kết quả nào.</p>";
                return;
            }

            results.forEach(set => {
                const setCard = document.createElement("div");
                setCard.className = "set-card";
                setCard.innerHTML = `<h4>${set.name}</h4><p>${set.terms} thuật ngữ • Tác giả: ${set.author}</p>`;
                searchSetGrid.appendChild(setCard);
            });
        }

        // Thêm JavaScript để xử lý dropdown menu của avatar
        document.getElementById("avatarButton").addEventListener("click", function() {
            document.getElementById("avatarContainer").classList.toggle("active");
        });

        // Thêm JavaScript để xử lý dropdown menu của plus sign
        document.getElementById("plusButton").addEventListener("click", function() {
            document.getElementById("dropdownMenu").classList.toggle("active");
        });

        // Đóng menu khi nhấp ra ngoài
        window.onclick = function(event) {
            if (!event.target.matches('.avatar-btn') && !event.target.matches('.avatar-img') && !event.target.matches('.plus-btn')) {
                var dropdowns = document.getElementsByClassName("avatar-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === "block") {
                        openDropdown.style.display = "none";
                    }
                }
                document.getElementById("avatarContainer").classList.remove("active");
                document.getElementById("dropdownMenu").classList.remove("active");
            }
        }
    </script>
</body>
</html>
