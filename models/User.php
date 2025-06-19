<?php
    class User extends Model {
        public function getByUsername($username) {
            $stmt = $this->dbconn->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_object();
        }

        public function getById($user_id) {
            $stmt = $this->dbconn->prepare("SELECT * FROM user WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_object();
        }

        public function createUser($username, $password, $email) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->dbconn->prepare("INSERT INTO user (email, password, username) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed_password, $username);
            
            return $stmt->execute();
        }

        public function deleteUser($user_id){
            $stmt = $this->dbconn->prepare("DELETE FROM laporan WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $stmt = $this->dbconn->prepare("DELETE FROM transaksi WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $stmt = $this->dbconn->prepare("DELETE FROM kategori WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $stmt = $this->dbconn->prepare("DELETE FROM target WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $stmt = $this->dbconn->prepare("DELETE FROM tanggungan WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $stmt = $this->dbconn->prepare("DELETE FROM arsip WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $stmt = $this->dbconn->prepare("DELETE FROM profile WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $stmt = $this->dbconn->prepare("DELETE FROM user WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }

        public function updateUser($user_id, $username, $email){
            $stmt = $this->dbconn->prepare("UPDATE user SET email = ?, username = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $email, $username, $user_id);
            
            return $stmt->execute();
        }

        public function updateUserData($user_id, $username, $password, $email){
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->dbconn->prepare("UPDATE user SET email = ?, password = ?, username = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $email, $hashed_password, $username, $user_id);
            
            return $stmt->execute();
        }

        public function getAllUser() {
            $stmt = $this->dbconn->prepare("SELECT * FROM user where privilege = 'user'");
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function getAll() {
            $stmt = $this->dbconn->prepare("SELECT * FROM user");
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }