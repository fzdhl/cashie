<?php
    class Report extends Model {
        public function getByUsername($username) {
            $stmt = $this->dbconn->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_object();
        }

        public function insertTransaction($user_id, $transaction_type, $jumlah, $description){
            $stmt = $this->dbconn->prepare("INSERT INTO catatan_keuangan (user_id, jenis_transaksi, jumlah, keterangan, kategori_id) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param("isis", $user_id, $transaction_type, $jumlah, $description);
            $stmt->execute();
        }

        public function getPemasukanPengeluaran($username, $selectedMonth, $selectedYear, $reportType){
            $stmt = $this->dbconn->prepare("SELECT DAY(created_at) AS hari, 
                    CASE 
                        WHEN jenis_transaksi = 'pengeluaran' THEN -1 * SUM(jumlah)
                        ELSE SUM(jumlah)
                        END AS total_harian
                    FROM catatan_keuangan t 
                    JOIN user u ON t.user_id = u.user_id 
                    WHERE jenis_transaksi = ? AND username = ? AND MONTH(created_at) = ? AND YEAR(created_at) = ?
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
                    WHERE jenis_transaksi = ? AND username = ? AND MONTH(created_at) = ? AND YEAR(created_at) = ?
                    ORDER BY jenis_transaksi DESC");
            $stmt->bind_param("ssii", $reportType, $username, $selectedMonth, $selectedYear);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        }
    }