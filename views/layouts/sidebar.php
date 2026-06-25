<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? '';
$userName = Session::get('nama') ?: 'User';
$userRole = Session::get('role') ?: 'Operator';
$avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2563eb&color=fff';

$menuMain = [
    ['page' => 'dashboard', 'label' => 'Dashboard', 'href' => 'index.php?page=dashboard', 'icon' => 'D'],
    ['page' => 'transaksi', 'label' => 'Transaksi Laundry', 'href' => 'index.php?page=transaksi', 'icon' => 'T'],
    ['page' => 'pelanggan', 'label' => 'Data Pelanggan', 'href' => 'index.php?page=pelanggan', 'icon' => 'C'],
    ['page' => 'paket', 'label' => 'Paket Cucian', 'href' => 'index.php?page=paket', 'icon' => 'P'],
    ['page' => 'log_aktivitas', 'label' => 'Log Aktivitas', 'href' => 'index.php?page=log_aktivitas', 'icon' => 'L'],
];

$menuReports = [
    ['page' => 'print_transaksi', 'label' => 'Laporan Transaksi', 'href' => 'index.php?page=print_transaksi', 'icon' => 'R'],
    ['page' => 'print_pelanggan', 'label' => 'Laporan Pelanggan', 'href' => 'index.php?page=print_pelanggan', 'icon' => 'R'],
    ['page' => 'print_paket', 'label' => 'Laporan Paket', 'href' => 'index.php?page=print_paket', 'icon' => 'R'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --fw-primary: #2563eb;
            --fw-primary-dark: #1e3a8a;
            --fw-primary-soft: #eff6ff;
            --fw-shell-bg: #f6f8fc;
            --fw-panel: #ffffff;
            --fw-line: #e2e8f0;
            --fw-text: #0f172a;
            --fw-muted: #64748b;
            --fw-sidebar-width: 300px;
            --fw-topbar-height: 72px;
        }

        body {
            margin: 0;
            background: var(--fw-shell-bg);
            color: var(--fw-text);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .fw-app-shell,
        .fw-app-shell * {
            box-sizing: border-box;
            font-family: inherit;
        }

        .fw-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--fw-sidebar-width);
            background: var(--fw-panel);
            border-right: 1px solid var(--fw-line);
            z-index: 50;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .fw-brand {
            height: var(--fw-topbar-height);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 18px;
            border-bottom: 1px solid var(--fw-line);
            text-decoration: none;
            color: var(--fw-text);
        }

        .fw-brand-mark {
            width: 150px;
            height: auto;
            max-height: 58px;
            object-fit: contain;
            flex-shrink: 0;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
        }

        .fw-brand-title {
            display: grid;
            line-height: 1.1;
        }

        .fw-brand-title strong {
            font-size: 18px;
            letter-spacing: 0;
            color: var(--fw-primary-dark);
        }

        .fw-brand-title span {
            color: var(--fw-muted);
            font-size: 12px;
            font-weight: 500;
        }

        .fw-sidebar-nav {
            padding: 16px;
        }

        .fw-nav-section {
            margin-bottom: 28px;
        }

        .fw-nav-section-title {
            color: #334155;
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin: 0 0 10px 12px;
        }

        .fw-nav-link {
            min-height: 46px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            margin-bottom: 4px;
            border-radius: 8px;
            color: #1f2937;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.16s ease, color 0.16s ease, transform 0.16s ease;
        }

        .fw-nav-link:hover {
            background: #f1f5f9;
            color: var(--fw-primary);
            transform: translateX(2px);
        }

        .fw-nav-link.is-active {
            background: var(--fw-primary-soft);
            color: var(--fw-primary);
        }

        .fw-nav-icon {
            width: 20px;
            height: 20px;
            display: inline-grid;
            place-items: center;
            border-radius: 6px;
            border: 1px solid #bfdbfe;
            color: var(--fw-primary);
            font-size: 11px;
            font-weight: 800;
            flex: 0 0 20px;
        }

        .fw-layout {
            min-height: 100vh;
            padding-left: var(--fw-sidebar-width);
        }

        .fw-topbar {
            position: sticky;
            top: 0;
            left: auto;
            right: auto;
            z-index: 40;
            height: var(--fw-topbar-height);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 0 24px;
            background: rgba(255, 255, 255, 0.96);
            color: var(--fw-text);
            border-bottom: 1px solid var(--fw-line);
            box-shadow: none;
            backdrop-filter: blur(12px);
        }

        .fw-menu-toggle {
            width: 44px;
            height: 44px;
            display: none;
            border: 1px solid var(--fw-line);
            border-radius: 8px;
            background: #ffffff;
            color: var(--fw-primary-dark);
            font-size: 20px;
            cursor: pointer;
        }

        .fw-topbar-title {
            min-width: 0;
        }

        .fw-topbar-title strong {
            display: block;
            color: var(--fw-primary-dark);
            font-size: 15px;
            font-weight: 800;
        }

        .fw-topbar-title span {
            color: var(--fw-muted);
            display: block;
            font-size: 12px;
            margin-top: 2px;
        }

        .fw-topbar-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-left: auto;
        }

        .fw-notification {
            position: relative;
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border: 1px solid var(--fw-line);
            border-radius: 50%;
            background: #ffffff;
            color: var(--fw-primary-dark);
            font-weight: 800;
        }

        .fw-notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 20px;
            height: 20px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: #ef4444;
            color: #ffffff;
            border: 2px solid #ffffff;
            font-size: 11px;
            font-weight: 800;
        }

        .fw-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px 6px 6px;
            border: 1px solid var(--fw-line);
            border-radius: 999px;
            background: #ffffff;
        }

        .fw-user-pill img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }

        .fw-user-copy {
            display: grid;
            line-height: 1.1;
        }

        .fw-user-copy strong {
            color: var(--fw-text);
            font-size: 13px;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .fw-user-copy span {
            color: var(--fw-muted);
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .fw-main {
            margin: 0;
            padding: 28px 24px 0;
            min-height: calc(100vh - var(--fw-topbar-height));
            width: auto;
        }

        .fw-page-frame {
            max-width: 1560px;
            margin: 0 auto;
        }

        .fw-main .fw-master-container,
        .fw-main .fw-setting-container {
            padding: 0 !important;
            background: transparent !important;
            min-height: auto !important;
        }

        .fw-main .fw-master-card,
        .fw-main .fw-setting-card {
            border-radius: 8px !important;
            box-shadow: none !important;
            max-width: none !important;
        }

        .fw-app-footer {
            position: static;
            bottom: auto;
            width: auto;
            margin-left: 0;
            background: transparent;
            color: var(--fw-muted);
            font-size: 13px;
            padding: 22px 0;
            text-align: center;
        }

        body.fw-sidebar-open .fw-sidebar {
            transform: translateX(0);
        }

        @media (max-width: 1024px) {
            .fw-sidebar {
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                box-shadow: 16px 0 40px rgba(15, 23, 42, 0.18);
            }

            .fw-layout {
                padding-left: 0;
            }

            .fw-menu-toggle {
                display: inline-grid;
                place-items: center;
            }
        }

        @media (max-width: 720px) {
            .fw-topbar {
                padding: 0 14px;
            }

            .fw-main {
                padding: 18px 14px 0;
            }

            .fw-user-copy,
            .fw-topbar-title {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="fw-app-shell">
    <aside class="fw-sidebar" aria-label="Navigasi utama">
        <a class="fw-brand" href="index.php?page=dashboard">
            <img src="assets/uploads/silaundry2.png" alt="Si Laundry" class="fw-brand-mark">
        </a>

        <nav class="fw-sidebar-nav">
            <div class="fw-nav-section">
                <span class="fw-nav-section-title">Main</span>
                <?php foreach ($menuMain as $item): ?>
                    <a class="fw-nav-link <?= $currentPage === $item['page'] ? 'is-active' : ''; ?>" href="<?= $item['href']; ?>">
                        <span class="fw-nav-icon"><?= $item['icon']; ?></span>
                        <span><?= $item['label']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="fw-nav-section">
                <span class="fw-nav-section-title">Laporan</span>
                <?php foreach ($menuReports as $item): ?>
                    <a class="fw-nav-link <?= $currentPage === $item['page'] ? 'is-active' : ''; ?>" href="<?= $item['href']; ?>" target="_blank">
                        <span class="fw-nav-icon"><?= $item['icon']; ?></span>
                        <span><?= $item['label']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="fw-nav-section">
                <span class="fw-nav-section-title">Account</span>
                <a class="fw-nav-link <?= $currentPage === 'registeradmin' ? 'is-active' : ''; ?>" href="index.php?page=registeradmin">
                    <span class="fw-nav-icon">S</span>
                    <span>Register Admin</span>
                </a>
                <a class="fw-nav-link <?= $currentPage === 'setting' ? 'is-active' : ''; ?>" href="index.php?page=setting">
                    <span class="fw-nav-icon">S</span>
                    <span>Pengaturan</span>
                </a>
                <a class="fw-nav-link" href="index.php?page=logout" onclick="return confirm('Yakin ingin keluar dari sistem?')">
                    <span class="fw-nav-icon">O</span>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <div class="fw-layout">
        <header class="fw-topbar">
            <button class="fw-menu-toggle" type="button" aria-label="Buka menu" data-sidebar-toggle>&#9776;</button>
            <div class="fw-topbar-title">
                <strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentPage))); ?></strong>
                <span><?= $currentAction ? htmlspecialchars(ucfirst($currentAction)) : 'Ringkasan operasional laundry'; ?></span>
            </div>

            <div class="fw-topbar-actions">
                <div class="fw-notification" aria-label="Notifikasi">
                    N
                    <span class="fw-notification-badge">2</span>
                </div>
                <div class="fw-user-pill">
                    <img src="<?= $avatarUrl; ?>" alt="Foto profil">
                    <span class="fw-user-copy">
                        <strong><?= htmlspecialchars($userName); ?></strong>
                        <span><?= htmlspecialchars($userRole); ?></span>
                    </span>
                </div>
            </div>
        </header>

        <main class="fw-main">
            <div class="fw-page-frame">
