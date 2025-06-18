<?php
    class AdminCalendarController extends Controller {
        public function __construct() {
            session_start();
            // Redirect jika pengguna bukan admin
            if (!isset($_SESSION['user']) || $_SESSION['user']->privilege != 'admin') {
                header('Location: ?c=DashboardController&m=index');
                exit;
            }
        }

        public function index() {
            $model = $this->loadModel('Transaction'); // Load model Transaction
            $transactions = $model->getAllTransactions(); // Panggil method baru untuk mendapatkan semua transaksi
            $this->loadView('adminCalendar', ['transactions' => $transactions]); // Load view admin baru dengan data transaksi
        }

        public function update() {
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['transaksi_id'])) {
                echo json_encode(["isSuccess" => false, "info" => "Permintaan tidak valid."]);
                exit();
            }

            $model = $this->loadModel('Transaction');
            $transactionId = $_POST['transaksi_id'];
            
            // Kumpulkan data dari POST
            $data = [
                'tanggal_transaksi' => $_POST['tanggal_transaksi'] ?? null,
                'user_id' => $_POST['user_id'] ?? null,
                'kategori_id' => $_POST['kategori_id'] ?? null,
                'keterangan' => $_POST['keterangan'] ?? null,
                'jumlah' => $_POST['jumlah'] ?? null
            ];
            
            // Hapus nilai null agar tidak masuk ke query update
            $data = array_filter($data, fn($value) => !is_null($value));

            if ($model->updateTransactionAdmin($transactionId, $data)) {
                echo json_encode(["isSuccess" => true, "info" => "Transaksi ID $transactionId berhasil diperbarui."]);
            } else {
                echo json_encode(["isSuccess" => false, "info" => "Gagal memperbarui Transaksi ID $transactionId."]);
            }
            exit();
        }

        public function deleteTransaction() {
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
                echo json_encode(["isSuccess" => false, "info" => "Permintaan tidak valid."]);
                exit();
            }

            $model = $this->loadModel('Transaction');
            $transactionId = $_POST['id'];

            if ($model->deleteTransactionByIdAdmin($transactionId)) {
                echo json_encode(["isSuccess" => true, "info" => "Transaksi berhasil dihapus."]);
            } else {
                echo json_encode(["isSuccess" => false, "info" => "Gagal menghapus transaksi."]);
            }
            exit();
        }

        public function resetAllTransactions() {
             if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ?c=AdminCalendarController&m=index');
                exit();
            }

            $model = $this->loadModel('Transaction');
            $model->deleteAllTransactions();
            // Redirect kembali ke halaman utama setelah reset
            header('Location: ?c=AdminCalendarController&m=index');
            exit();
        }
    }
?>