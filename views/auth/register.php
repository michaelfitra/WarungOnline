<?php
include $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
session_start();
$alertMessage = '';
$alertType = '';
if (isset($_SESSION['register_alert'])) {
    $alertMessage = $_SESSION['register_alert']['message'];
    $alertType = $_SESSION['register_alert']['type']; // success / error
    unset($_SESSION['register_alert']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="<?= URL::assets('css/style.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Daftar</h2>
            <p>Sudah Punya Akun? <a href="login.php" class="register-link">Login</a></p>

            <form method="POST" action="register_process.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" style="width: 100%" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" style="width: 100%" required>
                </div> <br>

                <button type="submit" class="login-button">Daftar</button>
            </form>
        </div>
    </div>

    <?php if (!empty($alertMessage)): ?>
    <script>
        Swal.fire({
            icon: '<?= $alertType ?>',
            title: '<?= $alertType === "success" ? "Berhasil" : "Gagal" ?>',
            text: <?= json_encode($alertMessage) ?>,
            confirmButtonColor: '#3085d6',
        }).then(() => {
            <?php if ($alertType === 'success'): ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
</body>
</html>
