    <?php


if (!defined('BASE_URL') && !isset($page)) {
    header("Location: index.php");
    exit;
}

$db = Database::getInstance()->getConnection();
$user_id = Session::get('user_id');
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_password') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Password baru tidak cocok!';
    } else {
        $stmt = $db->prepare("SELECT password FROM tbl_user WHERE id_user = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!password_verify($old_password, $user['password'])) {
            $error = 'Password lama tidak sesuai!';
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE tbl_user SET password = ? WHERE id_user = ?");
            if ($stmt->execute([$hashed, $user_id])) {
                $message = 'Password berhasil diubah!';
            } else {
                $error = 'Gagal mengubah password!';
            }
        }
    }
}

$stmt = $db->prepare("SELECT * FROM tbl_user WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php include 'views/layouts/sidebar.php'; ?>

<style>
    .setting-container {
        padding: 32px;
        background-color: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    .setting-header {
        margin-bottom: 32px;
    }
    
    .setting-header h1 {
        margin: 0 0 8px 0;
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .setting-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    
    .setting-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }
    
    .setting-card h2 {
        margin: 0 0 24px 0;
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
    }
    
    .form-group {
        margin-bottom: 16px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
    }
    
    .form-group input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }
    
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    .btn-primary {
        background: #3b82f6;
        color: white;
    }
    
    .btn-primary:hover {
        background: #2563eb;
    }
    
    .btn-secondary {
        background: #e2e8f0;
        color: #1e293b;
    }
    
    .btn-secondary:hover {
        background: #cbd5e1;
    }
    
    .message {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 16px;
        font-size: 14px;
    }
    
    .message.success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .message.error {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .user-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .info-item {
        padding: 12px;
        background: #f8fafc;
        border-radius: 6px;
    }
    
    .info-item label {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .info-item p {
        margin: 0;
        font-size: 14px;
        color: #1e293b;
        font-weight: 500;
    }
</style>

<div class="setting-container">
    <div class="setting-header">
        <h1>Pengaturan Akun</h1>
        <p>Kelola informasi akun dan keamanan Anda</p>
    </div>
    
    <?php if (!empty($message)): ?>
    <div class="message success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
    <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="setting-card">
        <h2>Informasi Akun</h2>
        <div class="user-info">
            <div class="info-item">
                <label>Username</label>
                <p><?php echo htmlspecialchars($user['nama_user']); ?></p>
            </div>
            <div class="info-item">
                <label>Nama Lengkap</label>
                <p><?php echo htmlspecialchars($user['nama']); ?></p>
            </div>
            <div class="info-item">
                <label>Role</label>
                <p><?php echo htmlspecialchars($user['role']); ?></p>
            </div>
        </div>
    </div>
    
    <div class="setting-card">
        <h2>Ubah Password</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update_password">
            
            <div class="form-group">
                <label>Password Lama</label>
                <input type="password" name="old_password" required>
            </div>
            
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="new_password" required>
            </div>
            
            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-primary">Ubah Password</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
