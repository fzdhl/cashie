<?php
    class User extends Model {
        public function getByUsername($username) {
            $stmt = $this->dbconn->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_object();
        }

        public function createUser($username, $password, $email) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // 3. masukkan data user ke dalam tabel users
            // $stmt = $this->dbconn->query("INSERT INTO user (email, password, username) VALUES (?, ?, ?)");
            $stmt = $this->dbconn->prepare("INSERT INTO user (email, password, username) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed_password, $username);
            
            // 4. kembalikan hasil dari querynya
            return $stmt->execute();
        }
    }