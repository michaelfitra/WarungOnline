<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/includes/db.php';

$product = null; // Inisialisasi variabel produk
$product_id = null;
$message = '';
$message_type = '';

// Ambil ID produk dari URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = filter_var($_GET['id'], FILTER_VALIDATE_INT); // Sanitasi ID
    
    if ($product_id) {
        try {
            // Ambil data produk dari database
            $stmt = $pdo->prepare("SELECT id, nama, deskripsi, harga, gambar, kategori FROM produk WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                $message = "Produk tidak ditemukan.";
                $message_type = "warning";
            }
        } catch (PDOException $e) {
            $message = "Terjadi kesalahan saat memuat detail produk: " . $e->getMessage();
            $message_type = "danger";
            error_log("Error fetching product detail (ID: " . $product_id . "): " . $e->getMessage());
        }
    } else {
        $message = "ID produk tidak valid.";
        $message_type = "danger";
    }
} else {
    $message = "ID produk tidak diberikan.";
    $message_type = "danger";
}

// Jika produk tidak ditemukan atau ada error, arahkan kembali ke homepage atau tampilkan pesan
if (!$product || $message_type === "danger" || $message_type === "warning") {
    // Anda bisa mengarahkan kembali ke homepage:
    // header('Location: ' . URL::base('index.php'));
    // exit();
    // Atau tampilkan halaman dengan pesan error
    // Biarkan kode HTML di bawah menampilkan pesan.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - <?= $product ? htmlspecialchars($product['nama']) : 'Produk Tidak Ditemukan'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= URL::base('assets/css/detail.css'); ?>">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="py-5">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-md-10 col-lg-8">

                <?php if ($message): // Tampilkan pesan jika ada ?>
                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($product): // Tampilkan detail produk hanya jika produk ditemukan ?>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-4 mb-md-0">
                            <div class="product-image-container bg-light rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 450px; overflow: hidden;">
                                <?php if (!empty($product['gambar'])): ?>
                                    <img src="<?= URL::base('assets/images/' . htmlspecialchars($product['gambar'])); ?>" class="img-fluid" alt="<?= htmlspecialchars($product['nama']); ?>" style="max-height: 100%; width: auto; object-fit: contain;">
                                <?php else: ?>
                                    <i class="fas fa-image fa-5x text-secondary"></i>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="product-details p-3">
                                <h1 class="product-title fw-bold mb-3"><?= htmlspecialchars($product['nama']); ?></h1>
                                <p class="product-category text-muted mb-2"><small>Kategori: <?= htmlspecialchars($product['kategori'] ?? 'Umum'); ?></small></p>
                                <p class="product-description text-muted mb-4">
                                    <?= nl2br(htmlspecialchars($product['deskripsi'])); ?>
                                </p>

                                <h2 class="product-price fw-bold mb-4">Rp <?= number_format($product['harga'], 0, ',', '.'); ?></h2>

                                <div class="d-flex align-items-center mb-4">
                                    <div class="input-group quantity-control me-3" style="width: 120px;">
                                        <button class="btn btn-outline-secondary" type="button" id="decrement-qty"><i class="fas fa-minus"></i></button>
                                        <input type="text" class="form-control text-center" value="1" id="product-quantity" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="increment-qty"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                    <button class="btn btn-outline-warning btn-lg me-md-2 add-to-cart-btn" type="button" data-product-id="<?= htmlspecialchars($product['id']); ?>">
                                        <i class="fas fa-cart-plus me-2"></i> + Keranjang
                                    </button>
                                    <button class="btn btn-warning btn-lg buy-now-btn" type="button" data-product-id="<?= htmlspecialchars($product['id']); ?>">Beli Sekarang</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <p>Produk yang Anda cari tidak ditemukan atau ada kesalahan dalam memuat data.</p>
                        <a href="<?= URL::base('index.php'); ?>" class="btn btn-primary">Kembali ke Beranda</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Logika untuk kontrol kuantitas
        document.addEventListener('DOMContentLoaded', function() {
            const decrementBtn = document.getElementById('decrement-qty');
            const incrementBtn = document.getElementById('increment-qty');
            const quantityInput = document.getElementById('product-quantity');

            if (decrementBtn && incrementBtn && quantityInput) {
                decrementBtn.addEventListener('click', function() {
                    let currentQty = parseInt(quantityInput.value);
                    if (currentQty > 1) {
                        quantityInput.value = currentQty - 1;
                    }
                });

                incrementBtn.addEventListener('click', function() {
                    let currentQty = parseInt(quantityInput.value);
                    quantityInput.value = currentQty + 1;
                });
            }

            // Anda akan menambahkan logika AJAX untuk tombol "Tambah Keranjang" dan "Beli Sekarang" di sini nanti
            // Untuk saat ini, tombol hanya ada tanpa fungsionalitas keranjang
            const addToCartBtn = document.querySelector('.add-to-cart-btn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const quantity = parseInt(quantityInput.value);
                    alert(`Produk ID: ${productId} dengan jumlah ${quantity} ditambahkan ke keranjang (belum berfungsi penuh)!`);
                    // Nanti di sini Anda akan kirim data ke PHP via AJAX untuk menambah ke session/database keranjang
                });
            }

            const buyNowBtn = document.querySelector('.buy-now-btn');
            if (buyNowBtn) {
                buyNowBtn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const quantity = parseInt(quantityInput.value);
                    alert(`Membeli Produk ID: ${productId} dengan jumlah ${quantity} sekarang (belum berfungsi penuh)!`);
                    // Nanti di sini Anda akan kirim data ke PHP via AJAX atau redirect ke halaman checkout
                });
            }
        });
    </script>
</body>
</html>