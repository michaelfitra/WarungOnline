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
                        <!-- item -->
                        <div class="row align-items-center mb-4 pb-3 border-bottom">
                            <div class="col-3 col-md-2">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <div class="cart-item-img bg-light rounded" style="width: 80px; height: 80px;">
                                        <i
                                            class="fas fa-image fa-2x text-secondary d-flex align-items-center justify-content-center h-100"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="col-9 col-md-4">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <p class="mb-0 fw-bold">Pocari Sweat Minuman Isotonik Botol 500 ml</p>
                                </a>
                            </div>
                            <div
                                class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-center align-items-center">
                                <div class="input-group input-group-sm quantity-control" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-minus"></i></button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center">
                                <h5 class="mb-0 fw-bold me-3">Rp 3.300</h5>
                                <button class="btn btn-link text-danger p-0 delete-item">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </button>
                            </div>
                        </div>
                        <!-- item -->
                        <div class="row align-items-center mb-4 pb-3 border-bottom">
                            <div class="col-3 col-md-2">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <div class="cart-item-img bg-light rounded" style="width: 80px; height: 80px;">
                                        <i
                                            class="fas fa-image fa-2x text-secondary d-flex align-items-center justify-content-center h-100"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="col-9 col-md-4">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <p class="mb-0 fw-bold">Pocari Sweat Minuman Isotonik Botol 500 ml</p>
                                </a>
                            </div>
                            <div
                                class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-center align-items-center">
                                <div class="input-group input-group-sm quantity-control" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-minus"></i></button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center">
                                <h5 class="mb-0 fw-bold me-3">Rp 3.300</h5>
                                <button class="btn btn-link text-danger p-0 delete-item">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </button>
                            </div>
                        </div>
                        <!-- item -->
                        <div class="row align-items-center mb-4 pb-3 border-bottom">
                            <div class="col-3 col-md-2">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <div class="cart-item-img bg-light rounded" style="width: 80px; height: 80px;">
                                        <i
                                            class="fas fa-image fa-2x text-secondary d-flex align-items-center justify-content-center h-100"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="col-9 col-md-4">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <p class="mb-0 fw-bold">Pocari Sweat Minuman Isotonik Botol 500 ml</p>
                                </a>
                            </div>
                            <div
                                class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-center align-items-center">
                                <div class="input-group input-group-sm quantity-control" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-minus"></i></button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center">
                                <h5 class="mb-0 fw-bold me-3">Rp 3.300</h5>
                                <button class="btn btn-link text-danger p-0 delete-item">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </button>
                            </div>
                        </div>
                        <!-- item -->
                        <div class="row align-items-center mb-4 pb-3 border-bottom">
                            <div class="col-3 col-md-2">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <div class="cart-item-img bg-light rounded" style="width: 80px; height: 80px;">
                                        <i
                                            class="fas fa-image fa-2x text-secondary d-flex align-items-center justify-content-center h-100"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="col-9 col-md-4">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <p class="mb-0 fw-bold">Pocari Sweat Minuman Isotonik Botol 500 ml</p>
                                </a>
                            </div>
                            <div
                                class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-center align-items-center">
                                <div class="input-group input-group-sm quantity-control" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-minus"></i></button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center">
                                <h5 class="mb-0 fw-bold me-3">Rp 3.300</h5>
                                <button class="btn btn-link text-danger p-0 delete-item">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </button>
                            </div>
                        </div>
                        <!-- item -->
                        <div class="row align-items-center mb-4 pb-3">
                            <div class="col-3 col-md-2">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <div class="cart-item-img bg-light rounded" style="width: 80px; height: 80px;">
                                        <i
                                            class="fas fa-image fa-2x text-secondary d-flex align-items-center justify-content-center h-100"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="col-9 col-md-4">
                                <a href="<?= URL::base('views/detail.php'); ?>" class="text-decoration-none text-dark">
                                    <p class="mb-0 fw-bold">Pocari Sweat Minuman Isotonik Botol 500 ml</p>
                                </a>
                            </div>
                            <div
                                class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-center align-items-center">
                                <div class="input-group input-group-sm quantity-control" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-minus"></i></button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center">
                                <h5 class="mb-0 fw-bold me-3">Rp 3.300</h5>
                                <button class="btn btn-link text-danger p-0 delete-item">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="subtotal" class="form-label text-muted">Subtotal</label>
                            <input type="text"
                                class="form-control border-top-0 border-start-0 border-end-0 rounded-0 ps-0"
                                id="subtotal" value="" readonly style="border-color: #ddd;">
                        </div>
                        <div class="mb-4">
                            <label for="diskon" class="form-label text-muted">Diskon</label>
                            <input type="text"
                                class="form-control border-top-0 border-start-0 border-end-0 rounded-0 ps-0" id="diskon"
                                value="" readonly style="border-color: #ddd;">
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
</body>

</html>