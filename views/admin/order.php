<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/admin.css">

</head>

<body>
    <?php include '../../includes/header_admin.php' ?>
    <div class="d-flex">
        <?php $activePage = 'order'; ?>
        <?php include '../../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-10">
            <!-- Topbar -->
            <!-- <div class="topbar">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-dark text-decoration-none" id="adminDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-fill"></i> Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="#">Laporkan Masalah</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
                    </ul>
                </div>
            </div> -->

            <!-- Page Content -->
            <div class="main-content">
                <h5><strong>Order</strong></h5>

                <!-- Card Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3>6</h3>
                            <p>Total Pesanan</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3>6</h3>
                            <p>Menunggu Konfirmasi</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3>6</h3>
                            <p>Siap Dijemput</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <h3>6</h3>
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
                                <th>Status Pembayaran</th>
                                <th>Metode Ambil</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>001</td>
                                <td>Andi</td>
                                <td>28 Mei 2025<br>10:00</td>
                                <td>Rp 100.000</td>
                                <td>Sudah Dibayar</td>
                                <td>Ambil di Toko</td>
                                <td><span class="badge-selesai">Selesai</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>002</td>
                                <td>Andi</td>
                                <td>28 Mei 2025<br>10:00</td>
                                <td>Rp 100.000</td>
                                <td>Sudah Dibayar</td>
                                <td>Pesan Kurir Sendiri</td>
                                <td><span class="badge-selesai">Selesai</span></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>003</td>
                                <td>Andi</td>
                                <td>28 Mei 2025<br>10:00</td>
                                <td>Rp 100.000</td>
                                <td>Sudah Dibayar</td>
                                <td>Pesan Kurir Sendiri</td>
                                <td><span class="badge-selesai">Selesai</span></td>
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