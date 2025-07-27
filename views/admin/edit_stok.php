<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
include '../../includes/db.php';
require_once '../../includes/auth_check.php';
require_admin(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    try {
        $stmt = $pdo->prepare("UPDATE produk SET nama=?, kategori=?, stok=?, harga=? WHERE id=?");
        $stmt->execute([$nama, $kategori, $stok, $harga, $id]);
        
        header("Location: stok.php?status=update");
        exit;
    } catch (PDOException $e) {
        echo "Gagal memperbarui produk: " . $e->getMessage();
    }
}
?>