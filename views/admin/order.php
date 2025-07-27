<?php
// Sertakan koneksi database
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';
require_admin();

// Query untuk mendapatkan data pesanan
$query = "SELECT o.id, o.order_date, o.total_amount, o.status, u.email as nama_user 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          ORDER BY o.order_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mendapatkan statistik
$stats_query = "SELECT 
    COUNT(*) as total_pesanan,
    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as menunggu_konfirmasi,
    SUM(CASE WHEN status = 'siap_dijemput' THEN 1 ELSE 0 END) as siap_dijemput,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as pesanan_selesai
    FROM orders";
$stats_stmt = $pdo->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <!-- SweetAlert2 -->
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
        }

        .action-icons:hover {
            color: #007bff;
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
            <!-- Page Content -->
            <div class="main-content">
                <h5><strong>Order</strong></h5>

                <!-- Card Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3><?php echo $stats['total_pesanan']; ?></h3>
                            <p>Total Pesanan</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3><?php echo $stats['menunggu_konfirmasi']; ?></h3>
                            <p>Menunggu Konfirmasi</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3><?php echo $stats['siap_dijemput']; ?></h3>
                            <p>Siap Dijemput</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3><?php echo $stats['pesanan_selesai']; ?></h3>
                            <p>Pesanan Selesai</p>
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
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                                <th>Cetak Struk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8">Tidak ada pesanan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $index => $order): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($order['nama_user']); ?></td>
                                        <td><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></td>
                                        <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
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
                                            <i class="fa-solid fa-pen-to-square action-icons"
                                                onclick="editStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')"
                                                title="Edit Status"></i>
                                        </td>
                                        <td>
                                            <i class="fa-solid fa-print action-icons"
                                                onclick="printReceipt(<?php echo $order['id']; ?>)" title="Cetak Struk"></i>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('d-none');
        });

        function editStatus(orderId, currentStatus) {
            // Tampilkan SweetAlert dengan form dropdown
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
            // Tampilkan loading
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

        function printReceipt(orderId) {
            // Konfirmasi sebelum mencetak
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
                    // Tampilkan toast ketika membuka window print
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Membuka jendela cetak...',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    // Buka window print
                    window.open('../../actions/print_receipt.php?order_id=' + orderId, '_blank', 'width=800,height=600');
                }
            });
        }
    </script>
</body>

</html>