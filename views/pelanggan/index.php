<?php
if (!defined('BASE_URL') && !isset($page)) {
    header("Location: ../../index.php");
    exit;
}
require_once 'classes/Pelanggan.php';
$pelangganObj = new Pelanggan();

$action = $_GET['action'] ?? 'tampil';

if ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pelanggan = $_POST['id_user'] ?? null;
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    
    if (empty($id_pelanggan)) {
        $error = "Silakan pilih Akun User terlebih dahulu! Jika kosong, daftarkan user role client baru dulu.";
    } else {
        if ($pelangganObj->tambahPelanggan($id_pelanggan, $nama_pelanggan, $no_telp, $alamat)) {
            header("Location: index.php?page=pelanggan");
            exit;
        } else {
            $error = "Gagal menambah data pelanggan!";
        }
    }
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_GET['id'] ?? null;
    if ($pelangganObj->ubahPelanggan($id, $_POST['nama_pelanggan'], $_POST['no_telp'], $_POST['alamat'])) {
        header("Location: index.php?page=pelanggan");
        exit;
    } else {
        $error = "Gagal memperbarui data!";
    }
}

if ($action === 'hapus') {
    $id = $_GET['id'] ?? null;
    $pelangganObj->hapusPelanggan($id);
    header("Location: index.php?page=pelanggan");
    exit;
}
?>

<?php include 'views/layouts/sidebar.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .fw-master-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        padding: 32px;
        background-color: #f8fafc;
        min-height: 100vh;
        color: #1e293b;
    }

    .fw-master-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        padding: 24px;
        border: 1px solid #e2e8f0;
    }

    .fw-master-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .fw-master-header h2 {
        font-size: 22px;
        font-weight: 700;
        color: #1e3a8a;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .fw-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }

    .fw-btn-primary {
        background-color: #2563eb;
        color: #ffffff;
    }

    .fw-btn-primary:hover {
        background-color: #1d4ed8;
    }

    .fw-btn-success {
        background-color: #16a34a;
        color: #ffffff;
    }

    .fw-btn-success:hover {
        background-color: #15803d;
    }

    .fw-btn-danger {
        background-color: #dc2626;
        color: #ffffff;
        padding: 6px 12px;
        font-size: 12px;
    }

    .fw-btn-danger:hover {
        background-color: #b91c1c;
    }

    .fw-btn-warning {
        background-color: #ca8a04;
        color: #ffffff;
        padding: 6px 12px;
        font-size: 12px;
    }

    .fw-btn-warning:hover {
        background-color: #a16207;
    }

    .fw-btn-outline {
        background-color: transparent;
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .fw-btn-outline:hover {
        background-color: #f1f5f9;
    }

    .fw-table-responsive {
        width: 100%;
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .fw-table-custom {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }

    .fw-table-custom th {
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
        padding: 14px 18px;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
    }

    .fw-table-custom td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .fw-table-custom tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }

    .fw-table-custom tbody tr:hover {
        background-color: #f0fdf4;
    }

    .fw-form-group {
        margin-bottom: 20px;
    }

    .fw-form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }

    .fw-form-control {
        width: 100%;
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        color: #1e293b;
        background-color: #ffffff;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    .fw-form-control:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
</style>

<div class="fw-master-container">
    <div class="fw-master-card">

        <?php if ($action === 'tampil'): ?>
            <div class="fw-master-header">
                <h2>Manajemen Pelanggan Laundry</h2>
                <div style="display: flex; gap: 10px;">
                    <a href="index.php?page=print_pelanggan" target="_blank" class="fw-btn fw-btn-outline">🖨️ Cetak PDF</a>
                    <a href="index.php?page=pelanggan&action=tambah" class="fw-btn fw-btn-primary">➕ Tambah Pelanggan Baru</a>
                </div>
            </div>

            <div class="fw-table-responsive">
                <table class="fw-table-custom">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pelanggan (Kode User)</th>
                            <th>Nama Pelanggan</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data = $pelangganObj->ambilSemuaPelanggan();
                        $no = 1;
                        foreach ($data as $row) :
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><span style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;"><?= htmlspecialchars($row['id_pelanggan']); ?></span></td>
                            <td><strong><?= htmlspecialchars($row['nama_pelanggan']); ?></strong></td>
                            <td><?= htmlspecialchars($row['no_telp']); ?></td>
                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="index.php?page=pelanggan&action=edit&id=<?= $row['id_pelanggan']; ?>" class="fw-btn fw-btn-warning">✏️ Edit</a>
                                    <a href="index.php?page=pelanggan&action=hapus&id=<?= $row['id_pelanggan']; ?>" class="fw-btn fw-btn-danger" onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">🗑️ Hapus</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic; padding: 30px;">Belum ada data pelanggan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($action === 'tambah'): ?>
            <div class="fw-master-header">
                <h2>Tambah Pelanggan Baru</h2>
            </div>

            <?php if(isset($error)) echo "<p style='color:#dc2626; font-weight:600;'>$error</p>"; ?>

            <form method="post">
                <div class="fw-form-group">
                    <label>Pilih Akun User Pelanggan (Role Client)</label>
                    <select name="id_user" class="fw-form-control" required>
                        <option value="">-- Pilih Akun --</option>
                        <?php
                        $calon = $pelangganObj->ambilCalonPelanggan();
                        foreach ($calon as $u) {
                            echo "<option value='{$u['id_user']}'>{$u['nama']} ({$u['id_user']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="fw-form-group">
                    <label>Nama Lengkap Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="fw-form-control" placeholder="Masukkan nama pelanggan" required>
                </div>
                <div class="fw-form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="no_telp" class="fw-form-control" placeholder="Contoh: 08123456789" required>
                </div>
                <div class="fw-form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" class="fw-form-control" rows="4" placeholder="Masukkan alamat lengkap rumah pelanggan" required></textarea>
                </div>
                
                <div style="margin-top: 24px;">
                    <button type="submit" class="fw-btn fw-btn-success">💾 Simpan Data</button>
                    <a href="index.php?page=pelanggan" class="fw-btn fw-btn-outline">Batal</a>
                </div>
            </form>

        <?php elseif ($action === 'edit'): ?>
            <div class="fw-master-header">
                <h2>Edit Data Pelanggan</h2>
            </div>

            <?php 
            $id = $_GET['id'] ?? null;
            $row = $pelangganObj->ambilPelangganSajaBerdasarkanId($id);
            if (!$row) {
                echo "<p style='color:#dc2626; font-weight:600;'>Data tidak ditemukan!</p>";
                echo "<a href='index.php?page=pelanggan' class='fw-btn fw-btn-outline'>Kembali</a>";
            } else {
            ?>
                <?php if(isset($error)) echo "<p style='color:#dc2626; font-weight:600;'>$error</p>"; ?>
                
                <form method="post">
                    <div class="fw-form-group">
                        <label>Nama Lengkap Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="fw-form-control" value="<?= htmlspecialchars($row['nama_pelanggan']); ?>" required>
                    </div>
                    <div class="fw-form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp" class="fw-form-control" value="<?= htmlspecialchars($row['no_telp']); ?>" required>
                    </div>
                    <div class="fw-form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="fw-form-control" rows="4" required><?= htmlspecialchars($row['alamat']); ?></textarea>
                    </div>
                    
                    <div style="margin-top: 24px;">
                        <button type="submit" class="fw-btn fw-btn-primary">🔄 Update Data</button>
                        <a href="index.php?page=pelanggan" class="fw-btn fw-btn-outline">Batal</a>
                    </div>
                </form>
            <?php 
            }
        endif; 
        ?>

    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>