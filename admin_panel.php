<?php
session_start();


require_once 'includes/db.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Warung Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .admin-sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .admin-sidebar .nav-link {
            color: white;
            padding: 10px 15px;
        }
        .admin-sidebar .nav-link:hover {
            background-color: #495057;
        }
        .admin-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="admin-sidebar col-md-2">
            <h4 class="text-center mb-4">Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="admin_panel.php?page=dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_panel.php?page=products">
                        <i class="fas fa-box me-2"></i> Manajemen Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_panel.php?page=orders">
                        <i class="fas fa-shopping-cart me-2"></i> Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home me-2"></i> Kembali ke Toko
                    </a>
                </li>
            </ul>
        </div>

        <div class="admin-content col-md-10">
            <?php
            $page = $_GET['page'] ?? 'dashboard'; 

            switch ($page) {
                case 'dashboard':
                    echo '<h1>Selamat Datang di Admin Panel</h1>';
                    echo '<p>Gunakan sidebar untuk navigasi.</p>';
                    break;
                case 'products':
                    include 'admin/manage_products.php'; 
                    break;
                case 'orders':
                    echo '<h2>Manajemen Pesanan</h2>';
                    echo '<p>Daftar pesanan akan ditampilkan di sini.</p>';
                    break;
                default:
                    echo '<h1>Halaman Tidak Ditemukan</h1>';
                    break;
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>