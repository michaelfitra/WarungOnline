<?php
include '../includes/db.php';
require_once '../includes/auth_check.php';

// Ambil data pesanan user yang sedang login
try {
    $user_id = $_SESSION['user_id'];

    // Query untuk mengambil pesanan user dengan detail items
    $sql = "SELECT o.*, u.email,
                   COUNT(oi.id) as total_items,
                   GROUP_CONCAT(CONCAT(p.nama, ' (', oi.quantity, 'x)') SEPARATOR ', ') as items_detail
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN produk p ON oi.product_id = p.id
            WHERE o.user_id = ?
            GROUP BY o.id 
            ORDER BY o.order_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Statistik pesanan user
    $stats_sql = "SELECT 
        COUNT(*) as total_pesanan,
        COUNT(CASE WHEN status = 'diproses' THEN 1 END) as sedang_diproses,
        COUNT(CASE WHEN status = 'siap_dijemput' THEN 1 END) as siap_dijemput,
        COUNT(CASE WHEN status = 'selesai' THEN 1 END) as selesai,
        COALESCE(SUM(total_amount), 0) as total_belanja
        FROM orders WHERE user_id = ?";
    $stats_stmt = $pdo->prepare($stats_sql);
    $stats_stmt->execute([$user_id]);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    // Pesanan terbaru yang belum selesai
    $recent_sql = "SELECT id, order_date, status, total_amount FROM orders 
                   WHERE user_id = ? AND status != 'selesai' 
                   ORDER BY order_date DESC LIMIT 3";
    $recent_stmt = $pdo->prepare($recent_sql);
    $recent_stmt->execute([$user_id]);
    $recent_orders = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}

// Fungsi untuk format status
function getStatusBadge($status)
{
    switch ($status) {
        case 'diproses':
            return '<span class="status-badge status-processing"><i class="fas fa-clock me-1"></i>Diproses</span>';
        case 'siap_dijemput':
            return '<span class="status-badge status-ready"><i class="fas fa-check-circle me-1"></i>Siap Dijemput</span>';
        case 'selesai':
            return '<span class="status-badge status-completed"><i class="fas fa-check-double me-1"></i>Selesai</span>';
        default:
            return '<span class="status-badge status-unknown">Unknown</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>History Pesanan - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->
    <link rel="stylesheet" href="assets/css/index.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, #ff9a56 0%, #ff6b6b 100%);
        }

        .stats-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stats-card.info {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stats-card.primary {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }

        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0;
        }

        .stats-card p {
            margin-bottom: 0;
            opacity: 0.9;
        }

        .stats-card small {
            opacity: 0.8;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .search-filter-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            border: none;
            padding: 15px 10px;
        }

        .table td {
            padding: 15px 10px;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .status-processing {
            background: #fff3cd;
            color: #856404;
        }

        .status-ready {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-unknown {
            background: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            margin: 0 2px;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .filter-btn {
            border-radius: 20px;
            margin: 0 5px;
            padding: 5px 15px;
            border: 2px solid #dee2e6;
            background: white;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .alert-orders {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
        }

        .order-id-badge {
            background: #e9ecef;
            color: #495057;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .btn-whatsapp {
            background: #25D366;
            color: white;
        }

        .btn-whatsapp:hover {
            background: #1DA851;
            color: white;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container-fluid col-lg-10 col-xl-8 mx-auto">
        <div class="row">
            <!-- Main Content -->
            <div class="col-12">
                <div class="main-content p-4">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1"><i class="fas fa-history me-2"></i>History Pesanan</h4>
                                <p class="mb-0 opacity-75">Lihat riwayat dan status pesanan Anda</p>
                            </div>
                            <div class="text-end">
                                <small>Update terakhir: <?php echo date('d M Y, H:i'); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Alert untuk pesanan aktif -->
                    <?php if (!empty($recent_orders)): ?>
                        <div class="alert-orders">
                            <h6><i class="fas fa-shopping-bag me-2"></i>Pesanan Aktif</h6>
                            <p class="mb-2">Anda memiliki pesanan yang sedang dalam proses:</p>
                            <div class="row">
                                <?php foreach ($recent_orders as $order): ?>
                                    <div class="col-md-4 mb-1">
                                        <small>• Order #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?> -
                                            <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?> (Rp
                                            <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>)</small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3><?php echo $stats['total_pesanan']; ?></h3>
                                <p>Total Pesanan</p>
                                <small><i class="fas fa-shopping-cart me-1"></i>Semua waktu</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card warning">
                                <h3><?php echo $stats['sedang_diproses']; ?></h3>
                                <p>Sedang Diproses</p>
                                <small><i class="fas fa-clock me-1"></i>Menunggu konfirmasi</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card success">
                                <h3><?php echo $stats['siap_dijemput']; ?></h3>
                                <p>Siap Dijemput</p>
                                <small><i class="fas fa-check-circle me-1"></i>Ready for pickup</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card primary">
                                <h3>Rp <?php echo number_format($stats['total_belanja'], 0, ',', '.'); ?></h3>
                                <p>Total Belanja</p>
                                <small><i class="fas fa-wallet me-1"></i>Semua pesanan</small>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="search-filter-container">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="filter-btn active" data-status="all">Semua</button>
                                        <button type="button" class="filter-btn"
                                            data-status="diproses">Diproses</button>
                                        <button type="button" class="filter-btn" data-status="siap_dijemput">Siap
                                            Dijemput</button>
                                        <button type="button" class="filter-btn" data-status="selesai">Selesai</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <label for="search" class="me-2 fw-semibold">
                                        <i class="fas fa-search"></i>
                                    </label>
                                    <input type="text" id="search" class="form-control"
                                        placeholder="Cari berdasarkan ID pesanan atau item...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="ordersTable">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="10%">ID Pesanan</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="25%">Items</th>
                                        <th width="10%">Qty</th>
                                        <th width="12%">Total</th>
                                        <th width="12%">Status</th>
                                        <th width="11%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($orders)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                                                    <h5>Belum Ada Pesanan</h5>
                                                    <p>Anda belum memiliki riwayat pesanan. <a href="katalog.php"
                                                            class="text-decoration-none">Mulai berbelanja sekarang!</a></p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1;
                                        foreach ($orders as $order): ?>
                                            <tr data-status="<?= $order['status'] ?>">
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <span
                                                        class="order-id-badge">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">
                                                        <?= date('d M Y', strtotime($order['order_date'])) ?></div>
                                                    <small
                                                        class="text-muted"><?= date('H:i', strtotime($order['order_date'])) ?></small>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"
                                                        title="<?= htmlspecialchars($order['items_detail']) ?>">
                                                        <?= strlen($order['items_detail']) > 40 ? htmlspecialchars(substr($order['items_detail'], 0, 40)) . '...' : htmlspecialchars($order['items_detail']) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= $order['total_items'] ?> item</span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">Rp
                                                        <?= number_format($order['total_amount'], 0, ',', '.') ?></div>
                                                </td>
                                                <td>
                                                    <?= getStatusBadge($order['status']) ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <!-- WhatsApp Button -->
                                                        <a href="https://wa.me/+6285158889868?text=Halo,%20saya%20ingin%20menanyakan%20pesanan%20dengan%20ID%20<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?>"
                                                            target="_blank" class="btn btn-sm btn-whatsapp action-btn"
                                                            title="Chat Admin">
                                                            <i class="fab fa-whatsapp"></i>
                                                        </a>

                                                        <!-- View Detail Button -->
                                                        <button class="btn btn-sm btn-outline-info action-btn btn-detail"
                                                            data-order-id="<?= $order['id'] ?>" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </button>

                                                        <!-- Print Receipt Button -->
                                                        <button class="btn btn-sm btn-outline-secondary action-btn btn-print"
                                                            data-order-id="<?= $order['id'] ?>" title="Cetak Struk">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pesanan -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">
                        <i class="fas fa-receipt me-2"></i>Detail Pesanan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat detail pesanan...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function () {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const status = this.dataset.status;
                const table = document.getElementById('ordersTable');
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                for (let row of rows) {
                    if (row.cells.length === 1) continue; // Skip "no data" row

                    const rowStatus = row.dataset.status;
                    let show = true;

                    if (status !== 'all') {
                        show = rowStatus === status;
                    }

                    row.style.display = show ? '' : 'none';
                }
            });
        });

        // Search functionality
        document.getElementById('search').addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById('ordersTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                if (row.cells.length === 1) continue; // Skip "no data" row

                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let cell of cells) {
                    if (cell.textContent.toLowerCase().includes(searchTerm)) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        });

        // Detail button functionality
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.orderId;
                const modal = new bootstrap.Modal(document.getElementById('modalDetail'));

                // Reset modal content
                document.getElementById('orderDetailContent').innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat detail pesanan...</p>
                    </div>
                `;

                modal.show();

                // Fetch order details
                fetch(`../actions/get_order_details.php?order_id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let itemsHtml = '';
                            data.items.forEach(item => {
                                itemsHtml += `
                                    <tr>
                                        <td>${item.nama}</td>
                                        <td class="text-center">${item.quantity}</td>
                                        <td class="text-end">Rp ${parseInt(item.price).toLocaleString('id-ID')}</td>
                                        <td class="text-end">Rp ${(item.quantity * item.price).toLocaleString('id-ID')}</td>
                                    </tr>
                                `;
                            });

                            document.getElementById('orderDetailContent').innerHTML = `
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6>Informasi Pesanan</h6>
                                        <table class="table table-borderless table-sm">
                                            <tr><td>ID Pesanan:</td><td><strong>#${String(data.order.id).padStart(4, '0')}</strong></td></tr>
                                            <tr><td>Tanggal:</td><td>${new Date(data.order.order_date).toLocaleDateString('id-ID', {
                                year: 'numeric', month: 'long', day: 'numeric',
                                hour: '2-digit', minute: '2-digit'
                            })}</td></tr>
                                            <tr><td>Status:</td><td>${getStatusBadgeText(data.order.status)}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Informasi Customer</h6>
                                        <table class="table table-borderless table-sm">
                                            <tr><td>Email:</td><td>${data.order.email}</td></tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <h6>Detail Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Harga</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${itemsHtml}
                                            <tr class="table-info fw-bold">
                                                <td colspan="3" class="text-end">Total:</td>
                                                <td class="text-end">Rp ${parseInt(data.order.total_amount).toLocaleString('id-ID')}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            `;
                        } else {
                            document.getElementById('orderDetailContent').innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Gagal memuat detail pesanan: ${data.message}
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        document.getElementById('orderDetailContent').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Terjadi kesalahan saat memuat data.
                            </div>
                        `;
                    });
            });
        });

        // Print button functionality
        document.querySelectorAll('.btn-print').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.orderId;
                window.open(`../actions/print_receipt.php?order_id=${orderId}`, '_blank', 'width=800,height=600');
            });
        });

        // Helper function for status badge in modal
        function getStatusBadgeText(status) {
            switch (status) {
                case 'diproses':
                    return '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Diproses</span>';
                case 'siap_dijemput':
                    return '<span class="badge bg-info"><i class="fas fa-check-circle me-1"></i>Siap Dijemput</span>';
                case 'selesai':
                    return '<span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Selesai</span>';
                default:
                    return '<span class="badge bg-secondary">Unknown</span>';
            }
        }

        // Auto refresh every 2 minutes for active orders
        setInterval(() => {
            // Only reload if no modal is open and there are active orders
            if (!document.querySelector('.modal.show') && <?= !empty($recent_orders) ? 'true' : 'false' ?>) {
                location.reload();
            }
        }, 120000);
    </script>
</body>

</html>