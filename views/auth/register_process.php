<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Validasi sederhana
    if (empty($email) || empty($password)) {
        $_SESSION['register_alert'] = [
            'type' => 'error',
            'message' => 'Email dan Password wajib diisi.'
        ];
        header("Location: register.php");
        exit;
    }

    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_alert'] = [
            'type' => 'error',
            'message' => 'Email sudah terdaftar.'
        ];
        header("Location: register.php");
        exit;
    }

    // Hash password dan simpan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';

    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashed_password, $role);

    if ($stmt->execute()) {
        $_SESSION['register_alert'] = [
            'type' => 'success',
            'message' => 'Pendaftaran berhasil! Silakan login.'
        ];
    } else {
        $_SESSION['register_alert'] = [
            'type' => 'error',
            'message' => 'Terjadi kesalahan saat menyimpan data.'
        ];
    }

    $stmt->close();
    $conn->close();

    header("Location: register.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}
