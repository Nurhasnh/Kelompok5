--
Database : 'ARSLib'
--
--------------------

--------- Table Structure for table'admin'---------

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_buku VARCHAR(255) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    pdf VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    tanggal_peminjaman DATE NOT NULL,
    tanggal_pengembalian DATE NOT NULL,
    FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE TABLE borrowed_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_buku INT NOT NULL,
    member_id INT NOT NULL,
    tanggal_peminjaman DATE NOT NULL,
    tanggal_pengembalian DATE NOT NULL,
    FOREIGN KEY (id_buku) REFERENCES books(id),
    FOREIGN KEY (member_id) REFERENCES members(id)
);

