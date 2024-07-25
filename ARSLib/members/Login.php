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
    $username = clean_input($_POST["username"]);
    $password = clean_input($_POST["password"]);

    // Siapkan statement SQL untuk mengambil data pengguna
    $stmt = $mysqli->prepare("SELECT id, password FROM members WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Periksa apakah pengguna ditemukan
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Password benar, simpan informasi ke session
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            // Redirect ke homepage
            header('Location: homepage.php');
            exit;
        } else {
            // Password salah
            $error_message = "Password salah. Silakan coba lagi.";
        }
    } else {
        // Username tidak ditemukan
        $error_message = "Username tidak ditemukan. Silakan coba lagi.";
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
    <title>Login Page</title>
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

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">Selamat Datang!</div>
    <h2>Login</h2>
    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST" class="login-form">
        <input type="text" name="username" placeholder="Masukan Username" required>
        <input type="password" name="password" placeholder="Masukan Password" required>
        <div>
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Ingat saya</label>
            <a href="#" style="float: right;margin : 10px;">Lupa Password?</a>
        </div>
        <button type="submit">Login</button>
        <div class="link">
            Belum punya Akun? <a href="Daftar.php">Sign Up</a>
        </div>
    </form>
</div>

</body>
</html>
