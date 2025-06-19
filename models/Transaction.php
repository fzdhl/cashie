<?php
  include_once "models/Model.php";

  class Transaction extends Model {
    
    public function getRecentTransaction($userId){
        $query = "SELECT * FROM transaksi t JOIN kategori k ON k.kategori_id = t.kategori_id WHERE t.user_id= ? ORDER BY tanggal_transaksi ASC LIMIT 10";

        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Mengambil semua kategori milik seorang pengguna
    public function getCategoriesByUser($userId) {
        $query = "SELECT * FROM kategori WHERE user_id= ?";

        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // [MODIFIKASI] Mengambil semua tagihan (bills) yang belum lunas milik pengguna, termasuk kolom 'jumlah'
    public function getBillsByUser($userId) {
        $query = "SELECT tanggungan_id, tanggungan, jumlah FROM tanggungan WHERE user_id = ? AND status = 0"; // <<==== TAMBAHAN: ", jumlah" ====
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getGoalsByUser($userId) {
        $query = "SELECT target_id, target FROM target WHERE user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insertTransaction($data) {
        $query = "INSERT INTO transaksi (user_id, kategori_id, jumlah, keterangan, tanggal_transaksi, tanggungan_id, target_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->dbconn->prepare($query);
        
        $datetime = $data['date'] . ' ' . date('H:i:s');
        
        $bill_id = !empty($data['tagihan_id']) ? $data['tagihan_id'] : NULL;
        $goal_id = !empty($data['target_id']) ? $data['target_id'] : NULL;

        $stmt->bind_param(
            "iiissii",
            $data['user_id'],
            $data['kategori_id'],
            $data['jumlah'],
            $data['keterangan'],
            $datetime,
            $bill_id,
            $goal_id
        );
        return $stmt->execute();
    }

    public function getTransactionById($transactionId, $userId) {
        $query = "SELECT transaksi_id, kategori_id, jumlah, keterangan, tanggal_transaksi, tanggungan_id, target_id FROM transaksi WHERE transaksi_id = ? AND user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("ii", $transactionId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateTransaction($data) {
        $query = "UPDATE transaksi SET kategori_id = ?, jumlah = ?, keterangan = ?, tanggal_transaksi = ?, tanggungan_id = ?, target_id = ? WHERE transaksi_id = ? AND user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        
        $datetime = $data['date'] . ' ' . date('H:i:s');
        $bill_id = !empty($data['tagihan_id']) ? $data['tagihan_id'] : NULL;
        $goal_id = !empty($data['target_id']) ? $data['target_id'] : NULL;
        
        $stmt->bind_param(
            "iissiiii", 

            $data['kategori_id'],
            $data['jumlah'],
            $data['keterangan'],
            $datetime,
            $bill_id,
            $goal_id,
            $data['transaksi_id'],
            $data['user_id']
        );

        $stmt->execute();

        $affected_rows = $stmt->affected_rows;

        $stmt->close();

        return $affected_rows;
    }

    public function deleteTransaction($transactionId, $userId) {
        $query = "DELETE FROM transaksi WHERE transaksi_id = ? AND user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("ii", $transactionId, $userId);
        return $stmt->execute();
    }

    // [BARU] Menambahkan metode untuk mendapatkan ringkasan pemasukan dan pengeluaran
    public function getSummaryByUserId($userId) {
        $query = "SELECT
                SUM(CASE WHEN k.tipe = 'pemasukan' THEN t.jumlah ELSE 0 END) as total_pemasukan,
                SUM(CASE WHEN k.tipe = 'pengeluaran' THEN t.jumlah ELSE 0 END) as total_pengeluaran
            FROM
                transaksi AS t
            JOIN
                kategori AS k ON t.kategori_id = k.kategori_id
            WHERE
                t.user_id = ?
        ";
        $stmt = $this->dbconn->prepare($query);
        if (!$stmt) {
            // Handle error, misalnya dengan logging atau mengembalikan array kosong
            error_log("Query preparation failed: " . $this->dbconn->error);
            return ['total_pemasukan' => 0, 'total_pengeluaran' => 0];
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllTransactions() {
        $query = "SELECT 
                t.transaksi_id,
                t.tanggal_transaksi,
                t.jumlah,
                t.keterangan,
                u.username,
                k.kategori,
                k.tipe,
                t.user_id,
                t.kategori_id
            FROM 
                transaksi AS t
            JOIN 
                user AS u ON t.user_id = u.user_id
            JOIN 
                kategori AS k ON t.kategori_id = k.kategori_id
            ORDER BY 
                t.tanggal_transaksi DESC
        ";
        $stmt = $this->dbconn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

     // [BARU] Menghapus satu transaksi berdasarkan ID-nya saja (untuk admin)
    public function deleteTransactionByIdAdmin($transactionId) {
        $query = "DELETE FROM transaksi WHERE transaksi_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $transactionId);
        return $stmt->execute();
    }

    public function deleteAllTransactions() {
        $query = "TRUNCATE TABLE transaksi"; // TRUNCATE lebih efisien daripada DELETE FROM
        $stmt = $this->dbconn->prepare($query);
        return $stmt->execute();
    }

    // [BARU] Update transaksi secara dinamis oleh Admin
    public function updateTransactionAdmin($transactionId, $data)
    {
        $setClauses = [];
        $types = "";
        $params = [];

        // Bangun query secara dinamis berdasarkan data yang diterima
        if (isset($data['tanggal_transaksi'])) {
            $setClauses[] = "tanggal_transaksi = ?";
            $types .= "s";
            $params[] = $data['tanggal_transaksi'];
        }
        if (isset($data['user_id'])) {
            $setClauses[] = "user_id = ?";
            $types .= "i";
            $params[] = $data['user_id'];
        }
        if (isset($data['kategori_id'])) {
            $setClauses[] = "kategori_id = ?";
            $types .= "i";
            $params[] = $data['kategori_id'];
        }
        if (isset($data['keterangan'])) {
            $setClauses[] = "keterangan = ?";
            $types .= "s";
            $params[] = $data['keterangan'];
        }
        if (isset($data['jumlah'])) {
            $setClauses[] = "jumlah = ?";
            $types .= "d"; // Tipe 'd' untuk double/decimal
            $params[] = $data['jumlah'];
        }

        if (empty($setClauses)) {
            // Tidak ada data untuk diupdate
            return false;
        }

        $query = "UPDATE transaksi SET " . implode(", ", $setClauses) . " WHERE transaksi_id = ?";
        $types .= "i";
        $params[] = $transactionId;

        $stmt = $this->dbconn->prepare($query);
        if (!$stmt) {
            error_log("Gagal mempersiapkan statement update: " . $this->dbconn->error);
            return false;
        }
        
        $stmt->bind_param($types, ...$params);

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            error_log("Error saat update transaksi admin: " . $e->getMessage());
            return false;
        }
    }
  }
?>