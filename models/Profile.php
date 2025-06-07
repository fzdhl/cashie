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

        public function updatePhone($userID, $photo_dir, $phone_no) {
            $stmt = $this->dbconn->prepare("UPDATE PROFILE SET phone_no = ? WHERE user_id = ?");
            $stmt->bind_param("is", $userID, $phone_no);
            return $stmt->execute();
        }
    }