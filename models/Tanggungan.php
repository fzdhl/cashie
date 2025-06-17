<?php
class Tanggungan extends Model // class model untuk mengelola data
{
    // Metode untuk mengambil tanggungan berdasarkan user_id (untuk user biasa)
    public function getByUser($id_user)
    {
        // Memilih tanggungan_id secara eksplisit
        $stmt = $this->dbconn->prepare("SELECT t.tanggungan_id, t.user_id, t.tanggungan, t.jadwal_pembayaran, t.kategori_id, t.jumlah, t.status, k.kategori FROM tanggungan AS t JOIN kategori AS k ON t.kategori_id = k.kategori_id WHERE t.user_id = ? ORDER BY t.jadwal_pembayaran ASC");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // *** FUNGSI BARU UNTUK ADMIN: Mengambil semua tanggungan (tanpa filter user_id) ***
    public function getAll()
    {
        // Memilih tanggungan_id secara eksplisit dan mengganti 'users' dengan 'user'
        $stmt = $this->dbconn->prepare("SELECT t.tanggungan_id, t.user_id, t.tanggungan, t.jadwal_pembayaran, t.kategori_id, t.jumlah, t.status, k.kategori, u.username FROM tanggungan AS t JOIN kategori AS k ON t.kategori_id = k.kategori_id JOIN user AS u ON t.user_id = u.user_id ORDER BY t.jadwal_pembayaran ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Metode untuk mengambil satu tanggungan berdasarkan ID dan USER_ID (untuk validasi user biasa)
    public function getByIdAndUser($id_tanggungan, $id_user)
    {
        // Menggunakan tanggungan_id sebagai filter
        $stmt = $this->dbconn->prepare("SELECT * FROM tanggungan WHERE tanggungan_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id_tanggungan, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // menyimpan satu baris data baru ke tabel tanggungan (untuk user biasa dan admin)
    public function insert($data)
    {
        $stmt = $this->dbconn->prepare("INSERT INTO tanggungan (user_id, tanggungan, jadwal_pembayaran, kategori_id, jumlah, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "issiis",
            $data[0], // user_id (bisa dari sesi user atau input admin)
            $data[1], // tanggungan
            $data[2], // jadwal_pembayaran
            $data[3], // kategori_id
            $data[4], // jumlah
            $data[5]  // status
        );

        try {
            $stmt->execute();
            $result = array("isSuccess" => true);
        } catch (mysqli_sql_exception $e) {
            $code = $e->getCode();
            if ($code == 1062) {
                $result = array("isSuccess" => false, "info" => "Terjadi duplikasi data.");
            } elseif ($code == 1452) { // Foreign key constraint fails
                $result = array("isSuccess" => false, "info" => "Kategori ID atau User ID tidak ditemukan. Pastikan ID valid.");
            } else {
                $result = array("isSuccess" => false, "info" => "Error lainnya: " . $e->getMessage());
            }
        }
        return $result;
    }

    // Metode update untuk user biasa (memerlukan user_id untuk keamanan)
    public function update($id_tanggungan, $data)
    {
        // Pastikan $data memiliki user_id untuk filter keamanan
        if (!isset($data['user_id'])) {
            error_log("Error: user_id is missing for update method.");
            return false;
        }

        $setClauses = [];
        $types = "";
        $params = [];

        // Bangun klausa SET secara dinamis berdasarkan data yang tersedia
        if (isset($data['tanggungan'])) { $setClauses[] = "tanggungan = ?"; $types .= "s"; $params[] = $data['tanggungan']; }
        if (isset($data['jadwal_pembayaran'])) { $setClauses[] = "jadwal_pembayaran = ?"; $types .= "s"; $params[] = $data['jadwal_pembayaran']; }
        if (isset($data['kategori_id'])) { $setClauses[] = "kategori_id = ?"; $types .= "i"; $params[] = $data['kategori_id']; }
        if (isset($data['jumlah'])) { $setClauses[] = "jumlah = ?"; $types .= "i"; $params[] = $data['jumlah']; }
        if (isset($data['status'])) { $setClauses[] = "status = ?"; $types .= "s"; $params[] = $data['status']; }

        if (empty($setClauses)) {
            error_log("No update fields provided for tanggungan ID: " . $id_tanggungan);
            return false; // Tidak ada yang perlu diupdate
        }

        // Menggunakan tanggungan_id sebagai PRIMARY KEY dan user_id sebagai filter keamanan
        $query = "UPDATE tanggungan SET " . implode(", ", $setClauses) . " WHERE tanggungan_id = ? AND user_id = ?";
        $types .= "ii";
        $params[] = $id_tanggungan;
        $params[] = $data['user_id']; // Filter berdasarkan user_id

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

    // *** FUNGSI BARU UNTUK ADMIN: Update tanggungan berdasarkan ID saja (tanpa filter user_id) ***
    public function updateById($id_tanggungan, $data)
    {
        $setClauses = [];
        $types = "";
        $params = [];

        // Bangun klausa SET secara dinamis berdasarkan data yang tersedia
        if (isset($data['tanggungan'])) { $setClauses[] = "tanggungan = ?"; $types .= "s"; $params[] = $data['tanggungan']; }
        if (isset($data['jadwal_pembayaran'])) { $setClauses[] = "jadwal_pembayaran = ?"; $types .= "s"; $params[] = $data['jadwal_pembayaran']; }
        if (isset($data['kategori_id'])) { $setClauses[] = "kategori_id = ?"; $types .= "i"; $params[] = $data['kategori_id']; }
        if (isset($data['jumlah'])) { $setClauses[] = "jumlah = ?"; $types .= "i"; $params[] = $data['jumlah']; }
        if (isset($data['status'])) { $setClauses[] = "status = ?"; $types .= "s"; $params[] = $data['status']; }
        // Admin dapat mengubah user_id dari suatu tanggungan
        if (isset($data['user_id'])) { $setClauses[] = "user_id = ?"; $types .= "i"; $params[] = $data['user_id']; }

        if (empty($setClauses)) {
            error_log("No update fields provided for tanggungan ID: " . $id_tanggungan);
            return false; // Tidak ada yang perlu diupdate
        }

        // Menggunakan tanggungan_id sebagai PRIMARY KEY
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

    // mengubah status semua tanggungan milik user menjadi permanen (tidak bisa diubah)
    // Metode ini mungkin tidak lagi digunakan, tetapi tetap biarkan jika ada kebutuhan lain
    public function setPermanen($id_user)
    {
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET permanen = 1 WHERE user_id = ?");
        $stmt->bind_param("i", $id_user);
        return $stmt->execute();
    }

    // mengatur ulang semua tagihan milik user (untuk user biasa)
    public function resetStatus($id_user)
    {
        // permanen = 0 juga dihapus karena tidak ada lagi konsep permanen yang mencegah perubahan
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET status = 'Belum dibayar' WHERE user_id = ?");
        $stmt->bind_param("i", $id_user);
        return $stmt->execute();
    }

    // *** FUNGSI BARU UNTUK ADMIN: Mengatur ulang semua tagihan di sistem ***
    public function resetAllStatuses()
    {
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET status = 'Belum dibayar'");
        return $stmt->execute();
    }

    // menandai tanggungan sebagai 'Selesai' jika cocok dengan data pengeluaran
    public function updateStatusSelesai($id_user, $keterangan, $jumlah)
    {
        $stmt = $this->dbconn->prepare("
            UPDATE tanggungan 
            SET status = 'Selesai' 
            WHERE user_id = ? AND tanggungan = ? AND jumlah = ? AND status = 'Belum dibayar'
        ");
        $stmt->bind_param("isd", $id_user, $keterangan, $jumlah);
        return $stmt->execute();
    }

    // mengambil semua data pengeluaran milik user tertentu
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

    // menghapus satu data tanggungan berdasarkan id dan user_id yang sesuai (untuk user biasa)
    public function deleteById($id, $id_user)
    {
        // Menggunakan tanggungan_id sebagai PRIMARY KEY
        $stmt = $this->dbconn->prepare("
            DELETE FROM tanggungan 
            WHERE tanggungan_id = ? AND user_id = ?
        ");

        $stmt->bind_param("ii", $id, $id_user);
        return $stmt->execute();
    }

    // *** FUNGSI BARU UNTUK ADMIN: Menghapus tanggungan berdasarkan ID saja (tanpa filter user_id) ***
    public function deleteAnyById($id)
    {
        // Menggunakan tanggungan_id sebagai PRIMARY KEY
        $stmt = $this->dbconn->prepare("
            DELETE FROM tanggungan 
            WHERE tanggungan_id = ?
        ");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>