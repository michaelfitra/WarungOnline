<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

?>

<nav class="navbar navbar-expand-lg bg-none sticky-top py-3">
    <div class="container-fluid col-lg-10 col-xl-8 mx-auto gap-2">

        <a class="navbar-brand" href="<?= URL::base(''); ?>" style="font-family: 'Consolas'; !important;">
            Toko<b style="color: orange;">Barokah</b>
        </a>

        <form class="input-group me-3" action="<?= URL::base('index.php'); ?>" method="GET">
            <input type="text" class="form-control" placeholder="Cari produk apa?" aria-label="Cari produk"
                name="search" value="<?= htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button class="btn btn-dark" type="submit" id="searchButton"><i class="fas fa-search"></i></button>
        </form>

        <div class="d-flex align-item-center gap-2">
            <a class="btn btn-outline-warning " type="button" href="<?= URL::base('views/keranjang.php'); ?>">
                <i class="fas fa-shopping-basket fa-lg"></i>
            </a>
            <?php if ($isLoggedIn): ?>
                <a href="#" class="btn btn-outline-secondary ">Chat</a>
                <a href="<?= URL::base('actions/logout.php'); ?>" class="btn btn-dark">Logout</a>
            <?php else: ?>
                <a href="<?= URL::base('views/auth/login.php'); ?>" class="btn btn-outline-secondary ">Login</a>
                <a href="<?= URL::base('views/auth/register.php'); ?>" class="btn btn-dark">Daftar</a>
            <?php endif; ?>

        </div>

    </div>
</nav>