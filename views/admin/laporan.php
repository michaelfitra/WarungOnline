<?php
$activePage = 'laporan';
// Sertakan koneksi database
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';
require_admin();

// Inisialisasi variabel filter
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Base query untuk laporan penjualan
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

    // Query untuk produk terlaris
    $bestseller_query = "SELECT 
                           p.nama,
                           SUM(oi.quantity) as total_terjual
                         FROM order_items oi
                         JOIN orders o ON oi.order_id = o.id
                         JOIN produk p ON oi.product_id = p.id
                         WHERE o.status = 'selesai' $where_clause
                         GROUP BY p.id
                         ORDER BY total_terjual DESC
                         LIMIT 5";

    $bestseller_stmt = $pdo->prepare($bestseller_query);
    $bestseller_stmt->execute($params);
    $bestsellers = $bestseller_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error in laporan.php: " . $e->getMessage());
    $laporan_data = [];
    $summary = ['total_transaksi' => 0, 'total_item_terjual' => 0, 'total_pendapatan' => 0];
    $bestsellers = [];
}

// Pagination
$items_per_page = 10;
$total_items = count($laporan_data);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;
$paged_data = array_slice($laporan_data, $offset, $items_per_page);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laporan Penjualan - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-item {
            text-align: center;
            padding: 15px;
        }

        .summary-item h4 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }

        .summary-item p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }

        .bestseller-card {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 15px;
        }

        .filter-section {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include '../../includes/header_admin.php' ?>
    <div class="d-flex">
        <div class="col-md-2 d-none d-md-block" style="background-color: #19345e;">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        <!-- Main Content -->
        <div class="col-md-10">
            <!-- Page Content -->
            <div class="main-content">
                <!-- Summary Cards -->
                <div class="summary-card">
                    <div class="row">
                        <div class="col-md-4 summary-item">
                            <h4><?php echo number_format($summary['total_transaksi'] ?? 0); ?></h4>
                            <p>Total Transaksi</p>
                        </div>
                        <div class="col-md-4 summary-item">
                            <h4><?php echo number_format($summary['total_item_terjual'] ?? 0); ?></h4>
                            <p>Total Item Terjual</p>
                        </div>
                        <div class="col-md-4 summary-item">
                            <h4>Rp <?php echo number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.'); ?></h4>
                            <p>Total Pendapatan</p>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="filter-section">
                    <form method="GET" action="">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="bulan">Pilih Bulan</label>
                                <select id="bulan" name="bulan" class="form-select">
                                    <option value="">Semua Bulan</option>
                                    <option value="1" <?php echo ($bulan == '1') ? 'selected' : ''; ?>>Januari</option>
                                    <option value="2" <?php echo ($bulan == '2') ? 'selected' : ''; ?>>Februari</option>
                                    <option value="3" <?php echo ($bulan == '3') ? 'selected' : ''; ?>>Maret</option>
                                    <option value="4" <?php echo ($bulan == '4') ? 'selected' : ''; ?>>April</option>
                                    <option value="5" <?php echo ($bulan == '5') ? 'selected' : ''; ?>>Mei</option>
                                    <option value="6" <?php echo ($bulan == '6') ? 'selected' : ''; ?>>Juni</option>
                                    <option value="7" <?php echo ($bulan == '7') ? 'selected' : ''; ?>>Juli</option>
                                    <option value="8" <?php echo ($bulan == '8') ? 'selected' : ''; ?>>Agustus</option>
                                    <option value="9" <?php echo ($bulan == '9') ? 'selected' : ''; ?>>September</option>
                                    <option value="10" <?php echo ($bulan == '10') ? 'selected' : ''; ?>>Oktober</option>
                                    <option value="11" <?php echo ($bulan == '11') ? 'selected' : ''; ?>>November</option>
                                    <option value="12" <?php echo ($bulan == '12') ? 'selected' : ''; ?>>Desember</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="tahun">Pilih Tahun</label>
                                <select id="tahun" name="tahun" class="form-select">
                                    <option value="">Semua Tahun</option>
                                    <?php
                                    $current_year = date('Y');
                                    for ($i = $current_year; $i >= $current_year - 5; $i--) {
                                        $selected = ($tahun == $i) ? 'selected' : '';
                                        echo "<option value='$i' $selected>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2 mb-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Cari
                                </button>
                                <a href="laporan.php" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                                <button type="button" class="btn btn-success" onclick="exportToExcel()">
                                    <i class="fa fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>

                        <!-- Rentang Tanggal -->
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label for="tanggal">Pilih Tanggal Spesifik</label>
                                <input type="date" id="tanggal" name="tanggal" class="form-control"
                                    value="<?php echo $tanggal; ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2 mb-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-calendar"></i> Cari Tanggal
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Produk Terlaris -->
                <?php if (!empty($bestsellers)): ?>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6><strong>Produk Terlaris</strong></h6>
                            <div class="row">
                                <?php foreach ($bestsellers as $index => $bestseller): ?>
                                    <div class="col-md-4">
                                        <div class="bestseller-card">
                                            <strong><?php echo $index + 1; ?>.
                                                <?php echo htmlspecialchars($bestseller['nama']); ?></strong>
                                            <br><small><?php echo $bestseller['total_terjual']; ?> item terjual</small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tabel -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle bg-white">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID Produk</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Qty Terjual</th>
                                <th>Harga Satuan</th>
                                <th>Total Pendapatan</th>
                                <th>Transaksi</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($paged_data)): ?>
                                <tr>
                                    <td colspan="9">Tidak ada data penjualan untuk periode yang dipilih</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($paged_data as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $offset + $index + 1; ?></td>
                                        <td>PRD<?php echo str_pad($item['product_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                        <td><?php echo htmlspecialchars($item['kategori']); ?></td>
                                        <td><?php echo number_format($item['total_terjual']); ?></td>
                                        <td>Rp <?php echo number_format($item['harga_jual'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($item['total_pendapatan'], 0, ',', '.'); ?></td>
                                        <td><?php echo $item['jumlah_transaksi']; ?>x</td>
                                        <td><?php echo date('d/m/Y', strtotime($item['tanggal_penjualan'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($paged_data)): ?>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total Halaman Ini:</strong></td>
                                    <td><strong><?php echo number_format(array_sum(array_column($paged_data, 'total_terjual'))); ?></strong>
                                    </td>
                                    <td>-</td>
                                    <td><strong>Rp
                                            <?php echo number_format(array_sum(array_column($paged_data, 'total_pendapatan')), 0, ',', '.'); ?></strong>
                                    </td>
                                    <td colspan="2">-</td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end">
                            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link"
                                    href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">Previous</a>
                            </li>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link"
                                        href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link"
                                    href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
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

        function exportToExcel() {
            Swal.fire({
                title: 'Export ke Excel',
                text: 'Apakah Anda yakin ingin mengexport data laporan ke file Excel?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Export',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Mengexport Data...',
                        text: 'Mohon tunggu sebentar',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect ke export script dengan parameter yang sama
                    const currentParams = new URLSearchParams(window.location.search);
                    window.location.href = '../../actions/export_laporan.php?' + currentParams.toString();

                    // Tutup loading setelah delay
                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'File Excel berhasil didownload',
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }, 2000);
                }
            });
        }

        // Reset filter tanggal jika memilih bulan/tahun
        document.getElementById('bulan').addEventListener('change', function () {
            if (this.value) {
                document.getElementById('tanggal').value = '';
            }
        });

        document.getElementById('tahun').addEventListener('change', function () {
            if (this.value) {
                document.getElementById('tanggal').value = '';
            }
        });

        document.getElementById('tanggal').addEventListener('change', function () {
            if (this.value) {
                document.getElementById('bulan').value = '';
                document.getElementById('tahun').value = '';
            }
        });
    </script>
</body>

</html>