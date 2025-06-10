<?php
  include_once "models/Model.php";

  class Transaction extends Model {
    
    // Mengambil semua kategori milik seorang pengguna
    public function getCategoriesByUser($userId) {
        $query = "SELECT category_id, category, type FROM kategori WHERE user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // [BARU] Mengambil semua tagihan (bills) yang belum lunas milik pengguna
    public function getBillsByUser($userId) {
        $query = "SELECT bill_id, bill FROM bill WHERE user_id = ? AND paid_status = 'unpaid'";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // [BARU] Mengambil semua target (goals) milik pengguna
    public function getGoalsByUser($userId) {
        $query = "SELECT goal_id, goal FROM goal WHERE user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // [MODIFIKASI] Menyimpan data transaksi baru ke database termasuk bill_id dan goal_id
    public function insertTransaction($data) {
        $query = "INSERT INTO transaksi (user_id, category_id, amount, note, date, bill_id, goal_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->dbconn->prepare($query);
        
        $datetime = $data['date'] . ' ' . date('H:i:s');
        
        $bill_id = !empty($data['bill_id']) ? $data['bill_id'] : NULL;
        $goal_id = !empty($data['goal_id']) ? $data['goal_id'] : NULL;

        // Tipe data disesuaikan dengan skema: user(i), category(i), amount(d), note(s), date(s), bill(i), goal(i)
        $stmt->bind_param(
            "idssii",
            $data['user_id'],
            $data['category_id'],
            $data['amount'],
            $data['note'],
            $datetime,
            $bill_id,
            $goal_id
        );
        return $stmt->execute();
    }

    // ...
    public function getTransactionById($transactionId, $userId) {
        // [MODIFIKASI] Ambil juga bill_id dan goal_id
        $query = "SELECT transaction_id, category_id, amount, note, DATE(date) as date, bill_id, goal_id FROM transaksi WHERE transaction_id = ? AND user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("ii", $transactionId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateTransaction($data) {
        // [MODIFIKASI] Query UPDATE sekarang menyertakan bill_id dan goal_id
        $query = "UPDATE transaksi SET category_id = ?, amount = ?, note = ?, date = ?, bill_id = ?, goal_id = ? WHERE transaction_id = ? AND user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        
        $datetime = $data['date'] . ' ' . date('H:i:s');
        $bill_id = !empty($data['bill_id']) ? $data['bill_id'] : NULL;
        $goal_id = !empty($data['goal_id']) ? $data['goal_id'] : NULL;
        
        $stmt->bind_param(
            "idssii", // Tipe data: amount(d), note(s), date(s), bill(i), goal(i), transaction_id(i), user_id(i)
            // Saya ralat urutan dan tipe bind_param agar sesuai query
            // category(i), amount(d), note(s), date(s), bill(i), goal(i), transaction_id(i), user_id(i)
            $data['category_id'],
            $data['amount'],
            $data['note'],
            $datetime,
            $bill_id,
            $goal_id,
            $data['transaction_id'],
            $data['user_id']
        );
        return $stmt->execute();
    }
// ...

    public function deleteTransaction($transactionId, $userId) {
        $query = "DELETE FROM transaksi WHERE transaction_id = ? AND user_id = ?";
        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("ii", $transactionId, $userId);
        return $stmt->execute();
    }
  }
?>