<?php
if (!defined('BASE_URL') && !isset($page)) {
    header("Location: ../index.php");
    exit;
}

$db = Database::getInstance()->getConnection();

function fwDashboardValue(PDO $db, string $sql, array $params = [])
{
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        return 0;
    }
}

function fwDashboardRows(PDO $db, string $sql, array $params = [])
{
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

$totalPelanggan = (int) fwDashboardValue($db, "SELECT COUNT(*) FROM tbl_pelanggan");
$totalPaket = (int) fwDashboardValue($db, "SELECT COUNT(*) FROM tbl_paket");
$totalTransaksi = (int) fwDashboardValue($db, "SELECT COUNT(*) FROM tbl_transaksi");
$totalProses = (int) fwDashboardValue($db, "SELECT COUNT(*) FROM tbl_transaksi WHERE status_laundry IN ('proses', 'proses cuci')");
$totalSelesai = (int) fwDashboardValue($db, "SELECT COUNT(*) FROM tbl_transaksi WHERE status_laundry IN ('selesai', 'diambil')");
$totalBelumLunas = (int) fwDashboardValue($db, "SELECT COUNT(*) FROM tbl_transaksi WHERE status_bayar <> 'lunas'");
$totalLunas = (int) fwDashboardValue($db, "SELECT COUNT(*) FROM tbl_transaksi WHERE status_bayar = 'lunas'");
$totalPendapatan = (float) fwDashboardValue(
    $db,
    "SELECT COALESCE(SUM(d.subtotal), 0)
     FROM tbl_detail_transaksi d
     JOIN tbl_transaksi t ON t.id_transaksi = d.id_transaksi
     WHERE t.status_bayar = 'lunas'"
);
$pendapatanBulanIni = (float) fwDashboardValue(
    $db,
    "SELECT COALESCE(SUM(d.subtotal), 0)
     FROM tbl_detail_transaksi d
     JOIN tbl_transaksi t ON t.id_transaksi = d.id_transaksi
     WHERE t.status_bayar = 'lunas'
       AND MONTH(t.tgl_terima) = MONTH(CURRENT_DATE())
       AND YEAR(t.tgl_terima) = YEAR(CURRENT_DATE())"
);

$transaksiTerbaru = fwDashboardRows(
    $db,
    "SELECT
        t.id_transaksi,
        t.tgl_terima,
        t.status_laundry,
        t.status_bayar,
        p.nama_pelanggan,
        COALESCE(SUM(d.subtotal), 0) AS total
     FROM tbl_transaksi t
     JOIN tbl_pelanggan p ON p.id_pelanggan = t.id_pelanggan
     LEFT JOIN tbl_detail_transaksi d ON d.id_transaksi = t.id_transaksi
     GROUP BY
        t.id_transaksi,
        t.tgl_terima,
        t.status_laundry,
        t.status_bayar,
        p.nama_pelanggan
     ORDER BY t.tgl_terima DESC
     LIMIT 5"
);

$persenSelesai = $totalTransaksi > 0 ? min(100, round(($totalSelesai / $totalTransaksi) * 100)) : 0;
$persenLunas = $totalTransaksi > 0 ? min(100, round(($totalLunas / $totalTransaksi) * 100)) : 0;
$persenProses = $totalTransaksi > 0 ? min(100, round(($totalProses / $totalTransaksi) * 100)) : 0;
?>

<?php include 'views/layouts/sidebar.php'; ?>

<style>
    .fw-dashboard {
        --dash-blue: #2563eb;
        --dash-blue-dark: #1e3a8a;
        --dash-blue-soft: #eff6ff;
        --dash-cyan: #0891b2;
        --dash-amber: #d97706;
        --dash-green: #16a34a;
        color: #0f172a;
    }

    .fw-dashboard-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
        margin-bottom: 24px;
    }

    .fw-dashboard-hero h1 {
        color: #0f172a;
        font-size: 30px;
        line-height: 1.15;
        margin: 0 0 6px;
        letter-spacing: 0;
        font-weight: 800;
    }

    .fw-dashboard-hero p {
        color: #475569;
        margin: 0;
        font-size: 15px;
    }

    .fw-dashboard-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
    }

    .fw-dash-btn {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0 14px;
        background: #ffffff;
        color: #334155;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .fw-dash-btn.primary {
        background: var(--dash-blue);
        border-color: var(--dash-blue);
        color: #ffffff;
    }

    .fw-metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .fw-metric-card {
        min-height: 164px;
        display: flex;
        align-items: center;
        gap: 18px;
        border-radius: 8px;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        padding: 24px 28px;
    }

    .fw-metric-card:nth-child(2) {
        background: #eef8ff;
        border-color: #bae6fd;
    }

    .fw-metric-card:nth-child(3) {
        background: #f0f9ff;
        border-color: #bae6fd;
    }

    .fw-metric-card:nth-child(4) {
        background: #fff7ed;
        border-color: #fed7aa;
    }

    .fw-metric-icon {
        width: 50px;
        height: 50px;
        display: grid;
        place-items: center;
        border-radius: 8px;
        background: var(--dash-blue);
        color: #ffffff;
        font-size: 16px;
        font-weight: 800;
        flex: 0 0 50px;
        box-shadow: 0 14px 24px rgba(37, 99, 235, 0.22);
    }

    .fw-metric-card:nth-child(2) .fw-metric-icon {
        background: var(--dash-cyan);
        box-shadow: 0 14px 24px rgba(8, 145, 178, 0.2);
    }

    .fw-metric-card:nth-child(4) .fw-metric-icon {
        background: var(--dash-amber);
        box-shadow: 0 14px 24px rgba(217, 119, 6, 0.18);
    }

    .fw-metric-copy span {
        display: block;
        color: #1e293b;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .fw-metric-copy strong {
        display: block;
        color: #020617;
        font-size: 30px;
        line-height: 1;
        font-weight: 800;
    }

    .fw-metric-copy small {
        display: block;
        color: #2563eb;
        margin-top: 8px;
        font-size: 13px;
        font-weight: 600;
    }

    .fw-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .fw-summary-card,
    .fw-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }

    .fw-summary-card {
        min-height: 178px;
        padding: 28px 30px;
        display: grid;
        align-content: space-between;
        gap: 28px;
    }

    .fw-summary-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .fw-summary-head strong {
        display: block;
        color: #020617;
        font-size: 28px;
        line-height: 1;
        margin-bottom: 14px;
        font-weight: 800;
    }

    .fw-summary-head span {
        color: #0f172a;
        font-size: 16px;
        font-weight: 600;
    }

    .fw-summary-dot {
        width: 28px;
        height: 28px;
        border: 1px solid #93c5fd;
        border-radius: 6px;
        background: #eff6ff;
    }

    .fw-summary-foot {
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 18px;
        color: #334155;
        font-size: 14px;
    }

    .fw-summary-foot b {
        color: var(--dash-green);
    }

    .fw-summary-foot a {
        color: var(--dash-blue);
        font-weight: 700;
        text-decoration: underline;
    }

    .fw-dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
        gap: 20px;
    }

    .fw-panel-header {
        min-height: 76px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 0 30px;
        border-bottom: 1px solid #e2e8f0;
    }

    .fw-panel-header h2 {
        color: #0f172a;
        font-size: 20px;
        margin: 0;
        font-weight: 800;
    }

    .fw-panel-header a,
    .fw-panel-header span {
        color: #2563eb;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .fw-panel-body {
        padding: 24px 30px 30px;
    }

    .fw-recent-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .fw-recent-table th {
        background: #f8fafc;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 14px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .fw-recent-table td {
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        padding: 14px;
        vertical-align: middle;
    }

    .fw-recent-table tr:last-child td {
        border-bottom: 0;
    }

    .fw-recent-table strong {
        color: #0f172a;
        display: block;
        margin-bottom: 3px;
    }

    .fw-status {
        display: inline-flex;
        align-items: center;
        min-height: 26px;
        border-radius: 999px;
        padding: 0 10px;
        font-size: 12px;
        font-weight: 800;
        text-transform: capitalize;
    }

    .fw-status.blue {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .fw-status.green {
        background: #dcfce7;
        color: #15803d;
    }

    .fw-status.orange {
        background: #ffedd5;
        color: #c2410c;
    }

    .fw-info-stack {
        display: grid;
        gap: 20px;
    }

    .fw-progress-item label {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        color: #334155;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 9px;
    }

    .fw-progress-track {
        height: 10px;
        overflow: hidden;
        border-radius: 999px;
        background: #e2e8f0;
    }

    .fw-progress-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #2563eb, #60a5fa);
    }

    .fw-shortcuts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 6px;
    }

    .fw-shortcut {
        min-height: 72px;
        display: grid;
        align-content: center;
        gap: 4px;
        border-radius: 8px;
        border: 1px solid #dbeafe;
        background: #eff6ff;
        padding: 14px;
        text-decoration: none;
    }

    .fw-shortcut strong {
        color: #1e3a8a;
        font-size: 14px;
    }

    .fw-shortcut span {
        color: #64748b;
        font-size: 12px;
    }

    .fw-empty-state {
        color: #64748b;
        text-align: center;
        padding: 38px 12px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
    }

    @media (max-width: 1180px) {
        .fw-metric-grid,
        .fw-summary-grid,
        .fw-dashboard-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .fw-dashboard-grid .fw-panel:first-child {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 760px) {
        .fw-dashboard-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .fw-dashboard-actions {
            justify-content: flex-start;
            width: 100%;
        }

        .fw-dash-btn {
            flex: 1;
        }

        .fw-metric-grid,
        .fw-summary-grid,
        .fw-dashboard-grid,
        .fw-shortcuts {
            grid-template-columns: 1fr;
        }

        .fw-panel-header,
        .fw-panel-body,
        .fw-summary-card,
        .fw-metric-card {
            padding-left: 18px;
            padding-right: 18px;
        }
    }
</style>

<div class="fw-dashboard">
    <section class="fw-dashboard-hero">
        <div>
            <h1>Dashboard</h1>
            <p>Ringkasan data operasional laundry hari ini.</p>
        </div>
        <div class="fw-dashboard-actions">
            <a class="fw-dash-btn" href="index.php?page=pelanggan&action=tambah">Tambah Pelanggan</a>
            <a class="fw-dash-btn primary" href="index.php?page=transaksi&action=tambah">Buat Transaksi</a>
        </div>
    </section>

    <section class="fw-metric-grid" aria-label="Ringkasan utama">
        <article class="fw-metric-card">
            <span class="fw-metric-icon">C</span>
            <div class="fw-metric-copy">
                <span>Total Pelanggan</span>
                <strong><?= number_format($totalPelanggan); ?></strong>
                <small>Data pelanggan terdaftar</small>
            </div>
        </article>

        <article class="fw-metric-card">
            <span class="fw-metric-icon">T</span>
            <div class="fw-metric-copy">
                <span>Cucian Diproses</span>
                <strong><?= number_format($totalProses); ?></strong>
                <small>Nota masih berjalan</small>
            </div>
        </article>

        <article class="fw-metric-card">
            <span class="fw-metric-icon">P</span>
            <div class="fw-metric-copy">
                <span>Paket Cucian</span>
                <strong><?= number_format($totalPaket); ?></strong>
                <small>Pilihan layanan aktif</small>
            </div>
        </article>

        <article class="fw-metric-card">
            <span class="fw-metric-icon">B</span>
            <div class="fw-metric-copy">
                <span>Belum Lunas</span>
                <strong><?= number_format($totalBelumLunas); ?></strong>
                <small>Perlu follow up kasir</small>
            </div>
        </article>
    </section>

    <section class="fw-summary-grid" aria-label="Ringkasan tambahan">
        <article class="fw-summary-card">
            <div class="fw-summary-head">
                <div>
                    <strong><?= number_format($totalTransaksi); ?></strong>
                    <span>Total Transaksi</span>
                </div>
                <span class="fw-summary-dot"></span>
            </div>
            <div class="fw-summary-foot">
                <span><b><?= $persenSelesai; ?>%</b> selesai</span>
                <a href="index.php?page=transaksi">View</a>
            </div>
        </article>

        <article class="fw-summary-card">
            <div class="fw-summary-head">
                <div>
                    <strong>Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></strong>
                    <span>Pendapatan Lunas</span>
                </div>
                <span class="fw-summary-dot"></span>
            </div>
            <div class="fw-summary-foot">
                <span><b>Rp <?= number_format($pendapatanBulanIni, 0, ',', '.'); ?></b> bulan ini</span>
                <a href="index.php?page=print_transaksi" target="_blank">View</a>
            </div>
        </article>

        <article class="fw-summary-card">
            <div class="fw-summary-head">
                <div>
                    <strong><?= number_format($totalSelesai); ?></strong>
                    <span>Cucian Selesai</span>
                </div>
                <span class="fw-summary-dot"></span>
            </div>
            <div class="fw-summary-foot">
                <span><b><?= $persenProses; ?>%</b> masih proses</span>
                <a href="index.php?page=transaksi">View</a>
            </div>
        </article>
    </section>

    <section class="fw-dashboard-grid">
        <article class="fw-panel">
            <div class="fw-panel-header">
                <h2>Transaksi Terbaru</h2>
                <a href="index.php?page=transaksi">Lihat semua</a>
            </div>
            <div class="fw-panel-body">
                <?php if (!empty($transaksiTerbaru)): ?>
                    <div style="overflow-x:auto;">
                        <table class="fw-recent-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pelanggan</th>
                                    <th>Status</th>
                                    <th>Pembayaran</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksiTerbaru as $row): ?>
                                    <?php
                                    $statusLaundry = strtolower($row['status_laundry']);
                                    $statusBayar = strtolower($row['status_bayar']);
                                    $statusClass = $statusLaundry === 'proses' || $statusLaundry === 'proses cuci' ? 'orange' : 'green';
                                    $bayarClass = $statusBayar === 'lunas' ? 'green' : 'blue';
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['id_transaksi']); ?></strong><span><?= date('d M Y', strtotime($row['tgl_terima'])); ?></span></td>
                                        <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                                        <td><span class="fw-status <?= $statusClass; ?>"><?= htmlspecialchars($row['status_laundry']); ?></span></td>
                                        <td><span class="fw-status <?= $bayarClass; ?>"><?= htmlspecialchars($row['status_bayar']); ?></span></td>
                                        <td>Rp <?= number_format((float) $row['total'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="fw-empty-state">Belum ada transaksi laundry.</div>
                <?php endif; ?>
            </div>
        </article>

        <article class="fw-panel">
            <div class="fw-panel-header">
                <h2>Overall Information</h2>
                <span>Bulan ini</span>
            </div>
            <div class="fw-panel-body">
                <div class="fw-info-stack">
                    <div class="fw-progress-item">
                        <label><span>Transaksi selesai</span><span><?= $persenSelesai; ?>%</span></label>
                        <div class="fw-progress-track"><div class="fw-progress-fill" style="width: <?= $persenSelesai; ?>%;"></div></div>
                    </div>
                    <div class="fw-progress-item">
                        <label><span>Pembayaran lunas</span><span><?= $persenLunas; ?>%</span></label>
                        <div class="fw-progress-track"><div class="fw-progress-fill" style="width: <?= $persenLunas; ?>%;"></div></div>
                    </div>
                    <div class="fw-progress-item">
                        <label><span>Cucian diproses</span><span><?= $persenProses; ?>%</span></label>
                        <div class="fw-progress-track"><div class="fw-progress-fill" style="width: <?= $persenProses; ?>%;"></div></div>
                    </div>

                    <div class="fw-shortcuts">
                        <a class="fw-shortcut" href="index.php?page=paket">
                            <strong>Kelola Paket</strong>
                            <span>Harga dan jenis layanan</span>
                        </a>
                        <a class="fw-shortcut" href="index.php?page=pelanggan">
                            <strong>Kelola Pelanggan</strong>
                            <span>Kontak dan alamat</span>
                        </a>
                        <a class="fw-shortcut" href="index.php?page=transaksi">
                            <strong>Update Status</strong>
                            <span>Laundry dan pembayaran</span>
                        </a>
                        <a class="fw-shortcut" href="index.php?page=setting">
                            <strong>Profil Akun</strong>
                            <span>Nama, password, foto</span>
                        </a>
                    </div>
                </div>
            </div>
        </article>
    </section>
</div>

<?php include 'views/layouts/footer.php'; ?>
