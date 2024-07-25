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

// Ambil data buku berdasarkan ID
$book_id = $_GET['id'];
$result = $mysqli->query("SELECT * FROM books WHERE id = $book_id");
$book = $result->fetch_assoc();

// Proses form peminjaman
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $tanggal_peminjaman = date('Y-m-d');
    $tanggal_pengembalian = $_POST['tanggal_pengembalian'];

    $sql = "INSERT INTO peminjaman (book_id, nama, email, tanggal_peminjaman, tanggal_pengembalian) 
            VALUES ($book_id, '$nama', '$email', '$tanggal_peminjaman', '$tanggal_pengembalian')";

    if ($mysqli->query($sql) === TRUE) {
        $_SESSION['message'] = "Buku telah berhasil terpinjam, selamat membaca!";
        $_SESSION['success'] = true;
        header('Location: Perpustakaan.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Buku</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            padding: 10px 15px;
            border: none;
            background-color: #111;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        .form-group button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Form Peminjaman Buku</h1>
        <h2><?php echo $book['judul']; ?></h2>
        <p><?php echo $book['penulis']; ?></p>

        <form method="post">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="tanggal_pengembalian">Tanggal Pengembalian:</label>
                <input type="date" id="tanggal_pengembalian" name="tanggal_pengembalian" required>
            </div>
            <div class="form-group">
                <button type="submit">Pinjam Buku</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
$mysqli->close();
?>