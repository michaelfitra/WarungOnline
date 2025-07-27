<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
include '../../includes/db.php';
require_once '../../includes/auth_check.php';
require_admin(); 

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Ambil nama file gambar sebelum hapus
        $query = $pdo->prepare("SELECT gambar FROM produk WHERE id = ?");
        $query->execute([$id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $gambar = $data['gambar'];

            // Hapus file gambar jika ada
            if ($gambar) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/assets/images/' . $gambar;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Hapus dari database
            $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
            $stmt->execute([$id]);
            
            header("Location: stok.php?status=hapus");
            exit;
        } else {
            echo "Produk tidak ditemukan.";
        }
    } catch (PDOException $e) {
        echo "Gagal menghapus produk: " . $e->getMessage();
    }
} else {
    echo "ID tidak ditemukan.";
}
?>