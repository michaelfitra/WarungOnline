<?php $activePage = 'dashboard'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>
    <?php include '../../includes/header_admin.php'; ?>
    <div class="d-flex">
        <?php include '../../includes/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h4 class="mb-4"><strong>Dashboard Admin</strong></h4>

            <!-- Ringkasan -->
            <div class="row mb-4">
                <div class="col-md-3 mb-2">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5>Total Pesanan</h5>
                            <p class="fs-4">120</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5>Menunggu Konfirmasi</h5>
                            <p class="fs-4">6</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5>Siap Diambil</h5>
                            <p class="fs-4">8</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5>Pesanan Selesai</h5>
                            <p class="fs-4">100</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pesanan Terbaru -->
            <div class="card">
                <div class="card-header bg-light">
                    <strong>Pesanan Terbaru</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 text-center">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Andi</td>
                                <td>2025-07-08</td>
                                <td>Rp30.000</td>
                                <td><span class="badge bg-success">Selesai</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Budi</td>
                                <td>2025-07-08</td>
                                <td>Rp25.000</td>
                                <td><span class="badge bg-warning">Menunggu</span></td>
                            </tr>
                        </tbody>
                    </table>
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
    </script>

</body>

</html>