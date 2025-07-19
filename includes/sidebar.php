
<div class="sidebar col-md-2 d-none d-md-block" id="sidebar">
    <a href="<?= URL::base('views/admin/dashboard.php') ?>" class="<?= ($activePage == 'dashboard') ? 'active' : '' ?>">
        <i class="bi bi-grid"></i> 
        Dashboard
    </a>

    <a href="<?= URL::base('views/admin/stok.php') ?>" class="<?= ($activePage == 'stok') ? 'active' : '' ?>">
        <i class="bi bi-box-seam"></i> Stok
    </a>

    <a href="<?= URL::base('views/admin/order.php') ?>" class="<?= ($activePage == 'order') ? 'active' : '' ?>">
        <i class="bi bi-cart"></i> Order
    </a>

    <a href="<?= URL::base('views/admin/laporan.php') ?>" class="<?= ($activePage == 'laporan') ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-text"></i> Laporan
    </a>

    <a href="#" class="<?= ($activePage == 'chat') ? 'active' : '' ?>">
        <i class="bi bi-chat-dots"></i> Chat
    </a>
</div>