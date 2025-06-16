<?php
class Tanggungan extends Model // class model untuk mengelola data
{
    // Metode untuk mengambil tanggungan berdasarkan user_id (untuk user biasa)
    public function getByUser($id_user)
    {
        $stmt = $this->dbconn->prepare("SELECT t.*, k.kategori FROM tanggungan AS t JOIN kategori AS k ON t.kategori_id = k.kategori_id WHERE t.user_id = ? ORDER BY t.jadwal_pembayaran ASC");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // *** FUNGSI BARU UNTUK ADMIN: Mengambil semua tanggungan (tanpa filter user_id) ***
    public function getAll()
    {
        $stmt = $this->dbconn->prepare("SELECT t.*, k.kategori, u.username FROM tanggungan AS t JOIN kategori AS k ON t.kategori_id = k.kategori_id JOIN users AS u ON t.user_id = u.user_id ORDER BY t.jadwal_pembayaran ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Metode untuk mengambil satu tanggungan berdasarkan ID dan USER_ID (untuk validasi user biasa)
    public function getByIdAndUser($id_tanggungan, $id_user)
    {
        $stmt = $this->dbconn->prepare("SELECT * FROM tanggungan WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id_tanggungan, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // menyimpan satu baris data baru ke tabel tanggungan (untuk user biasa dan admin)
    public function insert($data)
    {
        // Hapus kolom 'permanen' dari VALUES karena kita tidak ingin mengaturnya secara default
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
            } elseif ($code == 1064) {
                $result = array("isSuccess" => false, "info" => "Kesalahan sintaks SQL.");
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

        $query = "UPDATE tanggungan SET tanggungan = ?, jadwal_pembayaran = ?, kategori_id = ?, jumlah = ?";
        $types = "ssii";
        $params = [
            $data['tanggungan'],
            $data['jadwal_pembayaran'],
            $data['kategori_id'],
            $data['jumlah']
        ];

        // Jika ada status di data, tambahkan ke query dan parameter
        if (isset($data['status'])) {
            $query .= ", status = ?";
            $types .= "s";
            $params[] = $data['status'];
        }

        $query .= " WHERE id = ? AND user_id = ?";
        $types .= "ii";
        $params[] = $id_tanggungan;
        $params[] = $data['user_id']; // Filter berdasarkan user_id

        $stmt = $this->dbconn->prepare($query);
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
        $query = "UPDATE tanggungan SET tanggungan = ?, jadwal_pembayaran = ?, kategori_id = ?, jumlah = ?";
        $types = "ssii";
        $params = [
            $data['tanggungan'],
            $data['jadwal_pembayaran'],
            $data['kategori_id'],
            $data['jumlah']
        ];

        // Admin bisa mengubah status
        if (isset($data['status'])) {
            $query .= ", status = ?";
            $types .= "s";
            $params[] = $data['status'];
        }

        $query .= " WHERE id = ?";
        $types .= "i";
        $params[] = $id_tanggungan;

        $stmt = $this->dbconn->prepare($query);
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
        // Menghapus kondisi 'permanen = 0' karena kita tidak lagi mengunci data
        $stmt = $this->dbconn->prepare("
            DELETE FROM tanggungan 
            WHERE id = ? AND user_id = ?
        ");

        $stmt->bind_param("ii", $id, $id_user);
        return $stmt->execute();
    }

    // *** FUNGSI BARU UNTUK ADMIN: Menghapus tanggungan berdasarkan ID saja (tanpa filter user_id) ***
    public function deleteAnyById($id)
    {
        $stmt = $this->dbconn->prepare("
            DELETE FROM tanggungan 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

