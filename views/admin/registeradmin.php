<?php
if (!defined('BASE_URL') && !isset($page)) {
    header("Location: index.php");
    exit;
}

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_user = trim($_POST['nama_user'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $role = $_POST['role'] ?? 'staff';
    
    if (empty($nama_user) || empty($password) || empty($nama)) {
        $error = 'Semua field harus diisi!';
    } else {
        try {
            $stmtCheckUser = $db->prepare("SELECT id_user FROM tbl_user WHERE nama_user = ?");
            $stmtCheckUser->execute([$nama_user]);
            
            if ($stmtCheckUser->fetch()) {
                $error = 'Username sudah terdaftar! Silakan gunakan username lain.';
            } else {
                $prefix = 'KSR-'; 
                if ($role === 'admin') {
                    $prefix = 'ADM-';
                } elseif ($role === 'manager') {
                    $prefix = 'MGR-';
                }
                
                $isUnique = false;
                $id_user = '';
                while (!$isUnique) {
                    $id_user = $prefix . rand(1000, 9999);
                    $stmtCheckId = $db->prepare("SELECT id_user FROM tbl_user WHERE id_user = ?");
                    $stmtCheckId->execute([$id_user]);
                    if (!$stmtCheckId->fetch()) {
                        $isUnique = true;
                    }
                }

                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare(
                    "INSERT INTO tbl_user (id_user, nama_user, password, role, nama) 
                     VALUES (?, ?, ?, ?, ?)"
                );
                
                if ($stmt->execute([$id_user, $nama_user, $hashed, $role, $nama])) {
                    $message = "User baru berhasil terdaftar dengan ID: $id_user";
                    
                    $log = new LogAktivitas();
                    $log->catatLog(Session::get('user_id'), "Registrasi user baru: $nama_user ($role) dengan ID $id_user");
                    
                    $_POST = [];
                } else {
                    $error = 'Gagal mendaftar user!';
                }
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<?php include 'views/layouts/sidebar.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .fw-setting-container { 
        font-family: 'Inter', sans-serif; 
        padding: 32px; 
        background: #f8fafc; 
        min-height: calc(100vh - 80px); 
    }
    .fw-setting-card { 
        background: #fff; 
        border-radius: 12px; 
        padding: 32px; 
        max-width: 800px; 
        margin: auto; 
        border: 1px solid #e2e8f0; 
    }
    .fw-setting-header { 
        border-bottom: 2px solid #f1f5f9; 
        margin-bottom: 24px; 
        padding-bottom: 8px;
    }
    .fw-setting-header h2 { 
        margin: 0 0 8px 0; 
        color: #1e3a8a; 
    }
    .fw-setting-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    .fw-form-group { 
        margin-bottom: 20px; 
    }
    .fw-form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #334155;
    }
    .fw-form-control { 
        width: 100%; 
        padding: 12px; 
        border: 1px solid #cbd5e1; 
        border-radius: 8px; 
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }
    .fw-form-control:focus {
        outline: none;
        border-color: #2563eb;
    }
    .fw-alert { 
        padding: 14px; 
        border-radius: 8px; 
        margin-bottom: 20px; 
    }
    .fw-alert-success { 
        background: #dcfce7; 
        color: #166534; 
    }
    .fw-alert-danger { 
        background: #fee2e2; 
        color: #991b1b; 
    }
    .fw-btn-container { 
        display: flex; 
        gap: 12px; 
        margin-top: 24px; 
    }
    .fw-btn-submit { 
        background: #16a34a; 
        color: #fff; 
        padding: 12px 24px; 
        border: none; 
        border-radius: 8px; 
        cursor: pointer;
        font-weight: 600;
    }
    .fw-btn-reset { 
        background: #fff; 
        border: 1px solid #cbd5e1; 
        padding: 12px 24px; 
        border-radius: 8px; 
        cursor: pointer;
        color: #475569;
    }
</style>

<div class="fw-setting-container">
    <div class="fw-setting-card">

        <div class="fw-setting-header">
            <h2>Registrasi User Baru</h2>
            <p>Tambahkan user baru ke sistem (Admin/Staff/Manager)</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="fw-alert fw-alert-success">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="fw-alert fw-alert-danger">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="fw-form-group">
                <label>Username</label>
                <input type="text" name="nama_user" class="fw-form-control" value="<?= htmlspecialchars($_POST['nama_user'] ?? ''); ?>" required>
            </div>

            <div class="fw-form-group">
                <label>Password</label>
                <input type="password" name="password" class="fw-form-control" required>
            </div>

            <div class="fw-form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="fw-form-control" value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
            </div>

            <div class="fw-form-group">
                <label>Role</label>
                <select name="role" class="fw-form-control" required>
                    <option value="staff" <?= ($_POST['role'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff / Kasir</option>
                    <option value="manager" <?= ($_POST['role'] ?? '') === 'manager' ? 'selected' : ''; ?>>Manager</option>
                    <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="fw-btn-container">
                <button type="submit" class="fw-btn-submit">Daftar User</button>
                <button type="reset" class="fw-btn-reset">Reset</button>
            </div>

        </form>

    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>