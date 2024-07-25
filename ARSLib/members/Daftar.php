<?php
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

// Fungsi untuk membersihkan data input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_lengkap = clean_input($_POST["nama_lengkap"]);
    $nim = clean_input($_POST["nim"]);
    $email = clean_input($_POST["email"]);
    $username = clean_input($_POST["username"]);
    $password = clean_input($_POST["password"]);

    // Enkripsi password sebelum disimpan
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Siapkan statement SQL untuk menyimpan data
    $stmt = $mysqli->prepare("INSERT INTO members (nama_lengkap, nim, email, username, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama_lengkap, $nim, $email, $username, $password_hash);

    // Eksekusi statement dan cek keberhasilan
    if ($stmt->execute()) {
        echo "Pendaftaran berhasil. Silakan <a href='Login.php'>login</a>.";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    // Tutup statement
    $stmt->close();
}

// Tutup koneksi
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
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
            width: 400px;
        }

        .container h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-container {
            display: flex;
            flex-direction: column;
        }

        .form-container div {
            margin-bottom: 15px;
        }

        .form-container label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-action {
            margin-top: 20px;
            text-align: center;
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

        .form-action a {
            margin-top: 10px;
            display: block;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .form-action a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Daftar</h1>
    <form action="Daftar.php" method="post" class="form-container">
        <div>
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required>
        </div>
        <div>
            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="text" id="email" name="email" required>
        </div>
        <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-action">
            <button type="submit">Daftar</button>
            <a href="Login.php">Sudah punya akun? Masuk di sini</a>
        </div>
    </form>
</div>

</body>
</html>
