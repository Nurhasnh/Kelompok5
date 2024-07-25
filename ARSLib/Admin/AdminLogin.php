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

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Cek kredensial
    $stmt = $mysqli->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        
        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Set session
            $_SESSION['admin_id'] = $id;
            $_SESSION['logged_in'] = true;
            
            // Redirect ke halaman admin utama
            header('Location: AdminHomepage.php');
            exit;
        } else {
            $_SESSION['message'] = 'Username atau password salah.';
            $_SESSION['success'] = false;
        }
    } else {
        $_SESSION['message'] = 'Username tidak ditemukan.';
        $_SESSION['success'] = false;
    }
    
    $stmt->close();
    $mysqli->close();
    
    header('Location: AdminLogin.php'); // Redirect ke halaman login untuk menampilkan pesan
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Page</title>
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

        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-form input[type="checkbox"] {
            width: auto;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: white;
            font-size: 16px;
        }

        .login-form button:hover {
            background-color: #444;
        }

        .login-form .link {
            margin-top: 10px;
            text-align: center;
        }

        .login-form .link a {
            color: #333;
            text-decoration: none;
        }

        .login-form .link a:hover {
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
    <div class="header">Selamat Datang, Admin!</div>
    <h2>Masuk</h2>
    <form action="AdminLogin.php" method="POST" class="login-form">
        <input type="text" name="username" placeholder="Masukkan Username" required>
        <input type="password" name="password" placeholder="Masukkan Password" required>
        <div>
            <input type="checkbox" id="remember">
            <label for="remember">Ingat saya</label>
            <a href="#" style="float: right;">Lupa Password?</a>
        </div>
        <button type="submit">Login</button>
        <div class="link">
            Belum punya Akun? <a href="AdminDaftar.php">Daftar</a>
        </div>
    </form>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
