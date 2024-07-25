<?php
$file = 'books.json';
$books = [];

if (file_exists($file)) {
    $books = json_decode(file_get_contents($file), true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Homepage</title>
    <style>
        /* CSS styling */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 50px;
            text-align: center;
            width: 80%;
            margin: 20px auto;
        }

        .search-bar input[type="text"] {
            width: calc(100% - 80px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-bar button {
            padding: 8px 15px;
            border: none;
            background-color: #333;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-bar button:hover {
            background-color: #444;
        }

        .card {
            width: calc(30% - 20px);
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            padding: 10px;
            cursor: pointer;
            transition: transform 0.2s;
            margin-right: 20px;
            display: inline-block;
            vertical-align: top;
        }

        .card:last-child {
            margin-right: 0;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 100%;
            height: 200px;
            background-color: #e0e0e0;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .card p {
            margin: 5px 0;
        }

        .card .rating {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card .rating span {
            font-size: 20px;
            color: #ffc107;
            cursor: pointer;
        }

        .card .rating span.active {
            color: #ff9800;
        }

        .card .comment-section {
            margin-top: 10px;
            text-align: left;
        }

        .card .comment-section form {
            display: flex;
            flex-direction: column;
        }

        .card .comment-section form textarea {
            resize: none;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .card .comment-section form button {
            align-self: flex-end;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            background-color: #5cb85c;
            color: white;
        }

        .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: white;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
            border-right: 1px solid #ccc;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 22px;
            color: black;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #f1f1f1;
        }

        .sidebar .closebtn {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 36px;
        }

        .openbtn {
            font-size: 20px;
            cursor: pointer;
            background-color: #333;
            color: white;
            padding: 10px 15px;
            border: none;
            position: fixed;
            top: 20px;
            left: 20px;
        }

        .openbtn:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="search-container">
            <input type="text" id="sidebarSearchInput" placeholder="Temukan..">
            <button type="submit" onclick="searchBooksSidebar()">🔍</button>
            <a href="homepage.php">Beranda</a>
            <a href="profil.php">Profil</a>
            <a href="perpustakaan.php">Perpustakaan</a>
            <a href="logout.php">Keluar</a>
        </div>
    </div>

    <div class="container">
        <h1>Daftar Buku</h1>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Temukan...">
            <button type="submit" onclick="searchBooks()">🔍</button>
        </div>
        <div class="results" id="results">
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $book): ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($book['gambar']); ?>" alt="Sampul Buku">
                        <h3><?php echo htmlspecialchars($book['judul']); ?></h3>
                        <p><?php echo htmlspecialchars($book['penulis']); ?></p>
                        <button class="detail" onclick="window.location.href='detail.php?id=<?php echo htmlspecialchars($book['id']); ?>'">Detail</button>
                        <div class="comment-section">
                            <form action="submit_comment.php" method="post">
                                <textarea name="comment" rows="3" placeholder="Tambahkan komentar..."></textarea>
                                <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['id']); ?>">
                                <button type="submit">Kirim</button>
                            </form>
                            <p>Komentar: <?php echo htmlspecialchars($book['komentar']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada buku yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <button class="openbtn" onclick="openNav()">☰</button>

    <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "250px";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
        }

        function searchBooks() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let cards = document.getElementsByClassName('card');
            for (let i = 0; i < cards.length; i++) {
                let title = cards[i].getElementsByTagName('h3')[0].innerText.toLowerCase();
                if (title.indexOf(input) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
