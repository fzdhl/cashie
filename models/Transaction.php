<?php
  include_once "models/Model.php";

  class Transaction extends Model {
    
    public function getCategoriesByUser($userId) {
        $query = "SELECT * FROM kategori WHERE user_id= ?";

        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBillsByUser($userId) {
        $query = "SELECT tanggungan_id, tanggungan FROM tanggungan WHERE user_id = ? AND status = 0";
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
        return $stmt->execute();
    }

    public function deleteTransaction($transactionId, $userId) {
        $query = "DELETE FROM transaksi WHERE transaksi_id = ? AND user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("ii", $transactionId, $userId);
        return $stmt->execute();
    }
  }
?>