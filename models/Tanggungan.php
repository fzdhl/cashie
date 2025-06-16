<?php
class Tanggungan extends Model // class model untuk mengelola data
{

    public function getByUser($id_user)
    {
        $stmt = $this->dbconn->prepare("SELECT t.*, k.kategori FROM tanggungan AS t JOIN kategori AS k ON t.kategori_id = k.kategori_id WHERE t.user_id = ? ORDER BY t.jadwal_pembayaran ASC");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // menyimpan satu baris data baru ke tabel tanggungan
    public function insert($data)
    {
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
            } elseif ($code == 1064) {
                $result = array("isSuccess" => false, "info" => "Kesalahan sintaks SQL.");
            } else {
                $result = array("isSuccess" => false, "info" => "Error lainnya: " . $e->getMessage());
            }
        }

        return $result;
    }

    // mengubah status semua tanggungan milik user menjadi permanen (tidak bisa diubah)
    public function setPermanen($id_user)
    {
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET permanen = 1 WHERE user_id = ?");
        $stmt->bind_param("i", $id_user);
        return $stmt->execute();
    }

    // mengatur ulang semua tagihan milik user
    public function resetStatus($id_user)
    {
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET status = 'Belum dibayar', permanen = 0 WHERE user_id = ?");
        $stmt->bind_param("i", $id_user);
        return $stmt->execute();
    }

    // menandai tanggungan sebagai 'Selesai' jika cocok dengan data pembayaran
    public function updateStatusSelesai($id_user, $nama, $jumlah)
    {
        $stmt = $this->dbconn->prepare("
        UPDATE tanggungan 
        SET status = 'Selesai' 
        WHERE user_id = ? AND nama = ? AND jumlah = ? AND status = 'Belum dibayar'
    ");
        $stmt->bind_param("isd", $id_user, $nama, $jumlah);
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

    // menghapus satu data tanggungan berdasarkan id dan user_id yang sesuai
    public function deleteById($id, $id_user)
    {
        $stmt = $this->dbconn->prepare("
        DELETE FROM tanggungan 
        WHERE id = ? AND user_id = ? AND permanen = 0
    ");

        $stmt->bind_param("ii", $id, $id_user);
        return $stmt->execute();
    }
}
?>