<?php
  include_once "models/Model.php";

  class Calendar extends Model {
    public function __construct() {
      parent::__construct();
    }

    public function getTransactionsByDate($date, $userId) {
      // [MODIFIKASI] Tambahkan t.transaction_id pada query SELECT
      $query = "  SELECT * FROM transaksi AS t
                  JOIN
                      kategori AS k ON t.kategori_id = k.kategori_id
                  WHERE
                      t.user_id = ? AND DATE(t.tanggal_transaksi) = ?
      ";
      
      $stmt = $this->dbconn->prepare($query);
      if (!$stmt) {
        return ['error' => 'Query preparation failed: ' . $this->dbconn->error];
      }
      
      $stmt->bind_param("is", $userId, $date);
      $stmt->execute();
      $result = $stmt->get_result();
      $transactions = $result->fetch_all(MYSQLI_ASSOC); // mengambil semua baris hasil query
      // dan menyimpannya sebagai array asosiatif ke variabel $transactions= 0

      $stmt->close();
      $income = 0;
      $expense = 0;
      
      foreach ($transactions as $transaction) {
        if (stripos($transaction['tipe'], 'pemasukan') !== false || stripos($transaction['tipe'], 'income') !== false) {
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