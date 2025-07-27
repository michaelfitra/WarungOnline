<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
?>

<!-- Navbar Atas -->
<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark justify-content-between px-3">
  <!-- Tombol Burger -->
  <button class="btn d-md-none" id="sidebarToggle">
    <i class="bi bi-list" style="font-size: 1.5rem; color: white;"></i>
  </button>

  <!-- Kedai BAROKAH -->
  <a class="navbar-brand" href="<?= URL::base(''); ?>" style="font-family: 'Consolas'; !important;"><span
      style="color: white;">Toko</span><b style="color: orange;">Barokah</b></a>

  <!-- Profil Admin Dropdown -->
  <div class="dropdown">
    <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-decoration-none" href="#"
      id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
      <img src="https://api.dicebear.com/7.x/adventurer-neutral/svg?seed=mail@ashallendesign.co.uk" alt="Profile"
        class="rounded-circle me-2" width="30" height="30">
      <span class="d-none d-sm-inline">Admin</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
      <li><a class="dropdown-item" href="#">Laporkan Masalah</a></li>
      <li>
        <hr class="dropdown-divider">
      </li>
      <li><a class="dropdown-item text-danger" href="<?= URL::base('actions/logout.php'); ?>">Logout</a></li>
    </ul>
  </div>
</nav>