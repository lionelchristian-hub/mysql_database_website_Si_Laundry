<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Pelanggan.php';

$pelanggan = new Pelanggan();
$data = $pelanggan->ambilSemuaPelanggan();

if (!class_exists('FPDF')) {
    require_once __DIR__ . '/fpdf/fpdf.php'; 
}

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// ==========================================
// KOP SURAT
// ==========================================
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'LAUNDRY APP SYSTEM', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Daftar Pelanggan Tetap dan Riwayat Kontak Terdaftar', 0, 1, 'C');
$pdf->Ln(3);
$pdf->Cell(0, 0, '', 'B', 1);
$pdf->Ln(5);

// ==========================================
// JUDUL LAPORAN & TANGGAL CETAK
// ==========================================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'DAFTAR MASTER DATA PELANGGAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Tanggal Cetak: ' . date('d-m-Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// ==========================================
// HEADER TABEL
// ==========================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 8, 'No', 1, 0, 'C');
$pdf->Cell(45, 8, 'Nama Pelanggan', 1, 0, 'C');
$pdf->Cell(40, 8, 'No. Telepon', 1, 0, 'C');
$pdf->Cell(90, 8, 'Alamat Lengkap', 1, 0, 'C');
$pdf->Ln();

// ==========================================
// DATA TABEL (Looping)
// ==========================================
$pdf->SetFont('Arial', '', 10);
$no = 1;

foreach ($data as $row) {
    $pdf->Cell(15, 7, $no++, 1, 0, 'C');
    $pdf->Cell(45, 7, $row['nama_pelanggan'], 1, 0, 'L');
    $pdf->Cell(40, 7, $row['no_telp'], 1, 0, 'C');
    $pdf->Cell(90, 7, $row['alamat'], 1, 0, 'L');
    $pdf->Ln();
}

$pdf->Output('I', 'laporan_master_pelanggan.pdf');
exit;
?>