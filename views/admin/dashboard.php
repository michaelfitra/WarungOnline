<?php
$activePage = 'dashboard';
// Sertakan koneksi database
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header('Location: ' . base_url('views/auth/login.php'));
    exit;
}

try {
    // Query untuk statistik utama
    $stats_query = "SELECT 
        (SELECT COUNT(*) FROM orders WHERE status = 'selesai') as total_penjualan,
        (SELECT COUNT(*) FROM orders WHERE status = 'diproses') as pesanan_pending,
        (SELECT COUNT(*) FROM produk) as total_produk,
        (SELECT COUNT(*) FROM users WHERE role = 'user') as total_pelanggan,
        (SELECT SUM(total_amount) FROM orders WHERE status = 'selesai') as total_pendapatan,
        (SELECT SUM(total_amount) FROM orders WHERE status = 'selesai' AND DATE(order_date) = CURDATE()) as pendapatan_hari_ini,
        (SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()) as penjualan_hari_ini,
        (SELECT COUNT(*) FROM produk WHERE stok <= 10) as produk_stok_rendah";

    $stats_stmt = $pdo->prepare($stats_query);
    $stats_stmt->execute();
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    // Query untuk penjualan 7 hari terakhir (untuk chart)
    $chart_query = "SELECT 
        DATE(order_date) as tanggal,
        COUNT(*) as jumlah_pesanan,
        COALESCE(SUM(total_amount), 0) as total_pendapatan
        FROM orders 
        WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
        AND status = 'selesai'
        GROUP BY DATE(order_date)
        ORDER BY tanggal ASC";

    $chart_stmt = $pdo->prepare($chart_query);
    $chart_stmt->execute();
    $chart_data = $chart_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query untuk pesanan terbaru
    $recent_orders_query = "SELECT 
        o.id, o.order_date, o.total_amount, o.status,
        u.email as customer_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.order_date DESC
        LIMIT 5";

    $recent_orders_stmt = $pdo->prepare($recent_orders_query);
    $recent_orders_stmt->execute();
    $recent_orders = $recent_orders_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query untuk produk terlaris bulan ini
    $bestseller_query = "SELECT 
        p.nama,
        SUM(oi.quantity) as total_terjual,
        SUM(oi.quantity * oi.price) as total_pendapatan
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN produk p ON oi.product_id = p.id
        WHERE MONTH(o.order_date) = MONTH(CURDATE()) 
        AND YEAR(o.order_date) = YEAR(CURDATE())
        AND o.status = 'selesai'
        GROUP BY p.id
        ORDER BY total_terjual DESC
        LIMIT 5";

    $bestseller_stmt = $pdo->prepare($bestseller_query);
    $bestseller_stmt->execute();
    $bestsellers = $bestseller_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query untuk produk stok rendah
    $low_stock_query = "SELECT nama, stok, kategori 
        FROM produk 
        WHERE stok <= 10 
        ORDER BY stok ASC 
        LIMIT 5";

    $low_stock_stmt = $pdo->prepare($low_stock_query);
    $low_stock_stmt->execute();
    $low_stock = $low_stock_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error in dashboard.php: " . $e->getMessage());
    // Set default values jika error
    $stats = [
        'total_penjualan' => 0,
        'pesanan_pending' => 0,
        'total_produk' => 0,
        'total_pelanggan' => 0,
        'total_pendapatan' => 0,
        'pendapatan_hari_ini' => 0,
        'penjualan_hari_ini' => 0,
        'produk_stok_rendah' => 0
    ];
    $chart_data = [];
    $recent_orders = [];
    $bestsellers = [];
    $low_stock = [];
}

// Prepare chart data untuk JavaScript
$chart_labels = [];
$chart_values = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('d/m', strtotime($date));

    $found = false;
    foreach ($chart_data as $data) {
        if ($data['tanggal'] == $date) {
            $chart_values[] = (int) $data['total_pendapatan'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $chart_values[] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Admin - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .dashboard-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .dashboard-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .dashboard-card.danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .dashboard-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }

        .dashboard-card p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .dashboard-card .icon {
            font-size: 3rem;
            opacity: 0.3;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .widget-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            border: none;
        }

        .widget-card h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 10px;
        }

        .order-item {
            border-left: 4px solid #007bff;
            padding: 10px 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 0 10px 10px 0;
        }

        .order-item.pending {
            border-left-color: #ffc107;
        }

        .order-item.success {
            border-left-color: #28a745;
        }

        .bestseller-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .bestseller-item:last-child {
            border-bottom: none;
        }

        .stock-alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .stock-alert.critical {
            background: #f8d7da;
            border-color: #f5c6cb;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include '../../includes/header_admin.php' ?>
    <div class="d-flex">
        <div class="col-md-2 d-none d-md-block" style="background-color: #19345e; min-height: 100vh;">
            <?php include '../../includes/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-10">
            <!-- Page Content -->
            <div class="main-content">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <h4><i class="fa fa-dashboard"></i> Selamat Datang di Dashboard Admin</h4>
                    <p>Kelola toko Anda dengan mudah dan pantau performa bisnis secara real-time</p>
                    <small><?php echo date('l, d F Y'); ?></small>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="dashboard-card success position-relative">
                            <h3><?php echo number_format($stats['total_penjualan']); ?></h3>
                            <p>Total Penjualan</p>
                            <i class="fa fa-shopping-cart icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card warning position-relative">
                            <h3><?php echo number_format($stats['pesanan_pending']); ?></h3>
                            <p>Pesanan Pending</p>
                            <i class="fa fa-clock icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card info position-relative">
                            <h3><?php echo number_format($stats['total_produk']); ?></h3>
                            <p>Total Produk</p>
                            <i class="fa fa-box icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card position-relative">
                            <h3><?php echo number_format($stats['total_pelanggan']); ?></h3>
                            <p>Total Pelanggan</p>
                            <i class="fa fa-users icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Revenue Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="dashboard-card success position-relative">
                            <h3>Rp <?php echo number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.'); ?></h3>
                            <p>Total Pendapatan</p>
                            <i class="fa fa-money-bill icon"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card info position-relative">
                            <h3>Rp <?php echo number_format($stats['pendapatan_hari_ini'] ?? 0, 0, ',', '.'); ?></h3>
                            <p>Pendapatan Hari Ini</p>
                            <i class="fa fa-calendar-day icon"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card danger position-relative">
                            <h3><?php echo number_format($stats['produk_stok_rendah']); ?></h3>
                            <p>Produk Stok Rendah</p>
                            <i class="fa fa-exclamation-triangle icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Charts and Tables Row -->
                <div class="row">
                    <!-- Chart -->
                    <div class="col-md-8">
                        <div class="widget-card">
                            <h6><i class="fa fa-chart-line"></i> Penjualan 7 Hari Terakhir</h6>
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Produk Terlaris -->
                    <div class="col-md-4">
                        <div class="widget-card">
                            <h6><i class="fa fa-star"></i> Produk Terlaris Bulan Ini</h6>
                            <?php if (empty($bestsellers)): ?>
                                <p class="text-muted">Belum ada data penjualan bulan ini</p>
                            <?php else: ?>
                                <?php foreach ($bestsellers as $index => $bestseller): ?>
                                    <div class="bestseller-item">
                                        <div>
                                            <strong><?php echo $index + 1; ?>.
                                                <?php echo htmlspecialchars($bestseller['nama']); ?></strong>
                                            <br><small class="text-muted"><?php echo $bestseller['total_terjual']; ?>
                                                terjual</small>
                                        </div>
                                        <span class="badge bg-success">Rp
                                            <?php echo number_format($bestseller['total_pendapatan'], 0, ',', '.'); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-md-8">
                        <div class="widget-card">
                            <h6><i class="fa fa-list"></i> Pesanan Terbaru</h6>
                            <?php if (empty($recent_orders)): ?>
                                <p class="text-muted">Belum ada pesanan</p>
                            <?php else: ?>
                                <?php foreach ($recent_orders as $order): ?>
                                    <div
                                        class="order-item <?php echo ($order['status'] == 'selesai') ? 'success' : 'pending'; ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Order
                                                    #<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></strong>
                                                <br><small><?php echo htmlspecialchars($order['customer_name']); ?></small>
                                                <br><small
                                                    class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <strong>Rp
                                                    <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong>
                                                <br>
                                                <span
                                                    class="badge <?php echo ($order['status'] == 'selesai') ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <div class="text-center mt-3">
                                <a href="order.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-eye"></i> Lihat Semua Pesanan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="col-md-4">
                        <div class="widget-card">
                            <h6><i class="fa fa-exclamation-triangle text-warning"></i> Peringatan Stok Rendah</h6>
                            <?php if (empty($low_stock)): ?>
                                <div class="alert alert-success">
                                    <i class="fa fa-check-circle"></i> Semua produk stok aman
                                </div>
                            <?php else: ?>
                                <?php foreach ($low_stock as $product): ?>
                                    <div class="stock-alert <?php echo ($product['stok'] <= 5) ? 'critical' : ''; ?>">
                                        <strong><?php echo htmlspecialchars($product['nama']); ?></strong>
                                        <br><small class="text-muted"><?php echo $product['kategori']; ?></small>
                                        <br><span
                                            class="badge <?php echo ($product['stok'] <= 5) ? 'bg-danger' : 'bg-warning'; ?>">
                                            Stok: <?php echo $product['stok']; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                                <div class="text-center mt-3">
                                    <a href="stok.php" class="btn btn-outline-warning btn-sm">
                                        <i class="fa fa-boxes"></i> Kelola Stok
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="widget-card">
                            <h6><i class="fa fa-bolt"></i> Aksi Cepat</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="tambah_stok.php" class="btn btn-primary w-100 mb-2">
                                        <i class="fa fa-plus"></i> Tambah Produk
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="order.php" class="btn btn-success w-100 mb-2">
                                        <i class="fa fa-list"></i> Lihat Pesanan
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="laporan.php" class="btn btn-info w-100 mb-2">
                                        <i class="fa fa-chart-bar"></i> Lihat Laporan
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="stok.php" class="btn btn-warning w-100 mb-2">
                                        <i class="fa fa-boxes"></i> Kelola Stok
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('d-none');
        });

        // Chart.js Configuration
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?php echo json_encode($chart_values); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });

        // Auto refresh setiap 5 menit untuk data real-time
        setInterval(function () {
            location.reload();
        }, 300000); // 5 menit
    </script>
</body>

</html>