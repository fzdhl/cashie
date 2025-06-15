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
            $stmt = $this->dbconn->prepare("INSERT INTO target (user_id, target, jumlah) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $user_id, $target, $amount);
            
            $result = $stmt->execute();

            if (!$result) {
                die("SQL error: " . $this->dbconn->error);
            }

            return $result;          
        }

        public function update($target_id, $target, $amount) {
            $stmt = $this->dbconn->prepare("UPDATE target SET target = ?, jumlah = ? WHERE target_id = ?");
            $stmt->bind_param("sii", $target, $amount, $target_id);
            
            $result = $stmt->execute();

            if (!$result) {
                die("SQL error: " . $this->dbconn->error);
            }

            return $result;
        }

        public function delete($target_id) {
            $stmt = $this->dbconn->prepare("DELETE FROM target WHERE target_id = ?");
            $stmt->bind_param("i", $target_id);

            $result = $stmt->execute();

            if (!$result) {
                die("SQL error: " . $this->dbconn->error);
            }

            return $result;            
        }
    }