<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: ../views/keranjang.php');
    exit;
}

$userId = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$total = 0;

foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
}

// Simpan ke tabel orders
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
$stmt->execute([$userId, $total]);
$orderId = $pdo->lastInsertId();

// Simpan setiap item
foreach ($cart as $item) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$orderId, $item['id'], $item['qty'], $item['price']]);
}

// Kosongkan keranjang
unset($_SESSION['cart']);

header("Location: ../index.php?success=1");
exit;
