<?php
class Arsip extends Model {
    public function getByUser($user_id) {
        $stmt = $this->dbconn->prepare("SELECT * FROM arsip WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result(); // Mengembalikan objek mysqli_result
    }

    public function insert($user_id, $path, $desc) {
        $stmt = $this->dbconn->prepare("INSERT INTO arsip (user_id, file_path, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $path, $desc);
        return $stmt->execute();
    }

    public function updateDescription($id, $desc, $user_id) {
        $stmt = $this->dbconn->prepare("UPDATE arsip SET description = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $desc, $id, $user_id);
        return $stmt->execute();
    }

    public function delete($id, $user_id) {
        $stmt = $this->dbconn->prepare("DELETE FROM arsip WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }

    public function getById($id, $user_id) {
        $stmt = $this->dbconn->prepare("SELECT * FROM arsip WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }
}