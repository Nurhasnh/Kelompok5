<?php
session_start();

// Cek apakah admin sudah login
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

// Process form to upload a new book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'upload') {
        $id_buku = $_POST['id_buku'];
        $judul = $_POST['judul'];
        $penulis = $_POST['penulis'];
        $deskripsi = $_POST['deskripsi'];

        // Process book image upload
        $gambar = '';
        if ($_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['gambar']['name'];
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');

            if (in_array($file_ext, $allowed_ext)) {
                $gambar = 'uploads/' . uniqid() . '.' . $file_ext;
                move_uploaded_file($file_tmp, $gambar);
            }
        }

        // Process book PDF upload
        $pdf = '';
        if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['pdf']['name'];
            $file_tmp = $_FILES['pdf']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = array('pdf');

            if (in_array($file_ext, $allowed_ext)) {
                $pdf = 'uploads/' . uniqid() . '.' . $file_ext;
                move_uploaded_file($file_tmp, $pdf);
            }
        }

        // Save book data into database
        $stmt = $mysqli->prepare("INSERT INTO books (id_buku, judul, penulis, deskripsi, gambar, pdf) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $id_buku, $judul, $penulis, $deskripsi, $gambar, $pdf);
        $stmt->execute();

        // Check if the data was inserted successfully
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = 'Buku berhasil diunggah dan telah masuk ke perpustakaan.';
            $_SESSION['success'] = true;
        } else {
            $_SESSION['message'] = 'Terjadi kesalahan saat mengunggah buku.';
            $_SESSION['success'] = false;
        }

        $stmt->close();
        $mysqli->close();

        header('Location: AdminHomepage.php'); // Redirect to AdminHomepage.php to display updated book list
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage</title>
    <style>
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
            width: 800px;
        }

        .container h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container div {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 15px;
        }

        .form-container label {
            width: 20%;
            text-align: right;
            margin-right: 10px;
        }

        .form-container input, .form-container textarea, .form-container file {
            width: 75%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container textarea {
            resize: none;
        }

        .form-container .upload-success {
            display: flex;
            align-items: center;
            margin-top: 10px;
            justify-content: center;
        }

        .form-container .upload-success .icon {
            margin-right: 10px;
            color: green;
            font-size: 24px;
        }

        .form-container .upload-success p {
            color: green;
            margin: 0;
            font-weight: bold;
        }

        .form-action {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 20px;
        }

        .form-action button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .form-action button:hover {
            background-color: #444;
        }

        .form-action button.delete {
            background-color: red;
        }

        .form-action button.delete:hover {
            background-color: darkred;
        }

        /* Sidebar styles */
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

    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="AdminHomepage.php">Beranda</a>
        <a href="AdminLibrary.php">Admin Library</a>
        <a href="logout.php">Keluar</a>
    </div>

    <button class="openbtn" onclick="openNav()">☰</button>

    <div class="container">
        <h1>POSTINGAN ADMIN</h1>
        <form action="AdminHomepage.php" method="post" enctype="multipart/form-data" class="form-container">
            <div>
                <label for="id_buku">ID Buku</label>
                <input type="text" id="id_buku" name="id_buku">
            </div>
            <div>
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul">
            </div>
            <div>
                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis">
            </div>
            <div>
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4"></textarea>
            </div>
            <div>
                <label for="gambar">Upload Gambar</label>
                <input type="file" id="gambar" name="gambar" accept="image/*">
            </div>
            <div>
                <label for="pdf">Upload PDF</label>
                <input type="file" id="pdf" name="pdf" accept=".pdf">
            </div>
            <div class="form-action">
                <button type="submit" name="action" value="upload">Upload Buku</button>
            </div>
        </form>
        <?php if (isset($_SESSION['message']) && $_SESSION['success']): ?>
            <div class="form-container upload-success">
                <div class="icon">&#10004;</div>
                <p><?php echo $_SESSION['message']; ?></p>
            </div>
            <?php unset($_SESSION['message']); ?>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </div>

    <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "250px";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
        }
    </script>

</body>
</html>
