<?php
// File: /views/admin/order.php (Versi yang disempurnakan)
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';
require_admin();

// Query untuk mendapatkan data pesanan dengan detail item
$query = "SELECT o.id, o.order_date, o.total_amount, o.status, u.email as nama_user,
          COUNT(oi.id) as total_items
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          LEFT JOIN order_items oi ON o.id = oi.order_id
          GROUP BY o.id, o.order_date, o.total_amount, o.status, u.email
          ORDER BY o.order_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mendapatkan statistik
$stats_query = "SELECT 
    COUNT(*) as total_pesanan,
    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as menunggu_konfirmasi,
    SUM(CASE WHEN status = 'siap_dijemput' THEN 1 ELSE 0 END) as siap_dijemput,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as pesanan_selesai,
    SUM(total_amount) as total_pendapatan
    FROM orders";
$stats_stmt = $pdo->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Query untuk produk dengan stok rendah (kurang dari 10)
$low_stock_query = "SELECT nama, stok FROM produk WHERE stok < 10 ORDER BY stok ASC";
$low_stock_stmt = $pdo->prepare($low_stock_query);
$low_stock_stmt->execute();
$low_stock_products = $low_stock_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order Management - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .badge-diproses {
            background: #ffc107;
            color: #000;
            padding: 5px 10px;
            border-radius: 15px;
        }

        .badge-siap-dijemput {
            background: #17a2b8;
            color: #fff;
            padding: 5px 10px;
            border-radius: 15px;
        }

        .badge-selesai {
            background: #28a745;
            color: #fff;
            padding: 5px 10px;
            border-radius: 15px;
        }

        .action-icons {
            cursor: pointer;
            margin: 0 5px;
            font-size: 16px;
        }

        .action-icons:hover {
            color: #007bff;
            transform: scale(1.1);
            transition: all 0.2s;
        }

        .low-stock-alert {
            background: linear-gradient(45deg, #ff6b6b, #ffa500);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .card-box {
            transition: transform 0.2s;
        }

        .card-box:hover {
            transform: translateY(-5px);
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .order-details-btn {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
        }

        .order-details-btn:hover {
            color: #0056b3;
        }
    </style>
</head>

<body>
    <?php include '../../includes/header_admin.php' ?>
    <div class="d-flex">
        <?php $activePage = 'order'; ?>
        <div class="col-md-2 d-none d-md-block" style="background-color: #19345e; height: 100vh;">
            <?php include '../../includes/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-10">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5><strong>Order Management</strong></h5>
                    <div class="text-muted">
                        <i class="fas fa-clock"></i> Update terakhir: <?php echo date('d M Y, H:i'); ?>
                    </div>
                </div>

                <!-- Alert untuk stok rendah -->
                <?php if (!empty($low_stock_products)): ?>
                <div class="low-stock-alert">
                    <h6><i class="fas fa-exclamation-triangle"></i> Peringatan Stok Rendah!</h6>
                    <p class="mb-2">Produk berikut memiliki stok kurang dari 10:</p>
                    <ul class="mb-0">
                        <?php foreach ($low_stock_products as $product): ?>
                        <li><?php echo htmlspecialchars($product['nama']); ?> - Stok: <?php echo $product['stok']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Card Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3><?php echo $stats['total_pesanan']; ?></h3>
                            <p>Total Pesanan</p>
                            <small class="text-muted">All time</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3><?php echo $stats['menunggu_konfirmasi']; ?></h3>
                            <p>Menunggu Konfirmasi</p>
                            <small class="text-warning">Perlu tindakan</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3><?php echo $stats['siap_dijemput']; ?></h3>
                            <p>Siap Dijemput</p>
                            <small class="text-info">Ready</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3>Rp <?php echo number_format($stats['total_pendapatan'], 0, ',', '.'); ?></h3>
                            <p>Total Pendapatan</p>
                            <small class="text-success">All time</small>
                        </div>
                    </div>
                </div>

                <!-- Tabel Order -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle bg-white">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Tanggal</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada pesanan</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $index => $order): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <strong>#<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['nama_user']); ?></td>
                                        <td>
                                            <div><?php echo date('d M Y', strtotime($order['order_date'])); ?></div>
                                            <small class="text-muted"><?php echo date('H:i', strtotime($order['order_date'])); ?></small>
                                        </td>
                                        <td>
                                            <button class="order-details-btn" onclick="showOrderDetails(<?php echo $order['id']; ?>)">
                                                <?php echo $order['total_items']; ?> item(s)
                                            </button>
                                        </td>
                                        <td>
                                            <strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge-<?php echo str_replace(' ', '-', $order['status']); ?>">
                                                <?php
                                                switch ($order['status']) {
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
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <i class="fa-solid fa-pen-to-square action-icons"
                                                    onclick="editStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')"
                                                    title="Edit Status"></i>
                                                <i class="fa-solid fa-eye action-icons"
                                                    onclick="showOrderDetails(<?php echo $order['id']; ?>)"
                                                    title="Lihat Detail"></i>
                                                <i class="fa-solid fa-print action-icons"
                                                    onclick="printReceipt(<?php echo $order['id']; ?>)" 
                                                    title="Cetak Struk"></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('d-none');
        });

        function editStatus(orderId, currentStatus) {
            Swal.fire({
                title: 'Edit Status Pesanan',
                html: `
                    <div class="text-start">
                        <label for="swal-status-select" class="form-label">Status Pesanan:</label>
                        <select id="swal-status-select" class="form-select">
                            <option value="diproses" ${currentStatus === 'diproses' ? 'selected' : ''}>Diproses</option>
                            <option value="siap_dijemput" ${currentStatus === 'siap_dijemput' ? 'selected' : ''}>Siap Dijemput</option>
                            <option value="selesai" ${currentStatus === 'selesai' ? 'selected' : ''}>Selesai</option>
                        </select>
                        <div class="mt-3">
                            <small class="text-info">
                                <i class="fas fa-info-circle"></i>
                                Mengubah status akan otomatis mengatur stok produk.
                            </small>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                preConfirm: () => {
                    const newStatus = document.getElementById('swal-status-select').value;
                    if (!newStatus) {
                        Swal.showValidationMessage('Pilih status pesanan');
                        return false;
                    }
                    return newStatus;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus(orderId, result.value);
                }
            });
        }

        function updateStatus(orderId, newStatus) {
            Swal.fire({
                title: 'Memperbarui Status...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('../../actions/update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Status pesanan berhasil diperbarui',
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal memperbarui status: ' + data.message,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memperbarui status',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        }

        function showOrderDetails(orderId) {
            Swal.fire({
                title: 'Memuat Detail...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('../../actions/get_order_details.php?order_id=' + orderId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const items = data.items.map(item => 
                        `<tr>
                            <td>${item.nama}</td>
                            <td>${item.quantity}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(item.quantity * item.price)}</td>
                        </tr>`
                    ).join('');

                    Swal.fire({
                        title: `Detail Pesanan #${String(orderId).padStart(3, '0')}`,
                        html: `
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${items}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Total</th>
                                            <th>Rp ${new Intl.NumberFormat('id-ID').format(data.total)}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        `,
                        width: '600px',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#007bff'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal memuat detail pesanan',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memuat detail pesanan',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        }

        function printReceipt(orderId) {
            Swal.fire({
                title: 'Cetak Struk',
                text: 'Apakah Anda yakin ingin mencetak struk pesanan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Cetak',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Membuka jendela cetak...',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    window.open('../../actions/print_receipt.php?order_id=' + orderId, '_blank', 'width=800,height=600');
                }
            });
        }

        // Auto refresh setiap 5 menit
        setInterval(() => {
            location.reload();
        }, 300000); // 5 menit = 300000 ms
    </script>
</body>

</html>