<?php
session_start();
require_once '../../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../../index.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Password tidak sesuai.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan.";
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
