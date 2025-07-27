<?php
// File: /actions/get_order_details.php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Pastikan hanya admin yang bisa mengakses
require_admin();

// Set header untuk JSON response
header('Content-Type: application/json');

try {
    if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
        throw new Exception('Order ID tidak ditemukan');
    }
    
    $order_id = (int)$_GET['order_id'];
    
    // Query untuk mendapatkan detail pesanan
    $query = "SELECT 
                oi.id,
                oi.quantity,
                oi.price,
                p.nama,
                p.stok as current_stock
              FROM order_items oi
              LEFT JOIN produk p ON oi.product_id = p.id
              WHERE oi.order_id = ?
              ORDER BY oi.id";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        throw new Exception('Detail pesanan tidak ditemukan');
    }
    
    // Hitung total
    $total = 0;
    foreach ($items as $item) {
        $total += $item['quantity'] * $item['price'];
    }
    
    // Query untuk mendapatkan informasi pesanan
    $order_query = "SELECT 
                      o.order_date,
                      o.status,
                      u.email as customer_email
                    FROM orders o
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.id = ?";
    
    $order_stmt = $pdo->prepare($order_query);
    $order_stmt->execute([$order_id]);
    $order_info = $order_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Response sukses
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'order_info' => $order_info
    ]);
    
} catch (Exception $e) {
    // Response error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>