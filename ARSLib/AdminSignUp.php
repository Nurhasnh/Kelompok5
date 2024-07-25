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

// Proses formulir pendaftaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Cek jika username atau email sudah ada
    $stmt = $mysqli->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = 'Username atau email sudah terdaftar.';
        $_SESSION['success'] = false;
    } else {
        // Simpan data admin baru
        $stmt = $mysqli->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Pendaftaran berhasil. Silakan login.';
            $_SESSION['success'] = true;
        } else {
            $_SESSION['message'] = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
            $_SESSION['success'] = false;
        }
        
        $stmt->close();
    }
    
    $mysqli->close();
    
    header('Location: AdminSignUp.php'); // Redirect ke halaman pendaftaran untuk menampilkan pesan
    exit;
}
?>
