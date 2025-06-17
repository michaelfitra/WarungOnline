<?php
include $_SERVER['DOCUMENT_ROOT'] . '/WarungOnline/system/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="<?= URL::assets ('css/style.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <p>Belum Punya Akun? <a href="register.php" class="register-link">Daftar</a></p>

            <form>
                <div class="form-group">
                    <label for="email_handphone">Email/No.Handphone</label>
                    <input type="text" id="email_handphone" name="email_handphone">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="remember_me" name="remember_me">
                    <label for="remember_me">Ingat saya</label>
                </div>

                <button type="submit" class="login-button">Login</button>
            </form>
        </div>
    </div>
</body>
</html>