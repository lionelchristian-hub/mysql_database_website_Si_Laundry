<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Transaksi.php';

$transaksi = new Transaksi();
$data = $transaksi->ambilSemuaTransaksi();

if (!class_exists('FPDF')) {
    require_once __DIR__ . '/fpdf/fpdf.php'; 
}

// Menggunakan format Landscape (L) agar muat banyak kolom seperti referensi print_cuti
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// ==========================================
// KOP SURAT (Mengikuti gaya Landscape print_cuti)
// ==========================================
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'LAUNDRY APP SYSTEM', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Laporan Rekapitulasi Transaksi Masuk dan Riwayat Pembayaran', 0, 1, 'C');
$pdf->Ln(3);
$pdf->Cell(0, 0, '', 'B', 1);
$pdf->Ln(5);

// ==========================================
// JUDUL LAPORAN & TANGGAL CETAK
// ==========================================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'LAPORAN RIWAYAT TRANSAKSI LAUNDRY', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Tanggal Cetak: ' . date('d-m-Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// ==========================================
// HEADER TABEL (Lebar total disesuaikan dengan area A4 Landscape)
// ==========================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 8, 'No', 1, 0, 'C');
$pdf->Cell(55, 8, 'ID Transaksi', 1, 0, 'C');
$pdf->Cell(45, 8, 'Tanggal Terima', 1, 0, 'C');
$pdf->Cell(55, 8, 'Nama Pelanggan', 1, 0, 'C');
$pdf->Cell(40, 8, 'Status Laundry', 1, 0, 'C');
$pdf->Cell(35, 8, 'Status Bayar', 1, 0, 'C');
$pdf->Cell(32, 8, 'Kasir', 1, 0, 'C');
$pdf->Ln();

// ==========================================
// DATA TABEL (Looping)
// ==========================================
$pdf->SetFont('Arial', '', 9);
$no = 1;

foreach ($data as $row) {
    // Format tanggal terima agar lebih mudah dibaca (d-m-Y H:i)
    $tanggal_formatted = date('d-m-Y H:i', strtotime($row['tgl_terima']));

    $pdf->Cell(15, 7, $no++, 1, 0, 'C');
    $pdf->Cell(55, 7, $row['id_transaksi'], 1, 0, 'C');
    $pdf->Cell(45, 7, $tanggal_formatted, 1, 0, 'C');
    $pdf->Cell(55, 7, $row['nama_pelanggan'], 1, 0, 'L');
    $pdf->Cell(40, 7, ucfirst($row['status_laundry']), 1, 0, 'C');
    $pdf->Cell(35, 7, ucfirst($row['status_bayar']), 1, 0, 'C');
    $pdf->Cell(32, 7, $row['nama_kasir'], 1, 0, 'L');
    $pdf->Ln();
}

$pdf->Output('I', 'laporan_transaksi_laundry.pdf');
exit;
?>