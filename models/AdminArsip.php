<?php
class AdminArsip extends Model {
    // Fetches all archive entries from the database including username
    public function getAll() {
        $stmt = $this->dbconn->prepare("SELECT a.*, u.username FROM arsip a JOIN user u ON a.user_id = u.user_id ORDER BY a.created_at DESC");
        $stmt->execute();
        return $stmt->get_result(); // Returns mysqli_result object
    }

    // Metode insert dihapus karena admin tidak lagi bisa mengupload data.

    // Updates the description of an existing archive entry
    public function updateDescription($id, $desc) {
        $stmt = $this->dbconn->prepare("UPDATE arsip SET description = ? WHERE id = ?");
        $stmt->bind_param("si", $desc, $id);
        return $stmt->execute();
    }

    // Deletes an archive entry from the database
    public function delete($id) {
        $stmt = $this->dbconn->prepare("DELETE FROM arsip WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Fetches a single archive entry by its ID
    public function getById($id) {
        $stmt = $this->dbconn->prepare("SELECT * FROM arsip WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }
}
?>