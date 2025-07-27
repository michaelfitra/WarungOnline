<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
include '../../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];

    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/assets/images/';
        $fileName = time() . '_' . basename($_FILES['gambar']['name']);
        $targetPath = $uploadDir . $fileName;

        // Validasi ekstensi gambar (opsional tapi disarankan)
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExt)) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
                $gambar = $fileName;
            } else {
                die("Gagal memindahkan file gambar.");
            }
        } else {
            die("Ekstensi file tidak didukung.");
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO produk (nama, deskripsi, harga, gambar, stok, kategori) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $deskripsi, $harga, $gambar, $stok, $kategori]);
        
        header("Location: stok.php?status=berhasil");
        exit;
    } catch (PDOException $e) {
        echo "Gagal menambahkan produk: " . $e->getMessage();
    }
}
?>