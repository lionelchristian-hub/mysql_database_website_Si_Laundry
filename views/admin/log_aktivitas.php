<?php
if (!defined('BASE_URL') && !isset($page)) {
    header("Location: index.php");
    exit;
}

$db = Database::getInstance()->getConnection();
$logObj = new LogAktivitas();

$logs = $logObj->getLatestLogs(50);
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
</style>

<div class="fw-master-container">
    <div class="fw-master-card">
        <div class="fw-master-header">
            <h2>Log Aktivitas</h2>
        </div>

        <div class="fw-table-responsive">
            <table class="fw-table-custom">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td><?= date('d/m/Y H:i:s', strtotime($log['waktu'])); ?></td>
                        <td><strong><?= htmlspecialchars($log['nama'] ?? 'Unknown'); ?></strong></td>
                        <td><?= htmlspecialchars($log['aktivitas']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic; padding: 30px;">Belum ada log aktivitas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>