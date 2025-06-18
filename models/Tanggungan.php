<?php
class Tanggungan extends Model
{
    public function getByUser($id_user)
    {
        $stmt = $this->dbconn->prepare("
            SELECT 
                t.tanggungan_id, t.user_id, t.tanggungan, t.jadwal_pembayaran, t.kategori_id, t.jumlah, 
                CASE 
                    WHEN t.status = 0 THEN 'Belum dibayar' 
                    WHEN t.status = 1 THEN 'Selesai' 
                    ELSE 'Tidak Diketahui' 
                END AS status,
                k.kategori 
            FROM tanggungan AS t 
            JOIN kategori AS k ON t.kategori_id = k.kategori_id 
            WHERE t.user_id = ? 
            ORDER BY t.jadwal_pembayaran ASC
        ");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->dbconn->prepare("
            SELECT 
                t.tanggungan_id, t.user_id, t.tanggungan, t.jadwal_pembayaran, t.kategori_id, t.jumlah, 
                CASE 
                    WHEN t.status = 0 THEN 'Belum dibayar' 
                    WHEN t.status = 1 THEN 'Selesai' 
                    ELSE 'Tidak Diketahui' 
                END AS status,
                k.kategori, u.username 
            FROM tanggungan AS t 
            JOIN kategori AS k ON t.kategori_id = k.kategori_id 
            JOIN user AS u ON t.user_id = u.user_id 
            ORDER BY t.jadwal_pembayaran ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByIdAndUser($id_tanggungan, $id_user)
    {
        $stmt = $this->dbconn->prepare("
            SELECT 
                t.tanggungan_id, t.user_id, t.tanggungan, t.jadwal_pembayaran, t.kategori_id, t.jumlah, 
                CASE 
                    WHEN t.status = 0 THEN 'Belum dibayar' 
                    WHEN t.status = 1 THEN 'Selesai' 
                    ELSE 'Tidak Diketahui' 
                END AS status
            FROM tanggungan AS t 
            WHERE t.tanggungan_id = ? AND t.user_id = ?
        ");
        $stmt->bind_param("ii", $id_tanggungan, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Metode baru yang ditemukan: getNextTanggungan
    public function getNextTanggungan($id_user){
        $stmt = $this->dbconn->prepare("SELECT *, 
            DAY(jadwal_pembayaran) - DAY(CURDATE()) AS sisa_hari
            FROM tanggungan
            WHERE user_id = ? 
            AND DAY(jadwal_pembayaran) - DAY(CURDATE()) >= 0
            ORDER BY sisa_hari ASC
            LIMIT 3");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function insert($data)
    {
        $status_db = ($data[5] === 'Selesai') ? 1 : 0;
        $stmt = $this->dbconn->prepare("INSERT INTO tanggungan (user_id, tanggungan, jadwal_pembayaran, kategori_id, jumlah, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "issiis",
            $data[0],
            $data[1],
            $data[2],
            $data[3],
            $data[4],
            $data[5]
        );

        try {
            $stmt->execute();
            $result = array("isSuccess" => true);
        } catch (mysqli_sql_exception $e) {
            $code = $e->getCode();
            if ($code == 1062) {
                $result = array("isSuccess" => false, "info" => "Terjadi duplikasi data.");
            } elseif ($code == 1452) {
                $result = array("isSuccess" => false, "info" => "Kategori ID atau User ID tidak ditemukan. Pastikan ID valid.");
            } else {
                $result = array("isSuccess" => false, "info" => "Error lainnya: " . $e->getMessage());
            }
        }
        return $result;
    }

    public function update($id_tanggungan, $data)
    {
        if (!isset($data['user_id'])) {
            error_log("Error: user_id is missing for update method.");
            return false;
        }

        $setClauses = [];
        $types = "";
        $params = [];

        if (isset($data['tanggungan'])) {
            $setClauses[] = "tanggungan = ?";
            $types .= "s";
            $params[] = $data['tanggungan'];
        }
        if (isset($data['jadwal_pembayaran'])) {
            $setClauses[] = "jadwal_pembayaran = ?";
            $types .= "s";
            $params[] = $data['jadwal_pembayaran'];
        }
        if (isset($data['kategori_id'])) {
            $setClauses[] = "kategori_id = ?";
            $types .= "i";
            $params[] = $data['kategori_id'];
        }
        if (isset($data['jumlah'])) {
            $setClauses[] = "jumlah = ?";
            $types .= "i";
            $params[] = $data['jumlah'];
        }
        if (isset($data['status'])) {
            $status_db = ($data['status'] === 'Selesai') ? 1 : 0;
            $setClauses[] = "status = ?";
            $types .= "i";
            $params[] = $status_db;
        }
        if (empty($setClauses)) {
            error_log("No update fields provided for tanggungan ID: " . $id_tanggungan);
            return false;
        }

        $query = "UPDATE tanggungan SET " . implode(", ", $setClauses) . " WHERE tanggungan_id = ? AND user_id = ?";
        $types .= "ii";
        $params[] = $id_tanggungan;
        $params[] = $data['user_id'];

        $stmt = $this->dbconn->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare update statement for user: " . $this->dbconn->error);
            return false;
        }
        $stmt->bind_param($types, ...$params);

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            error_log("Error updating tanggungan for user: " . $e->getMessage());
            return false;
        }
    }

    public function updateById($id_tanggungan, $data)
    {
        $setClauses = [];
        $types = "";
        $params = [];

        if (isset($data['tanggungan'])) {
            $setClauses[] = "tanggungan = ?";
            $types .= "s";
            $params[] = $data['tanggungan'];
        }
        if (isset($data['jadwal_pembayaran'])) {
            $setClauses[] = "jadwal_pembayaran = ?";
            $types .= "s";
            $params[] = $data['jadwal_pembayaran'];
        }
        if (isset($data['kategori_id'])) {
            $setClauses[] = "kategori_id = ?";
            $types .= "i";
            $params[] = $data['kategori_id'];
        }
        if (isset($data['jumlah'])) {
            $setClauses[] = "jumlah = ?";
            $types .= "i";
            $params[] = $data['jumlah'];
        }
        if (isset($data['status'])) {
            $status_db = ($data['status'] === 'Selesai') ? 1 : 0;
            $setClauses[] = "status = ?";
            $types .= "i";
            $params[] = $status_db;
        }
        if (isset($data['user_id'])) {
            $setClauses[] = "user_id = ?";
            $types .= "i";
            $params[] = $data['user_id'];
        }

        if (empty($setClauses)) {
            error_log("No update fields provided for tanggungan ID: " . $id_tanggungan);
            return false;
        }

        $query = "UPDATE tanggungan SET " . implode(", ", $setClauses) . " WHERE tanggungan_id = ?";
        $types .= "i";
        $params[] = $id_tanggungan;

        $stmt = $this->dbconn->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare updateById statement: " . $this->dbconn->error);
            return false;
        }
        $stmt->bind_param($types, ...$params);

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            error_log("Error updating tanggungan by admin: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatusByDetails($userId, $tanggunganNama, $jumlah) {
        $query = "UPDATE tanggungan SET status = 1 WHERE user_id = ? AND tanggungan = ? AND jumlah = ? AND status = 0";
        $stmt = $this->dbconn->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare updateStatusByDetails statement: " . $this->dbconn->error);
            return false;
        }
        $stmt->bind_param("isi", $userId, $tanggunganNama, $jumlah);
        
        try {
            $stmt->execute();
            return $stmt->affected_rows;
        } catch (mysqli_sql_exception $e) {
            error_log("Error updating tanggungan status by details: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memperbarui status tanggungan menjadi 'Selesai' (1) berdasarkan tanggungan_id, user_id, dan jumlah.
     * Hanya akan memperbarui jika status tanggungan saat ini adalah 'Belum dibayar' (0).
     * Ini adalah metode yang lebih akurat untuk sinkronisasi berdasarkan pilihan tagihan.
     *
     * @param int $userId ID pengguna yang memiliki tanggungan.
     * @param int $tanggunganId ID tanggungan yang akan diperbarui.
     * @param int $jumlah Jumlah nominal tanggungan yang harus cocok.
     * @return int|bool Jumlah baris yang terpengaruh jika berhasil, atau false jika ada error.
     */
    public function updateStatusByBillIdAndAmount($userId, $tanggunganId, $jumlah) {
        $query = "UPDATE tanggungan SET status = 1 WHERE tanggungan_id = ? AND user_id = ? AND jumlah = ? AND status = 0";
        $stmt = $this->dbconn->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare updateStatusByBillIdAndAmount statement: " . $this->dbconn->error);
            return false;
        }
        $stmt->bind_param("iii", $tanggunganId, $userId, $jumlah);
        
        try {
            $stmt->execute();
            return $stmt->affected_rows;
        } catch (mysqli_sql_exception $e) {
            error_log("Error updating tanggungan status by bill_id and amount: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengatur status tanggungan kembali menjadi 'Belum dibayar' (0) berdasarkan tanggungan_id dan user_id.
     * Ini akan digunakan ketika transaksi terkait dihapus.
     *
     * @param int $userId ID pengguna yang memiliki tanggungan.
     * @param int $tanggunganId ID tanggungan yang akan direset statusnya.
     * @return int|bool Jumlah baris yang terpengaruh jika berhasil, atau false jika ada error.
     */
    public function resetStatusByBillIdAndUser($userId, $tanggunganId) {
        $query = "UPDATE tanggungan SET status = 0 WHERE tanggungan_id = ? AND user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare resetStatusByBillIdAndUser statement: " . $this->dbconn->error);
            return false;
        }
        $stmt->bind_param("ii", $tanggunganId, $userId);
        
        try {
            $stmt->execute();
            return $stmt->affected_rows;
        } catch (mysqli_sql_exception $e) {
            error_log("Error resetting tanggungan status by bill_id and user: " . $e->getMessage());
            return false;
        }
    }

    public function setPermanen($id_user)
    {
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET permanen = 1 WHERE user_id = ?");
        $stmt->bind_param("i", $id_user);
        return $stmt->execute();
    }

    public function resetStatus($id_user)
    {
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET status = 0 WHERE user_id = ?");
        $stmt->bind_param("i", $id_user);
        return $stmt->execute();
    }

    public function resetAllStatuses()
    {
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET status = 0");
        return $stmt->execute();
    }

    public function updateStatusSelesai($id_user, $keterangan, $jumlah)
    {
        $stmt = $this->dbconn->prepare("
            UPDATE tanggungan 
            SET status = 1 
            WHERE user_id = ? AND tanggungan = ? AND jumlah = ? AND status = 0
        ");
        $stmt->bind_param("isd", $id_user, $keterangan, $jumlah);
        return $stmt->execute();
    }

    public function getPengeluaranUser($id_user)
    {
        $stmt = $this->dbconn->prepare("
            SELECT keterangan, jumlah 
            FROM catatan_keuangan 
            WHERE user_id = ? AND jenis_transaksi = 'pengeluaran'
        ");

        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteById($id, $id_user)
    {
        $stmt = $this->dbconn->prepare("
            DELETE FROM tanggungan 
            WHERE tanggungan_id = ? AND user_id = ?
        ");

        $stmt->bind_param("ii", $id, $id_user);
        return $stmt->execute();
    }

    public function deleteAnyById($id)
    {
        $stmt = $this->dbconn->prepare("
            DELETE FROM tanggungan 
            WHERE tanggungan_id = ?
        ");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}