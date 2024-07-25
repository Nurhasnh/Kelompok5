<?php
session_start();

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

// Proses pendaftaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Simpan data ke database
    $stmt = $mysqli->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Pendaftaran berhasil! Silakan login.';
        $_SESSION['success'] = true;
        header('Location: AdminLogin.php');
        exit;
    } else {
        $_SESSION['message'] = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
        $_SESSION['success'] = false;
    }

    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign-Up Page</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .signup-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .signup-form button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: white;
            font-size: 16px;
        }

        .signup-form button:hover {
            background-color: #444;
        }

        .signup-form .link {
            margin-top: 10px;
            text-align: center;
        }

        .signup-form .link a {
            color: #333;
            text-decoration: none;
        }

        .signup-form .link a:hover {
            text-decoration: underline;
        }

        .header {
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Daftar</h2>
    <form action="AdminDaftar.php" method="POST" class="signup-form">
        <input type="text" name="username" placeholder="Masukan Username" required>
        <input type="email" name="email" placeholder="Masukan Email" required>
        <input type="password" name="password" placeholder="Masukan Password" required>
        <button type="submit">Daftar</button>
        <div class="link">
            Sudah punya akun? <a href="AdminLogin.php">Masuk</a>
        </div>
    </form>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
