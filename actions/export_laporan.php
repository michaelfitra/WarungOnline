<?php
// actions/export_laporan.php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Periksa apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Akses ditolak');
}

// Ambil parameter filter yang sama dengan laporan.php
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Base query untuk laporan penjualan (sama dengan laporan.php)
$where_conditions = [];
$params = [];

// Filter berdasarkan bulan dan tahun
if (!empty($bulan) && !empty($tahun)) {
    $where_conditions[] = "MONTH(o.order_date) = ? AND YEAR(o.order_date) = ?";
    $params[] = $bulan;
    $params[] = $tahun;
}

// Filter berdasarkan tanggal spesifik
if (!empty($tanggal)) {
    $where_conditions[] = "DATE(o.order_date) = ?";
    $params[] = $tanggal;
}

// Jika tidak ada filter, tampilkan bulan ini
if (empty($where_conditions)) {
    $where_conditions[] = "MONTH(o.order_date) = MONTH(CURDATE()) AND YEAR(o.order_date) = YEAR(CURDATE())";
}

$where_clause = !empty($where_conditions) ? "AND " . implode(" AND ", $where_conditions) : "";

try {
    // Query untuk mendapatkan data laporan penjualan
    $query = "SELECT 
                p.id as product_id,
                p.nama as nama_produk,
                p.kategori,
                p.harga as harga_jual,
                SUM(oi.quantity) as total_terjual,
                SUM(oi.quantity * oi.price) as total_pendapatan,
                DATE(o.order_date) as tanggal_penjualan,
                COUNT(DISTINCT o.id) as jumlah_transaksi
              FROM order_items oi
              JOIN orders o ON oi.order_id = o.id
              JOIN produk p ON oi.product_id = p.id
              WHERE o.status = 'selesai' $where_clause
              GROUP BY p.id, DATE(o.order_date)
              ORDER BY o.order_date DESC, p.nama ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $laporan_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Query untuk mendapatkan ringkasan total
    $summary_query = "SELECT 
                        COUNT(DISTINCT o.id) as total_transaksi,
                        SUM(oi.quantity) as total_item_terjual,
                        SUM(oi.quantity * oi.price) as total_pendapatan
                      FROM order_items oi
                      JOIN orders o ON oi.order_id = o.id
                      WHERE o.status = 'selesai' $where_clause";
    
    $summary_stmt = $pdo->prepare($summary_query);
    $summary_stmt->execute($params);
    $summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Database error in export_laporan.php: " . $e->getMessage());
    die('Terjadi kesalahan saat mengambil data');
}

// Set header untuk download Excel
$filename = "Laporan_Penjualan_" . date('Y-m-d_H-i-s') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Tentukan periode untuk judul
$periode = '';
if (!empty($tanggal)) {
    $periode = "Tanggal: " . date('d/m/Y', strtotime($tanggal));
} elseif (!empty($bulan) && !empty($tahun)) {
    $nama_bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $periode = "Periode: " . $nama_bulan[$bulan] . " " . $tahun;
} else {
    $periode = "Periode: " . date('F Y');
}

// Output HTML yang akan diinterpretasi sebagai Excel
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin-bottom: 20px; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KEDAI BAROKAH</h2>
        <h3>LAPORAN PENJUALAN</h3>
        <p>' . $periode . '</p>
        <p>Dicetak pada: ' . date('d/m/Y H:i:s') . '</p>
    </div>
    
    <div class="summary">
        <h4>RINGKASAN:</h4>
        <table style="width: 50%;">
            <tr>
                <td class="text-left"><strong>Total Transaksi</strong></td>
                <td class="text-right">' . number_format($summary['total_transaksi'] ?? 0) . '</td>
            </tr>
            <tr>
                <td class="text-left"><strong>Total Item Terjual</strong></td>
                <td class="text-right">' . number_format($summary['total_item_terjual'] ?? 0) . '</td>
            </tr>
            <tr>
                <td class="text-left"><strong>Total Pendapatan</strong></td>
                <td class="text-right">Rp ' . number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') . '</td>
            </tr>
        </table>
    </div>
    
    <h4>DETAIL PENJUALAN:</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Produk</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Qty Terjual</th>
                <th>Harga Satuan</th>
                <th>Total Pendapatan</th>
                <th>Jumlah Transaksi</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>';

if (empty($laporan_data)) {
    echo '<tr><td colspan="9">Tidak ada data penjualan untuk periode yang dipilih</td></tr>';
} else {
    $no = 1;
    $total_qty = 0;
    $total_pendapatan = 0;
    
    foreach ($laporan_data as $item) {
        $total_qty += $item['total_terjual'];
        $total_pendapatan += $item['total_pendapatan'];
        
        echo '<tr>
                <td>' . $no++ . '</td>
                <td>PRD' . str_pad($item['product_id'], 3, '0', STR_PAD_LEFT) . '</td>
                <td class="text-left">' . htmlspecialchars($item['nama_produk']) . '</td>
                <td>' . htmlspecialchars($item['kategori']) . '</td>
                <td>' . number_format($item['total_terjual']) . '</td>
                <td>Rp ' . number_format($item['harga_jual'], 0, ',', '.') . '</td>
                <td>Rp ' . number_format($item['total_pendapatan'], 0, ',', '.') . '</td>
                <td>' . $item['jumlah_transaksi'] . '</td>
                <td>' . date('d/m/Y', strtotime($item['tanggal_penjualan'])) . '</td>
              </tr>';
    }
    
    // Total row
    echo '<tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL:</td>
                <td>' . number_format($total_qty) . '</td>
                <td>-</td>
                <td>Rp ' . number_format($total_pendapatan, 0, ',', '.') . '</td>
                <td colspan="2">-</td>
              </tr>';
}

echo '        </tbody>
    </table>
    
    <div style="margin-top: 30px;">
        <p><strong>Keterangan:</strong></p>
        <ul>
            <li>Laporan ini menampilkan data penjualan dari pesanan yang sudah selesai</li>
            <li>Data dikelompokkan berdasarkan produk dan tanggal penjualan</li>
            <li>Total pendapatan dihitung dari quantity × harga saat transaksi</li>
        </ul>
    </div>
    
    <div style="margin-top: 50px; text-align: right;">
        <p>Mengetahui,</p>
        <br><br><br>
        <p>_____________________</p>
        <p>Admin Kedai Barokah</p>
    </div>
</body>
</html>';

exit;
?>