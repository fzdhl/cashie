<?php
    class Target extends Model {
        public function getByUserId($user_id) {
            $stmt = $this->dbconn->prepare("SELECT * FROM target WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function insert($user_id, $target, $amount) {
            try {
                $stmt = $this->dbconn->prepare("INSERT INTO target (user_id, target, jumlah) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $user_id, $target, $amount);
                
                $stmt->execute();

                $status = "Penambahan berhasil!";
            } catch(mysqli_sql_exception $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $status = "Penambahan gagal: Target sudah ada.";
                } else {
                    $status = "Penambahan gagal: database error:" . $e->getMessage();
                }
            }
            return $status;
        }

        public function update($target_id, $target, $amount) {
            try {
                $stmt = $this->dbconn->prepare("UPDATE target SET target = ?, jumlah = ? WHERE target_id = ?");
                $stmt->bind_param("sii", $target, $amount, $target_id);
                
                $stmt->execute();

                $status = "Edit berhasil!";
            } catch(mysqli_sql_exception $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $status = "Edit gagal: Target sudah ada.";
                } else {
                    $status = "Edit gagal: database error:" . $e->getMessage();
                }
            }
            return $status;
        }

        public function delete($target_id) {
            try {
                $stmt = $this->dbconn->prepare("DELETE FROM target WHERE target_id = ?");
                $stmt->bind_param("i", $target_id);
    
                $stmt->execute();

                $status = "Hapus berhasil!";
            } catch(mysqli_sql_exception $e) {
                $status = "Database error:" . $e->getMessage();
            }

            return $status;           
        }
    }