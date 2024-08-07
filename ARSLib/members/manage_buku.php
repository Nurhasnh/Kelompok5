<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id_buku = $_POST['id_buku'];
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $deskripsi = $_POST['deskripsi'];
    $gambar_path = '';
    $pdf_path = '';

    // Handle file uploads
    if (isset($_FILES['gambar']) && $_FILES['gambar']['size'] > 0) {
        $gambar = $_FILES['gambar'];
        $target_dir = "uploads/";
        $gambar_path = $target_dir . basename($gambar['name']);
        move_uploaded_file($gambar['tmp_name'], $gambar_path);
    }

    if (isset($_FILES['pdf']) && $_FILES['pdf']['size'] > 0) {
        $pdf = $_FILES['pdf'];
        $target_dir = "uploads/";
        $pdf_path = $target_dir . basename($pdf['name']);
        move_uploaded_file($pdf['tmp_name'], $pdf_path);
    }

    // Load current data
    $file = 'books.json';
    $current_data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    if ($action == 'upload') {
        // Save book details to a file
        $book_details = [
            'id' => $id_buku,
            'judul' => $judul,
            'penulis' => $penulis,
            'deskripsi' => $deskripsi,
            'gambar' => $gambar_path,
            'pdf' => $pdf_path
        ];
        $current_data[] = $book_details;
    } elseif ($action == 'update') {
        // Update book details
        foreach ($current_data as &$book) {
            if ($book['id'] == $id_buku) {
                $book['judul'] = $judul;
                $book['penulis'] = $penulis;
                $book['deskripsi'] = $deskripsi;
                if ($gambar_path) $book['gambar'] = $gambar_path;
                if ($pdf_path) $book['pdf'] = $pdf_path;
                break;
            }
        }
    } elseif ($action == 'delete') {
        // Delete book
        $current_data = array_filter($current_data, function ($book) use ($id_buku) {
            return $book['id'] !== $id_buku;
        });
    }

    file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT));

    // Redirect to the library page
    header('Location: Perpustakaan.php');
    exit;
}
?>
