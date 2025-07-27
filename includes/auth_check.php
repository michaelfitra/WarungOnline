<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
require_once 'db.php';

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . URL::base('views/auth/login.php'));
        exit;
    }
}

function require_admin() {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        // Jika bukan admin, redirect ke halaman lain
        header('Location: ' . URL::base('index.php'));
        exit;
    }
}
