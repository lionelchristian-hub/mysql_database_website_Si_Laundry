-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2026 at 05:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_silaundry`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_detail_transaksi`
--

CREATE TABLE `tbl_detail_transaksi` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` varchar(50) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `subtotal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_transaksi`
--

INSERT INTO `tbl_detail_transaksi` (`id_detail`, `id_transaksi`, `id_paket`, `qty`, `subtotal`, `created_at`) VALUES
(8, 'TRX-20260621-547', 6, 4.00, 80000, '2026-06-21 15:36:03'),
(9, 'TRX-20260621-917', 2, 4.00, 640000, '2026-06-21 15:39:10'),
(10, 'TRX-20260621-337', 6, 5.00, 125000, '2026-06-21 15:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_log_aktivitas`
--

CREATE TABLE `tbl_log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` varchar(50) NOT NULL,
  `aktivitas` text NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_log_aktivitas`
--

INSERT INTO `tbl_log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu`) VALUES
(26, 'ADM001', 'User Pepe Lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 05:04:07'),
(27, 'ADM001', ' Pepe Lele berhasil keluar dari sistem (Logout)', '2026-06-21 05:06:22'),
(28, 'ADM001', 'User Pepe Lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 05:11:44'),
(29, 'ADM001', ' Pepe Lele berhasil keluar dari sistem (Logout)', '2026-06-21 05:37:56'),
(30, 'ADM001', 'Berhasil Login ke dalam sistem', '2026-06-21 13:39:11'),
(31, 'ADM001', 'Logout dari sistem', '2026-06-21 13:39:48'),
(32, 'ADM001', 'User Pepe Lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 13:44:41'),
(33, 'ADM001', ' Pepe Lele berhasil keluar dari sistem (Logout)', '2026-06-21 13:54:51'),
(34, 'CLT001', 'User Raper (Role: client) berhasil masuk ke sistem (Login)', '2026-06-21 13:54:56'),
(35, 'CLT001', ' Raper berhasil keluar dari sistem (Logout)', '2026-06-21 14:06:28'),
(36, 'ADM001', 'User Pepe Lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 14:06:36'),
(37, 'ADM001', ' Pepe Lele berhasil keluar dari sistem (Logout)', '2026-06-21 14:11:29'),
(38, 'ADM001', 'User Pepe Lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 14:14:38'),
(39, 'ADM001', ' Pepe Lele berhasil keluar dari sistem (Logout)', '2026-06-21 14:48:06'),
(40, 'CLT97607', 'User pelanggan (Role: client) berhasil masuk ke sistem (Login)', '2026-06-21 14:55:11'),
(41, 'CLT97607', ' pelanggan berhasil keluar dari sistem (Logout)', '2026-06-21 14:55:59'),
(42, 'CLT39069', 'User pelanggan saja (Role: client) berhasil masuk ke sistem (Login)', '2026-06-21 15:00:42'),
(43, 'CLT39069', ' pelanggan saja berhasil keluar dari sistem (Logout)', '2026-06-21 15:01:21'),
(44, 'ADM001', 'User Pepe Lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:01:43'),
(45, 'ADM001', 'Mendaftarkan profil pelanggan untuk: pelanggan 1', '2026-06-21 15:02:38'),
(46, 'ADM001', ' Lele Pepe berhasil keluar dari sistem (Logout)', '2026-06-21 15:06:07'),
(47, 'ADM001', 'User Lele Pepe (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:06:11'),
(48, 'ADM001', 'Registrasi user baru: mimin (admin) dengan ID ADM-1569', '2026-06-21 15:06:40'),
(49, 'ADM001', ' Lele Pepe berhasil keluar dari sistem (Logout)', '2026-06-21 15:06:43'),
(50, 'ADM-1569', 'User mimin (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:06:49'),
(51, 'ADM-1569', 'Registrasi user baru: manager (manager) dengan ID MGR-2481', '2026-06-21 15:07:10'),
(52, 'ADM-1569', ' mimin berhasil keluar dari sistem (Logout)', '2026-06-21 15:07:13'),
(53, 'MGR-2481', 'User manager (Role: manager) berhasil masuk ke sistem (Login)', '2026-06-21 15:07:18'),
(54, 'MGR-2481', ' manager berhasil keluar dari sistem (Logout)', '2026-06-21 15:11:13'),
(55, 'CLT16509', 'User client (Role: client) berhasil masuk ke sistem (Login)', '2026-06-21 15:14:46'),
(56, 'CLT16509', ' client berhasil keluar dari sistem (Logout)', '2026-06-21 15:15:21'),
(57, 'ADM001', 'User Lele Pepe (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:15:32'),
(58, 'ADM001', 'Memperbarui profil pelanggan ID #CLT39069 (pelanggan 1)', '2026-06-21 15:16:21'),
(59, 'ADM001', 'Memperbarui profil pelanggan ID #CLT39069 (pelanggan 2)', '2026-06-21 15:16:27'),
(60, 'ADM001', 'Menghapus pelanggan ID #CLT39069', '2026-06-21 15:16:30'),
(61, 'ADM001', 'Mendaftarkan profil pelanggan untuk: si client', '2026-06-21 15:16:58'),
(62, 'ADM001', 'Registrasi user baru: si atmin (admin) dengan ID ADM-1632', '2026-06-21 15:20:12'),
(63, 'ADM001', ' Pepe lele berhasil keluar dari sistem (Logout)', '2026-06-21 15:20:16'),
(64, 'ADM-1632', 'User si atmin (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:20:22'),
(65, 'ADM-1632', 'Registrasi user baru: managerr (manager) dengan ID MGR-2724', '2026-06-21 15:20:44'),
(66, 'ADM-1632', ' si atmin berhasil keluar dari sistem (Logout)', '2026-06-21 15:20:46'),
(67, 'MGR-2724', 'User managerr (Role: manager) berhasil masuk ke sistem (Login)', '2026-06-21 15:21:01'),
(68, 'MGR-2724', ' managerr berhasil keluar dari sistem (Logout)', '2026-06-21 15:21:19'),
(69, 'ADM-1632', 'User si atmin (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:21:26'),
(70, 'ADM-1632', 'Registrasi user baru: si kasir (staff) dengan ID KSR-4032', '2026-06-21 15:21:55'),
(71, 'ADM-1632', ' si atmin berhasil keluar dari sistem (Logout)', '2026-06-21 15:21:58'),
(72, 'KSR-4032', 'User si kasir (Role: staff) berhasil masuk ke sistem (Login)', '2026-06-21 15:22:04'),
(73, 'KSR-4032', ' si kasir berhasil keluar dari sistem (Logout)', '2026-06-21 15:22:34'),
(74, 'ADM001', 'User Pepe lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:22:42'),
(75, 'ADM001', ' Pepe lele berhasil keluar dari sistem (Logout)', '2026-06-21 15:23:12'),
(76, 'ADM001', 'User Pepe lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:23:21'),
(77, 'ADM001', ' Pepe lele berhasil keluar dari sistem (Logout)', '2026-06-21 15:23:48'),
(78, 'ADM001', 'User Pepe lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:23:59'),
(79, 'ADM001', ' Pepe lele berhasil keluar dari sistem (Logout)', '2026-06-21 15:24:17'),
(80, 'ADM001', 'User Pepe lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:24:45'),
(81, 'ADM001', ' Pepe lele berhasil keluar dari sistem (Logout)', '2026-06-21 15:24:55'),
(82, 'CLT16509', 'User client (Role: client) berhasil masuk ke sistem (Login)', '2026-06-21 15:25:11'),
(83, 'CLT16509', ' client berhasil keluar dari sistem (Logout)', '2026-06-21 15:25:20'),
(84, 'ADM001', 'User Pepe lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:25:25'),
(85, 'ADM001', ' Pepe lele berhasil keluar dari sistem (Logout)', '2026-06-21 15:26:09'),
(86, 'CLT42601', 'User customer (Role: client) berhasil masuk ke sistem (Login)', '2026-06-21 15:32:25'),
(87, 'CLT42601', ' customer berhasil keluar dari sistem (Logout)', '2026-06-21 15:32:56'),
(88, 'ADM001', 'User Pepe lele (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:33:04'),
(89, 'ADM001', 'Memperbarui profil pelanggan ID #CLT16509 (siapa ya)', '2026-06-21 15:34:00'),
(90, 'ADM001', 'Menghapus pelanggan ID #CLT16509', '2026-06-21 15:34:04'),
(91, 'ADM001', 'Mendaftarkan profil pelanggan untuk: customer', '2026-06-21 15:34:31'),
(92, 'ADM001', ' lele Pepe berhasil keluar dari sistem (Logout)', '2026-06-21 15:37:09'),
(93, 'ADM001', 'User lele Pepe (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:37:13'),
(94, 'ADM001', 'Registrasi user baru: epep (admin) dengan ID ADM-8836', '2026-06-21 15:37:38'),
(95, 'ADM001', ' lele Pepe berhasil keluar dari sistem (Logout)', '2026-06-21 15:37:40'),
(96, 'ADM-8836', 'User epep (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:37:52'),
(97, 'ADM-8836', 'Registrasi user baru: menejer (manager) dengan ID MGR-5882', '2026-06-21 15:38:46'),
(98, 'ADM-8836', ' epep berhasil keluar dari sistem (Logout)', '2026-06-21 15:38:48'),
(99, 'MGR-5882', 'User menejer (Role: manager) berhasil masuk ke sistem (Login)', '2026-06-21 15:38:55'),
(100, 'MGR-5882', ' menejer berhasil keluar dari sistem (Logout)', '2026-06-21 15:39:28'),
(101, 'ADM001', 'User lele Pepe (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:39:41'),
(102, 'ADM001', 'Registrasi user baru: kashir (staff) dengan ID KSR-2017', '2026-06-21 15:39:54'),
(103, 'ADM001', ' lele Pepe berhasil keluar dari sistem (Logout)', '2026-06-21 15:39:55'),
(104, 'KSR-2017', 'User kashir (Role: staff) berhasil masuk ke sistem (Login)', '2026-06-21 15:40:00'),
(105, 'KSR-2017', 'Mendaftarkan profil pelanggan untuk: client', '2026-06-21 15:40:25'),
(106, 'KSR-2017', ' kashir berhasil keluar dari sistem (Logout)', '2026-06-21 15:41:05'),
(107, 'ADM001', 'User lele Pepe (Role: admin) berhasil masuk ke sistem (Login)', '2026-06-21 15:47:51'),
(108, 'ADM001', 'Registrasi user baru: ADMIN1 (admin) dengan ID ADM-7398', '2026-06-21 15:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_paket`
--

CREATE TABLE `tbl_paket` (
  `id_paket` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `harga_per_unit` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_paket`
--

INSERT INTO `tbl_paket` (`id_paket`, `nama_paket`, `jenis`, `harga_per_unit`, `created_at`) VALUES
(1, 'Cuci Kilat (Express)', 'Cepat', 50000, '2026-06-20 19:58:57'),
(2, 'Cuci Reguler', 'Standar', 40000, '2026-06-20 19:58:57'),
(3, 'Cuci + Setrika', 'Premium', 60000, '2026-06-20 19:58:57'),
(6, 'bebas murah', 'satuan', 5000, '2026-06-21 15:35:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pelanggan`
--

CREATE TABLE `tbl_pelanggan` (
  `id_pelanggan` varchar(50) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_telp` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pelanggan`
--

INSERT INTO `tbl_pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_telp`, `alamat`, `created_at`) VALUES
('CLT16509', 'client', '342632456', 'sdfasgf', '2026-06-21 15:40:25'),
('CLT42601', 'customer', '545437542', 'dimana ya', '2026-06-21 15:34:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaksi`
--

CREATE TABLE `tbl_transaksi` (
  `id_transaksi` varchar(50) NOT NULL,
  `id_pelanggan` varchar(50) NOT NULL,
  `id_user` varchar(50) NOT NULL,
  `tgl_terima` datetime NOT NULL,
  `tgl_selesai` datetime DEFAULT NULL,
  `status_laundry` enum('proses','selesai','diambil') DEFAULT 'proses',
  `status_bayar` enum('belum lunas','lunas') DEFAULT 'belum lunas',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_transaksi`
--

INSERT INTO `tbl_transaksi` (`id_transaksi`, `id_pelanggan`, `id_user`, `tgl_terima`, `tgl_selesai`, `status_laundry`, `status_bayar`, `bukti_bayar`, `created_at`) VALUES
('TRX-20260621-337', 'CLT16509', 'KSR-2017', '2026-06-21 17:40:53', '2026-06-21 17:41:02', 'selesai', 'belum lunas', NULL, '2026-06-21 15:40:53'),
('TRX-20260621-547', 'CLT42601', 'ADM001', '2026-06-21 17:36:03', NULL, 'proses', 'belum lunas', NULL, '2026-06-21 15:36:03'),
('TRX-20260621-917', 'CLT42601', 'MGR-5882', '2026-06-21 17:39:10', '2026-06-21 17:39:17', 'selesai', 'lunas', NULL, '2026-06-21 15:39:10');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` varchar(50) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff','client') NOT NULL DEFAULT 'client',
  `nama` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `nama_user`, `password`, `role`, `nama`, `foto`, `created_at`) VALUES
('ADM-1569', 'mimin', '$2y$10$LJWYLcSN0ZIXjR.QcLGQwuxvBaG6zCrDHhle5qEVEiB80u./LZwnC', 'admin', 'mimin', NULL, '2026-06-21 15:06:40'),
('ADM-1632', 'si atmin', '$2y$10$9VC9Hc.ZNgf.LY0Jn95DpOdXjJhqkW2TxS.qRbfYl5iPZbeo6N4rm', 'admin', 'si atmin', NULL, '2026-06-21 15:20:12'),
('ADM-7398', 'ADMIN1', '$2y$10$0ovgY6MCZkoCSQLyo6DTdeFPtRvmDBiIV1h43M8FnKlVxOyHaY4LO', 'admin', 'ADMIN1', NULL, '2026-06-21 15:48:05'),
('ADM-8836', 'epep', '$2y$10$.CbCpfM98gjWelkBNcbUoOIIsAvcAxm5Ol73Wi4dvYEg9DaGgndIm', 'admin', 'epep', NULL, '2026-06-21 15:37:38'),
('ADM001', 'Pepe', '$2y$10$IOaNTFPUi9nzcJDsgtTVzOE.5OXe3c.pqmFXw.zNduZk7FTpR34ua', 'admin', 'lele Pepe', 'user_1782056225_5951.jpg', '2026-06-20 19:58:57'),
('ADM11', 'admin', '$2y$10$jSMPCYHwLQE4bXVe9fukD.bqEASRPHJkSYpY0gg/fBvgnFZDHoKiy', 'admin', 'Admin Super', NULL, '2026-06-20 19:58:57'),
('Client1', 'Vinzen', '$2y$10$5FSfU3FwAiJZIC3mvRaYruadZxch2JZD9qQrXQnLLbJPGpeYKlPIe', 'client', 'Vinzenne', NULL, '2026-06-20 19:58:57'),
('CLT001', 'Lepar', '$2y$10$aTsg1Y2VstDGvV6LaoXOPuN490EfQvKl/pKkAQM2WgnTB5o9eC2DO', 'client', 'Raper', NULL, '2026-06-20 19:58:57'),
('CLT16509', 'client', '$2y$10$Z6rD6h.2KFVeo0bLwI/ZnOSW8aIM4Nt08RdSKoiXA..c1xoig2.9a', 'client', 'client', NULL, '2026-06-21 15:14:39'),
('CLT29604', 'lempar', '$2y$10$ct/cKW26DpGsSjMOZpd4bOr.v4/G/LUO44ecHFiFwTO4ez33AHjZ6', 'client', 'Lempar', NULL, '2026-06-20 19:58:57'),
('CLT33856', 'paler', '$2y$10$vaNxlwKz/BbaN5Ao5B5oWeer8IG3O2PXEf.4vohrXX7c98cgLOeyC', 'client', 'Paler', 'IMG-1781787654.jpg', '2026-06-20 19:58:57'),
('CLT38295', 'lele', '$2y$10$EFN3jA5Maamq.QM3RMsNm.pEI0LjuQ7NFXFZo7oiBfpvIrapQkph6', 'client', 'Lele', NULL, '2026-06-20 19:58:57'),
('CLT39069', 'pelanggan 1', '$2y$10$txysnZiQbK8o8gQei5ZYYOpXCXa0vk3PiHf6GRZd.FgfYCqDBeOZe', 'client', 'pelanggan saja', NULL, '2026-06-21 15:00:35'),
('CLT42601', 'customer', '$2y$10$29edYh1Q3MTfTSkID4foquIcw65QhhYf0CBii4hBr5xsppR9UG/Ti', 'client', 'customer', NULL, '2026-06-21 15:32:18'),
('CLT97607', 'pelanggan saja', '$2y$10$OfP.nIqsJ0kkRDC77Ygg4ezuUdftH.QWwfptwcX7hdM/tt45S/Vri', 'client', 'pelanggan', NULL, '2026-06-21 14:54:51'),
('KSR-2017', 'kashir', '$2y$10$QjHqW94PcUVHP3WNBgFIrOh2Peam0YV4Ye94fAKZqgqnNz5wNTROS', 'staff', 'kashir', NULL, '2026-06-21 15:39:54'),
('KSR-4032', 'si kasir', '$2y$10$xa7K5i2RiVl/dbdCuV4n7ebaUkKP2DGTDef1GHHm2QFcnpBg5qjmC', 'staff', 'si kasir', NULL, '2026-06-21 15:21:55'),
('KSR11', 'kasir', '$2y$10$2A0srzOQMmk9WK0ZH672heIGQWb/hnPW/tR1379I6QGxVEOsHreI.', 'manager', 'kasirsuper', NULL, '2026-06-20 20:02:13'),
('MGR-2481', 'manager', '$2y$10$AWnGrAptc3TCWApQ7nYRp.3CMU48PUzcuzWbf8ji9/0OQO60Y37yW', 'manager', 'manager', NULL, '2026-06-21 15:07:10'),
('MGR-2724', 'managerr', '$2y$10$H4c8HcYxaPyvGb85xfJW7.B4.bAeXdu61GkDIPASpXgB7PX.3olEC', 'manager', 'managerr', NULL, '2026-06-21 15:20:44'),
('MGR-5882', 'menejer', '$2y$10$pzesJWT9rwMDXiFH.eFF2u.zmc1v7FzMm0vcT69wPjeKy4oPBCebW', 'manager', 'menejer', NULL, '2026-06-21 15:38:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_detail_transaksi`
--
ALTER TABLE `tbl_detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_transaksi` (`id_transaksi`),
  ADD KEY `fk_detail_paket` (`id_paket`);

--
-- Indexes for table `tbl_log_aktivitas`
--
ALTER TABLE `tbl_log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tbl_paket`
--
ALTER TABLE `tbl_paket`
  ADD PRIMARY KEY (`id_paket`);

--
-- Indexes for table `tbl_pelanggan`
--
ALTER TABLE `tbl_pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `tbl_transaksi`
--
ALTER TABLE `tbl_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_pelanggan` (`id_pelanggan`),
  ADD KEY `fk_transaksi_user` (`id_user`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nama_user` (`nama_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_detail_transaksi`
--
ALTER TABLE `tbl_detail_transaksi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_log_aktivitas`
--
ALTER TABLE `tbl_log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `tbl_paket`
--
ALTER TABLE `tbl_paket`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_detail_transaksi`
--
ALTER TABLE `tbl_detail_transaksi`
  ADD CONSTRAINT `fk_detail_paket` FOREIGN KEY (`id_paket`) REFERENCES `tbl_paket` (`id_paket`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `tbl_transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_log_aktivitas`
--
ALTER TABLE `tbl_log_aktivitas`
  ADD CONSTRAINT `tbl_log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_transaksi`
--
ALTER TABLE `tbl_transaksi`
  ADD CONSTRAINT `fk_transaksi_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `tbl_pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
