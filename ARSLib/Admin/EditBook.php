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

// Cek apakah ID buku telah disediakan
if (isset($_POST['id_buku'])) {
    $id_buku = $_POST['id_buku'];

    // Ambil data buku berdasarkan ID
    $stmt = $mysqli->prepare("SELECT * FROM books WHERE id_buku = ?");
    $stmt->bind_param("i", $id_buku);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();

    // Jika formulir telah dikirimkan
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $judul = $_POST['judul'];
        $penulis = $_POST['penulis'];
        $deskripsi = $_POST['deskripsi'];
        $gambar = $_POST['gambar']; // URL gambar, asumsi bahwa gambar diupload ke server dan hanya URL disimpan

        // Update data buku
        $stmt = $mysqli->prepare("UPDATE books SET judul = ?, penulis = ?, deskripsi = ?, gambar = ? WHERE id_buku = ?");
        $stmt->bind_param("ssssi", $judul, $penulis, $deskripsi, $gambar, $id_buku);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = 'Buku berhasil diperbarui.';
        $_SESSION['success'] = true;

        header('Location: Perpustakaan.php');
        exit;
    }
} else {
    header('Location: Perpustakaan.php');
    exit;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
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
            max-width: 600px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .container h1 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
        }

        input[type="text"],
        textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit Buku</h1>
        <form method="POST" action="EditBook.php">
            <input type="hidden" name="id_buku" value="<?php echo htmlspecialchars($book['id_buku']); ?>">
            <label for="judul">Judul:</label>
            <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($book['judul']); ?>" required>
            <label for="penulis">Penulis:</label>
            <input type="text" id="penulis" name="penulis" value="<?php echo htmlspecialchars($book['penulis']); ?>" required>
            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($book['deskripsi']); ?></textarea>
            <label for="gambar">URL Gambar:</label>
            <input type="text" id="gambar" name="gambar" value="<?php echo htmlspecialchars($book['gambar']); ?>" required>
            <button type="submit" name="action" value="update">Update</button>
        </form>
    </div>

</body>
</html>
