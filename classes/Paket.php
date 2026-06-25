<?php
class Paket {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function tambah($nama_paket, $jenis, $harga_per_unit) {
        try {
            $sql = "INSERT INTO tbl_paket (nama_paket, jenis, harga_per_unit) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$nama_paket, $jenis, $harga_per_unit]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ambilSemuaPaket() {
        try {
            $sql = "SELECT * FROM tbl_paket ORDER BY id_paket DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function ambilPaketBerdasarkanId($id_paket) {
        try {
            $sql = "SELECT * FROM tbl_paket WHERE id_paket = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_paket]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ubahPaket($id_paket, $nama_paket, $jenis, $harga_per_unit) {
        try {
            $sql = "UPDATE tbl_paket SET nama_paket = ?, jenis = ?, harga_per_unit = ? WHERE id_paket = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$nama_paket, $jenis, $harga_per_unit, $id_paket]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function hapusPaket($id_paket) {
        try {
            $sql = "DELETE FROM tbl_paket WHERE id_paket = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$id_paket]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>