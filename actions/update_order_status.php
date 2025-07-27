<?php
// actions/update_order_status.php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Periksa apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

// Periksa method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

// Ambil data JSON dari request body
$input = json_decode(file_get_contents('php://input'), true);

// Validasi input
if (!isset($input['order_id']) || !isset($input['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$order_id = (int) $input['order_id'];
$new_status = trim($input['status']);

// Validasi status yang diizinkan
$allowed_statuses = ['diproses', 'siap_dijemput', 'selesai'];
if (!in_array($new_status, $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

try {
    // Periksa apakah pesanan ada
    $check_query = "SELECT id FROM orders WHERE id = ?";
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute([$order_id]);
    
    if (!$check_stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }
    
    // Update status pesanan
    $update_query = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $pdo->prepare($update_query);
    $result = $update_stmt->execute([$new_status, $order_id]);
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Status berhasil diperbarui',
            'order_id' => $order_id,
            'new_status' => $new_status
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status']);
    }
    
} catch (PDOException $e) {
    error_log("Database error in update_order_status.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database']);
}
?>