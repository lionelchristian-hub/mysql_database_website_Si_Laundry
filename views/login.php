<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new User();
    if ($auth->login($_POST['nama_user'], $_POST['password'])) {
        if (Session::get('role') === 'client') {
            header('Location: index.php?page=client_dashboard');
        } else {
            header('Location: index.php?page=dashboard');
        }
        exit;
    } else $error = "Login gagal!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
<div class="auth-shell">
    <section class="auth-art" aria-label="Ilustrasi laundry">
        <div class="auth-brand">
            <img src="assets/uploads/silaundry2.png" alt="Si Laundry" class="auth-brand-mark">
        </div>
        <div class="auth-art-inner">
            <div class="laundry-scene" aria-hidden="true">
                <span class="bubble b1"></span>
                <span class="bubble b2"></span>
                <span class="bubble b3"></span>
                <span class="bubble b4"></span>
                <span class="clothes one"></span>
                <span class="clothes two"></span>
                <span class="clothes three"></span>
                <div class="laundry-basket"></div>
                <div class="laundry-machine">
                    <div class="machine-window"></div>
                </div>
            </div>
            <h1 class="auth-art-title">Kelola laundry lebih rapi.</h1>
            <p class="auth-art-copy">Pantau pelanggan, paket cucian, transaksi, dan pembayaran dari satu dashboard yang bersih.</p>
        </div>
    </section>

    <section class="auth-form-side">
        <div class="login-form auth-card">
            <a href="index.php?page=landing" style="display: inline-block; margin-bottom: 20px; color: #2563eb; text-decoration: none; font-weight: 600; font-size: 14px; padding: 8px 12px; border: 1px solid #2563eb; border-radius: 6px; transition: all 0.2s;">
                ← Kembali ke Beranda
            </a>
            <img src="assets/uploads/silaundry2.png" alt="Si Laundry" class="auth-symbol">
            <h2>Login Sistem Laundry</h2>
            <p class="auth-subtitle">Masuk untuk melanjutkan pengelolaan operasional laundry.</p>
            <?php if(isset($error)) echo "<p class='auth-error'>$error</p>"; ?>
            <form method="post">
                <input type="text" name="nama_user" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn btn-primary">Masuk</button>
                <p class="auth-link-row">Belum punya akun? <a href="index.php?page=register">Daftar</a></p>
            </form>
        </div>
    </section>
</div>
</body>
</html>
