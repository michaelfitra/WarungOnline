<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            if ($user['role'] === 'admin') {
                header("Location: /WarungOnline/views/admin/dashboard.php");
                exit;
            } else {
                $last_page = $_SESSION['last_page'] ?? '/WarungOnline/index';
                header("Location: $last_page");
                exit;
            }
        } else {
            $_SESSION['error'] = "Password tidak sesuai.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Akun tidak ditemukan.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Akses tidak valid.";
    header("Location: login.php");
    exit;
}
