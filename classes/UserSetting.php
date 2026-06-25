<?php
class UserSetting {
    private $db;
    private $uploadDir = 'assets/uploads/';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }


    public function getUserById($id_user) {
        try {
            $stmt = $this->db->prepare("SELECT id_user, nama_user, nama, role, foto FROM tbl_user WHERE id_user = ?");
            $stmt->execute([$id_user]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateProfile($id_user, $nama, $password_baru = '', $file_foto = null) {
        try {
            $user = $this->getUserById($id_user);
            if (!$user) {
                return ['status' => 'error', 'message' => 'User tidak ditemukan'];
            }

            $foto_filename = $user['foto']; 

            if ($file_foto && isset($file_foto['name']) && $file_foto['name'] !== '') {
                $validasi_foto = $this->validateFoto($file_foto);
                if ($validasi_foto['status'] !== 'success') {
                    return $validasi_foto;
                }

                if (!empty($user['foto']) && file_exists($this->uploadDir . $user['foto'])) {
                    unlink($this->uploadDir . $user['foto']);
                }

                $foto_filename = $this->uploadFoto($file_foto);
                if (!$foto_filename) {
                    return ['status' => 'error', 'message' => 'Gagal upload foto'];
                }
            }

            $updateFields = ['nama = ?'];
            $updateValues = [$nama];

            if (!empty($password_baru)) {
                $updateFields[] = 'password = ?';
                $updateValues[] = password_hash($password_baru, PASSWORD_DEFAULT);
            }

            if ($foto_filename) {
                $updateFields[] = 'foto = ?';
                $updateValues[] = $foto_filename;
            }

            $updateFields[] = 'id_user = ?';
            $updateValues[] = $id_user;

            $sql = "UPDATE tbl_user SET " . implode(', ', array_slice($updateFields, 0, -1)) . " WHERE id_user = ?";
            $stmt = $this->db->prepare($sql);

            if ($stmt->execute($updateValues)) {
                return ['status' => 'success', 'message' => 'Profile berhasil diperbarui'];
            } else {
                return ['status' => 'error', 'message' => 'Gagal memperbarui profile'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function validateFoto($file) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if ($file['size'] > $max_size) {
            return ['status' => 'error', 'message' => 'Ukuran foto maksimal 2MB'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            return ['status' => 'error', 'message' => 'Format foto hanya jpg, jpeg, png, gif'];
        }

        return ['status' => 'success'];
    }

    private function uploadFoto($file) {
        try {
            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0755, true);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'user_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $filepath = $this->uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return $filename;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
?>