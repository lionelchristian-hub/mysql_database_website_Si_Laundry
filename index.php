<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/LogAktivitas.php';
$log = new LogAktivitas();
require_once 'classes/Session.php';
require_once 'classes/User.php';

$auth = new User();

if (!isset($_GET['page'])) {
    if ($auth->isLoggedIn()) {
        if (Session::get('role') === 'client') {
            header('Location: index.php?page=client_dashboard');
        } else {
            header('Location: index.php?page=dashboard');
        }
    } else {
        header('Location: index.php?page=landing');
    }
    exit;
}

$page = $_GET['page'];

switch ($page) {
    case 'login':
        include 'views/login.php';
        break;
        
    case 'register':
        include 'views/register.php';
        break;

    case 'landing':
        include 'views/landing.php';
        break;

    case 'antrean':
        include 'views/antrean.php';
        break;

    case 'client_dashboard':
        if (!Session::get('user_id') || Session::get('role') !== 'client') {
            header('Location: index.php?page=login');
            exit;
        }
        include 'views/client/dashboard.php';
        break;

    case 'setting':
        if (!Session::get('user_id')) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (Session::get('role') === 'client') {
            include 'views/client/setting.php'; 
            
        } else {
            include 'views/setting/index.php';
        }
        break;

    case 'dashboard':
    case 'paket':
    case 'pelanggan':
    case 'transaksi':
    case 'print_paket':
    case 'print_pelanggan':
    case 'print_transaksi':
        if (!$auth->isLoggedIn() || Session::get('role') === 'client') {
            echo "<script>alert('Akses ditolak! Halaman ini hanya untuk Pekerja.'); window.location='index.php?page=dashboard';</script>";
            header('Location: index.php?page=client_dashboard');
            exit;
        }

        if ($page == 'dashboard') include 'views/dashboard.php';
        if ($page == 'paket') include 'views/paket/index.php';
        if ($page == 'pelanggan') include 'views/pelanggan/index.php';
        if ($page == 'transaksi') include 'views/transaksi/index.php';
        if ($page == 'print_paket') include 'views/report/print_paket.php';
        if ($page == 'print_pelanggan') include 'views/report/print_pelanggan.php';
        if ($page == 'print_transaksi') include 'views/report/print_transaksi.php';
        break;

    case 'log_aktivitas':
        if (!$auth->isLoggedIn() || (Session::get('role') !== 'admin' && Session::get('role') !== 'manager')) {
            echo "<script>alert('Akses ditolak! Halaman ini hanya untuk Admin/Manager.'); window.location='index.php?page=dashboard';</script>";
            exit;
        }
        include 'views/admin/log_aktivitas.php';
        break;

    case 'registeradmin':
        if (!$auth->isLoggedIn() || Session::get('role') !== 'admin') {
            echo "<script>alert('Akses ditolak! Halaman ini hanya untuk Admin.'); window.location='index.php?page=dashboard';</script>";
            exit;
        }
        include 'views/admin/registeradmin.php';
        break;

    case 'logout': 
        $auth->logout();
        header('Location: index.php?page=login');
        break;
        
    default:
        echo "<h3 style='text-align:center; margin-top:50px;'>Error 404: Halaman tidak ditemukan!</h3>";
        break;
}
?>