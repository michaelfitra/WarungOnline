<?php
include $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
?>

</html>
<nav class="navbar navbar-expand-lg bg-none sticky-top py-3">
    <div class="container-fluid col-lg-10 col-xl-8 mx-auto gap-2">

        <a class="navbar-brand" href="<?= URL::base(''); ?>" style="font-family: 'Consolas'; !ipmportant;">
            Toko<b style="color: orange;">Barokah</b>
        </a>

        <div class="input-group me-3">
            <input type="text" class="form-control" placeholder="Cari produk apa?" aria-label="Cari produk">
            <button class="btn btn-dark" type="button" id="button-addon2"><i class="fas fa-search"></i></button>
        </div>

        <div class="d-flex align-item-center gap-2">
            <a class="btn btn-outline-warning " type="button" href="<?= URL::base('views/keranjang.php'); ?>">
                <i class="fas fa-shopping-basket fa-lg"></i>
            </a>
            <a href="<?= URL::base('views/auth/login.php'); ?>" class="btn btn-outline-secondary ">Login</a>
            <a href="<?= URL::base('views/auth/register.php'); ?>" class="btn btn-dark">Daftar</a>
        </div>

    </div>
</nav>