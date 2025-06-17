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
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="col-lg-10 col-xl-8 mx-auto">
        <div class="container-fluid mt-4">
            <h3 class="mb-3">Kategori</h3>
            <div class="row mb-5 justify-content-start">
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                    <a href="<?= URL::base('views/katalog.php'); ?>" class="text-decoration-none text-dark">
                        <div class="card h-100 text-center py-4 bg-white shadow-sm border-0">
                            <i class="fas fa-utensils fa-2x mb-2 text-warning"></i>
                            <p class="card-text fw-bold">Makanan</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                    <a href="<?= URL::base('views/katalog.php'); ?>" class="text-decoration-none text-dark">
                    <div class="card h-100 text-center py-4 bg-white shadow-sm border-0">
                        <i class="fas fa-cocktail fa-2x mb-2 text-warning"></i>
                        <p class="card-text fw-bold">Minuman</p>
                    </div>
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                    <a href="<?= URL::base('views/katalog.php'); ?>" class="text-decoration-none text-dark">
                    <div class="card h-100 text-center py-4 bg-white shadow-sm border-0">
                        <i class="fas fa-hand-sparkles fa-2x mb-2 text-warning"></i>
                        <p class="card-text fw-bold">Kesehatan & <br>Kebersihan</p>
                    </div>
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                    <a href="<?= URL::base('views/katalog.php'); ?>" class="text-decoration-none text-dark">
                    <div class="card h-100 text-center py-4 bg-white shadow-sm border-0">
                        <i class="fas fa-mortar-pestle fa-2x mb-2 text-warning"></i>
                        <p class="card-text fw-bold">Dapur & <br>Bahan Masak</p>
                    </div>
                    </a>
                </div>
            </div>

            <h3 class="mb-3">Produk Rekomendasi</h3>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3 mb-5">
                <?php include 'includes/card.php'; ?>
                <?php include 'includes/card.php'; ?>
                <?php include 'includes/card.php'; ?>
                <?php include 'includes/card.php'; ?>
                <?php include 'includes/card.php'; ?>
                <?php include 'includes/card.php'; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>