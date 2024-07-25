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

INSERT INTO 'admins' ('id','username','email','password') VALUES
(1,'nurhasnh','nurhasnh20@gmail.com','nunuy20');