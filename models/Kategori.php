<?php
class Kategori extends Model {
    public function getAllCategoriesByUser($userId) {
        $stmt = $this->dbconn->prepare("SELECT * FROM kategori WHERE user_id = ? ORDER BY kategori ASC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategoryById($kategoriId, $userId) {
        $stmt = $this->dbconn->prepare("SELECT * FROM kategori WHERE kategori_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $kategoriId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object();
    }

    public function getCategoryByNameAndType($userId, $kategori, $tipe) {
        $stmt = $this->dbconn->prepare("SELECT * FROM kategori WHERE user_id = ? AND kategori = ? AND tipe = ?");
        $stmt->bind_param("iss", $userId, $kategori, $tipe);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object();
    }

    public function addCategory($userId, $kategori, $tipe, $icon) {
        $stmt = $this->dbconn->prepare("INSERT INTO kategori (user_id, kategori, tipe, icon) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $kategori, $tipe, $icon);
        try {
            $stmt->execute();
            return array("isSuccess" => true, "insertId" => $this->dbconn->insert_id);
        } catch (mysqli_sql_exception $e) {
            $code = $e->getCode();
            if ($code == 1062) { // Duplicate entry for unique key
                return array("isSuccess" => false, "info" => "Kategori dengan nama dan tipe yang sama sudah ada.");
            }
            return array("isSuccess" => false, "info" => "Error database: " . $e->getMessage());
        }
    }

    public function updateCategory($kategoriId, $userId, $kategori, $tipe, $icon) {
        $stmt = $this->dbconn->prepare("UPDATE kategori SET kategori = ?, tipe = ?, icon = ? WHERE kategori_id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $kategori, $tipe, $icon, $kategoriId, $userId);
        return $stmt->execute();
    }

    public function deleteCategory($kategoriId, $userId) {
        $stmt = $this->dbconn->prepare("DELETE FROM kategori WHERE kategori_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $kategoriId, $userId);
        return $stmt->execute();
    }
}
?>