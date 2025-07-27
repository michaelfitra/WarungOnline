<?php
session_start();
require_once '../../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($email) || empty($password)) {
        $_SESSION['register_alert'] = [
            'message' => 'Email dan password wajib diisi.',
            'type' => 'error'
        ];
        header("Location: register.php");
        exit;
    }

    // Cek apakah email sudah digunakan
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $emailExists = $stmt->fetchColumn();

    if ($emailExists) {
        $_SESSION['register_alert'] = [
            'message' => 'Email sudah terdaftar.',
            'type' => 'error'
        ];
        header("Location: register.php");
        exit;
    }

    // Simpan user baru
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'user')");
    $stmt->execute([$email, $hashedPassword]);

    $_SESSION['register_alert'] = [
        'message' => 'Registrasi berhasil. Silakan login.',
        'type' => 'success'
    ];
    header("Location: register.php");
    exit;
} else {
    header("Location: register.php");
    exit;
}
