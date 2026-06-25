<?php
class Pelanggan {
    private $db;
    private $log;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->log = new LogAktivitas();
    }
    
    public function ambilCalonPelanggan() {
        try {
            $sql = "SELECT id_user, nama_user, nama 
                    FROM tbl_user 
                    WHERE role = 'client' 
                    AND id_user NOT IN (SELECT id_pelanggan FROM tbl_pelanggan)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function ambilSemuaPelanggan() {
        try {
            $sql = "SELECT * FROM tbl_pelanggan ORDER BY nama_pelanggan ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function tambahPelanggan($id_pelanggan, $nama_pelanggan, $no_telp, $alamat) {
        try {
            $sql = "INSERT INTO tbl_pelanggan (id_pelanggan, nama_pelanggan, no_telp, alamat) VALUES (?, ?, ?, ?)";
            $this->log->catatLog(Session::get('user_id'), "Mendaftarkan profil pelanggan untuk: " . $nama_pelanggan);
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_pelanggan, $nama_pelanggan, $no_telp, $alamat]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ambilPelangganSajaBerdasarkanId($id_pelanggan) {
        try {
            $sql = "SELECT * FROM tbl_pelanggan WHERE id_pelanggan = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_pelanggan]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ubahPelanggan($id_pelanggan, $nama_pelanggan, $no_telp, $alamat) {
        try {
            $sql = "UPDATE tbl_pelanggan SET nama_pelanggan = ?, no_telp = ?, alamat = ? WHERE id_pelanggan = ?";
            $this->log->catatLog(Session::get('user_id'), "Memperbarui profil pelanggan ID #" . $id_pelanggan . " (" . $nama_pelanggan . ")");
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$nama_pelanggan, $no_telp, $alamat, $id_pelanggan]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function hapusPelanggan($id_pelanggan) {
        try {
            $sql = "DELETE FROM tbl_pelanggan WHERE id_pelanggan = ?";
            $this->log->catatLog(Session::get('user_id'), "Menghapus pelanggan ID #" . $id_pelanggan);
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_pelanggan]);
        } catch (PDOException $e) {
            return false;
        }
    }
}