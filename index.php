<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .alert-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .product-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-top-left-radius: calc(.25rem - 1px);
            border-top-right-radius: calc(.25rem - 1px);
        }

        .card-body .product-title {
            height: auto;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>

<body>
    <?php
    require_once 'includes/db.php';
    include 'includes/header.php';

    $products = [];
    $search_query = '';

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_query = '%' . $_GET['search'] . '%';
        $sql = "SELECT id, nama, deskripsi, harga, gambar, kategori FROM produk WHERE nama LIKE ? ORDER BY nama LIMIT 12";
    } else {
        $sql = "SELECT id, nama, deskripsi, harga, gambar, kategori FROM produk ORDER BY RAND() LIMIT 6";
    }

    try {
        if (!empty($search_query)) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$search_query]);
        } else {
            $stmt = $pdo->query($sql);
        }
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Gagal memuat produk: " . $e->getMessage() . "</div>";
    }
    ?>

    <div class="col-lg-10 col-xl-8 mx-auto">
        <div class="container-fluid mt-4">
            <?php if (empty($search_query)): ?>
                <h3 class="mb-3">Kategori</h3>
                <div class="row mb-5 justify-content-start">
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                        <a href="<?= base_url('views/katalog.php?kategori=Makanan'); ?>"
                            class="text-decoration-none text-dark">
                            <div class="card h-100 text-center py-4 bg-white shadow-sm border-0">
                                <i class="fas fa-utensils fa-2x mb-2 text-warning"></i>
                                <p class="card-text fw-bold">Makanan</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                        <a href="<?= base_url('views/katalog.php?kategori=Minuman'); ?>"
                            class="text-decoration-none text-dark">
                            <div class="card h-100 text-center py-4 bg-white shadow-sm border-0">
                                <i class="fas fa-cocktail fa-2x mb-2 text-warning"></i>
                                <p class="card-text fw-bold">Minuman</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                        <a href="<?= base_url('views/katalog.php?kategori=Kesehatan & Kebersihan'); ?>"
                            class="text-decoration-none text-dark">
                            <div class="card h-100 text-center py-4 bg-white shadow-sm border-0">
                                <i class="fas fa-hand-sparkles fa-2x mb-2 text-warning"></i>
                                <p class="card-text fw-bold">Kesehatan & <br>Kebersihan</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                        <a href="<?= base_url('views/katalog.php?kategori=Dapur & Bahan Masak'); ?>"
                            class="text-decoration-none text-dark">
                            <div class="card h-100 text-center py-4 bg-white shadow-sm border-0">
                                <i class="fas fa-mortar-pestle fa-2x mb-2 text-warning"></i>
                                <p class="card-text fw-bold">Dapur & <br>Bahan Masak</p>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <h3 class="mb-3">
                <?php if (!empty($search_query)): ?>
                    Hasil Pencarian untuk "<?= htmlspecialchars($_GET['search']); ?>"
                <?php else: ?>
                    Produk Rekomendasi
                <?php endif; ?>
            </h3>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3 mb-5">
                <?php
                if (!empty($products)) {
                    foreach ($products as $product) {
                        include 'includes/card.php';
                    }
                } else {
                    if (!empty($search_query)) {
                        echo "<div class='col-12'><p>Tidak ada produk yang ditemukan untuk '" . htmlspecialchars($_GET['search']) . "'.</p></div>";
                    } else {
                        echo "<div class='col-12'><p>Tidak ada produk yang tersedia saat ini.</p></div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div id="notification-container" class="alert-fixed"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            const notificationContainer = document.getElementById('notification-container');

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.dataset.productId;

                    fetch('actions/add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'product_id=' + productId
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification(data.message, 'success');
                            } else {
                                showNotification(data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('Terjadi kesalahan saat menambahkan produk ke keranjang.', 'danger');
                        });
                });
            });

            function showNotification(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                notificationContainer.appendChild(alertDiv);
                setTimeout(() => {
                    bootstrap.Alert.getInstance(alertDiv)?.close();
                }, 3000);
            }
        });
    </script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Pesanan Anda akan diproses.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    <?php endif; ?>

</body>

</html>