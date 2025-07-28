<?php
require_once '../includes/db.php'; 

$products = [];
$selected_category = '';

if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $selected_category = $_GET['kategori'];
    $sql = "SELECT id, nama, deskripsi, harga, gambar, kategori FROM produk WHERE kategori = ? ORDER BY nama";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$selected_category]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Gagal memuat produk kategori: " . $e->getMessage() . "</div>";
    }
} else {
    $sql = "SELECT id, nama, deskripsi, harga, gambar, kategori FROM produk ORDER BY nama";
    try {
        $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Gagal memuat semua produk: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <style>
        .product-img {
            width: 100%;
            height: 180px; 
            object-fit: cover; 
            border-top-left-radius: calc(.25rem - 1px);
            border-top-right-radius: calc(.25rem - 1px);
        }
        .card-body .product-title {
            height: 48px; 
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #dc3545; 
        }
        .alert-fixed { 
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container col-lg-10 col-xl-8 mx-auto mt-3">
        <h3 class="mb-3">
            <?php
            if (!empty($selected_category)) {
                echo "Produk Kategori: " . htmlspecialchars($selected_category);
            } else {
                echo "Semua Produk";
            }
            ?>
        </h3>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3 mb-5">
            <?php
            if (!empty($products)) {
                foreach ($products as $product) {
                    include '../includes/card.php';
                }
            } else {
                echo "<div class='col-12'><p class='text-center'>Tidak ada produk yang tersedia untuk kategori ini.</p></div>";
            }
            ?>
        </div>
    </div>

    <div id="notification-container" class="alert-fixed"></div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            const notificationContainer = document.getElementById('notification-container');

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.dataset.productId;

                    fetch('../actions/add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'product_id=' + productId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                        } else {
                            showNotification(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan saat menambahkan produk ke keranjang.', 'danger');
                    });
                });
            });

            function showNotification(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                notificationContainer.appendChild(alertDiv);
                setTimeout(() => {
                    bootstrap.Alert.getInstance(alertDiv)?.close();
                }, 3000);
            }
        });
    </script>
</body>

</html>