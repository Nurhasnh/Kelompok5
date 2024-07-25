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

// Fungsi untuk membaca data dari database
$result = $mysqli->query('SELECT * FROM books');
$books = $result->fetch_all(MYSQLI_ASSOC);

// Process form to return a book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'return') {
        $id_buku = $_POST['id_buku'];

        // Process book return logic
        $stmt = $mysqli->prepare("DELETE FROM borrowed_books WHERE id_buku = ?");
        $stmt->bind_param("s", $id_buku);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = 'Buku berhasil dikembalikan.';
        $_SESSION['success'] = true;

        header('Location: Perpustakaan.php');
        exit;
    }
}

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
        }

        .book {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
            max-width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

        .rating-form {
            margin-top: 10px;
            text-align: center;
        }

        .rating-form .stars {
            display: inline-flex;
            font-size: 24px;
        }

        .rating-form .stars input {
            display: none;
        }

        .rating-form .stars label {
            color: grey; /* Grey color for empty star */
            cursor: pointer;
        }

        .rating-form .stars label:hover,
        .rating-form .stars label:hover ~ label,
        .rating-form .stars input:checked ~ label {
            color: gold; /* Gold color for filled star */
        }

        .rating-form textarea {
            padding: 5px;
            font-size: 14px;
            margin-top: 10px;
            resize: vertical;
            width: 100%;
        }

        .rating-form button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: white;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }

        .rating-form button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Perpustakaan</h1>
        <div class="books-container">
            <?php foreach ($books as $book): ?>
                <div class="book">
                    <div style="display: flex;">
                        <?php if ($book['gambar']): ?>
                            <img src="<?php echo $book['gambar']; ?>" alt="Book Cover">
                        <?php endif; ?>
                        <div class="book-info">
                            <h2><?php echo $book['judul']; ?></h2>
                            <p><strong>Penulis:</strong> <?php echo $book['penulis']; ?></p>
                            <p><?php echo $book['deskripsi']; ?></p>
                            <div class="rating-form">
                                <form method="POST" action="Perpustakaan.php">
                                    <input type="hidden" name="id_buku" value="<?php echo $book['id_buku']; ?>">
                                    <div class="stars">
                                        <input type="radio" id="star5_<?php echo $book['id_buku']; ?>" name="rating" value="5"><label for="star5_<?php echo $book['id_buku']; ?>">&#9733;</label>
                                        <input type="radio" id="star4_<?php echo $book['id_buku']; ?>" name="rating" value="4"><label for="star4_<?php echo $book['id_buku']; ?>">&#9733;</label>
                                        <input type="radio" id="star3_<?php echo $book['id_buku']; ?>" name="rating" value="3"><label for="star3_<?php echo $book['id_buku']; ?>">&#9733;</label>
                                        <input type="radio" id="star2_<?php echo $book['id_buku']; ?>" name="rating" value="2"><label for="star2_<?php echo $book['id_buku']; ?>">&#9733;</label>
                                        <input type="radio" id="star1_<?php echo $book['id_buku']; ?>" name="rating" value="1"><label for="star1_<?php echo $book['id_buku']; ?>">&#9733;</label>
                                    </div>
                                    <textarea name="review" placeholder="Tulis komentar Anda..." required></textarea>
                                    <button type="submit" name="action" value="rating">Berikan Rating</button>
                                </form>
                            </div>
                            <div>
                                <h3>Rating: <?php echo isset($book['rating']) ? $book['rating'] : 'Belum ada rating'; ?></h3>
                                <h3>Reviews:</h3>
                                <ul>
                                    <?php if (!empty($book['reviews'])): ?>
                                        <?php foreach ($book['reviews'] as $review): ?>
                                            <li>
                                                <strong><?php echo $review['nama']; ?>:</strong> <?php echo $review['komentar']; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li>Belum ada review untuk buku ini.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="book-actions">
                        <form method="POST" action="Perpustakaan.php">
                            <input type="hidden" name="id_buku" value="<?php echo $book['id_buku']; ?>">
                            <button type="submit" name="action" value="return">Kembalikan Buku</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
