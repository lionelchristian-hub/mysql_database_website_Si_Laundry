<?php

$db = Database::getInstance()->getConnection();
$transaksi = null;
$statusMessage = '';
$statusType = '';
$notaInput = isset($_POST['nomor_nota']) ? trim($_POST['nomor_nota']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($notaInput)) {
    try {
        $stmt = $db->prepare("
            SELECT 
                t.id_transaksi,
                t.tgl_terima,
                t.tgl_selesai,
                t.status_laundry,
                t.status_bayar,
                p.nama_pelanggan,
                p.no_telp,
                p.alamat,
                GROUP_CONCAT(pk.nama_paket SEPARATOR ', ') as paket_names,
                COALESCE(SUM(dt.subtotal), 0) as total_harga
            FROM tbl_transaksi t
            JOIN tbl_pelanggan p ON t.id_pelanggan = p.id_pelanggan
            LEFT JOIN tbl_detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
            LEFT JOIN tbl_paket pk ON dt.id_paket = pk.id_paket
            WHERE t.id_transaksi = ?
            GROUP BY t.id_transaksi
        ");
        $stmt->execute([$notaInput]);
        $transaksi = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($transaksi) {
            $statusType = 'success';
            $statusMessage = 'Data transaksi ditemukan ✓';
        } else {
            $statusType = 'error';
            $statusMessage = 'Nomor nota tidak ditemukan. Pastikan nomor sudah benar.';
        }
    } catch (PDOException $e) {
        $statusType = 'error';
        $statusMessage = 'Error database: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .antrean-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .antrean-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            padding-top: 20px;
        }

        .antrean-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .antrean-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .nav-back {
            color: white;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
            display: inline-block;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .nav-back:hover {
            opacity: 1;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .form-group input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input::placeholder {
            color: #94a3b8;
        }

        .form-group button {
            padding: 12px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .form-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        .form-hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 8px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .status-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-header-left h2 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .status-header-left p {
            font-size: 13px;
            opacity: 0.9;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-badge.proses {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .status-badge.selesai {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .status-badge.diambil {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .status-body {
            padding: 24px;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 16px;
            color: #1e293b;
            font-weight: 500;
        }

        .info-value.code {
            font-family: 'Monaco', 'Courier New', monospace;
            background: #f1f5f9;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
        }

        .timeline {
            margin-top: 20px;
        }

        .timeline-title {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .timeline-item {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            position: relative;
            padding-left: 0;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 18px;
            top: 40px;
            width: 2px;
            height: 20px;
            background: #e2e8f0;
        }

        .timeline-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .timeline-item.active .timeline-dot {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .timeline-content {
            flex: 1;
            padding-top: 4px;
        }

        .timeline-status {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .timeline-date {
            font-size: 12px;
            color: #94a3b8;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-action {
            flex: 1;
            padding: 12px 16px;
            text-align: center;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-action.primary {
            background: linear-gradient(135deg, #395cf8 0%, #4535bc 100%);
            color: white;
        }

        .btn-action.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(49, 82, 232, 0.3);
        }

        .btn-action.outline {
            background: white;
            color: #667eea;
            border: 2px solid #93a3eb;
        }

        .btn-action.outline:hover {
            background: #f0f4ff;
        }

        .live-update {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 10px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .live-indicator {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .empty-state-text {
            font-size: 14px;
            color: #64748b;
        }

        @media (max-width: 640px) {
            .antrean-header h1 {
                font-size: 24px;
            }

            .form-card {
                padding: 20px;
            }

            .form-group {
                flex-direction: column;
            }

            .status-header {
                flex-direction: column;
                text-align: center;
            }

            .status-header-left {
                margin-bottom: 12px;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="antrean-container">
        <a href="index.php?page=landing" class="nav-back">← Kembali ke Beranda</a>

        <div class="antrean-header">
            <h1>Cek Status Laundry</h1>
            <p>Pantau perkembangan cucian Anda secara real-time</p>
        </div>

        <div class="form-card">
            <form method="POST" id="statusForm">
                <div class="form-group">
                    <input 
                        type="text" 
                        name="nomor_nota" 
                        placeholder="Masukkan nomor nota (contoh: TRX-20260621-123)"
                        value="<?= htmlspecialchars($notaInput) ?>"
                        required
                    >
                    <button type="submit">Cek Status</button>
                </div>
                <p class="form-hint">
                    💡 Nomor nota diberikan saat Anda membuat order di halaman landing page.
                </p>
            </form>
        </div>

        <?php if ($statusMessage !== ''): ?>
            <div class="alert <?= $statusType ?>">
                <?= htmlspecialchars($statusMessage) ?>
            </div>
        <?php endif; ?>

        <?php if ($transaksi): ?>
            <div class="status-card">
                <div class="status-header">
                    <div class="status-header-left">
                        <h2><?= htmlspecialchars($transaksi['id_transaksi']) ?></h2>
                        <p><?= htmlspecialchars($transaksi['nama_pelanggan']) ?></p>
                    </div>
                    <span class="status-badge <?= strtolower($transaksi['status_laundry']) ?>">
                        <?php 
                        $status = strtolower($transaksi['status_laundry']);
                        if ($status === 'proses' || $status === 'proses cuci') {
                            echo '⏳ Sedang Diproses';
                        } elseif ($status === 'selesai') {
                            echo '✅ Siap Diambil';
                        } else {
                            echo '📦 Sudah Diambil';
                        }
                        ?>
                    </span>
                </div>

                <div class="status-body">
                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">Pelanggan</span>
                            <span class="info-value"><?= htmlspecialchars($transaksi['nama_pelanggan']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Telepon</span>
                            <span class="info-value"><?= htmlspecialchars($transaksi['no_telp']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Paket Laundry</span>
                            <span class="info-value"><?= htmlspecialchars($transaksi['paket_names'] ?? 'N/A') ?></span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">Tanggal Terima</span>
                            <span class="info-value"><?= date('d M Y H:i', strtotime($transaksi['tgl_terima'])) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total Harga</span>
                            <span class="info-value">Rp <?= number_format((float) $transaksi['total_harga'], 0, ',', '.') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status Bayar</span>
                            <span class="info-value" style="color: <?= strtolower($transaksi['status_bayar']) === 'lunas' ? '#10b981' : '#f59e0b' ?>">
                                <?= strtolower($transaksi['status_bayar']) === 'lunas' ? '✅ Sudah Lunas' : '⏳ Belum Lunas' ?>
                            </span>
                        </div>
                    </div>

                    <div class="timeline">
                        <div class="timeline-title">📊 Tahap Proses</div>
                        <div class="timeline-item <?= strtolower($transaksi['status_laundry']) !== 'proses' ? 'active' : '' ?>">
                            <div class="timeline-dot">1</div>
                            <div class="timeline-content">
                                <div class="timeline-status">Pesanan Diterima</div>
                                <div class="timeline-date"><?= date('d M Y H:i', strtotime($transaksi['tgl_terima'])) ?></div>
                            </div>
                        </div>

                        <div class="timeline-item <?= strtolower($transaksi['status_laundry']) === 'proses' || strtolower($transaksi['status_laundry']) === 'proses cuci' ? 'active' : '' ?>">
                            <div class="timeline-dot">2</div>
                            <div class="timeline-content">
                                <div class="timeline-status">Sedang Diproses</div>
                                <div class="timeline-date">
                                    <?php
                                    if (strtolower($transaksi['status_laundry']) === 'proses' || strtolower($transaksi['status_laundry']) === 'proses cuci') {
                                        echo 'Dalam antrian...';
                                    } else {
                                        echo 'Selesai pada ' . date('d M Y H:i', strtotime($transaksi['tgl_selesai']));
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item <?= strtolower($transaksi['status_laundry']) !== 'proses' && strtolower($transaksi['status_laundry']) !== 'proses cuci' ? 'active' : '' ?>">
                            <div class="timeline-dot">3</div>
                            <div class="timeline-content">
                                <div class="timeline-status">Siap Diambil</div>
                                <div class="timeline-date">
                                    <?php
                                    if (strtolower($transaksi['status_laundry']) === 'selesai') {
                                        echo 'Menunggu pengambilan...';
                                    } else {
                                        echo 'Sudah diambil';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button class="btn-action outline" onclick="location.reload()">🔄 Segarkan</button>
                        <a href="index.php?page=landing" class="btn-action primary">🏠 Kembali ke Beranda</a>
                    </div>

                    <div class="live-update">
                        <span class="live-indicator"></span>
                        Data diperbarui otomatis setiap 30 detik
                    </div>
                </div>
            </div>
        <?php elseif ($statusMessage === ''): ?>
            <div class="form-card">
                <div class="empty-state">
                    <div class="empty-state-icon">🔍</div>
                    <div class="empty-state-title">Cari Nomor Nota Anda</div>
                    <p class="empty-state-text">Masukkan nomor nota di atas untuk melihat status laundry Anda secara real-time</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        <?php if ($transaksi): ?>
        setInterval(() => {
            document.getElementById('statusForm').submit();
        }, 30000); 
        <?php endif; ?>
    </script>
</body>
</html>
