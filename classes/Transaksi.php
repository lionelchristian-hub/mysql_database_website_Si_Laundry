<?php
class Transaksi {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function tambah($id_pelanggan, $id_user, $data_paket, $data_qty, $data_harga) {
        try {
            $this->db->beginTransaction();

            $id_transaksi = 'TRX-' . date('Ymd') . '-' . rand(100, 999);
            $tgl_terima = date('Y-m-d H:i:s');

            $sqlHeader = "INSERT INTO tbl_transaksi (id_transaksi, id_pelanggan, id_user, tgl_terima, status_laundry, status_bayar) 
                          VALUES (?, ?, ?, ?, 'proses', 'belum lunas')";
            $stmtHeader = $this->db->prepare($sqlHeader);
            $stmtHeader->execute([$id_transaksi, $id_pelanggan, $id_user, $tgl_terima]);
            $sqlDetail = "INSERT INTO tbl_detail_transaksi (id_transaksi, id_paket, qty, subtotal) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->db->prepare($sqlDetail);
            $jumlah_item = count($data_paket);
            for ($i = 0; $i < $jumlah_item; $i++) {
                $id_paket = $data_paket[$i];
                $qty = $data_qty[$i];
                $harga = $data_harga[$i];
                $subtotal = $qty * $harga;
                $stmtDetail->execute([$id_transaksi, $id_paket, $qty, $subtotal]);
            }
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function ambilSemuaTransaksi() {
        try {
            $sql = "SELECT t.*, p.nama_pelanggan, u.nama AS nama_kasir 
                    FROM tbl_transaksi t 
                    JOIN tbl_pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                    JOIN tbl_user u ON t.id_user = u.id_user 
                    ORDER BY t.tgl_terima DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function ubahStatus($id_transaksi, $status_laundry, $status_bayar) {
        try {
            $tgl_selesai = ($status_laundry == 'selesai' || $status_laundry == 'diambil') ? date('Y-m-d H:i:s') : null;
            $sql = "UPDATE tbl_transaksi SET status_laundry = ?, status_bayar = ?, tgl_selesai = ? WHERE id_transaksi = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status_laundry, $status_bayar, $tgl_selesai, $id_transaksi]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>