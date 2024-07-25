<?php
// Simulasi data buku, ganti dengan logika pengambilan data sesungguhnya
$books = [
    [
        'id' => '1',
        'judul' => 'Judul Buku 1',
        'penulis' => 'Penulis 1',
        'gambar' => 'gambar1.jpg',
        'deskripsi' => 'Deskripsi buku 1',
        'rating' => 4,
        'komentar' => 'Komentar tentang buku 1'
    ],
    [
        'id' => '2',
        'judul' => 'Judul Buku 2',
        'penulis' => 'Penulis 2',
        'gambar' => 'gambar2.jpg',
        'deskripsi' => 'Deskripsi buku 2',
        'rating' => 5,
        'komentar' => 'Komentar tentang buku 2'
    ]
];

// Ambil id buku dari parameter GET
$book_id = $_GET['id'];

// Cari buku berdasarkan id
$book = array_filter($books, function ($b) use ($book_id) {
    return $b['id'] == $book_id;
});

// Ambil buku pertama dari hasil pencarian (harusnya hanya satu buku)
$book = reset($book);

if (!$book) {
    die('Buku tidak ditemukan.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku - <?php echo htmlspecialchars($book['judul']); ?></title>
    <style>
        /* CSS styling */
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
            text-align: center;
            width: 80%;
            margin: 20px auto;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            padding: 20px;
            cursor: pointer;
        }

        .card img {
            width: 100%;
            max-width: 300px;
            height: auto;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 24px;
            margin: 10px 0;
        }

        .card p {
            margin: 5px 0;
        }

        .card .love-button {
            padding: 8px 15px;
            border: none;
            background-color: #ccc;
            color: black;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .card .love-button:hover {
            background-color: #e74c3c;
            color: white;
        }

        .card .love-count {
            font-size: 16px;
            margin-top: 5px;
            color: #888;
        }

        .card .rating {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card .rating span {
            font-size: 20px;
            color: #ffc107;
            cursor: pointer;
        }

        .card .rating span.active {
            color: #ff9800;
        }

        .card .comment-section {
            margin-top: 20px;
            text-align: left;
        }

        .card .comment-section form {
            display: flex;
            flex-direction: column;
        }

        .card .comment-section form textarea {
            resize: none;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .card .comment-section form button {
            align-self: flex-end;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            background-color: #5cb85c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detail Buku</h1>
        <div class="card">
            <img src="<?php echo htmlspecialchars($book['gambar']); ?>" alt="Sampul Buku">
            <h3><?php echo htmlspecialchars($book['judul']); ?></h3>
            <p><?php echo htmlspecialchars($book['penulis']); ?></p>
            <p><?php echo htmlspecialchars($book['deskripsi']); ?></p>
            <div class="love-section">
                <button class="love-button" id="loveButton" onclick="toggleLove()">&#x2665; Love</button>
                <div class="love-count" id="loveCount">0 Love</div>
            </div>
            <div class="rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="<?php echo $i <= $book['rating'] ? 'active' : ''; ?>">★</span>
                <?php endfor; ?>
            </div>
            <div class="comment-section">
                <form action="submit_comment.php" method="post">
                    <textarea name="comment" rows="3" placeholder="Tambahkan komentar..."></textarea>
                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['id']); ?>">
                    <button type="submit">Kirim</button>
                </form>
                <p>Komentar: <?php echo htmlspecialchars($book['komentar']); ?></p>
            </div>
        </div>
        <a href="homepage.php">Kembali ke Daftar Buku</a>
        <a href="peminjaman.php?id=<?php echo htmlspecialchars($book['id']); ?>&judul=<?php echo htmlspecialchars($book['judul']); ?>&penulis=<?php echo htmlspecialchars($book['penulis']); ?>&deskripsi=<?php echo htmlspecialchars($book['deskripsi']); ?>">Pinjam Buku</a>
    </div>

    <script>
        let loveCount = 0;

        function toggleLove() {
            let loveButton = document.getElementById('loveButton');
            if (loveButton.style.backgroundColor === 'rgb(231, 76, 60)') {
                loveButton.style.backgroundColor = '#ccc';
                loveButton.style.color = 'black';
                loveCount--;
            } else {
                loveButton.style.backgroundColor = '#e74c3c';
                loveButton.style.color = 'white';
                loveCount++;
            }
            document.getElementById('loveCount').innerText = loveCount + (loveCount === 1 ? ' Love' : ' Loves');
        }
    </script>
</body>
</html>
