<?php $activePage = 'laporan'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laporan Penjualan - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/admin.css">

</head>

<body>
    <?php include '../../includes/header_admin.php' ?>
    <div class="d-flex">
        <?php include '../../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-10">

            <!-- Page Content -->
            <div class="main-content">
                <h5><strong>Data Laporan Penjualan</strong></h5>

                <!-- Filter Form -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="bulan">Pilih Bulan</label>
                        <select id="bulan" class="form-select">
                            <option>Bulan</option>
                            <option>Januari</option>
                            <option>Februari</option>
                            <option>Maret</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="tahun">Pilih Tahun</label>
                        <select id="tahun" class="form-select">
                            <option>Tahun</option>
                            <option>2023</option>
                            <option>2024</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2 mb-2">
                        <button class="btn btn-primary">Cari</button>
                        <button class="btn btn-secondary">Refresh</button>
                        <!-- <button class="btn btn-success">Excel</button> -->
                    </div>
                </div>

                <!-- Rentang Tanggal -->
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <label for="tanggal">Pilih Tanggal</label>
                        <input type="date" id="tanggal" class="form-control">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2 mb-2">
                        <button class="btn btn-primary">Cari</button>
                        <!-- <button class="btn btn-success">Excel</button> -->
                    </div>
                </div>

                <!-- Tabel -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle bg-white">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Modal</th>
                                <th>Total</th>
                                <th>Tanggal Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>BRG001</td>
                                <td>Kopi Arabika</td>
                                <td>Minuman</td>
                                <td>10</td>
                                <td>Rp10.000</td>
                                <td>Rp15.000</td>
                                <td>2025-07-08</td>
                            </tr>
                            <!-- Tambah baris lainnya sesuai data -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Total Terjual:</strong></td>
                                <td>Rp100.000</td>
                                <td>Keuntungan: Rp50.000</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li class="page-item disabled">
                            <a class="page-link">Previous</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('d-none');
        });
    </script>

</body>

</html>