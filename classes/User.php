<?php
class User {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }
    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_user WHERE nama_user = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            Session::set('user_id', $user['id_user']);
            Session::set('nama_user', $user['nama_user']);
            Session::set('role', $user['role']);
            Session::set('nama', $user['nama']);
            $log = new LogAktivitas();
            $pesan_log = "User " . $user['nama'] . " (Role: " . $user['role'] . ") berhasil masuk ke sistem (Login)";
            $log->catatLog($user['id_user'], $pesan_log);
            return true;
        }
        return false;
    }
    public function isLoggedIn() { return Session::get('user_id') !== null; }
    public function logout() { 
        $id_user = Session::get('user_id');
        if ($id_user) {
            $log = new LogAktivitas();
            $log->catatLog(Session::get('user_id'), " " . Session::get('nama') . " berhasil keluar dari sistem (Logout)");
        }
        Session::destroy(); 
    }
    public function registerAdmin($username, $password, $nama_lengkap, $role) {
        try {
            $id_user = 'ADM-' . rand(1000, 9999); 
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO tbl_user (id_user, nama_user, password, role, nama) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_user, $username, $password_hash, $role, $nama_lengkap]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function registerClient($username, $password, $nama_lengkap) {
        try {
            $this->db->beginTransaction();
            $id_user = 'CLI-' . rand(1000, 9999); 
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $sqlUser = "INSERT INTO tbl_user (id_user, nama_user, password, role, nama) VALUES (?, ?, ?, 'client', ?)";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([$id_user, $username, $password_hash, $nama_lengkap]);
            $sqlPelanggan = "INSERT INTO tbl_pelanggan (id_pelanggan, nama_pelanggan, no_telp, alamat) VALUES (?, ?, '', '')";
            $stmtPelanggan = $this->db->prepare($sqlPelanggan);
            $stmtPelanggan->execute([$id_user, $nama_lengkap]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
}