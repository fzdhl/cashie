<?php
    class Profile extends Model {
        public function getByUserID($userID) {
            $stmt = $this->dbconn->prepare("SELECT * FROM profile WHERE user_id = ?");
            $stmt->bind_param("s", $userID);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_object();
        } 

        public function createProfile($userID) {
            $stmt = $this->dbconn->prepare("INSERT INTO profile (user_id) VALUES (?)");
            $stmt->bind_param("i", $userID);
            return $stmt->execute();
        }

        public function updatePhoto($userID, $photo_dir) {
            $stmt = $this->dbconn->prepare("UPDATE profile SET photo_dir = ? WHERE user_id = ?");
            $stmt->bind_param("si", $photo_dir, $userID);
            
            $result = $stmt->execute();

            if (!$result) {
                die("SQL error: " . $this->dbconn->error);
            }

            return $result;
        }

        public function updatePhone($userID, $phone_no) {
            $stmt = $this->dbconn->prepare("UPDATE profile SET phone_no = ? WHERE user_id = ?");
            $stmt->bind_param("si", $phone_no, $userID);

            $result = $stmt->execute();

            if (!$result) {
                die("SQL error: " . $this->dbconn->error);
            }

            return $result;
        }
    }