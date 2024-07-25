<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: AdminLogin.php');
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

// Proses pengembalian buku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'return') {
    $id_buku = $_POST['id_buku'];
    $member_id = $_SESSION['member_id']; // Pastikan member_id sudah diset di sesi

    // Hapus entri peminjaman
    $stmt = $mysqli->prepare("DELETE FROM borrowed_books WHERE id_buku = ? AND member_id = ?");
    $stmt->bind_param("ii", $id_buku, $member_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Terima kasih sudah membaca buku.';
        $_SESSION['success'] = true;
    } else {
        $_SESSION['message'] = 'Gagal mengembalikan buku.';
        $_SESSION['success'] = false;
    }
    $stmt->close();
    
    header('Location: Perpustakaan.php');
    exit;
}

// Fungsi untuk membaca data buku yang dipinjam dari database
$query = 'SELECT books.*, borrowed_books.member_id FROM books 
          JOIN borrowed_books ON books.id = borrowed_books.id_buku
          WHERE borrowed_books.member_id = ?';

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION['member_id']);
$stmt->execute();

$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .container h1 {
            text-align: center;
        }

        .books-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .book {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
            max-width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .book:hover {
            transform: scale(1.02);
        }

        .book img {
            width: 100%;
            height: auto;
        }

        .book-info {
            padding: 15px;
        }

        .book-info h2 {
            font-size: 18px;
            margin: 0;
        }

        .book-info p {
            margin: 10px 0;
        }

        .book-actions {
            padding: 15px;
            background-color: #f9f9f9;
            text-align: center;
            display: flex;
            justify-content: center;
        }

        .book-actions button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }

        .book-actions button:hover {
            background-color: #444;
        }

        .message {
            padding: 10px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            text-align: center;
            color: #333;
        }

        .message.success {
            border-color: #4CAF50;
            color: #4CAF50;
        }

        .message.error {
            border-color: #F44336;
            color: #F44336;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Perpustakaan</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo $_SESSION['success'] ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['success']); ?>
        <?php endif; ?>

        <div class="books-container">
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $book): ?>
                    <div class="book">
                        <?php if (!empty($book['gambar']) && file_exists($book['gambar'])): ?>
                            <img src="<?php echo htmlspecialchars($book['gambar']); ?>" alt="Book Cover">
                        <?php else: ?>
                            <img src="assets/img1.png" alt="No Image Available">
                        <?php endif; ?>
                        <div class="book-info">
                            <h2><?php echo htmlspecialchars($book['judul']); ?></h2>
                            <p><strong>Penulis:</strong> <?php echo htmlspecialchars($book['penulis']); ?></p>
                            <p><?php echo htmlspecialchars($book['deskripsi']); ?></p>
                        </div>
                        <div class="book-actions">
                            <form method="POST" action="Perpustakaan.php">
                                <input type="hidden" name="id_buku" value="<?php echo $book['id_buku']; ?>">
                                <button type="submit" name="action" value="return">Kembalikan Buku</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
