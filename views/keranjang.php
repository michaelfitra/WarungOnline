<?php
session_start();

function base_url($path = '')
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base_folder = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base_folder = str_replace('/views', '', $base_folder);
    if ($base_folder === '//') {
        $base_folder = '/';
    } elseif (substr($base_folder, -1) !== '/') {
        $base_folder .= '/';
    }

    $full_base_url = $protocol . '://' . $host . $base_folder;
    return $full_base_url . $path;
}

// Handle AJAX request untuk update quantity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax_update']) && $_POST['ajax_update'] == '1') {
    $index = $_POST['index'];
    $quantity = max(1, (int) $_POST['quantity']);
    
    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['qty'] = $quantity;
        
        // Return updated values
        $item = $_SESSION['cart'][$index];
        $item_total = $quantity * $item['price'];
        
        // Calculate new cart totals
        $cart_total = 0;
        $total_qty = 0;
        foreach ($_SESSION['cart'] as $cart_item) {
            $cart_total += ($cart_item['qty'] ?? 0) * ($cart_item['price'] ?? 0);
            $total_qty += ($cart_item['qty'] ?? 0);
        }
        
        echo json_encode([
            'success' => true,
            'item_total' => number_format($item_total, 0, ',', '.'),
            'cart_total' => number_format($cart_total, 0, ',', '.'),
            'total_qty' => $total_qty
        ]);
        exit;
    }
    
    echo json_encode(['success' => false]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $id => $quantity) {
        $quantity = max(1, (int) $quantity);
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] = $quantity;
        }
    }
}

if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/keranjang.css">
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

        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .cart-item-img {
            border-radius: 8px;
            overflow: hidden;
        }

        .quantity-control {
            border-radius: 8px;
            overflow: hidden;
        }

        .quantity-control .btn {
            border: 1px solid #dee2e6;
            background: #f8f9fa;
            color: #495057;
            transition: all 0.3s ease;
        }

        .quantity-control .btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .quantity-control .form-control {
            border-left: 0;
            border-right: 0;
            text-align: center;
            font-weight: 600;
        }

        .quantity-control .form-control:focus {
            border-color: #667eea;
            box-shadow: none;
        }

        .delete-item {
            color: #dc3545;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .delete-item:hover {
            background: #dc3545;
            color: white;
        }

        .product-name {
            color: #495057;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .product-name:hover {
            color: #667eea;
        }

        .price-tag {
            font-size: 1.1rem;
            font-weight: 600;
            color: #28a745;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
        }

        .summary-sidebar {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 20px;
        }

        .empty-cart-container {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-cart-container i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .checkout-security {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
        }

        .item-count-badge {
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 8px;
        }

        /* Loading animation */
        .updating {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .stats-card h3 {
                font-size: 2rem;
            }
            
            .cart-item {
                padding: 15px;
            }
            
            .summary-sidebar {
                position: static;
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid col-lg-10 col-xl-8 mx-auto">
        <div class="row">
            <div class="col-12">
                <div class="main-content p-4">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">
                                    <i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja
                                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                                        <span class="item-count-badge" id="item-count-badge"><?= count($_SESSION['cart']); ?></span>
                                    <?php endif; ?>
                                </h4>
                                <p class="mb-0 opacity-75">Kelola produk dalam keranjang belanja Anda</p>
                            </div>
                            <div class="text-end">
                                <small>Update terakhir: <?php echo date('d M Y, H:i'); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Statistics -->
                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                        <?php 
                        $total_items = count($_SESSION['cart']);
                        $total_qty = array_sum(array_column($_SESSION['cart'], 'qty'));
                        $total_harga = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $total_harga += ($item['qty'] ?? 0) * ($item['price'] ?? 0);
                        }
                        ?>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <h3 id="total-items"><?php echo $total_items; ?></h3>
                                    <p>Jenis Produk</p>
                                    <small><i class="fas fa-cubes me-1"></i>Dalam keranjang</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card success">
                                    <h3 id="total-qty"><?php echo $total_qty; ?></h3>
                                    <p>Total Quantity</p>
                                    <small><i class="fas fa-calculator me-1"></i>Semua item</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card primary">
                                    <h3 id="total-harga-stats">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></h3>
                                    <p>Total Harga</p>
                                    <small><i class="fas fa-wallet me-1"></i>Belum termasuk ongkir</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Cart Items -->
                        <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                            <div class="table-container">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>Daftar Produk
                                    </h5>
                                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                                        <span class="badge bg-primary" id="product-count"><?= count($_SESSION['cart']); ?> Produk</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Header untuk desktop -->
                                <div class="row border-bottom pb-2 mb-3 d-none d-md-flex text-muted">
                                    <div class="col-md-6">Nama Produk</div>
                                    <div class="col-md-3 text-center">Jumlah</div>
                                    <div class="col-md-3 text-end">Total</div>
                                </div>

                                <div id="cart-items-container">
                                    <?php
                                    $total_harga = 0;
                                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                        foreach ($_SESSION['cart'] as $index => $item) {
                                            $item_qty = $item['qty'] ?? 0;
                                            $item_price = $item['price'] ?? 0;
                                            $item_name = $item['name'] ?? 'Produk Tidak Dikenal';
                                            $item_gambar = $item['gambar'] ?? 'default.jpg';

                                            $total_per_item = $item_qty * $item_price;
                                            $total_harga += $total_per_item;
                                            ?>
                                            <div class="cart-item" data-index="<?= $index; ?>">
                                                <div class="row align-items-center">
                                                    <div class="col-3 col-md-2">
                                                        <a href="<?= base_url('views/detail.php'); ?>" class="text-decoration-none">
                                                            <div class="cart-item-img" style="width: 80px; height: 80px;">
                                                                <img src="<?= base_url('assets/images/' . htmlspecialchars($item_gambar)); ?>"
                                                                    class="img-fluid rounded" alt="<?= htmlspecialchars($item_name); ?>"
                                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <div class="col-9 col-md-4">
                                                        <a href="<?= base_url('views/detail.php'); ?>" class="product-name">
                                                            <h6 class="mb-1"><?= htmlspecialchars($item_name); ?></h6>
                                                        </a>
                                                        <small class="text-muted">Harga: Rp <?= number_format($item_price, 0, ',', '.'); ?></small>
                                                    </div>
                                                    <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-center">
                                                        <div class="input-group quantity-control" style="width: 140px;">
                                                            <button class="btn btn-outline-secondary minus-btn" type="button" data-index="<?= $index; ?>">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <input type="number" class="form-control qty-input"
                                                                data-index="<?= $index; ?>"
                                                                data-price="<?= $item_price; ?>"
                                                                value="<?= htmlspecialchars($item_qty); ?>" min="1">
                                                            <button class="btn btn-outline-secondary plus-btn" type="button" data-index="<?= $index; ?>">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-end align-items-center gap-3">
                                                        <span class="price-tag item-total" data-index="<?= $index; ?>">
                                                            Rp <?= number_format($total_per_item, 0, ',', '.'); ?>
                                                        </span>
                                                        <a href="?remove=<?= htmlspecialchars($index); ?>"
                                                            class="delete-item"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')"
                                                            title="Hapus Item">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="empty-cart-container">
                                            <i class="fas fa-shopping-cart"></i>
                                            <h4>Keranjang Anda Kosong</h4>
                                            <p class="mb-4">Silakan tambahkan produk ke keranjang untuk melanjutkan belanja.</p>
                                            <a href="<?= base_url('views/katalog.php'); ?>" class="btn btn-primary">
                                                <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Sidebar -->
                        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                        <div class="col-12 col-lg-4">
                            <div class="summary-sidebar">
                                <h5 class="mb-4">
                                    <i class="fas fa-calculator me-2"></i>Ringkasan Pesanan
                                </h5>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Subtotal (<span id="sidebar-item-count"><?= count($_SESSION['cart']); ?></span> item)</span>
                                        <span class="fw-semibold" id="sidebar-subtotal">Rp <?= number_format($total_harga, 0, ',', '.'); ?></span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Biaya Pengiriman</span>
                                        <span class="fw-semibold text-success">Gratis</span>
                                    </div>
                                </div>

                                <hr>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between">
                                        <h6>Total Pembayaran</h6>
                                        <h5 class="text-success" id="sidebar-total">Rp <?= number_format($total_harga, 0, ',', '.'); ?></h5>
                                    </div>
                                </div>

                                <?php if ($isLoggedIn): ?>
                                    <button class="btn btn-warning w-100 py-3 fw-bold" data-bs-toggle="modal" data-bs-target="#qrisModal">
                                        <i class="fas fa-credit-card me-2"></i>Checkout Sekarang
                                    </button>
                                <?php else: ?>
                                    <a href="<?= base_url('views/auth/login.php'); ?>" class="btn btn-warning w-100 py-3 fw-bold"
                                        onclick="return confirm('Silakan login terlebih dahulu sebelum checkout.')">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login untuk Checkout
                                    </a>
                                <?php endif; ?>

                                <div class="checkout-security">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>Pembayaran 100% aman & terpercaya
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal QRIS -->
    <div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrisModalLabel">
                        <i class="fas fa-qrcode me-2"></i>Scan QR untuk Bayar
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <img src="<?= base_url('assets/images/qris_dummy.webp'); ?>" alt="QRIS" class="img-fluid"
                            style="max-height: 300px; border-radius: 10px;">
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Total pembayaran: <strong id="modal-total">Rp <?= number_format($total_harga, 0, ',', '.'); ?></strong>
                    </div>
                    <p class="text-muted">Setelah melakukan pembayaran, klik tombol "Saya Sudah Bayar" untuk mengkonfirmasi pesanan.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <form action="<?= base_url('actions/checkout.php'); ?>" method="POST">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle me-2"></i>Saya Sudah Bayar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const plusButtons = document.querySelectorAll('.plus-btn');
            const minusButtons = document.querySelectorAll('.minus-btn');
            const qtyInputs = document.querySelectorAll('.qty-input');

            // Function to update cart totals
            function updateCartTotals(data) {
                // Update statistics cards
                document.getElementById('total-qty').textContent = data.total_qty;
                document.getElementById('total-harga-stats').textContent = 'Rp ' + data.cart_total;
                
                // Update sidebar
                document.getElementById('sidebar-subtotal').textContent = 'Rp ' + data.cart_total;
                document.getElementById('sidebar-total').textContent = 'Rp ' + data.cart_total;
                
                // Update modal
                document.getElementById('modal-total').textContent = 'Rp ' + data.cart_total;
            }

            // Function to send AJAX request
            function updateQuantity(index, quantity) {
                const cartItem = document.querySelector(`[data-index="${index}"]`);
                cartItem.classList.add('updating');
                
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        cartItem.classList.remove('updating');
                        
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    // Update item total
                                    const itemTotalElement = document.querySelector(`.item-total[data-index="${index}"]`);
                                    itemTotalElement.textContent = 'Rp ' + response.item_total;
                                    
                                    // Update cart totals
                                    updateCartTotals(response);
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }
                        }
                    }
                };
                
                xhr.send(`ajax_update=1&index=${index}&quantity=${quantity}`);
            }

            // Plus button event listeners
            plusButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const index = this.getAttribute('data-index');
                    const input = document.querySelector(`.qty-input[data-index="${index}"]`);
                    const newValue = parseInt(input.value) + 1;
                    input.value = newValue;
                    updateQuantity(index, newValue);
                });
            });

            // Minus button event listeners
            minusButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const index = this.getAttribute('data-index');
                    const input = document.querySelector(`.qty-input[data-index="${index}"]`);
                    const currentValue = parseInt(input.value);
                    if (currentValue > 1) {
                        const newValue = currentValue - 1;
                        input.value = newValue;
                        updateQuantity(index, newValue);
                    }
                });
            });

            // Input change event listeners
            qtyInputs.forEach(input => {
                input.addEventListener('change', function () {
                    const index = this.getAttribute('data-index');
                    let newValue = parseInt(this.value);
                    
                    // Ensure minimum value is 1
                    if (newValue < 1 || isNaN(newValue)) {
                        newValue = 1;
                        this.value = newValue;
                    }
                    
                    updateQuantity(index, newValue);
                });

                // Also handle input events for real-time updates
                input.addEventListener('input', function () {
                    const index = this.getAttribute('data-index');
                    let newValue = parseInt(this.value);
                    
                    // Only update if value is valid and greater than 0
                    if (newValue >= 1 && !isNaN(newValue)) {
                        updateQuantity(index, newValue);
                    }
                });
            });

            // Smooth animations
            const cartItems = document.querySelectorAll('.cart-item');
            cartItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>

</html>