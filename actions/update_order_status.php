<?php
// File: /actions/update_order_status.php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_admin();

// Set header untuk JSON response
header('Content-Type: application/json');

try {
    // Ambil data JSON dari request body
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['order_id']) || !isset($input['status'])) {
        throw new Exception('Data tidak lengkap');
    }
    
    $order_id = (int)$input['order_id'];
    $new_status = $input['status'];
    
    // Validasi status yang diizinkan
    $allowed_statuses = ['diproses', 'siap_dijemput', 'selesai'];
    if (!in_array($new_status, $allowed_statuses)) {
        throw new Exception('Status tidak valid');
    }
    
    // Mulai transaction
    $pdo->beginTransaction();
    
    // Ambil status lama pesanan
    $query = "SELECT status FROM orders WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception('Pesanan tidak ditemukan');
    }
    
    $old_status = $order['status'];
    
    // Jika status tidak berubah, tidak perlu melakukan apa-apa
    if ($old_status === $new_status) {
        echo json_encode(['success' => true, 'message' => 'Status tidak berubah']);
        exit;
    }
    
    // Ambil semua item dalam pesanan
    $query = "SELECT oi.product_id, oi.quantity 
              FROM order_items oi 
              WHERE oi.order_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Logika pengaturan stok berdasarkan perubahan status
    if ($old_status === 'diproses' && ($new_status === 'siap_dijemput' || $new_status === 'selesai')) {
        // Status berubah dari diproses ke siap_dijemput/selesai: kurangi stok
        foreach ($order_items as $item) {
            $update_query = "UPDATE produk SET stok = stok - ? WHERE id = ? AND stok >= ?";
            $update_stmt = $pdo->prepare($update_query);
            $result = $update_stmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
            
            if (!$result || $update_stmt->rowCount() === 0) {
                // Cek apakah produk masih ada dan stoknya cukup
                $check_query = "SELECT nama, stok FROM produk WHERE id = ?";
                $check_stmt = $pdo->prepare($check_query);
                $check_stmt->execute([$item['product_id']]);
                $product = $check_stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$product) {
                    throw new Exception('Produk tidak ditemukan');
                } else {
                    throw new Exception("Stok tidak mencukupi untuk produk: {$product['nama']} (Stok tersedia: {$product['stok']}, Dibutuhkan: {$item['quantity']})");
                }
            }
        }
    } 
    elseif (($old_status === 'siap_dijemput' || $old_status === 'selesai') && $new_status === 'diproses') {
        // Status berubah dari siap_dijemput/selesai ke diproses: kembalikan stok
        foreach ($order_items as $item) {
            $update_query = "UPDATE produk SET stok = stok + ? WHERE id = ?";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute([$item['quantity'], $item['product_id']]);
        }
    }
    elseif ($old_status === 'siap_dijemput' && $new_status === 'selesai') {
        // Status berubah dari siap_dijemput ke selesai: tidak ada perubahan stok
        // (stok sudah dikurangi saat status diproses -> siap_dijemput)
    }
    elseif ($old_status === 'selesai' && $new_status === 'siap_dijemput') {
        // Status berubah dari selesai ke siap_dijemput: tidak ada perubahan stok
        // (stok tetap dalam keadaan sudah dikurangi)
    }
    
    // Update status pesanan
    $update_order_query = "UPDATE orders SET status = ? WHERE id = ?";
    $update_order_stmt = $pdo->prepare($update_order_query);
    $update_order_stmt->execute([$new_status, $order_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Response sukses
    echo json_encode([
        'success' => true, 
        'message' => 'Status pesanan berhasil diperbarui',
        'old_status' => $old_status,
        'new_status' => $new_status
    ]);
    
} catch (Exception $e) {
    // Rollback jika terjadi error
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    // Response error
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>