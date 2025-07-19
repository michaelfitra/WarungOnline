<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
include '../../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    $stmt = $conn->prepare("UPDATE produk SET nama=?, kategori=?, stok=?, harga=? WHERE id=?");
    $stmt->bind_param("ssidi", $nama, $kategori, $stok, $harga, $id);

    if ($stmt->execute()) {
        header("Location: stok.php?status=update");
    } else {
        echo "Gagal memperbarui produk: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
