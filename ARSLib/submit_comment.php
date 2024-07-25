<?php
// Mendapatkan data komentar dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = htmlspecialchars($_POST['comment']);
    $book_id = htmlspecialchars($_POST['book_id']);
    
    // Membaca data buku dari file JSON
    $file = 'books.json';
    $books = [];
    if (file_exists($file)) {
        $books = json_decode(file_get_contents($file), true);
    }

    // Menambahkan komentar ke buku yang sesuai
    foreach ($books as &$book) {
        if ($book['id'] == $book_id) {
            $book['komentar'] .= "\n" . $comment;
            break;
        }
    }

    // Menyimpan kembali data buku ke file JSON
    file_put_contents($file, json_encode($books, JSON_PRETTY_PRINT));
    
    // Redirect kembali ke halaman utama
    header('Location: homepage.php');
    exit;
}
?>
