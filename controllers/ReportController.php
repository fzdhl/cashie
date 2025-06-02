<?php
    class ReportController extends Controller {
        public function __construct() {
            session_start();
            if (!isset($_SESSION['user'])) {
                header("Location: ?c=UserController&m=loginView");
            }
        }

        public function addTransaction(){
            $username = $_SESSION['user'];
            $username = $username->username;
            $transaction_type = $_POST['transaction_type'];
            $IDR = $_POST['IDR'];
            $description = $_POST['description'];

            $model = $this->loadModel("Report");
            $userTable = $model->getByUsername($username);

            if($transaction_type == "Pengeluaran") $IDR *= -1;

            $user_id = $userTable->user_id;

            $model->insertTransaction($user_id, $transaction_type, $IDR, $description);

            header("Location: ?c=ReportController&m=report");
        }

        public function report() {
            $username = $_SESSION['user'];
            $username = $username->username;

            $selectedDate = $this->checkSessionDate();
            $selectedMonth = $selectedDate['month'];
            $selectedYear = $selectedDate['year'];

            $averagePemasukan = 0;
            $averagePengeluaran = 0;
            $transactionData = $this->getTransactionData($username, $selectedMonth, $selectedYear, "Pemasukan");
            $averagePemasukan = $this->calcAverageData($transactionData);
            $dateAndTotal = $this->setDateAndTotal($transactionData, $selectedMonth, $selectedYear);
            $datePemasukan = $dateAndTotal['date'];
            $totalPemasukan = $dateAndTotal['total'];
            $listPemasukan = $this->getFullTableBy($username, $selectedMonth, $selectedYear, "Pemasukan");

            $transactionData = $this->getTransactionData($username, $selectedMonth, $selectedYear, "Pengeluaran");
            $averagePengeluaran = $this->calcAverageData($transactionData);
            $dateAndTotal = $this->setDateAndTotal($transactionData, $selectedMonth, $selectedYear);
            $datePengeluaran = $dateAndTotal['date'];
            $totalPengeluaran = $dateAndTotal['total'];
            $listPengeluaran = $this->getFullTableBy($username, $selectedMonth, $selectedYear, "Pengeluaran");


            $bulanList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $data = [
                'username' => $username,
                'selectedMonth' => $selectedMonth,
                'selectedYear' => $selectedYear,
                'transactionData' => $transactionData,
                'datePemasukan' => $datePemasukan,
                'totalPemasukan' => $totalPemasukan,
                'datePengeluaran' => $datePengeluaran,
                'totalPengeluaran' => $totalPengeluaran,
                'avrPemasukan' => $averagePemasukan,
                'avrPengeluaran' => $averagePengeluaran,
                'listPemasukan' => $listPemasukan,
                'listPengeluaran' => $listPengeluaran,
                'bulanList' => $bulanList
            ];
            $this->loadView("report", $data);
        }

        public function selectDate(){
            $_SESSION['month'] = $_POST['month'];
            $_SESSION['year'] = $_POST['year'];
            header("Location: ?c=ReportController&m=report");
        }

        public function checkSessionDate(){
            if(isset($_SESSION['month'])){
                return [
                    'month' => $_SESSION['month'],
                    'year' => $_SESSION['year']
                ];
            }
            else{
                return [
                    'month' => date('n'),
                    'year' => date('Y')
                ];
            }
        }

        public function calcAverageData($Data){
            $length = date("d");
            $sum = $this->getSumData($Data);
            $average = $sum/$length;
            $average = round($average, 2);
            return $average;
        }

        public function getFullTableBy($username, $selectedMonth, $selectedYear, $reportType){
            $model = $this->loadModel("Report");

            $transactionTable = $model->getTableByTransactionType($reportType, $username, $selectedMonth, $selectedYear);
            return $transactionTable;
        }

        public function getSumData($Data){
            $sum = 0;
            foreach($Data as $num){
                $sum += $num;
            }
            return $sum;
        }

        public function getTransactionData($username, $selectedMonth, $selectedYear, $reportType) {
            $model = $this->loadModel("Report");


            $transactionTable = $model->getPemasukanPengeluaran($username, $selectedMonth, $selectedYear, $reportType);

            $transactionData = [];
            while ($row = $transactionTable->fetch_assoc()) {
                $transactionData[(int)$row['hari']] = $row['total_harian'];
            }
            return $transactionData;
        }

        public function setDateAndTotal($transactionData, $selectedMonth, $selectedYear){
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
            $date = [];
            $total = [];

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date[] = $i;
                $total[] = isset($transactionData[$i]) ? (float)$transactionData[$i] : 0;
            }
            return [
                'date' => $date,
                'total' => $total
            ];
        }
    }