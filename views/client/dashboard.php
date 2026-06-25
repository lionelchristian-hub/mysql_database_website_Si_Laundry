<?php


if (!defined('BASE_URL') && !isset($page)) {
    header("Location: index.php");
    exit;
}

$db = Database::getInstance()->getConnection();
$user_id = Session::get('user_id');

$stmt = $db->prepare("
    SELECT DISTINCT p.* FROM tbl_pelanggan p
    JOIN tbl_transaksi t ON p.id_pelanggan = t.id_pelanggan
    WHERE t.id_user = ?
    LIMIT 1
");
$stmt->execute([$user_id]);
$pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
    SELECT t.*, p.nama_pelanggan
    FROM tbl_transaksi t
    JOIN tbl_pelanggan p ON t.id_pelanggan = p.id_pelanggan
    WHERE p.nama_pelanggan = ? 
    ORDER BY t.tgl_terima DESC
");
if ($pelanggan) {
    $stmt->execute([$pelanggan['nama_pelanggan']]);
    $transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $transaksi = [];
}

$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status_laundry = 'proses' THEN 1 ELSE 0 END) as proses,
        SUM(CASE WHEN status_laundry = 'selesai' THEN 1 ELSE 0 END) as selesai
    FROM tbl_transaksi t
    JOIN tbl_pelanggan p ON t.id_pelanggan = p.id_pelanggan
    WHERE p.nama_pelanggan = ?
");
if ($pelanggan) {
    $stmt->execute([$pelanggan['nama_pelanggan']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stats = ['total' => 0, 'proses' => 0, 'selesai' => 0];
}
?>

<?php include 'views/layouts/sidebar.php'; ?>

<style>
    .client-dashboard {
        padding: 32px;
        background-color: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    .client-header {
        margin-bottom: 32px;
    }
    
    .client-header h1 {
        margin: 0 0 8px 0;
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .client-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    
    .stat-card h3 {
        margin: 0 0 8px 0;
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
    }
    
    .stat-card .value {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .transaksi-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    
    .transaksi-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .transaksi-table th {
        background: #f8fafc;
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .transaksi-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
        color: #475569;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-badge.proses {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-badge.selesai {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-badge.diambil {
        background: #d1fae5;
        color: #065f46;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }
    
    .empty-state p {
        color: #64748b;
        font-size: 14px;
    }
</style>

<div class="client-dashboard">
    <div class="client-header">
        <h1>Dashboard Pelanggan</h1>
        <p>Pantau status laundry Anda</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Order</h3>
            <p class="value"><?php echo $stats['total'] ?? 0; ?></p>
        </div>
        <div class="stat-card">
            <h3>Proses</h3>
            <p class="value"><?php echo $stats['proses'] ?? 0; ?></p>
        </div>
        <div class="stat-card">
            <h3>Selesai</h3>
            <p class="value"><?php echo $stats['selesai'] ?? 0; ?></p>
        </div>
    </div>
    
    <div class="transaksi-table">
        <h2 style="padding: 16px 16px 0 16px; margin: 0; font-size: 18px; font-weight: 600;">Riwayat Order</h2>
        <?php if (count($transaksi) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No. Nota</th>
                    <th>Tanggal</th>
                    <th>Status Laundry</th>
                    <th>Status Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transaksi as $t): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($t['id_transaksi']); ?></strong></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($t['tgl_terima'])); ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($t['status_laundry']); ?>">
                            <?php echo htmlspecialchars($t['status_laundry']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge <?php echo strtolower($t['status_bayar']) === 'lunas' ? 'selesai' : 'proses'; ?>">
                            <?php echo htmlspecialchars($t['status_bayar']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <p>Belum ada order. Silakan pesan laundry melalui halaman landing.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
