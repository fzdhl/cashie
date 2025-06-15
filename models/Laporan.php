<?php
    class Laporan extends Model {
        public function getByUsername($username) {
            $stmt = $this->dbconn->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_object();
        }

        public function cekID($user_id){
            $stmt = $this->dbconn->prepare("SELECT * FROM user WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->num_rows > 0;
        }

        // public function insertTransaction($user_id, $transaction_type, $jumlah, $description){
        //     $stmt = $this->dbconn->prepare("INSERT INTO catatan_keuangan (user_id, jenis_transaksi, jumlah, keterangan) VALUES (?, ?, ?, ?)");
        //     $stmt->bind_param("isis", $user_id, $transaction_type, $jumlah, $description);
        //     $stmt->execute();
        // }

        // public function insertLaporanBulanan($user_id, $tanggal_bulanan){
        //     $stmt = $this->dbconn->prepare("INSERT INTO laporan (user_id, tanggal_bulanan) VALUES (?, 'bulanan', ?)");
        //     $stmt->bind_param("is", $user_id, $tanggal_bulanan);
        //     $stmt->execute();
        // }

        public function getAll(){
            $stmt = $this->dbconn->prepare("SELECT * FROM laporan");
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        }

        public function getLaporanByTanggalBulanan($user_id, $tanggal_bulanan){
            $stmt = $this->dbconn->prepare("SELECT * FROM laporan WHERE user_id = ? AND tanggal_bulanan = ? ");
            $stmt->bind_param("is", $user_id, $tanggal_bulanan);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        }

        public function insertLaporanMingguan($user_id, $tanggal_awal, $tanggal_akhir, $catatan){
            $stmt = $this->dbconn->prepare("INSERT INTO laporan (user_id, tanggal_awal, tanggal_akhir, catatan) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $tanggal_awal, $tanggal_akhir, $catatan);
            $stmt->execute();
        }

        public function deleteLaporan($user_id, $laporan_id){
            $stmt = $this->dbconn->prepare("DELETE FROM laporan WHERE laporan_id = ?");
            $stmt->bind_param("i", $laporan_id);
            $stmt->execute();
        }

        public function getDaftarLaporan($user_id, $jenis_transaksi){
            $stmt = $this->dbconn->prepare
            ("SELECT 
                l.laporan_id, 
                l.user_id, 
                l.tanggal_awal, 
                l.tanggal_akhir, 
                COALESCE(SUM(ck.jumlah), 0) AS jumlah,
                l.catatan
            FROM 
                laporan l
            LEFT JOIN 
                catatan_keuangan ck 
                ON ck.user_id = l.user_id
                AND ck.jenis_transaksi = ?
                AND ck.tanggal_transaksi >= l.tanggal_awal 
                AND ck.tanggal_transaksi < DATE_ADD(l.tanggal_akhir, INTERVAL 1 DAY)
            WHERE 
                l.user_id = ? 
            GROUP BY 
                l.laporan_id, l.tanggal_awal, l.tanggal_akhir
            ");
            $stmt->bind_param("si", $jenis_transaksi, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        }

        public function getLaporanMingguan($laporan_id, $reportType, $username) {
            $query = "SELECT DAY(ck.tanggal_transaksi) AS hari, 
                    CASE 
                        WHEN ck.jenis_transaksi = 'pengeluaran' THEN -1 * SUM(ck.jumlah)
                        ELSE SUM(ck.jumlah)
                    END AS total_harian
                    FROM catatan_keuangan ck
                    JOIN user u ON ck.user_id = u.user_id
                    JOIN laporan l ON l.user_id = u.user_id AND l.laporan_id = ?
                    WHERE ck.jenis_transaksi = ?
                        AND u.username = ?
                        AND ck.tanggal_transaksi >= l.tanggal_awal
                        AND ck.tanggal_transaksi < DATE_ADD(l.tanggal_akhir, INTERVAL 1 DAY)
                    GROUP BY hari
                    ORDER BY hari ASC
                ";
            $stmt = $this->dbconn->prepare($query);
            $stmt->bind_param("iss", $laporan_id, $reportType, $username); // i = laporan_id (int), s = reportType (string), s = username (string)
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        }
        
        public function getTableByDate($laporan_id, $reportType, $username){
            $stmt = $this->dbconn->prepare("SELECT * 
                    FROM catatan_keuangan ck 
                    JOIN user u ON ck.user_id = u.user_id 
                    JOIN laporan l on l.laporan_id = ? AND l.user_id = u.user_id
                    WHERE ck.jenis_transaksi = ? 
                        AND username = ? 
                        AND ck.tanggal_transaksi >= l.tanggal_awal
                        AND ck.tanggal_transaksi < DATE_ADD(l.tanggal_akhir, INTERVAL 1 DAY)
                    ORDER BY ck.tanggal_transaksi DESC");
            $stmt->bind_param("iss", $laporan_id, $reportType, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        }

        public function getLaporan($laporan_id){
            $stmt = $this->dbconn->prepare("SELECT * FROM laporan where laporan_id = ?");
            $stmt->bind_param("i", $laporan_id);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_object();
        }

        public function getPemasukanPengeluaran($username, $selectedMonth, $selectedYear, $reportType){
            $stmt = $this->dbconn->prepare("SELECT DAY(tanggal_transaksi) AS hari, 
                    CASE 
                        WHEN jenis_transaksi = 'pengeluaran' THEN -1 * SUM(jumlah)
                        ELSE SUM(jumlah)
                        END AS total_harian
                    FROM catatan_keuangan t 
                    JOIN user u ON t.user_id = u.user_id 
                    WHERE jenis_transaksi = ? AND username = ? AND MONTH(tanggal_transaksi) = ? AND YEAR(tanggal_transaksi) = ?
                    GROUP BY hari 
                    ORDER BY hari ASC");
            $stmt->bind_param("ssii", $reportType, $username, $selectedMonth, $selectedYear);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        }

        public function getTableByTransactionType($reportType, $username, $selectedMonth, $selectedYear){
            $stmt = $this->dbconn->prepare("SELECT * 
                    FROM catatan_keuangan t 
                    JOIN user u ON t.user_id = u.user_id 
                    WHERE jenis_transaksi = ? AND username = ? AND MONTH(tanggal_transaksi) = ? AND YEAR(tanggal_transaksi) = ?
                    ORDER BY jenis_transaksi DESC");
            $stmt->bind_param("ssii", $reportType, $username, $selectedMonth, $selectedYear);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        }

        public function updateLaporan($laporan_id, $tanggal_awal, $tanggal_akhir, $catatan){
            $stmt = $this->dbconn->prepare("UPDATE laporan 
                    SET tanggal_awal = ?, tanggal_akhir = ?, catatan = ?
                    WHERE laporan_id = ?");
            $stmt->bind_param("sssi", $tanggal_awal, $tanggal_akhir, $catatan, $laporan_id);
            $stmt->execute();
        }
        
    }