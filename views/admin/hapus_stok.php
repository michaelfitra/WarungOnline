<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
include '../../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil nama file gambar sebelum hapus
    $query = $conn->prepare("SELECT gambar FROM produk WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $gambar = $data['gambar'];

        // Hapus file gambar jika ada
        if ($gambar) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/assets/images/' . $gambar;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus dari database
        $stmt = $conn->prepare("DELETE FROM produk WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: stok.php?status=hapus");
            exit;
        } else {
            echo "Gagal menghapus produk: " . $conn->error;
        }
    } else {
        echo "Produk tidak ditemukan.";
    }

    $query->close();
    $stmt->close();
    $conn->close();
} else {
    echo "ID tidak ditemukan.";
}
?>
