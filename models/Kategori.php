<?php
class Kategori extends Model {
    public function getAllCategoriesByUser($userId) {
        // Jika userId adalah null, ambil semua kategori beserta username-nya. Ini untuk admin.
        if ($userId === null) {
            $stmt = $this->dbconn->prepare("SELECT k.*, u.username FROM kategori k JOIN user u ON k.user_id = u.user_id ORDER BY k.user_id ASC, k.kategori ASC");
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            // Logika asli untuk user biasa: ambil kategori berdasarkan user_id mereka
            $stmt = $this->dbconn->prepare("SELECT * FROM kategori WHERE user_id = ? ORDER BY kategori ASC");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt = $this->dbconn->prepare("SELECT * FROM kategori WHERE user_id = ? ORDER BY kategori ASC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategoriesByUserAndType($userId, $tipe) {
        $stmt = $this->dbconn->prepare("SELECT * FROM kategori WHERE user_id = ? AND tipe = ? ORDER BY kategori ASC");
        $stmt->bind_param("is", $userId, $tipe);
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

    // Metode baru untuk admin: mendapatkan kategori berdasarkan ID tanpa filter user_id
    public function getCategoryByIdAdmin($kategoriId) {
        $stmt = $this->dbconn->prepare("SELECT k.*, u.username FROM kategori k JOIN user u ON k.user_id = u.user_id WHERE kategori_id = ?");
        $stmt->bind_param("i", $kategoriId);
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
            // Check if user_id exists before inserting, foreign key constraint (error code 1452)
            if ($code == 1452) { 
                return array("isSuccess" => false, "info" => "User ID tidak valid atau tidak ditemukan.");
            }
            return array("isSuccess" => false, "info" => "Error database: " . $e->getMessage());
        }
    }

    public function updateCategory($kategoriId, $userId, $kategori, $tipe, $icon) {
        $stmt = $this->dbconn->prepare("UPDATE kategori SET kategori = ?, tipe = ?, icon = ? WHERE kategori_id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $kategori, $tipe, $icon, $kategoriId, $userId);
        return $stmt->execute();
    }

    // Metode baru untuk admin: update kategori dan user_id-nya
    public function updateCategoryAdmin($kategoriId, $userId, $kategori, $tipe, $icon) {
        $stmt = $this->dbconn->prepare("UPDATE kategori SET user_id = ?, kategori = ?, tipe = ?, icon = ? WHERE kategori_id = ?");
        $stmt->bind_param("isssi", $userId, $kategori, $tipe, $icon, $kategoriId);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            $code = $e->getCode();
            if ($code == 1062) {
                // Duplicate entry for unique key
                return false; 
            }
            // Check if user_id exists before updating (foreign key constraint)
            if ($code == 1452) { 
                return false; // User ID not found
            }
            return false; // Other database error
        }
    }

    public function deleteCategory($kategoriId, $userId) {
        $stmt = $this->dbconn->prepare("DELETE FROM kategori WHERE kategori_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $kategoriId, $userId);
        return $stmt->execute();
    }

    // Metode baru untuk admin: hapus kategori siapa saja
    public function deleteCategoryAdmin($kategoriId) {
        $stmt = $this->dbconn->prepare("DELETE FROM kategori WHERE kategori_id = ?");
        $stmt->bind_param("i", $kategoriId);
        return $stmt->execute();
    }
}

?>