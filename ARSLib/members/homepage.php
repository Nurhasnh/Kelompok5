<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Konfigurasi database
$host = 'localhost';
$db = 'ARSLib';
$user = 'root'; // Sesuaikan dengan username database Anda
$pass = ''; // Sesuaikan dengan password database Anda

// Buat koneksi
$mysqli = new mysqli($host, $user, $pass, $db);

// Periksa koneksi
if ($mysqli->connect_error) {
    die('Koneksi gagal: ' . $mysqli->connect_error);
}

// Fungsi untuk membaca data dari database
$result = $mysqli->query('SELECT * FROM books');
$books = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            padding: 20px;
        }
        .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }
        .sidebar a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #f1f1f1;
        }
        .sidebar .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }
        .openbtn {
            font-size: 20px;
            cursor: pointer;
            background-color: #111;
            color: white;
            padding: 10px 15px;
            border: none;
        }
        .openbtn:hover {
            background-color: #444;
        }
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .search-bar input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }
        .search-bar button {
            padding: 10px 15px;
            border: none;
            background-color: #111;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        .search-bar button:hover {
            background-color: #444;
        }
        .book-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .book-item {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1 1 calc(33.333% - 20px);
            box-sizing: border-box;
        }
        .book-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .book-item h2 {
            font-size: 1.5em;
            margin: 10px 0;
        }
        .book-item p {
            color: #555;
        }
        .book-item a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #111;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .book-item a:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="homepage.php">Beranda</a>
        <a href="perpustakaan.php">Perpustakaan</a>
        <a href="logout.php">Keluar</a>
    </div>

    <!-- Main content -->
    <div class="container">
        <button class="openbtn" onclick="openNav()">☰ Menu</button>
        <h1>Daftar Buku</h1>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Temukan...">
            <button type="submit" onclick="searchBooks()">🔍 Cari</button>
        </div>
        <div class="book-list">
            <?php foreach ($books as $book): ?>
                <div class="book-item">
                    <?php if (!empty($book['gambar'])): ?>
                        <img src="<?php echo $book['gambar']; ?>" alt="<?php echo $book['judul']; ?>">
                    <?php endif; ?>
                    <h2><?php echo $book['judul']; ?></h2>
                    <p><?php echo $book['penulis']; ?></p>
                    <p><?php echo $book['deskripsi']; ?></p>
                    <?php if (!empty($book['pdf'])): ?>
                        <a href="pinjam.php?id=<?php echo $book['id']; ?>">Pinjam Buku</a>
                        <a href="kembalikan.php?id=<?php echo $book['id']; ?>" style="background-color: red;">Kembalikan Buku</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "250px";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
        }

        function searchBooks() {
            var input = document.getElementById("searchInput").value.toLowerCase();
            var books = document.getElementsByClassName("book-item");

            for (var i = 0; i < books.length; i++) {
                var title = books[i].getElementsByTagName("h2")[0].innerText.toLowerCase();
                if (title.includes(input)) {
                    books[i].style.display = "block";
                } else {
                    books[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>

<?php
$mysqli->close();
?>