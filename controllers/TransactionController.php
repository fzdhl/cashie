<?php
  include_once "controllers/Controller.php";

  class TransactionController extends Controller {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkLogin() {
        if (!isset($_SESSION['user']->user_id)) { /*user_id*/
            header('Location: ?c=UserController&m=loginView');
            exit();
        }
    }

    public function addProcess() {
        // $this->checkLogin(); // Pastikan login diaktifkan jika diperlukan
        $response = ['status' => 'error', 'message' => 'Invalid request.'];
        // die(var_dump($_POST)); // Gunakan ini untuk debugging jika perlu melihat data POST

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'kategori_id' => $_POST['category_id'],
                'jumlah' => $_POST['amount'],
                'keterangan' => $_POST['note'],
                'date' => $_POST['date'],
                'user_id' => $_SESSION['user']->user_id,
                // Pastikan bill_id dan target_id adalah integer atau null
                'tagihan_id' => isset($_POST['bill_id']) && $_POST['bill_id'] !== '' ? (int)$_POST['bill_id'] : null,
                'target_id' => isset($_POST['goal_id']) && $_POST['goal_id'] !== '' ? (int)$_POST['goal_id'] : null
            ];

            $transactionModel = $this->loadModel('Transaction');
            // KategoriModel tidak lagi diperlukan untuk sinkronisasi bill_id, tetapi mungkin diperlukan untuk fitur lain
            // $kategoriModel = $this->loadModel('Kategori'); 
            $tanggunganModel = $this->loadModel('Tanggungan'); // Muat model Tanggungan

            if ($transactionModel->insertTransaction($data)) {
                $response = ['status' => 'success', 'message' => 'Transaksi berhasil disimpan!'];

                // === LOGIKA SINKRONISASI BARU BERDASARKAN PILIH TAGIHAN (bill_id) ===
                // Hanya lakukan sinkronisasi jika bill_id dipilih dan valid
                if (!empty($data['tagihan_id'])) {
                    // Dapatkan info tanggungan berdasarkan ID yang dipilih di form
                    $tanggunganInfo = $tanggunganModel->getByIdAndUser($data['tagihan_id'], $data['user_id']);

                    // Periksa jika tanggungan ditemukan dan jumlahnya cocok
                    if ($tanggunganInfo && (int)$tanggunganInfo['jumlah'] === (int)$data['jumlah']) {
                        // Coba perbarui status tanggungan menjadi Selesai (1)
                        $affectedRows = $tanggunganModel->updateStatusByBillIdAndAmount(
                            $data['user_id'],
                            $data['tagihan_id'],
                            $data['jumlah']
                        );
                        
                        if ($affectedRows > 0) {
                            $response['message'] .= ' Status tagihan "' . htmlspecialchars($tanggunganInfo['tanggungan']) . '" berhasil diperbarui menjadi Selesai.';
                        }
                    } else if ($tanggunganInfo && (int)$tanggunganInfo['jumlah'] !== (int)$data['jumlah']) {
                        // Opsi: Beri tahu pengguna jika jumlah tidak cocok meskipun tagihan dipilih
                        $response['message'] .= ' Perhatian: Jumlah transaksi tidak cocok dengan jumlah tagihan "' . htmlspecialchars($tanggunganInfo['tanggungan']) . '". Status tidak diperbarui.';
                    }
                }
                // === AKHIR LOGIKA SINKRONISASI BARU ===

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
            $transaction = $transactionModel->getTransactionById($transactionId, $_SESSION['user']->user_id);

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
                'user_id' => $_SESSION['user']->user_id,
                'bill_id' => isset($_POST['bill_id']) && $_POST['bill_id'] !== '' ? (int)$_POST['bill_id'] : null,
                'goal_id' => isset($_POST['goal_id']) && $_POST['goal_id'] !== '' ? (int)$_POST['goal_id'] : null
            ];

            $transactionModel = $this->loadModel('Transaction');
            $affectedrows = $transactionModel->updateTransaction($data);

            // Membersihkan debugging lama dan mengaktifkan kembali respons yang benar
            if ($affectedrows > 0) {
                $response = ['status' => 'success', 'message' => 'Transaksi berhasil diperbarui!'];
            } else if ($affectedrows === 0) { // Gunakan '===' untuk membedakan 0 dari false/null
                $response['message'] = 'Tidak ada perubahan yang dilakukan atau data tidak ditemukan.';
            } else {
                $response['message'] = 'Gagal memperbarui transaksi di database.';
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
            $userId = $_SESSION['user']->user_id;

            $transactionModel = $this->loadModel('Transaction');
            $tanggunganModel = $this->loadModel('Tanggungan'); // Muat model Tanggungan

            // Dapatkan detail transaksi sebelum dihapus untuk mengetahui tanggungan_id-nya
            $transactionDetails = $transactionModel->getTransactionById($transactionId, $userId);

            if ($transactionModel->deleteTransaction($transactionId, $userId)) {
                $response = ['status' => 'success', 'message' => 'Transaksi berhasil dihapus!'];

                // === LOGIKA SINKRONISASI SAAT PENGHAPUSAN TRANSAKSI ===
                // Jika transaksi yang dihapus terkait dengan tanggungan (memiliki tanggungan_id)
                if ($transactionDetails && !empty($transactionDetails['tanggungan_id'])) {
                    // Panggil metode untuk mereset status tanggungan menjadi Belum dibayar (0)
                    $affectedRows = $tanggunganModel->resetStatusByBillIdAndUser(
                        $userId,
                        $transactionDetails['tanggungan_id']
                    );

                    if ($affectedRows > 0) {
                        // Dapatkan nama tanggungan untuk pesan konfirmasi
                        $tanggunganInfo = $tanggunganModel->getByIdAndUser($transactionDetails['tanggungan_id'], $userId);
                        $response['message'] .= ' Status tagihan "' . htmlspecialchars($tanggunganInfo['tanggungan']) . '" direset menjadi Belum dibayar.';
                    }
                }
                // === AKHIR LOGIKA SINKRONISASI SAAT PENGHAPUSAN ===

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