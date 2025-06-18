<?php
  class CalendarController extends Controller {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function checkLogin() {
      if (!isset($_SESSION['user'])) {
          header('Location: ?c=UserController&m=loginView');
          exit();
      }
    }

    public function index() { //menampilkan hakaman kalender utama
      $this->checkLogin();
      $userId = $_SESSION['user']->user_id;
      
      $calendarModel = $this->loadModel('Calendar');
      $transactionModel = $this->loadModel('Transaction');

      $today = date('Y-m-d');
      $data = $calendarModel->getTransactionsByDate($today, $userId);
      $data['selected_date'] = $today;

      // [MODIFIKASI] Ambil juga data categories, bills, dan goals
      $data['kategori'] = $transactionModel->getCategoriesByUser($userId);
      $data['tagihan'] = $transactionModel->getBillsByUser($userId);
      $data['target'] = $transactionModel->getGoalsByUser($userId);

      $this->loadView('calendar', $data);
    }

    public function showTransactions() { //menyediakan data transaksi
      $this->checkLogin();
      
      if (!isset($_GET['date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Date parameter is missing.']);
        return;
      }
      
      $date = $_GET['date'];
      if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid date format. Please use YYYY-MM-DD.']);
        return;
      }
      
      $calendarModel = $this->loadModel('Calendar');
      $userId = $_SESSION['user']->user_id;
      $data = $calendarModel->getTransactionsByDate($date, $userId);

      header('Content-Type: application/json'); //Mengatur header respons untuk memberitahu browser bahwa data yang dikirim adalah format JSON.
      echo json_encode($data); //Mengubah array data PHP menjadi format JSON dan mengirimkannya sebagai respons. Data inilah yang akan diterima dan diolah oleh calendar.js untuk memperbarui tampilan di sisi pengguna.
    }
  }
?>