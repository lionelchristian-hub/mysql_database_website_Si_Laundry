<?php
if (!isset($page)) {
    header("Location: ../../index.php?page=register");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = Database::getInstance()->getConnection();
    
    $nama_user = htmlspecialchars($_POST['nama_user']);
    $nama_lengkap = htmlspecialchars($_POST['nama']);
    $password = $_POST['password'];

    $check = $db->prepare("SELECT COUNT(*) FROM tbl_user WHERE nama_user = ?");
    $check->execute([$nama_user]);
    
    if ($check->fetchColumn() > 0) {
        $error = "Username sudah digunakan! Silakan cari username lain.";
    } else {
        $id_user = 'CLT' . rand(10000, 99999);
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO tbl_user (id_user, nama_user, password, role, nama) VALUES (?, ?, ?, 'client', ?)");
        
        if ($stmt->execute([$id_user, $nama_user, $hashed, $nama_lengkap])) {
            echo "<script>
                    alert('Registrasi Berhasil! ID Anda: " . $id_user . ". Silakan login.');
                    window.location='index.php?page=login';
                  </script>";
            exit;
        } else {
            $error = "Registrasi gagal internal sistem!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pelanggan</title>
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
            <h1 class="auth-art-title">Mulai kelola order cucian.</h1>
            <p class="auth-art-copy">Buat akun untuk membantu kasir dan admin mencatat layanan laundry dengan tampilan yang nyaman.</p>
        </div>
    </section>

    <section class="auth-form-side">
        <div class="register-form auth-card">
            <a href="index.php?page=landing" style="display: inline-block; margin-bottom: 20px; color: #2563eb; text-decoration: none; font-weight: 600; font-size: 14px; padding: 8px 12px; border: 1px solid #2563eb; border-radius: 6px; transition: all 0.2s;">
                ← Kembali ke Beranda
            </a>
            <img src="assets/uploads/silaundry2.png" alt="Si Laundry" class="auth-symbol">
            <h2>Daftar Akun Pelanggan Baru</h2>
            <p class="auth-subtitle">Silakan buat akun untuk memantau status antrean laundry Anda.</p>
            
            <?php if(isset($error)): ?>
                <p class='auth-error'><?= $error; ?></p>
            <?php endif; ?>
            
            <form method="post">
                <input type="text" name="nama" placeholder="Masukkan nama lengkap Anda" required>
                <input type="text" name="nama_user" placeholder="Buat username baru" required>
                <input type="password" name="password" placeholder="Buat password aman" required>
                <button type="submit" class="btn btn-success">Daftar Pelanggan</button>
                <p class="auth-link-row">Sudah punya akun? <a href="index.php?page=login">Login Disini</a></p>
            </form>
        </div>
    </section>
</div>
</body>
</html>