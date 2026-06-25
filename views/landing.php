<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = Database::getInstance()->getConnection();
$orderMessage = '';
$orderStatus  = '';
$newTransactionId = '';

$isLoggedIn = isset($_SESSION['id_pelanggan']) || isset($_SESSION['id_user']) || isset($_SESSION['role']);

function fw_landing_rows(PDO $db, string $sql, array $params = [])
{
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function fw_landing_value(PDO $db, string $sql, array $params = [])
{
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['landing_order'])) {
    $nama    = trim($_POST['nama_pelanggan'] ?? '');
    $telepon = trim($_POST['no_telp'] ?? '');
    $alamat  = trim($_POST['alamat'] ?? '');
    $idPaket = !empty($_POST['id_paket']) ? (int) $_POST['id_paket'] : 0;
    $qty     = !empty($_POST['qty']) ? (float) $_POST['qty'] : 0;

    if (strlen($nama) < 3) {
        $orderStatus  = 'error';
        $orderMessage = 'Nama pelanggan minimal 3 karakter.';
    } elseif (strlen($telepon) < 10) {
        $orderStatus  = 'error';
        $orderMessage = 'Nomor telepon minimal 10 digit.';
    } elseif (strlen($alamat) < 5) {
        $orderStatus  = 'error';
        $orderMessage = 'Alamat minimal 5 karakter.';
    } elseif ($idPaket <= 0) {
        $orderStatus  = 'error';
        $orderMessage = 'Pilih paket laundry yang valid.';
    } elseif ($qty <= 0) {
        $orderStatus  = 'error';
        $orderMessage = 'Jumlah cucian harus lebih dari 0.';
    } else {
        try {
            $paketStmt = $db->prepare("SELECT id_paket, harga_per_unit FROM tbl_paket WHERE id_paket = ?");
            $paketStmt->execute([$idPaket]);
            $paket = $paketStmt->fetch(PDO::FETCH_ASSOC);

            $userStmt = $db->prepare(
                "SELECT id_user FROM tbl_user 
                 WHERE role IN ('staff','manager','admin') 
                 ORDER BY FIELD(role,'staff','manager','admin') 
                 LIMIT 1"
            );
            $userStmt->execute();
            $userId = $userStmt->fetchColumn();

            if (!$paket || empty($paket['id_paket'])) {
                $orderStatus  = 'error';
                $orderMessage = 'Paket laundry tidak ditemukan. Pilih ulang paket.';
            } elseif (empty($userId)) {
                $orderStatus  = 'error';
                $orderMessage = 'Belum ada akun staff penerima order. Hubungi admin terlebih dahulu.';
            } else {
                $paket['harga_per_unit'] = (float) $paket['harga_per_unit'];
                $subtotal = round($qty * $paket['harga_per_unit'], 2);

                $db->beginTransaction();

                $pelangganStmt = $db->prepare(
                    "INSERT INTO tbl_pelanggan (nama_pelanggan, no_telp, alamat) VALUES (?, ?, ?)"
                );
                if (!$pelangganStmt->execute([$nama, $telepon, $alamat])) {
                    throw new Exception("Gagal insert pelanggan");
                }
                $idPelanggan = (int) $db->lastInsertId();
                
                if ($idPelanggan <= 0) {
                    throw new Exception("ID pelanggan tidak tergenerate");
                }

                $newTransactionId = '';
                for ($attempt = 0; $attempt < 10; $attempt++) {
                    $newTransactionId = 'TRX-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
                    $cekStmt = $db->prepare("SELECT COUNT(*) FROM tbl_transaksi WHERE id_transaksi = ?");
                    $cekStmt->execute([$newTransactionId]);
                    $sudahAda = (int) $cekStmt->fetchColumn();
                    if ($sudahAda == 0) break;
                }
                
                if (empty($newTransactionId)) {
                    throw new Exception("Gagal generate ID transaksi");
                }

                $trxStmt = $db->prepare(
                    "INSERT INTO tbl_transaksi 
                        (id_transaksi, id_pelanggan, id_user, tgl_terima, status_laundry, status_bayar)
                     VALUES (?, ?, ?, NOW(), 'proses', 'belum lunas')"
                );
                if (!$trxStmt->execute([$newTransactionId, $idPelanggan, $userId])) {
                    throw new Exception("Gagal insert transaksi");
                }

                $detailStmt = $db->prepare(
                    "INSERT INTO tbl_detail_transaksi (id_transaksi, id_paket, qty, subtotal) 
                     VALUES (?, ?, ?, ?)"
                );
                if (!$detailStmt->execute([$newTransactionId, $idPaket, $qty, $subtotal])) {
                    throw new Exception("Gagal insert detail transaksi");
                }

                $db->commit();
                $orderStatus  = 'success';
                $orderMessage = 'Order berhasil dikirim! Simpan nomor nota: <strong>' . $newTransactionId . '</strong>';
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $orderStatus  = 'error';
            $orderMessage = 'Order gagal: ' . $e->getMessage();
        }
    }
}

$paketLaundry  = fw_landing_rows($db, "SELECT * FROM tbl_paket ORDER BY harga_per_unit ASC");
$antreanAktif  = (int) fw_landing_value($db, "SELECT COUNT(*) FROM tbl_transaksi WHERE status_laundry = 'proses'");
$selesaiAktif  = (int) fw_landing_value($db, "SELECT COUNT(*) FROM tbl_transaksi WHERE status_laundry = 'selesai'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SI Laundry — Laundry Cepat & Terpercaya</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --blue-50:  #eff6ff;
      --blue-100: #dbeafe;
      --blue-500: #3b82f6;
      --blue-600: #2563eb;
      --blue-700: #1d4ed8;
      --blue-900: #1e3a8a;
      --navy:     #0c1f4a;
      --slate-50: #f8fafc;
      --slate-100:#f1f5f9;
      --slate-400:#94a3b8;
      --slate-500:#64748b;
      --slate-700:#334155;
      --slate-800:#1e293b;
      --slate-900:#0f172a;
      --white:    #ffffff;
      --radius:   12px;
      --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
      --shadow-md: 0 4px 20px rgba(30,64,175,.12);
      --shadow-lg: 0 12px 40px rgba(30,64,175,.18);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background: var(--slate-50);
      color: var(--slate-900);
      line-height: 1.6;
    }

    a { color: inherit; text-decoration: none; }
    img { display: block; max-width: 100%; }

    .nav {
      position: sticky;
      top: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 14px clamp(20px, 5vw, 80px);
      background: rgba(255,255,255,.88);
      backdrop-filter: blur(18px);
      border-bottom: 1px solid var(--blue-100);
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 800;
      font-size: 20px;
      color: var(--navy);
    }

    .nav-logo-icon {
      width: 36px;
      height: 36px;
      background: var(--blue-600);
      border-radius: 8px;
      display: grid;
      place-items: center;
      color: white;
      font-size: 18px;
      flex-shrink: 0;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .nav-link {
      padding: 8px 14px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      color: var(--slate-700);
      transition: background .15s;
    }

    .nav-link:hover { background: var(--blue-50); }

    .nav-cta {
      padding: 9px 18px;
      background: var(--blue-600);
      color: var(--white);
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      box-shadow: 0 4px 14px rgba(37,99,235,.3);
      transition: background .15s, transform .1s;
    }

    .nav-cta:hover { background: var(--blue-700); transform: translateY(-1px); }

    .hero {
      display: grid;
      grid-template-columns: 1fr 420px;
      gap: 48px;
      align-items: center;
      padding: 80px clamp(20px, 5vw, 80px) 72px;
      background: linear-gradient(135deg, #e8f0fe 0%, #f0f7ff 40%, var(--white) 100%);
      position: relative;
      overflow: hidden;
    }

    .hero::before,
    .hero::after {
      content: '';
      position: absolute;
      border-radius: 50%;
      pointer-events: none;
    }
    .hero::before {
      width: 420px; height: 420px;
      background: radial-gradient(circle, rgba(96,165,250,.22), transparent 70%);
      top: -100px; left: -80px;
    }
    .hero::after {
      width: 300px; height: 300px;
      background: radial-gradient(circle, rgba(56,189,248,.15), transparent 70%);
      bottom: -80px; right: 380px;
    }

    .hero-copy { position: relative; z-index: 1; }

    .hero-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      background: var(--white);
      border: 1px solid var(--blue-100);
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      color: var(--blue-700);
      letter-spacing: .04em;
      text-transform: uppercase;
      margin-bottom: 24px;
    }

    .hero-eyebrow-dot {
      width: 6px; height: 6px;
      background: var(--blue-500);
      border-radius: 50%;
    }

    .hero-title {
      font-family: 'DM Serif Display', Georgia, serif;
      font-size: clamp(40px, 5.5vw, 72px);
      line-height: 1.05;
      color: var(--navy);
      margin-bottom: 20px;
    }

    .hero-title em {
      font-style: normal;
      color: var(--blue-600);
    }

    .hero-desc {
      font-size: 17px;
      color: var(--slate-500);
      max-width: 540px;
      margin-bottom: 36px;
      line-height: 1.8;
    }

    .hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 13px 22px;
      border-radius: var(--radius);
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: all .15s;
    }

    .btn-primary {
      background: var(--blue-600);
      color: var(--white);
      box-shadow: 0 6px 20px rgba(37,99,235,.28);
    }

    .btn-primary:hover { background: var(--blue-700); transform: translateY(-1px); }

    .btn-outline {
      background: var(--white);
      color: var(--blue-700);
      border: 1.5px solid var(--blue-100);
    }

    .btn-outline:hover { border-color: var(--blue-500); background: var(--blue-50); }

    .hero-card {
      background: var(--white);
      border: 1px solid var(--blue-100);
      border-radius: 20px;
      padding: 28px;
      box-shadow: var(--shadow-lg);
      position: relative;
      z-index: 1;
    }

    .hero-card-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .hero-card-icon {
      width: 40px; height: 40px;
      background: var(--blue-50);
      border-radius: 10px;
      display: grid;
      place-items: center;
      font-size: 20px;
    }

    .hero-card-header h3 {
      font-size: 15px;
      font-weight: 700;
      color: var(--slate-700);
    }

    .hero-card-header span {
      font-size: 12px;
      color: var(--slate-400);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 20px;
    }

    .stat-box {
      background: var(--blue-50);
      border: 1px solid var(--blue-100);
      border-radius: 12px;
      padding: 16px;
    }

    .stat-number {
      font-size: 36px;
      font-weight: 800;
      color: var(--navy);
      line-height: 1;
      margin-bottom: 4px;
    }

    .stat-label {
      font-size: 12px;
      font-weight: 600;
      color: var(--slate-500);
    }

    .hero-card-note {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
      font-weight: 600;
      color: #166534;
    }

    .section {
      padding: 72px clamp(20px, 5vw, 80px);
    }

    .section-label {
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--blue-600);
      margin-bottom: 10px;
    }

    .section-title {
      font-family: 'DM Serif Display', Georgia, serif;
      font-size: clamp(28px, 3.5vw, 44px);
      color: var(--navy);
      margin-bottom: 12px;
      line-height: 1.1;
    }

    .section-desc {
      color: var(--slate-500);
      font-size: 16px;
      max-width: 520px;
      line-height: 1.75;
    }

    .section-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 24px;
      margin-bottom: 36px;
    }

    .promo-bg { background: var(--white); padding-top: 40px; padding-bottom: 40px; }

    .promo-carousel .swiper-slide{
      box-shadow: var(--shadow-md);
    }

    .promo-controls {
      margin-top: 18px;
      display: flex;
      justify-content: center;
      gap: 12px;
    }

    .promo-controls .swiper-button-prev,
    .promo-controls .swiper-button-next {
      position: relative;
      width: 44px;
      height: 44px;
      border-radius: 999px;
      background: rgba(255,255,255,0.12) !important;
      border: 1px solid rgba(255,255,255,0.25) !important;
      color: #ffffff !important;
      outline: none;
      transform: translateY(0);
    }

    .promo-controls .swiper-button-prev:hover,
    .promo-controls .swiper-button-next:hover {
      background: rgba(255,255,255,0.18) !important;
      transform: translateY(-1px);
    }

    .swiper-slide {
      background: linear-gradient(135deg, var(--blue-600) 0%, var(--navy) 100%);
      border-radius: 20px;
      padding: 32px 40px;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 200px;
      position: relative;
      overflow: hidden;
    }

    .swiper-slide::after {
      content: '✨';
      position: absolute;
      right: -20px;
      bottom: -40px;
      font-size: 140px;
      opacity: 0.1;
      pointer-events: none;
    }

    .slide-badge {
      display: inline-block;
      padding: 4px 12px;
      background: rgba(255,255,255,0.2);
      border-radius: 99px;
      font-size: 12px;
      font-weight: 700;
      margin-bottom: 12px;
      backdrop-filter: blur(4px);
    }

    .slide-title { font-size: 24px; font-weight: 800; margin-bottom: 8px; }
    .slide-desc { font-size: 15px; color: #bfdbfe; max-width: 80%; }

    .swiper-pagination-bullet { background: var(--blue-500); }
    .swiper-pagination-bullet-active { background: var(--blue-700); }

    .packages-bg { background: var(--slate-50); }

    .packages-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .package-card {
      border-radius: 16px;
      border: 1.5px solid var(--slate-100);
      background: var(--white);
      padding: 24px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      transition: box-shadow .2s, border-color .2s, transform .2s;
    }

    .package-card:hover {
      box-shadow: var(--shadow-lg);
      border-color: var(--blue-200);
      transform: translateY(-3px);
    }

    .package-top { flex: 1; }

    .package-badge {
      display: inline-block;
      padding: 3px 10px;
      background: var(--blue-50);
      color: var(--blue-700);
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 12px;
    }

    .package-name {
      font-size: 18px;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 6px;
    }

    .package-price {
      font-size: 28px;
      font-weight: 800;
      color: var(--blue-600);
    }

    .package-cta {
      display: block;
      text-align: center;
      padding: 11px;
      background: var(--blue-50);
      color: var(--blue-700);
      border-radius: 8px;
      font-size: 13px;
      font-weight: 700;
      transition: background .15s;
    }

    .package-cta:hover { background: var(--blue-100); }

    .how-bg { background: var(--slate-900); color: var(--white); }
    .how-bg .section-title { color: var(--white); }
    .how-bg .section-desc { color: var(--slate-400); }
    .how-bg .section-label { color: var(--blue-400); }

    .steps-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      position: relative;
    }

    .steps-grid::before {
      content: '';
      position: absolute;
      top: 28px;
      left: calc(12.5% + 20px);
      right: calc(12.5% + 20px);
      height: 2px;
      background: repeating-linear-gradient(90deg, var(--slate-700) 0, var(--slate-700) 8px, transparent 8px, transparent 16px);
    }

    .step-card {
      background: var(--slate-800);
      border: 1px solid var(--slate-700);
      border-radius: 16px;
      padding: 24px 20px;
      text-align: center;
      position: relative;
      transition: transform 0.3s ease;
    }
    
    .step-card:hover { transform: translateY(-5px); }

    .step-num {
      width: 56px; height: 56px;
      background: var(--blue-600);
      color: var(--white);
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: 20px;
      font-weight: 800;
      margin: 0 auto 18px;
      position: relative;
      z-index: 1;
      box-shadow: 0 4px 14px rgba(37,99,235,.3);
    }

    .step-title {
      font-size: 15px;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 8px;
    }

    .step-desc {
      font-size: 13px;
      color: var(--slate-400);
      line-height: 1.7;
    }

    .order-bg { background: var(--white); }

    .order-layout {
      display: grid;
      grid-template-columns: 1fr 480px;
      gap: 48px;
      align-items: start;
    }

    .order-info-card {
      background: var(--navy);
      border-radius: 20px;
      padding: 36px;
      color: var(--white);
      position: sticky;
      top: 80px;
      box-shadow: 0 10px 30px rgba(12, 31, 74, 0.15);
    }

    .order-info-card .section-label { color: var(--blue-400); }
    .order-info-card .section-title { color: var(--white); }
    .order-info-card .section-desc  { color: var(--slate-300); margin-bottom: 30px; }

    .order-checklist {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .order-checklist li {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      font-size: 14px;
      color: var(--white);
      line-height: 1.5;
    }

    .check-icon {
      flex-shrink: 0;
      width: 24px; height: 24px;
      background: var(--blue-600);
      color: white;
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: 12px;
      font-weight: bold;
      margin-top: -2px;
    }

    .order-form-card {
      background: var(--white);
      border: 1px solid var(--slate-200);
      border-radius: 20px;
      padding: 32px;
      box-shadow: var(--shadow-lg);
    }

    .locked-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 40px 20px;
      background: var(--slate-50);
      border-radius: 12px;
      border: 1px dashed var(--slate-300);
    }

    .lock-icon {
      font-size: 48px;
      margin-bottom: 16px;
    }

    .locked-state h3 {
      font-size: 20px;
      color: var(--navy);
      margin-bottom: 10px;
    }

    .locked-state p {
      font-size: 14px;
      color: var(--slate-500);
      margin-bottom: 24px;
      line-height: 1.6;
    }

    .form-title {
      font-size: 20px;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--slate-100);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-label {
      display: block;
      font-size: 12px;
      font-weight: 700;
      color: var(--slate-700);
      letter-spacing: .04em;
      text-transform: uppercase;
      margin-bottom: 7px;
    }

    .form-control {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid var(--slate-200);
      border-radius: 10px;
      font-family: inherit;
      font-size: 14px;
      color: var(--slate-900);
      background: var(--slate-50);
      transition: all .2s;
      outline: none;
    }

    .form-control:focus {
      border-color: var(--blue-500);
      background: var(--white);
      box-shadow: 0 0 0 3px rgba(59,130,246,.15);
    }

    textarea.form-control {
      min-height: 84px;
      resize: vertical;
    }

    .form-alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 14px 16px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .form-alert.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .form-alert.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    .form-submit {
      width: 100%;
      padding: 14px;
      background: var(--blue-600);
      color: var(--white);
      border: none;
      border-radius: 10px;
      font-family: inherit;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(37,99,235,.28);
      transition: all .2s;
      margin-top: 4px;
    }

    .form-submit:hover { background: var(--blue-700); transform: translateY(-2px); }

    .form-note {
      font-size: 12px;
      color: var(--slate-400);
      text-align: center;
      margin-top: 12px;
    }

    .footer {
      background: var(--navy);
      color: #bfdbfe;
      padding: 40px clamp(20px, 5vw, 80px);
    }

    .footer-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
    }

    .footer-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 700;
      font-size: 17px;
      color: var(--white);
    }

    .footer-brand-icon {
      width: 34px; height: 34px;
      background: var(--blue-600);
      border-radius: 8px;
      display: grid;
      place-items: center;
      font-size: 16px;
    }

    .footer-links {
      display: flex;
      gap: 24px;
      font-size: 14px;
    }

    .footer-links a { transition: color .15s; }
    .footer-links a:hover { color: var(--white); }

    .footer-copy {
      font-size: 13px;
      color: #6b91c9;
    }

    @media (max-width: 1024px) {
      .hero { grid-template-columns: 1fr; }
      .hero-card { max-width: 480px; }
      .steps-grid { grid-template-columns: repeat(2, 1fr); }
      .steps-grid::before { display: none; }
      .order-layout { grid-template-columns: 1fr; }
      .order-info-card { position: static; }
      .packages-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
      .packages-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
      .footer-inner { flex-direction: column; align-items: flex-start; gap: 16px; }
    }

    @media (max-width: 600px) {
      .nav-links .nav-link { display: none; }
      .steps-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav class="nav">
  <a class="nav-logo" href="index.php?page=landing">
    <span class="nav-logo-icon">🧺</span>
    <span>SI Laundry</span>
  </a>
  <div class="nav-links">
    <a class="nav-link" href="#promo">Promo</a>
    <a class="nav-link" href="#paket">Paket</a>
    <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
    <a class="nav-link" href="#order">Order</a>
    <a class="nav-link" href="index.php?page=antrean">Cek Status</a>
    <a class="nav-cta" href="index.php?page=login">Masuk / Daftar</a>
  </div>
</nav>

<header class="hero">
  <div class="hero-copy">
    <div class="hero-eyebrow">
      <span class="hero-eyebrow-dot"></span>
      Sistem Penerimaan Order Aktif
    </div>
    <h1 class="hero-title">
      Cucian bersih,<br><em>tanpa repot</em><br>dari rumah.
    </h1>
    <p class="hero-desc">
      Pesan laundry online, pilih paket, dan pantau status cucian Anda secara real-time. 
      Tidak perlu antre — cukup isi form dan cucian kami urus.
    </p>
    <div class="hero-actions">
      <a class="btn btn-primary" href="#order">
        🧺 Buat Order Sekarang
      </a>
      <a class="btn btn-outline" href="index.php?page=antrean">
        Cek Status Cucian
      </a>
    </div>
  </div>

  <aside class="hero-card">
    <div class="hero-card-header">
      <div class="hero-card-icon">📊</div>
      <div>
        <h3>Status Operasional</h3>
        <span>Update otomatis</span>
      </div>
    </div>
    <div class="stats-grid">
      <div class="stat-box">
        <div class="stat-number"><?= number_format($antreanAktif) ?></div>
        <div class="stat-label">Sedang diproses</div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?= number_format($selesaiAktif) ?></div>
        <div class="stat-label">Siap diambil</div>
      </div>
    </div>
    <div class="hero-card-note">
      ✅ Sistem buka &amp; menerima order sekarang
    </div>
  </aside>
</header>

<section class="section promo-bg" id="promo">
  <div class="swiper mySwiper promo-carousel">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <span class="slide-badge">✨ Promo Pengguna Baru</span>
        <h3 class="slide-title">Diskon 20%</h3>
        <p class="slide-desc">Gunakan kode <strong>BARU20</strong> saat di kasir untuk potongan harga spesial.</p>
      </div>

      <div class="swiper-slide" style="background: linear-gradient(135deg, #10b981 0%, #065f46 100%);">
        <span class="slide-badge">🚚 Gratis Jemput</span>
        <h3 class="slide-title">Layanan Antar Jemput</h3>
        <p class="slide-desc">Gratis biaya jemput untuk radius 5km dengan minimal transaksi Rp 50.000.</p>
      </div>

      <div class="swiper-slide" style="background: linear-gradient(135deg, #2563eb 0%, #0f172a 100%);">
        <span class="slide-badge">🧼 Laundry Kilat</span>
        <h3 class="slide-title">Proses 1–2 Hari</h3>
        <p class="slide-desc">Cucian Anda diproses cepat dengan standar kebersihan yang konsisten.</p>
      </div>
    </div>

    <div class="swiper-pagination"></div>

    <div class="promo-controls" aria-hidden="true">
      <button class="swiper-button-prev" type="button"></button>
      <button class="swiper-button-next" type="button"></button>
    </div>
  </div>
</section>

<section class="section packages-bg" id="paket">
  <div class="section-head">
    <div>
      <p class="section-label">Layanan kami</p>
      <h2 class="section-title">Pilih Paket Laundry</h2>
      <p class="section-desc">Semua paket dihitung per kilogram atau satuan sesuai jenis layanan.</p>
    </div>
  </div>

  <div class="packages-grid">
    <?php if (empty($paketLaundry)): ?>
      <div class="package-card">
        <div class="package-top">
          <div class="package-badge">Info</div>
          <div class="package-name">Paket belum tersedia</div>
        </div>
        <div class="package-price">Rp 0</div>
      </div>
    <?php else: ?>
      <?php foreach ($paketLaundry as $idx => $p): ?>
        <div class="package-card">
          <div class="package-top">
            <div class="package-badge"><?= htmlspecialchars($p['jenis']) ?></div>
            <div class="package-name"><?= htmlspecialchars($p['nama_paket']) ?></div>
          </div>
          <div class="package-price">
            Rp <?= number_format((int)$p['harga_per_unit'], 0, ',', '.') ?>
            <span style="font-size:13px; font-weight:500; color:var(--slate-400);">/ kg</span>
          </div>
          <a class="package-cta" href="#order">Pilih paket ini</a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<section class="section how-bg" id="cara-kerja">
  <div class="section-head">
    <div>
      <p class="section-label">Mudah &amp; cepat</p>
      <h2 class="section-title">Cara Kerja</h2>
    </div>
  </div>
  <div class="steps-grid">
    <div class="step-card">
      <div class="step-num">1</div>
      <div class="step-title">Isi Form Order</div>
      <p class="step-desc">Masukkan data dan pilih paket laundry melalui form terkunci kami.</p>
    </div>
    <div class="step-card">
      <div class="step-num">2</div>
      <div class="step-title">Antar Cucian</div>
      <p class="step-desc">Bawa cucian ke outlet atau tunggu konfirmasi penjemputan dari staff.</p>
    </div>
    <div class="step-card">
      <div class="step-num">3</div>
      <div class="step-title">Pantau Status</div>
      <p class="step-desc">Gunakan nomor nota untuk melacak cucian Anda secara real-time.</p>
    </div>
    <div class="step-card">
      <div class="step-num">4</div>
      <div class="step-title">Ambil &amp; Bayar</div>
      <p class="step-desc">Bayar di outlet setelah notifikasi cucian selesai Anda terima.</p>
    </div>
  </div>
</section>

<section class="section order-bg" id="order">
  <div class="order-layout">

    <div class="order-info-card">
      <p class="section-label">Akses Pelanggan</p>
      <h2 class="section-title">Buat Order<br>Sekarang</h2>
      <p class="section-desc">Data pesanan Anda akan langsung masuk ke sistem antrean mesin cuci kami secara real-time.</p>
      
      <ul class="order-checklist">
        <li>
          <span class="check-icon">✓</span>
          Transparansi harga sesuai paket terpilih.
        </li>
        <li>
          <span class="check-icon">✓</span>
          Dapatkan ID Transaksi (Nota) seketika.
        </li>
        <li>
          <span class="check-icon">✓</span>
          Keamanan pakaian Anda adalah prioritas kami.
        </li>
        <li>
          <span class="check-icon">✓</span>
          Pembayaran tunai/non-tunai di kasir.
        </li>
      </ul>

      <div style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
        <p style="font-size: 13px; color: var(--blue-100); margin-bottom: 5px;">Butuh bantuan? Hubungi CS kami:</p>
        <p style="font-size: 18px; font-weight: bold; display: flex; align-items: center; gap: 8px;">
          📞 0812-XXXX-XXXX
        </p>
      </div>
    </div>

    <div class="order-form-card">
      <div class="form-title">🧾 Form Pemesanan</div>

      <?php if (!$isLoggedIn): ?>
        <div class="locked-state">
          <div class="lock-icon">🔒</div>
          <h3>Akses Form Terkunci</h3>
          <p>Untuk menjaga keamanan data dan melacak riwayat pesanan, silakan masuk ke akun Anda terlebih dahulu sebelum membuat order.</p>
          <a href="index.php?page=login" class="btn btn-primary" style="width: 100%; justify-content: center;">
            Masuk / Daftar Akun ➔
          </a>
        </div>
      <?php else: ?>
        <?php if ($orderMessage !== ''): ?>
          <div class="form-alert <?= $orderStatus === 'success' ? 'success' : 'error' ?>">
            <span><?= $orderStatus === 'success' ? '✅' : '⚠️' ?></span>
            <span><?= htmlspecialchars($orderMessage) ?></span>
          </div>
        <?php endif; ?>

        <form method="post" action="index.php?page=landing#order">
          <input type="hidden" name="landing_order" value="1">

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="nama_pelanggan">Nama Lengkap</label>
              <input class="form-control" type="text" id="nama_pelanggan"
                     name="nama_pelanggan" placeholder="Nama lengkap Anda" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="no_telp">No. Telepon</label>
              <input class="form-control" type="tel" id="no_telp"
                     name="no_telp" placeholder="08xxxxxxxxxx" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="alamat">Alamat Lengkap</label>
            <textarea class="form-control" id="alamat"
                      name="alamat" placeholder="Jalan, nomor rumah, kecamatan..." required></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="id_paket">Paket Laundry</label>
              <select class="form-control" id="id_paket" name="id_paket" required>
                <option value="">Pilih paket</option>
                <?php foreach ($paketLaundry as $p): ?>
                  <option value="<?= (int)$p['id_paket'] ?>">
                    <?= htmlspecialchars($p['nama_paket']) ?> — Rp <?= number_format((int)$p['harga_per_unit'], 0, ',', '.') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="qty">Berat (kg) / Qty</label>
              <input class="form-control" type="number" id="qty"
                     name="qty" min="0.1" step="0.1" placeholder="Contoh: 3.5" required>
            </div>
          </div>

          <button class="form-submit" type="submit">
            Kirim Order Laundry 🚀
          </button>
          <p class="form-note">Data Anda aman dan terenkripsi dalam sistem kami.</p>
        </form>
      <?php endif; ?>
    </div>

  </div>
</section>

<footer class="footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="footer-brand-icon">🧺</div>
      SI Laundry
    </div>
    <div class="footer-links">
      <a href="#paket">Paket</a>
      <a href="#cara-kerja">Cara Kerja</a>
      <a href="#order">Order</a>
      <a href="index.php?page=antrean">Cek Status</a>
    </div>
    <div class="footer-copy">
      &copy; <?= date('Y') ?> SI Laundry — Laundry cepat &amp; terpercaya.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
  var swiper = new Swiper(".promo-carousel", {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,
    autoplay: {
      delay: 4000,
      disableOnInteraction: false,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    autoplay: {
      delay: 4000,
      disableOnInteraction: false,
    },
    breakpoints: {
      768: { slidesPerView: 2 }
    }
  });
</script>

</body>
</html>