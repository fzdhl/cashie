<?php
class Tanggungan extends Model // class model untuk mengelola data
{

    public function getByUser($id_user)
    {
        // menyiapkan query untuk mengambil semua data tanggungan milik user tertentu
        // hasil diurutkan berdasarkan jadwal pembayaran paling awal
        $stmt = $this->dbconn->prepare("SELECT * FROM tanggungan WHERE user_id = ? ORDER BY jadwal_pembayaran ASC");

        // mengikat parameter user_id (tipe i = integer)
        $stmt->bind_param("i", $id_user);

        // menjalankan query
        $stmt->execute();

        // mengambil hasil dalam bentuk objek result set
        $result = $stmt->get_result();

        // mengembalikan semua data sebagai array asosiatif
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // menyimpan satu baris data baru ke tabel tanggungan
    public function insert($data)
    {
        // menyiapkan statement SQL untuk insert data baru
        $stmt = $this->dbconn->prepare("INSERT INTO tanggungan (user_id, nama, jadwal_pembayaran, jenis, jumlah, status) VALUES (?, ?, ?, ?, ?, ?)");

        // binding parameter ke statement SQL
        // i = integer, s = string
        $stmt->bind_param(
            "isssis",
            $data[0], // user_id (int)
            $data[1], // nama (string)
            $data[2], // jadwal_pembayaran (string/tanggal)
            $data[3], // jenis (string)
            $data[4], // jumlah (int)
            $data[5] // status (string)
        );

        try {
            // eksekusi statement
            $stmt->execute();

            // hasil berhasil
            $result = array("isSuccess" => true);
        } catch (mysqli_sql_exception $e) {
            // tangkap error dengan kode tertentu
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
        // permanen = 1 menandakan data tidak bisa diedit atau dihapus hingga dilakukan reset
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET permanen = 1 WHERE user_id = ?");

        // binding parameter: i = integer (id_user)
        $stmt->bind_param("i", $id_user);

        // eksekusi perintah dan kembalikan true/false sebagai hasilnya
        return $stmt->execute();
    }



    // mengatur ulang semua tagihan milik user
    public function resetStatus($id_user)
    {
        // reset dilakukan di awal bulan:
        // - status diubah kembali menjadi 'Belum dibayar'
        // - kolom permanen di-set ke 0 agar bisa diedit lagi
        $stmt = $this->dbconn->prepare("UPDATE tanggungan SET status = 'Belum dibayar', permanen = 0 WHERE user_id = ?");

        // bind parameter user_id (integer)
        $stmt->bind_param("i", $id_user);

        // eksekusi query dan kembalikan hasilnya
        return $stmt->execute();
    }



    // menandai tanggungan sebagai 'Selesai' jika cocok dengan data pembayaran
    // kondisi kecocokan: user_id, nama tagihan, jumlah, dan status saat ini masih 'Belum dibayar'
    public function updateStatusSelesai($id_user, $nama, $jumlah)
    {
        // siapkan query untuk mengubah status menjadi 'Selesai'
        $stmt = $this->dbconn->prepare("
        UPDATE tanggungan 
        SET status = 'Selesai' 
        WHERE user_id = ? AND nama = ? AND jumlah = ? AND status = 'Belum dibayar'
    ");

        // bind parameter:
        // i = integer (user_id)
        // s = string  (nama)
        // d = double  (jumlah)
        $stmt->bind_param("isd", $id_user, $nama, $jumlah);

        // eksekusi query dan kembalikan hasilnya
        return $stmt->execute();
    }



    // mengambil semua data pengeluaran milik user tertentu
    public function getPengeluaranUser($id_user)
    {
        // siapkan query untuk mengambil keterangan dan jumlah dari catatan keuangan
        // hanya untuk transaksi berjenis 'pengeluaran' dan milik user yang sesuai
        $stmt = $this->dbconn->prepare("
        SELECT keterangan, jumlah 
        FROM catatan_keuangan 
        WHERE user_id = ? AND jenis_transaksi = 'pengeluaran'
    ");

        // bind parameter: i = integer (user_id)
        $stmt->bind_param("i", $id_user);

        // eksekusi query
        $stmt->execute();

        // ambil hasilnya dalam bentuk result object
        $result = $stmt->get_result();

        // ubah hasil menjadi array asosiatif dan kembalikan
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // menghapus satu data tanggungan berdasarkan id dan user_id yang sesuai
// hanya bisa dihapus jika belum permanen (permanen = 0)
    public function deleteById($id, $id_user)
    {
        // siapkan query DELETE dengan validasi:
        // - id tanggungan cocok
        // - milik user tersebut
        // - belum dikunci permanen
        $stmt = $this->dbconn->prepare("
        DELETE FROM tanggungan 
        WHERE id = ? AND user_id = ? AND permanen = 0
    ");

        // bind parameter: i = integer
        $stmt->bind_param("ii", $id, $id_user);

        // eksekusi query dan kembalikan hasilnya (true/false)
        return $stmt->execute();
    }


}
?>