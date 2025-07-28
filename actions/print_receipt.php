<?php
// actions/print_receipt.php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Periksa parameter order_id
if (!isset($_GET['order_id'])) {
    die('Order ID tidak ditemukan');
}

$order_id = (int) $_GET['order_id'];

try {
    // Query untuk mendapatkan data pesanan
    $order_query = "SELECT o.id, o.order_date, o.total_amount, o.status, u.email as nama_user 
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.id 
                    WHERE o.id = ?";
    $order_stmt = $pdo->prepare($order_query);
    $order_stmt->execute([$order_id]);
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        die('Pesanan tidak ditemukan');
    }
    
    // Query untuk mendapatkan detail item pesanan
    $items_query = "SELECT oi.quantity, oi.price, p.nama as product_name 
                    FROM order_items oi 
                    JOIN produk p ON oi.product_id = p.id 
                    WHERE oi.order_id = ?";
    $items_stmt = $pdo->prepare($items_query);
    $items_stmt->execute([$order_id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die('Terjadi kesalahan database: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .receipt-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .receipt-header p {
            margin: 2px 0;
            font-size: 12px;
        }
        
        .receipt-info {
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        .receipt-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .receipt-items {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .item {
            margin-bottom: 8px;
            font-size: 12px;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
        }
        
        .receipt-total {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin-bottom: 15px;
        }
        
        .receipt-footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 11px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-diproses {
            background: #ffc107;
            color: #000;
        }
        
        .status-siap-dijemput {
            background: #17a2b8;
            color: #fff;
        }
        
        .status-selesai {
            background: #28a745;
            color: #fff;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
        
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fa fa-print"></i> Cetak
    </button>
    
    <div class="receipt">
        <div class="receipt-header">
            <h2>KEDAI BAROKAH</h2>
            <p>Jl. Contoh No. 123, Kota</p>
            <p>Telp: (021) 1234-5678</p>
        </div>
        
        <div class="receipt-info">
            <div>
                <span>No. Pesanan:</span>
                <span>#<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div>
                <span>Tanggal:</span>
                <span><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></span>
            </div>
            <div>
                <span>Pelanggan:</span>
                <span><?php echo htmlspecialchars($order['nama_user']); ?></span>
            </div>
            <div>
                <span>Status:</span>
                <span>
                    <span class="status-badge status-<?php echo str_replace(' ', '-', $order['status']); ?>">
                        <?php 
                        switch($order['status']) {
                            case 'diproses':
                                echo 'Diproses';
                                break;
                            case 'siap_dijemput':
                                echo 'Siap Dijemput';
                                break;
                            case 'selesai':
                                echo 'Selesai';
                                break;
                            default:
                                echo ucfirst($order['status']);
                        }
                        ?>
                    </span>
                </span>
            </div>
        </div>
        
        <div class="receipt-items">
            <?php foreach ($items as $item): ?>
                <div class="item">
                    <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                    <div class="item-details">
                        <span><?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></span>
                        <span>Rp <?php echo number_format($item['quantity'] * $item['price'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="receipt-total">
            <div style="display: flex; justify-content: space-between;">
                <span>TOTAL:</span>
                <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
            </div>
        </div>
        
        <div class="receipt-footer">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
            <p>Dicetak: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
    
    <script>
        // Auto print saat halaman dimuat (opsional)
        // window.onload = function() {
        //     window.print();
        // };
        
        // Tutup window setelah print selesai
        window.onafterprint = function() {
            // window.close();
        };
    </script>
</body>
</html>