<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

ob_start();

require_once __DIR__ . '/../../config/config.php';

if (!class_exists('User')) {
    require_once __DIR__ . '/../../classes/User.php';
}

if (!class_exists('Session')) {
    require_once __DIR__ . '/../../classes/Session.php';
}

if (!class_exists('UserSetting')) {
    require_once __DIR__ . '/../../classes/UserSetting.php';
}

$auth = new User();

if (!($auth instanceof User) || !$auth->isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

$userSetting   = new UserSetting();
$id_user_aktif = Session::get('user_id');

$message = '';
$message_type = '';

if (isset($_POST['btn_simpan'])) {
    $nama          = htmlspecialchars(trim($_POST['nama']));
    $password_baru = $_POST['password_baru'] ?? '';
    $file_foto     = $_FILES['foto_profil'] ?? null;

    $proses = $userSetting->updateProfile(
        $id_user_aktif,
        $nama,
        $password_baru,
        $file_foto
    );

    if (is_array($proses)) {
        $message      = $proses['message'] ?? '';
        $message_type = $proses['status'] ?? 'error';

        if ($message_type === 'success') {
            Session::set('nama', $nama);
        }
    } else {
        $message = 'Terjadi kesalahan sistem.';
        $message_type = 'error';
    }
}

$data_user = $userSetting->getUserById($id_user_aktif);
$data_user = is_array($data_user) ? $data_user : [];


if (!empty($data_user['foto'])) {
    $file_fisik = __DIR__ . '/../../assets/uploads/' . $data_user['foto'];
    if (file_exists($file_fisik)) {
        $foto_path = BASE_URL . 'assets/uploads/' . $data_user['foto'];
    } else {
        $foto_path = 'https://ui-avatars.com/api/?name=' .
            urlencode($data_user['nama'] ?? 'User') .
            '&background=2563eb&color=fff';
    }
} else {
    $foto_path = 'https://ui-avatars.com/api/?name=' .
        urlencode($data_user['nama'] ?? 'User') .
        '&background=2563eb&color=fff';
}
?>

<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .fw-setting-container { font-family: 'Inter', sans-serif; padding: 32px; background:#f8fafc; min-height:calc(100vh - 80px); }
    .fw-setting-card { background:#fff; border-radius:12px; padding:32px; max-width:800px; margin:auto; border:1px solid #e2e8f0; }
    .fw-setting-header { border-bottom:2px solid #f1f5f9; margin-bottom:24px; }
    .fw-setting-header h2 { margin:0; color:#1e3a8a; }
    .fw-profile-summary-row { display:flex; gap:20px; background:#f8fafc; padding:20px; border-radius:10px; margin-bottom:24px; }
    .fw-avatar-preview { width:90px; height:90px; border-radius:50%; object-fit:cover; border:3px solid #2563eb; }
    .fw-form-group { margin-bottom:20px; }
    .fw-form-control { width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:8px; }
    .fw-alert { padding:14px; border-radius:8px; margin-bottom:20px; }
    .fw-alert-success { background:#dcfce7; color:#166534; }
    .fw-alert-danger { background:#fee2e2; color:#991b1b; }
    .fw-btn-container { display:flex; gap:12px; margin-top:24px; }
    .fw-btn-submit { background:#16a34a; color:#fff; padding:12px 24px; border:none; border-radius:8px; }
    .fw-btn-reset { background:#fff; border:1px solid #cbd5e1; padding:12px 24px; border-radius:8px; }
</style>

<div class="fw-setting-container">
    <div class="fw-setting-card">

        <div class="fw-setting-header">
            <h2>Pengaturan Profil</h2>
        </div>

        <?php if (!empty($message)): ?>
            <div class="fw-alert <?= $message_type === 'success' ? 'fw-alert-success' : 'fw-alert-danger'; ?>">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="fw-profile-summary-row">
                <img src="<?= $foto_path; ?>" class="fw-avatar-preview">
                <div>
                    <p><strong>ID:</strong> <?= htmlspecialchars($data_user['id_user'] ?? '-'); ?></p>
                    <p><strong>Username:</strong> <?= htmlspecialchars($data_user['nama_user'] ?? '-'); ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($data_user['role'] ?? '-'); ?></p>
                </div>
            </div>

            <div class="fw-form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="fw-form-control"
                       value="<?= htmlspecialchars($data_user['nama'] ?? ''); ?>" required>
            </div>

            <div class="fw-form-group">
                <label>Password Baru</label>
                <input type="password" name="password_baru" class="fw-form-control"
                       placeholder="Kosongkan jika tidak diubah">
            </div>

            <div class="fw-form-group">
                <label>Foto Profil</label>
                <input type="file" name="foto_profil" class="fw-form-control">
            </div>

            <div class="fw-btn-container">
                <button type="submit" name="btn_simpan" class="fw-btn-submit">Simpan</button>
                <button type="reset" class="fw-btn-reset">Reset</button>
            </div>

        </form>

    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
<?php ob_end_flush(); ?>
