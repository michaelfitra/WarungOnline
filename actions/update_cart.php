<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity < 1) {
        unset($_SESSION['cart'][$productId]);
    } elseif (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] = $quantity;
    }
}

header('Location: ../cart.php'); 
exit;
?>