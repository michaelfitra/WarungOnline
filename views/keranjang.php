<?php
session_start();

function base_url($path = '') {
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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $id => $quantity) {
        $quantity = max(1, (int)$quantity); 
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/keranjang.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="col-lg-10 col-xl-8 mx-auto mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Keranjang Belanja</h3>

                        <div class="row border-bottom pb-2 mb-3 d-none d-md-flex text-muted">
                            <div class="col-md-6">Nama Produk</div>
                            <div class="col-md-3 text-center">Jumlah</div>
                            <div class="col-md-3 text-end">Total</div>
                        </div>

                        <form method="post" action="">
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
                                    <div class="row align-items-center mb-4 pb-3 border-bottom">
                                        <div class="col-3 col-md-2">
                                            <a href="<?= base_url('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                                <div class="cart-item-img bg-light rounded" style="width: 80px; height: 80px;">
                                                    <img src="<?= base_url('assets/images/' . htmlspecialchars($item_gambar)); ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item_name); ?>" style="width: 80px; height: 80px; object-fit: cover;">
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-9 col-md-4">
                                            <a href="<?= base_url('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                                <p class="mb-0 fw-bold"><?= htmlspecialchars($item_name); ?></p>
                                            </a>
                                        </div>
                                        <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-center align-items-center">
                                            <div class="input-group input-group-sm quantity-control" style="width: 120px;">
                                                <button class="btn btn-outline-secondary minus-btn" type="button"><i class="fas fa-minus"></i></button>
                                                <input type="number" class="form-control text-center qty-input" name="qty[<?= htmlspecialchars($index); ?>]" value="<?= htmlspecialchars($item_qty); ?>" min="1">
                                                <button class="btn btn-outline-secondary plus-btn" type="button"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center">
                                            <h5 class="mb-0 fw-bold me-3">Rp <?= number_format($total_per_item, 0, ',', '.'); ?></h5>
                                            <a href="?remove=<?= htmlspecialchars($index); ?>" class="btn btn-link text-danger p-0 delete-item">
                                                <i class="fas fa-trash-alt fa-lg"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo "<p>Keranjang Anda kosong.</p>";
                            }
                            ?>
                            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                                <button type="submit" name="update_cart" class="btn btn-warning">Update Keranjang</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="subtotal" class="form-label text-muted">Subtotal</label>
                            <input type="text" class="form-control border-top-0 border-start-0 border-end-0 rounded-0 ps-0"
                                id="subtotal" value="Rp <?= number_format($total_harga, 0, ',', '.'); ?>" readonly style="border-color: #ddd;">
                        </div>
                        <h4 class="mb-3">Total Pembayaran</h4>
                        <button class="btn btn-warning btn-lg w-100 py-3 fw-bold">Checkout Sekarang</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const plusButtons = document.querySelectorAll('.plus-btn');
                const minusButtons = document.querySelectorAll('.minus-btn');

                plusButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const input = this.parentNode.querySelector('.qty-input');
                        input.stepUp();
                    });
                });

                minusButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const input = this.parentNode.querySelector('.qty-input');
                        if (input.value > 1) {
                            input.stepDown();
                        }
                    });
                });
            });
        </script>
</body>

</html>