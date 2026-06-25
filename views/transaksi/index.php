<?php
require_once 'classes/Transaksi.php';
require_once 'classes/Pelanggan.php';
require_once 'classes/Paket.php';

$transaksiObj = new Transaksi();
$action = $_GET['action'] ?? 'tampil';

if ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = Session::get('user_id'); 
    $id_pelanggan = $_POST['id_pelanggan'];
    $data_paket = $_POST['id_paket']; 
    $data_qty = $_POST['qty'];
    $data_harga = $_POST['harga']; 

    if ($transaksiObj->tambah($id_pelanggan, $id_user, $data_paket, $data_qty, $data_harga)) {
        header("Location: index.php?page=transaksi");
        exit;
    } else {
        $error = "Gagal menyimpan transaksi!";
    }
}

if ($action === 'edit_status' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_GET['id'] ?? null;
    if ($transaksiObj->ubahStatus($id, $_POST['status_laundry'], $_POST['status_bayar'])) {
        header("Location: index.php?page=transaksi");
        exit;
    } else {
        $error = "Gagal memperbarui status transaksi!";
    }
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
    
    .fw-badge-proses { background-color: #fef3c7; color: #d97706; }
    .fw-badge-selesai { background-color: #e0f2fe; color: #0369a1; }
    .fw-badge-diambil { background-color: #dcfce7; color: #15803d; }
    
    .fw-badge-belum { background-color: #fee2e2; color: #b91c1c; }
    .fw-badge-lunas { background-color: #dcfce7; color: #15803d; }

    .fw-link-action {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }
    .fw-link-action:hover {
        text-decoration: underline;
    }

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
        box-sizing: border-box;
    }
    .fw-form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .fw-form-row-paket {
        display: flex;
        gap: 12px;
        max-width: 650px;
        align-items: center;
        flex-wrap: wrap;
        background-color: #f8fafc;
        padding: 14px;
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
    }
    .fw-form-row-paket > .fw-col-select { flex: 2; min-width: 200px; }
    .fw-form-row-paket > .fw-col-input { flex: 1; min-width: 120px; }
</style>

<div class="fw-master-container">
    <div class="fw-master-card">

<?php
switch ($action) {
    case 'tampil':
        $dataTransaksi = $transaksiObj->ambilSemuaTransaksi();
        ?>
        <div class="fw-master-header">
            <h2>Data Transaksi Laundry</h2>
            <div class="fw-action-buttons">
                <a href="index.php?page=print_transaksi" target="_blank" class="fw-btn fw-btn-outline">
                    Cetak Laporan PDF
                </a>
                <a href="index.php?page=transaksi&action=tambah" class="fw-btn fw-btn-primary">
                    Buat Transaksi Baru
                </a>
            </div>
        </div>

        <div class="fw-table-responsive">
            <table class="fw-table-custom">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Status Laundry</th>
                        <th>Status Bayar</th>
                        <th>Kasir</th>
                        <th style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataTransaksi as $row) : ?>
                    <tr>
                        <td><code><?= $row['id_transaksi']; ?></code></td>
                        <td><?= $row['tgl_terima']; ?></td>
                        <td><strong><?= $row['nama_pelanggan']; ?></strong></td>
                        <td>
                            <?php 
                            $status_laundry = strtolower($row['status_laundry']);
                            if ($status_laundry == 'proses' || $status_laundry == 'proses cuci') {
                                echo '<span class="fw-badge fw-badge-proses">Proses Cuci</span>';
                            } elseif ($status_laundry == 'selesai') {
                                echo '<span class="fw-badge fw-badge-selesai">Selesai</span>';
                            } else {
                                echo '<span class="fw-badge fw-badge-diambil">Sudah Diambil</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if (strtolower($row['status_bayar']) == 'lunas'): ?>
                                <span class="fw-badge fw-badge-lunas">Sudah Lunas</span>
                            <?php else: ?>
                                <span class="fw-badge fw-badge-belum">Belum Lunas</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['nama_kasir']; ?></td>
                        <td>
                            <a href="index.php?page=transaksi&action=edit_status&id=<?= $row['id_transaksi']; ?>" class="fw-link-action">Ubah Status</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($dataTransaksi)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; font-style: italic; padding: 30px;">Belum ada riwayat transaksi laundry.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        break;

    case 'tambah':
        $pelangganObj = new Pelanggan();
        $paketObj = new Paket();
        
        $listPelanggan = $pelangganObj->ambilSemuaPelanggan();
        $listPaket = $paketObj->ambilSemuaPaket();
        ?>
        <div class="fw-master-header">
            <h2>Buat Transaksi Laundry Baru</h2>
        </div>
        
        <?php if(isset($error)) echo "<p style='color:#dc2626; font-weight:600;'>$error</p>"; ?>
        
        <form method="post">
            <div class="fw-form-group">
                <label>Pilih Pelanggan</label>
                <select name="id_pelanggan" class="fw-form-control" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php foreach ($listPelanggan as $p) : ?>
                        <option value="<?= $p['id_pelanggan']; ?>"><?= $p['nama_pelanggan']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="fw-form-group">
                <label>Pilih Paket Cucian & Jumlah</label>
                <div class="fw-form-row-paket">
                    <div class="fw-col-select">
                        <select name="id_paket[]" class="fw-form-control" style="max-width:100%;" required>
                            <?php foreach ($listPaket as $pkt) : ?>
                                <option value="<?= $pkt['id_paket']; ?>"><?= $pkt['nama_paket']; ?> (Rp<?= number_format($pkt['harga_per_unit']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="fw-col-input">
                        <input type="number" name="qty[]" step="0.1" class="fw-form-control" style="max-width:100%;" placeholder="Berat (Kg) / Qty" required>
                    </div>
                    <div class="fw-col-input">
                        <input type="number" name="harga[]" class="fw-form-control" style="max-width:100%;" placeholder="Harga Jual (Rp)" required>
                    </div>
                </div>
            </div>

            <div style="margin-top: 28px;">
                <button type="submit" class="fw-btn fw-btn-success">Simpan Transaksi</button>
                <a href="index.php?page=transaksi" class="fw-btn fw-btn-outline" style="margin-left: 8px;">Batal</a>
            </div>
        </form>
        <?php
        break;

    case 'edit_status':
        $id = $_GET['id'] ?? null;
        ?>
        <div class="fw-master-header">
            <h2>Ubah Status Transaksi (ID: <?= htmlspecialchars($id); ?>)</h2>
        </div>
        
        <?php if(isset($error)) echo "<p style='color:#dc2626; font-weight:600;'>$error</p>"; ?>
        
        <form method="post">
            <div class="fw-form-group">
                <label>Status Proses Laundry</label>
                <select name="status_laundry" class="fw-form-control">
                    <option value="proses">Proses Cuci</option>
                    <option value="selesai">Selesai (Siap Diambil)</option>
                    <option value="diambil">Sudah Diambil Pelanggan</option>
                </select>
            </div>
            
            <div class="fw-form-group">
                <label>Status Pembayaran</label>
                <select name="status_bayar" class="fw-form-control">
                    <option value="belum lunas">Belum Lunas</option>
                    <option value="lunas">Sudah Lunas</option>
                </select>
            </div>
            
            <div style="margin-top: 28px;">
                <button type="submit" class="fw-btn fw-btn-primary">Update Status</button>
                <a href="index.php?page=transaksi" class="fw-btn fw-btn-outline" style="margin-left: 8px;">Kembali</a>
            </div>
        </form>
        <?php
        break;
}
?>

    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
