<?php
// includes/db.php

// KONFIGURASI DATABASE
$host = 'localhost';
$dbname = 'tokobarokah'; // Ganti dengan nama database Anda
$username = 'root';
$password = ''; // Kosongkan jika tidak ada password XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Fungsi base_url() - Ditempatkan di sini agar dapat diakses secara global
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    $base_folder = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    // Jika kita di subfolder (misal /views), kita perlu naik satu level
    // untuk mendapatkan root folder proyek (/WarungOnline)
    if (strpos($base_folder, '/views') !== false || strpos($base_folder, '/admin') !== false) {
        $base_folder = dirname($base_folder); // Naik satu level
    }

    // Pastikan base_folder selalu diakhiri dengan '/' kecuali jika itu root domain '/'
    if ($base_folder === '/') {
        $base_url_string = $protocol . '://' . $host . '/';
    } else {
        $base_url_string = $protocol . '://' . $host . $base_folder . '/';
    }

    // Gabungkan dengan path yang diberikan
    return $base_url_string . $path;
}


// Mulai sesi PHP jika belum dimulai (penting untuk keranjang belanja)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>