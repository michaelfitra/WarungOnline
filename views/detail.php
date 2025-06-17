<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Pocari Sweat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/detail.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="py-5">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="row">
                    <div class="col-12 col-md-6 mb-4 mb-md-0">
                        <div class="product-image-placeholder bg-light rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 450px;">
                            <i class="fas fa-image fa-5x text-secondary"></i>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="product-details p-3">
                            <h1 class="product-title fw-bold mb-3">Pocari Sweat</h1>
                            <p class="product-description text-muted mb-4">
                                Pocari Sweat adalah minuman isotonik yang populer di Jepang dan banyak negara Asia, termasuk
                                Indonesia. Minuman ini dirancang untuk menggantikan
                                cairan dan elektrolit yang hilang akibat aktivitas fisik,
                                terutama saat berkeringat. Pocari Sweat mengandung
                                berbagai ion penting seperti natrium, klorida, magnesium,
                                dan kalium.
                            </p>

                            <h2 class="product-price fw-bold mb-4">Rp 3.300</h2>

                            <div class="d-flex align-items-center mb-4">
                                <div class="input-group quantity-control me-3" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button"><i class="fas fa-minus"></i></button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-outline-warning btn-lg me-md-2" type="button">
                                    <i class="fas fa-cart-plus me-2"></i> + Keranjang
                                </button>
                                <button class="btn btn-warning btn-lg" type="button">Beli Sekarang</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>