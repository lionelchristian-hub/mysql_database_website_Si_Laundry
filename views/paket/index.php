<?php
require_once 'classes/Paket.php';
$paketObj = new Paket();

$action = $_GET['action'] ?? 'tampil';

if ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($paketObj->tambah($_POST['nama_paket'], $_POST['jenis'], $_POST['harga_per_unit'])) {
        header("Location: index.php?page=paket"); 
        exit;
    } else {
        $error = "Gagal menambah data paket!";
    }
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_GET['id'] ?? null;
    if ($paketObj->ubahPaket($id, $_POST['nama_paket'], $_POST['jenis'], $_POST['harga_per_unit'])) {
        header("Location: index.php?page=paket");
        exit;
    } else {
        $error = "Gagal memperbarui data!";
    }
}

if ($action === 'hapus') {
    $id = $_GET['id'] ?? null;
    $paketObj->hapusPaket($id);
    header("Location: index.php?page=paket");
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

    .fw-action-buttons {
        display: flex;
        gap: 12px;
    }

    .fw-btn {
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.15s ease;
        border: none;
        cursor: pointer;
    }

    .fw-btn-primary {
        background-color: #2563eb;
        color: #ffffff;
        border: 1px solid #2563eb;
    }
    .fw-btn-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
    }

    .fw-btn-success {
        background-color: #16a34a;
        color: #ffffff;
        border: 1px solid #16a34a;
    }
    .fw-btn-success:hover {
        background-color: #15803d;
        transform: translateY(-1px);
    }

    .fw-btn-outline {
        background-color: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .fw-btn-outline:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
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

    .fw-badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        text-transform: capitalize;
    }
    .fw-badge-kiloan {
        background-color: #e0f2fe;
        color: #0369a1;
    }
    .fw-badge-satuan {
        background-color: #fef3c7;
        color: #b45309;
    }

    .fw-link-edit {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
        margin-right: 12px;
    }
    .fw-link-edit:hover { text-decoration: underline; }

    .fw-link-delete {
        color: #dc2626;
        text-decoration: none;
        font-weight: 600;
    }
    .fw-link-delete:hover { text-decoration: underline; }

    .fw-form-group {
        margin-bottom: 20px;
    }
    .fw-form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .fw-form-control {
        width: 100%;
        max-width: 500px;
        padding: 10px 14px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        outline: none;
        background-color: #ffffff;
        transition: border-color 0.15s ease;
    }
    .fw-form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
</style>

<div class="fw-master-container">
    <div class="fw-master-card">

<?php
switch ($action) {
    case 'tampil':
        $dataPaket = $paketObj->ambilSemuaPaket();
        ?>
        <div class="fw-master-header">
            <h2>Data Master Paket Cucian</h2>
            <div class="fw-action-buttons">
                <a href="index.php?page=print_paket" target="_blank" class="fw-btn fw-btn-outline">
                    Cetak PDF
                </a>
                <a href="index.php?page=paket&action=tambah" class="fw-btn fw-btn-primary">
                    Tambah Paket Baru
                </a>
            </div>
        </div>

        <div class="fw-table-responsive">
            <table class="fw-table-custom">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>Jenis</th>
                        <th>Harga</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataPaket as $row) : ?>
                    <tr>
                        <td><strong><?= $row['nama_paket']; ?></strong></td>
                        <td>
                            <?php if ($row['jenis'] == 'kiloan'): ?>
                                <span class="fw-badge fw-badge-kiloan">Kiloan</span>
                            <?php else: ?>
                                <span class="fw-badge fw-badge-satuan">Satuan</span>
                            <?php endif; ?>
                        </td>
                        <td>Rp <?= number_format($row['harga_per_unit']); ?></td>
                        <td>
                            <a href="index.php?page=paket&action=edit&id=<?= $row['id_paket']; ?>" class="fw-link-edit">Edit</a>
                            <a href="index.php?page=paket&action=hapus&id=<?= $row['id_paket']; ?>" class="fw-link-delete" onclick="return confirm('Yakin ingin menghapus paket ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($dataPaket)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8; font-style: italic; padding: 30px;">Belum ada data paket cucian.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        break;

    case 'tambah':
        ?>
        <div class="fw-master-header">
            <h2>Tambah Paket Cucian Baru</h2>
        </div>
        
        <?php if(isset($error)) echo "<p style='color:#dc2626; font-weight:600;'>$error</p>"; ?>

        <form method="post">
            <div class="fw-form-group">
                <label>Nama Paket</label>
                <input type="text" name="nama_paket" class="fw-form-control" placeholder="Contoh: Paket Kilat Express" required>
            </div>
            
            <div class="fw-form-group">
                <label>Jenis Paket</label>
                <select name="jenis" class="fw-form-control" required>
                    <option value="kiloan">Kiloan</option>
                    <option value="satuan">Satuan</option>
                </select>
            </div>
            
            <div class="fw-form-group">
                <label>Harga per Unit (Rp)</label>
                <input type="number" name="harga_per_unit" class="fw-form-control" placeholder="Contoh: 10000" required>
            </div>
            
            <div style="margin-top: 24px;">
                <button type="submit" class="fw-btn fw-btn-success">Simpan Paket</button>
                <a href="index.php?page=paket" class="fw-btn fw-btn-outline" style="margin-left: 8px;">Kembali</a>
            </div>
        </form>
        <?php
        break;

    case 'edit':
        $id = $_GET['id'] ?? null;
        $row = $paketObj->ambilPaketBerdasarkanId($id);
        ?>
        <div class="fw-master-header">
            <h2>Edit Paket Cucian</h2>
        </div>
        
        <?php if(isset($error)) echo "<p style='color:#dc2626; font-weight:600;'>$error</p>"; ?>

        <form method="post">
            <div class="fw-form-group">
                <label>Nama Paket</label>
                <input type="text" name="nama_paket" class="fw-form-control" value="<?= $row['nama_paket']; ?>" required>
            </div>
            
            <div class="fw-form-group">
                <label>Jenis Paket</label>
                <select name="jenis" class="fw-form-control" required>
                    <option value="kiloan" <?= $row['jenis'] == 'kiloan' ? 'selected' : ''; ?>>Kiloan</option>
                    <option value="satuan" <?= $row['jenis'] == 'satuan' ? 'selected' : ''; ?>>Satuan</option>
                </select>
            </div>
            
            <div class="fw-form-group">
                <label>Harga per Unit (Rp)</label>
                <input type="number" name="harga_per_unit" class="fw-form-control" value="<?= $row['harga_per_unit']; ?>" required>
            </div>
            
            <div style="margin-top: 24px;">
                <button type="submit" class="fw-btn fw-btn-primary">Update Paket</button>
                <a href="index.php?page=paket" class="fw-btn fw-btn-outline" style="margin-left: 8px;">Batal</a>
            </div>
        </form>
        <?php
        break;
}
?>

    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
