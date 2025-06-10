<?php
  include_once "controllers/Controller.php";
  include_once "exceptions/NotFoundException.php";

  class CalendarController extends Controller {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?c=UserController&m=loginView');
            exit();
        }
    }

    public function index() {
      $this->checkLogin();
      $userId = $_SESSION['user_id'];
      
      $calendarModel = $this->loadModel('Calendar');
      $transactionModel = $this->loadModel('Transaction');

      $today = date('Y-m-d');
      $data = $calendarModel->getTransactionsByDate($today, $userId);
      $data['selected_date'] = $today;

      // [MODIFIKASI] Ambil juga data categories, bills, dan goals
      $data['categories'] = $transactionModel->getCategoriesByUser($userId);
      $data['bills'] = $transactionModel->getBillsByUser($userId);
      $data['goals'] = $transactionModel->getGoalsByUser($userId);

      $this->loadView('calendar', $data);
    }

    public function showTransactions() {
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
      $userId = $_SESSION['user_id'];
      $data = $calendarModel->getTransactionsByDate($date, $userId);

      header('Content-Type: application/json');
      echo json_encode($data);
    }
  }
?>