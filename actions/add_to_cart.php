<?php
require_once '../includes/db.php'; 

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];

    try {
        $stmt = $pdo->prepare("SELECT id, nama, harga, gambar FROM produk WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['qty']++; 
            } else {
                $_SESSION['cart'][$productId] = [
                    'id' => $product['id'],
                    'name' => $product['nama'],    
                    'price' => $product['harga'],  
                    'gambar' => $product['gambar'],
                    'qty' => 1                     
                ];
            }
            echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan.']);
        }
    } catch (PDOException $e) {
        error_log("Error adding to cart: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan produk.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
}
?>