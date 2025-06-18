<?php
    class DashboardController extends Controller {
        public function __construct() {
            session_start();
            if (!isset($_SESSION['user'])) {
                header("Location: ?c=UserController&m=loginView");
            }
            if ($_SESSION['user']->privilege == 'admin') {
                header("Location: ?c=AdminController&m=index");
            }
        }

        public function index() {

            $userId = $_SESSION['user']->user_id;
            $transactionModel = $this->loadModel('Transaction');
            $targetModel = $this->loadModel('Target');
            $tanggunganModel = $this->loadModel('Tanggungan');

            $summary = $transactionModel->getSummaryByUserId($userId);
            $pemasukan = $summary['total_pemasukan'] ?? 0;
            $pengeluaran = $summary['total_pengeluaran'] ?? 0;
            $saldo = $pemasukan - $pengeluaran;
            $data_transaksi = $transactionModel->getRecentTransaction($userId);
            $data_tanggungan = $tanggunganModel->getNextTanggungan($userId);

            // Mengambil data target
            $targetData = [
                "total" => $targetModel->getTotalByUserId($userId),
                "data" => $targetModel->getByUserId($userId, 3)
            ];

            // $this->loadView("dashboard", [
            //     "target" => [
            //         "total" => $this->loadModel('Target')->getTotalByUserId($_SESSION['user']->user_id),
            //         "data" => $this->loadModel('Target')->getByUserId($_SESSION['user']->user_id, 3)
            //     ]
            // ]);

            $this->loadView("dashboard", [
                "pemasukan" => $pemasukan,
                "pengeluaran" => $pengeluaran,
                "data_transaksi" => $data_transaksi,
                "data_tanggungan" => $data_tanggungan,
                "saldo" => $saldo,
                "target" => $targetData
            ]);
        }

        public function calendar() {
            $this->loadView("calendar");
        }

        public function catatanKeuangan() {
            $this->loadView("catatanKeuangan");
        }

        public function profile() {
            $this->loadView("profile");
        }

        public function goals() {
            $this->loadView("goals");
        }

        public function report() {
            $this->loadView("report");
        }

        public function kategori() {
            $this->loadView("kategori");
        }

        public function arsip() {
            $this->loadView("arsip");
        }

        public function logout() {  
            session_destroy();
            header("Location: ?c=UserController&m=loginView");
        }

        
    }