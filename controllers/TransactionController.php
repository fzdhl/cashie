<?php
  include_once "controllers/Controller.php";

  class TransactionController extends Controller {
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
    
    // public function addView() {
    //     $this->checkLogin();
    //     $transactionModel = $this->loadModel('Transaction');
    //     $categories = $transactionModel->getCategoriesByUser($_SESSION['user_id']);
    //     $this->loadView('add_transaction', ['categories' => $categories]);
    // }

    public function addProcess() {
        $this->checkLogin();
        $response = ['status' => 'error', 'message' => 'Invalid request.'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // [MODIFIKASI] Tangkap bill_id dan goal_id dari POST
            $data = [
                'kategori_id' => $_POST['category_id'],
                'jumlah' => $_POST['amount'],
                'keterangan' => $_POST['note'],
                'date' => $_POST['date'],
                'user_id' => $_SESSION['user_id'],
                'tagihan_id' => isset($_POST['bill_id']) ? $_POST['bill_id'] : null,
                'target_id' => isset($_POST['goal_id']) ? $_POST['goal_id'] : null
            ];

            $transactionModel = $this->loadModel('Transaction');
            if ($transactionModel->insertTransaction($data)) {
                $response = ['status' => 'success', 'message' => 'Transaksi berhasil disimpan!'];
            } else {
                $response['message'] = 'Gagal menyimpan transaksi ke database.';
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    public function getTransaction() {
        $this->checkLogin();
        
        if (isset($_GET['id'])) {
            $transactionId = $_GET['id'];
            $transactionModel = $this->loadModel('Transaction');
            $transaction = $transactionModel->getTransactionById($transactionId, $_SESSION['user_id']);

            if ($transaction) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'data' => $transaction]);
            } else {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Transaksi tidak ditemukan.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID Transaksi tidak disediakan.']);
        }
        exit();
    }

    // ...
    public function updateProcess() {
        $this->checkLogin();
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // [MODIFIKASI] Pastikan bill_id dan goal_id disertakan
            $data = [
                'transaction_id' => $_POST['transaction_id'],
                'category_id' => $_POST['category_id'],
                'amount' => $_POST['amount'],
                'note' => $_POST['note'],
                'date' => $_POST['date'],
                'user_id' => $_SESSION['user_id'],
                'bill_id' => isset($_POST['bill_id']) ? $_POST['bill_id'] : null,
                'goal_id' => isset($_POST['goal_id']) ? $_POST['goal_id'] : null
            ];

            $transactionModel = $this->loadModel('Transaction');
            if ($transactionModel->updateTransaction($data)) {
                $response = ['status' => 'success', 'message' => 'Transaksi berhasil diperbarui!'];
            } else {
                $response['message'] = 'Gagal memperbarui transaksi.';
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
// ...

    public function deleteProcess() {
        $this->checkLogin();
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $transactionId = $_POST['transaction_id'];
            $transactionModel = $this->loadModel('Transaction');
            
            if ($transactionModel->deleteTransaction($transactionId, $_SESSION['user_id'])) {
                $response = ['status' => 'success', 'message' => 'Transaksi berhasil dihapus!'];
            } else {
                $response['message'] = 'Gagal menghapus transaksi.';
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
  }
?>