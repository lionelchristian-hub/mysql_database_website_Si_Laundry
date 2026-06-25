<?php
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Paket.php';


$paket = new Paket();
$data = $paket->ambilSemuaPaket();

if (!class_exists('FPDF')) {
    require_once __DIR__ . '/fpdf/fpdf.php'; 
}

// Menggunakan orientasi 'P' (Portrait) karena kolom paket tidak terlalu lebar, berbeda dengan laporan cuti yang 'L' (Landscape)
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// ==========================================
// KOP SURAT (Mengikuti referensi print_cuti)
// ==========================================
if (class_exists('Usaha')) {
    $usaha = new Usaha();
    $dataUsaha = $usaha->getData();
} else {
    $dataUsaha = null;
}

if ($dataUsaha) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, strtoupper($dataUsaha['nama']), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, $dataUsaha['alamat'], 0, 1, 'C');
    $alamat2 = "Telp: " . $dataUsaha['nomor_telepon'] . " | Email: " . $dataUsaha['email'];
    $pdf->Cell(0, 5, $alamat2, 0, 1, 'C');
    $pdf->Ln(3);
    $pdf->Cell(0, 0, '', 'B', 1); // Garis pembatas kop surat
    $pdf->Ln(5);
} else {
    // Jika data profil usaha belum ada, tampilkan nama Laundry standar
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, 'LAUNDRY APP SYSTEM', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Daftar Harga dan Layanan Operasional Jasa Laundry', 0, 1, 'C');
    $pdf->Ln(3);
    $pdf->Cell(0, 0, '', 'B', 1);
    $pdf->Ln(5);
}

// ==========================================
// JUDUL LAPORAN & TANGGAL CETAK
// ==========================================
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'DAFTAR MASTER PAKET CUCIAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Tanggal Cetak: ' . date('d-m-Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// ==========================================
// HEADER TABEL (Menyesuaikan lebar kolom kertas Portrait)
// ==========================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 8, 'No', 1, 0, 'C');
$pdf->Cell(45, 8, 'ID Paket', 1, 0, 'C');
$pdf->Cell(65, 8, 'Nama Paket Layanan', 1, 0, 'C');
$pdf->Cell(30, 8, 'Jenis Paket', 1, 0, 'C');
$pdf->Cell(35, 8, 'Harga / Unit', 1, 0, 'C');
$pdf->Ln();

// ==========================================
// DATA TABEL (Looping Data dari Database)
// ==========================================
$pdf->SetFont('Arial', '', 10);
$no = 1;

foreach ($data as $row) {
    $pdf->Cell(15, 7, $no++, 1, 0, 'C');
    $pdf->Cell(45, 7, 'PKT-' . str_pad($row['id_paket'], 3, '0', STR_PAD_LEFT), 1, 0, 'C'); // Format ID agar rapi (Contoh: PKT-001)
    $pdf->Cell(65, 7, $row['nama_paket'], 1, 0, 'L');
    $pdf->Cell(30, 7, ucfirst($row['jenis']), 1, 0, 'C'); // Menggunakan ucfirst agar huruf pertama kapital (Kiloan/Satuan)
    $pdf->Cell(35, 7, 'Rp ' . number_format($row['harga_per_unit'], 0, ',', '.'), 1, 0, 'R'); // Format mata uang Rupiah rata kanan
    $pdf->Ln();
}

// Output PDF ke browser
$pdf->Output('I', 'laporan_master_paket.pdf');
exit;
?>