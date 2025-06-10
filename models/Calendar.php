<?php
  include_once "models/Model.php";

  class Calendar extends Model {
    public function __construct() {
      parent::__construct();
    }

    public function getTransactionsByDate($date, $userId) {
      // [MODIFIKASI] Tambahkan t.transaction_id pada query SELECT
      $query = " SELECT
              t.transaction_id, 
              k.category,
              k.type,
              t.amount,
              t.note
          FROM
              transaksi AS t
          JOIN
              kategori AS k ON t.category_id = k.category_id
          WHERE
              t.user_id = ? AND DATE(t.date) = ?
      ";
      
      $stmt = $this->dbconn->prepare($query);
      if (!$stmt) {
        return ['error' => 'Query preparation failed: ' . $this->dbconn->error];
      }
      
      $stmt->bind_param("is", $userId, $date);
      $stmt->execute();
      $result = $stmt->get_result();
      $transactions = $result->fetch_all(MYSQLI_ASSOC);
      $stmt->close();

      $income = 0;
      $expense = 0;
      
      foreach ($transactions as $transaction) {
        if (stripos($transaction['type'], 'pemasukan') !== false || stripos($transaction['type'], 'income') !== false) {
          $income += $transaction['amount'];
        } else {
          $expense += $transaction['amount'];
        }
      }

      $balance = $income - $expense;

      return [
        'transactions' => $transactions,
        'summary' => [
          'income' => $income,
          'expense' => $expense,
          'balance' => $balance,
          'total' => $balance
        ]
      ];
    }
  }
?>