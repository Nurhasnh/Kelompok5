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

// Cek apakah user sudah login sebagai admin
$adminLoggedIn = isset($_SESSION['admin_id']); // Sesuaikan dengan logika autentikasi admin yang sesuai

if (!$adminLoggedIn) {
    echo "Anda tidak memiliki akses untuk mengakses halaman ini.";
    exit;
}

// Query untuk mendapatkan total buku
$result = $mysqli->query("SELECT COUNT(*) AS totalBooks FROM books");
$totalBooks = $result->fetch_assoc()['totalBooks'];

// Query untuk mendapatkan buku yang dipinjam
$result = $mysqli->query("SELECT COUNT(*) AS borrowedBooks FROM books WHERE status = 'dipinjam'"); // Sesuaikan dengan kolom status
$borrowedBooks = $result->fetch_assoc()['borrowedBooks'];

// Hitung sisa buku yang tersedia
$availableBooks = $totalBooks - $borrowedBooks;

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .chart-container {
            margin-top: 20px;
            height: 300px;
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container button {
            padding: 10px 20px;
            border: none;
            background-color: #333;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }

        .button-container button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Dashboard Admin</h1>

    <table>
        <tr>
            <th>Total Buku</th>
            <td><?php echo $totalBooks; ?></td>
        </tr>
        <tr>
            <th>Buku yang Dipinjam</th>
            <td><?php echo $borrowedBooks; ?></td>
        </tr>
        <tr>
            <th>Sisa Buku Tersedia</th>
            <td><?php echo $availableBooks; ?></td>
        </tr>
    </table>

    <div class="chart-container">
        <canvas id="myChart"></canvas>
    </div>

    <div class="button-container">
        <button onclick="window.location.href = 'perpustakaan.php';">Kelola Perpustakaan</button>
    </div>
</div>

<script>
    // Data untuk grafik
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total Buku', 'Buku yang Dipinjam', 'Sisa Buku Tersedia'],
            datasets: [{
                label: 'Jumlah Buku',
                data: [<?php echo $totalBooks; ?>, <?php echo $borrowedBooks; ?>, <?php echo $availableBooks; ?>],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
