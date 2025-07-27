<?php
include '../../includes/db.php';
require_once '../../includes/auth_check.php';
require_admin();

include '../../includes/header_admin.php';

// Ambil data produk dengan statistik
try {
    $sql = "SELECT * FROM produk ORDER BY id ASC";
    $stmt = $pdo->query($sql);
    $produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistik produk
    $stats_sql = "SELECT 
        COUNT(*) as total_produk,
        SUM(stok) as total_stok,
        AVG(harga) as rata_harga,
        COUNT(CASE WHEN stok <= 10 THEN 1 END) as stok_rendah,
        COUNT(CASE WHEN stok = 0 THEN 1 END) as stok_habis
        FROM produk";
    $stats_stmt = $pdo->query($stats_sql);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Produk dengan stok rendah
    $low_stock_sql = "SELECT nama, stok FROM produk WHERE stok <= 10 ORDER BY stok ASC";
    $low_stock_stmt = $pdo->query($low_stock_sql);
    $low_stock_products = $low_stock_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Stock Management - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .search-filter-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
            color: #495057;
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

        .stock-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .stock-high {
            background: #d4edda;
            color: #155724;
        }

        .stock-medium {
            background: #fff3cd;
            color: #856404;
        }

        .stock-low {
            background: #f8d7da;
            color: #721c24;
        }

        .stock-empty {
            background: #dc3545;
            color: white;
        }

        .action-btn {
            margin: 0 2px;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .alert-stock {
            background: linear-gradient(135deg, #ff9a56 0%, #ff6b6b 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .category-badge {
            background: #e9ecef;
            color: #495057;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
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

        .filter-btn.active, .filter-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php $activePage = 'stok'; ?>
        <div class="col-md-2 d-none d-md-block" style="background-color: #19345e;">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-10">
            <div class="main-content">
                <?php if (isset($_GET['status'])): ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: '<?php
                            if ($_GET["status"] == "berhasil")
                                echo "Produk berhasil ditambahkan.";
                            elseif ($_GET["status"] == "update")
                                echo "Produk berhasil diupdate.";
                            elseif ($_GET["status"] == "hapus")
                                echo "Produk berhasil dihapus.";
                            ?>',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    </script>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-boxes me-2"></i>Stock Management</h4>
                            <p class="mb-0 opacity-75">Kelola inventori dan stok produk</p>
                        </div>
                        <div class="text-end">
                            <small>Update terakhir: <?php echo date('d M Y, H:i'); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Alert untuk stok rendah -->
                <?php if (!empty($low_stock_products)): ?>
                <div class="alert-stock">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Peringatan Stok Rendah!</h6>
                    <p class="mb-2">Produk berikut memiliki stok ≤ 10:</p>
                    <div class="row">
                        <?php foreach ($low_stock_products as $product): ?>
                        <div class="col-md-4 mb-1">
                            <small>• <?php echo htmlspecialchars($product['nama']); ?> (<?php echo $product['stok']; ?>)</small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo $stats['total_produk']; ?></h3>
                            <p>Total Produk</p>
                            <small><i class="fas fa-cubes me-1"></i>Semua kategori</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card success">
                            <h3><?php echo number_format($stats['total_stok']); ?></h3>
                            <p>Total Stok</p>
                            <small><i class="fas fa-warehouse me-1"></i>Semua item</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card warning">
                            <h3><?php echo $stats['stok_rendah']; ?></h3>
                            <p>Stok Rendah</p>
                            <small><i class="fas fa-exclamation-triangle me-1"></i>≤ 10 items</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card info">
                            <h3>Rp <?php echo number_format($stats['rata_harga'], 0, ',', '.'); ?></h3>
                            <p>Rata-rata Harga</p>
                            <small><i class="fas fa-chart-line me-1"></i>Per produk</small>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="search-filter-container">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-add btn-modern me-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="fas fa-plus me-2"></i>Tambah Produk
                                </button>
                                <div class="btn-group" role="group">
                                    <button type="button" class="filter-btn active" data-filter="all">Semua</button>
                                    <button type="button" class="filter-btn" data-filter="low">Stok Rendah</button>
                                    <button type="button" class="filter-btn" data-filter="empty">Stok Habis</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <label for="search" class="me-2 fw-semibold">
                                    <i class="fas fa-search"></i>
                                </label>
                                <input type="text" id="search" class="form-control" placeholder="Cari produk, kategori, atau ID...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="productTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">ID Barang</th>
                                    <th width="8%">Gambar</th>
                                    <th width="25%">Nama Barang</th>
                                    <th width="15%">Kategori</th>
                                    <th width="10%">Stok</th>
                                    <th width="12%">Harga</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($produk as $row): ?>
                                    <tr data-stock="<?= $row['stok'] ?>" data-category="<?= strtolower($row['kategori']) ?>">
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <span class="badge bg-secondary">BRG<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($row['gambar'] && file_exists('../../assets/images/' . $row['gambar'])): ?>
                                                <img src="../../assets/images/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama']) ?>" class="product-image">
                                            <?php else: ?>
                                                <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></div>
                                            <?php if ($row['deskripsi']): ?>
                                                <small class="text-muted"><?= htmlspecialchars(substr($row['deskripsi'], 0, 50)) . (strlen($row['deskripsi']) > 50 ? '...' : '') ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="category-badge"><?= htmlspecialchars($row['kategori']) ?></span>
                                        </td>
                                        <td>
                                            <span class="stock-badge <?php 
                                                if ($row['stok'] == 0) echo 'stock-empty';
                                                elseif ($row['stok'] <= 5) echo 'stock-low';
                                                elseif ($row['stok'] <= 10) echo 'stock-medium';
                                                else echo 'stock-high';
                                            ?>">
                                                <?= $row['stok'] ?>
                                                <?php if ($row['stok'] == 0): ?>
                                                    <i class="fas fa-exclamation-circle ms-1"></i>
                                                <?php elseif ($row['stok'] <= 10): ?>
                                                    <i class="fas fa-exclamation-triangle ms-1"></i>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-outline-primary action-btn btn-edit" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama']) ?>"
                                                    data-kategori="<?= htmlspecialchars($row['kategori']) ?>"
                                                    data-stok="<?= $row['stok'] ?>" 
                                                    data-harga="<?= $row['harga'] ?>"
                                                    data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEdit"
                                                    title="Edit Produk">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-info action-btn btn-view" 
                                                    data-id="<?= $row['id'] ?>"
                                                    title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger action-btn btn-hapus" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama']) ?>"
                                                    title="Hapus Produk">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Produk -->
        <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="tambah_stok.php" method="POST" enctype="multipart/form-data" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">
                            <i class="fas fa-plus me-2"></i>Tambah Produk Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-tag me-1"></i>Kategori
                                </label>
                                <input type="text" name="kategori" class="form-control" required placeholder="Contoh: Makanan, Minuman">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-cube me-1"></i>Nama Produk
                                </label>
                                <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama produk">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1"></i>Deskripsi
                            </label>
                            <textarea name="deskripsi" class="form-control" rows="3" required placeholder="Deskripsi produk..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-image me-1"></i>Gambar Produk
                            </label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF (Max: 2MB)</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-money-bill me-1"></i>Harga (Rp)
                                </label>
                                <input type="number" name="harga" class="form-control" required min="0" placeholder="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-warehouse me-1"></i>Stok Awal
                                </label>
                                <input type="number" name="stok" class="form-control" required min="0" placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fas fa-save me-2"></i>Simpan Produk
                        </button>
                        <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Produk -->
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="edit_stok.php" method="POST" class="modal-content">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">
                            <i class="fas fa-edit me-2"></i>Edit Produk
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-cube me-1"></i>Nama Produk
                                </label>
                                <input type="text" name="nama" id="edit-nama" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-tag me-1"></i>Kategori
                                </label>
                                <input type="text" name="kategori" id="edit-kategori" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1"></i>Deskripsi
                            </label>
                            <textarea name="deskripsi" id="edit-deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-warehouse me-1"></i>Stok
                                </label>
                                <input type="number" name="stok" id="edit-stok" class="form-control" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-money-bill me-1"></i>Harga (Rp)
                                </label>
                                <input type="number" name="harga" id="edit-harga" class="form-control" required min="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fas fa-save me-2"></i>Update Produk
                        </button>
                        <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('d-none');
        });

        // Edit button functionality
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-nama').value = this.dataset.nama;
                document.getElementById('edit-kategori').value = this.dataset.kategori;
                document.getElementById('edit-stok').value = this.dataset.stok;
                document.getElementById('edit-harga').value = this.dataset.harga;
                document.getElementById('edit-deskripsi').value = this.dataset.deskripsi || '';
            });
        });

        // Delete button functionality
        document.querySelectorAll('.btn-hapus').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const nama = this.dataset.nama;

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `Apakah Anda yakin ingin menghapus produk:<br><strong>"${nama}"</strong>?<br><br><small class="text-danger">Tindakan ini tidak dapat dibatalkan!</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-2"></i>Ya, Hapus!',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            text: 'Mohon tunggu sebentar',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        window.location.href = `hapus_stok.php?id=${id}`;
                    }
                });
            });
        });

        // View button functionality
        document.querySelectorAll('.btn-view').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const cells = row.querySelectorAll('td');
                const id = this.dataset.id;
                
                // Get data from the row
                const productData = {
                    id: cells[1].textContent.trim(),
                    name: cells[3].querySelector('.fw-semibold').textContent,
                    category: cells[4].textContent.trim(),
                    stock: cells[5].querySelector('.stock-badge').textContent.trim(),
                    price: cells[6].querySelector('.fw-semibold').textContent
                };

                Swal.fire({
                    title: 'Detail Produk',
                    html: `
                        <div class="text-start">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Produk:</strong></td>
                                    <td>${productData.id}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama:</strong></td>
                                    <td>${productData.name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kategori:</strong></td>
                                    <td>${productData.category}</td>
                                </tr>
                                <tr>
                                    <td><strong>Stok:</strong></td>
                                    <td>${productData.stock}</td>
                                </tr>
                                <tr>
                                    <td><strong>Harga:</strong></td>
                                    <td>${productData.price}</td>
                                </tr>
                            </table>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#667eea'
                });
            });
        });

        // Search functionality
        document.getElementById('search').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById('productTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
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

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                const table = document.getElementById('productTable');
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                for (let row of rows) {
                    const stock = parseInt(row.dataset.stock);
                    let show = true;

                    switch(filter) {
                        case 'low':
                            show = stock <= 10 && stock > 0;
                            break;
                        case 'empty':
                            show = stock === 0;
                            break;
                        case 'all':
                        default:
                            show = true;
                            break;
                    }

                    row.style.display = show ? '' : 'none';
                }
            });
        });

        // Auto refresh every 5 minutes
        setInterval(() => {
            // Only reload if no modal is open
            if (!document.querySelector('.modal.show')) {
                location.reload();
            }
        }, 300000);

        // Format number inputs
        document.querySelectorAll('input[type="number"]').forEach(input => {
            if (input.name === 'harga') {
                input.addEventListener('input', function() {
                    // Remove non-numeric characters except decimal point
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        });

        // Prevent negative values
        document.querySelectorAll('input[min="0"]').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value < 0) {
                    this.value = 0;
                }
            });
        });

        // File size validation
        document.querySelector('input[name="gambar"]').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    Swal.fire({
                        title: 'File Terlalu Besar!',
                        text: 'Ukuran file maksimal 2MB',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                    this.value = '';
                }
            }
        });
    </script>
</body>

</html>