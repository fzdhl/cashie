<?php
    class LaporanController extends Controller {
        public function __construct() {
            session_start();
            if (!isset($_SESSION['user'])) {
                header("Location: ?c=UserController&m=loginView");
            }
        }

        // public function addTransaction(){
        //     $username = $_SESSION['user'];
        //     $username = $username->username;
        //     $transaction_type = $_POST['transaction_type'];
        //     $IDR = $_POST['IDR'];
        //     $description = $_POST['description'];

        //     $model = $this->loadModel("Laporan");
        //     $userTable = $model->getByUsername($username);

        //     if($transaction_type == "Pengeluaran") $IDR *= -1;

        //     $user_id = $userTable->user_id;

        //     $model->insertTransaction($user_id, $transaction_type, $IDR, $description);

        //     header("Location: ?c=LaporanController&m=report");
        // }

        public function addLaporan() {
            $userSession = $_SESSION['user'];
            if (is_object($userSession)) {
                $username = $userSession->username;
            } else {
                $this->report(['error_addlaporan' => 'Session user tidak valid.']);
                return;
            }

            $model = $this->loadModel("Laporan");
            $userTable = $model->getByUsername($username);

            if (!$userTable) {
                $this->report(['error_addlaporan' => 'User tidak ditemukan.']);
                return;
            }

            $user_id = $userTable->user_id;

            $tanggal_awal = $_POST['tanggal_awal'] ?? null;
            $tanggal_akhir = $_POST['tanggal_akhir'] ?? null;
            $catatan = $_POST['catatan'] ?? '';

            $error = [];

            $errorMsg = $this->cekTanggalLaporan($tanggal_awal, $tanggal_akhir);
            if ($errorMsg) {
                $error['error_addlaporan'] = $errorMsg;
            }

            if (!empty($error)) {
                $this->report($error);
            } else {
                $model->insertLaporanMingguan($user_id, $tanggal_awal, $tanggal_akhir, $catatan);
                header("Location: ?c=LaporanController&m=report");
                exit;
            }
        }
        
        public function cekTanggalLaporan($tanggal_awal, $tanggal_akhir){
            try {
                $date_awal = new DateTime($tanggal_awal);
                $date_akhir = new DateTime($tanggal_akhir);

                if ($date_awal > $date_akhir) {
                    return "Tanggal awal harus lebih kecil atau sama dengan tanggal akhir.";
                } else if ($date_awal->format('n') != $date_akhir->format('n')) {
                    return "Laporan harus dalam bulan yang sama.";
                } else if ($date_awal->diff($date_akhir)->days > 7) {
                    return "Rentang waktu laporan mingguan tidak boleh lebih dari 7 hari.";
                }
            } catch (Exception $e) {
                return "Format tanggal tidak valid.";
            }
            return false;
        }


        public function report($error = []) {
            $username = $_SESSION['user'];
            $username = $username->username;

            $selectedDate = $this->checkSessionDate();
            $selectedMonth = $selectedDate['month'];
            $selectedYear = $selectedDate['year'];

            if(isset($_POST['laporan_type']) || (isset($_SESSION['laporan_type']) && $_SESSION['laporan_type'] === 'mingguan')){
                $laporan_Type = isset($_POST['laporan_type'])? $_POST['laporan_type'] : $_SESSION['laporan_type'];
                $laporan_id = isset($_POST['laporan_id'])? $_POST['laporan_id'] : $_SESSION['laporan_id'];
                $this->setLaporanType($laporan_Type, $laporan_id);

                $transactionData = $this->getTransactionMingguan($laporan_id, $username, 'Pemasukan');
                if($averagePemasukan = $this->calcAverageData($transactionData)){
                    $averagePemasukan = $this->calcAverageData($transactionData);
                }
                else $averagePemasukan = 0;

                $dateAndTotal = $this->setDateAndTotalMingguan($transactionData, $laporan_id);
                $datePemasukan = $dateAndTotal['date'];
                $totalPemasukan = $dateAndTotal['total'];
                $listPemasukan = $this->getFullTableMingguan($laporan_id, $username, 'Pemasukan');

                $transactionData = $this->getTransactionMingguan($laporan_id, $username, 'Pengeluaran');
                if($averagePengeluaran = $this->calcAverageData($transactionData)){
                    $averagePengeluaran = $this->calcAverageData($transactionData);
                }
                else $averagePengeluaran = 0;

                $dateAndTotal = $this->setDateAndTotalMingguan($transactionData, $laporan_id);
                $datePengeluaran = $dateAndTotal['date'];
                $totalPengeluaran = $dateAndTotal['total'];
                $listPengeluaran = $this->getFullTableMingguan($laporan_id, $username, 'Pengeluaran');
            }
            else{
                $transactionData = $this->getTransactionData($username, $selectedMonth, $selectedYear, "Pemasukan");
                $averagePemasukan = $this->calcAverageData($transactionData);
                if($averagePemasukan = $this->calcAverageData($transactionData)){
                    $averagePemasukan = $this->calcAverageData($transactionData);
                }
                else $averagePemasukan = 0;
                
                $dateAndTotal = $this->setDateAndTotal($transactionData, $selectedMonth, $selectedYear);
                $datePemasukan = $dateAndTotal['date'];
                $totalPemasukan = $dateAndTotal['total'];
                $listPemasukan = $this->getFullTableBy($username, $selectedMonth, $selectedYear, "Pemasukan");

                $transactionData = $this->getTransactionData($username, $selectedMonth, $selectedYear, "Pengeluaran");
                if($averagePengeluaran = $this->calcAverageData($transactionData)){
                    $averagePengeluaran = $this->calcAverageData($transactionData);
                }
                else $averagePengeluaran = 0;
                $dateAndTotal = $this->setDateAndTotal($transactionData, $selectedMonth, $selectedYear);
                $datePengeluaran = $dateAndTotal['date'];
                $totalPengeluaran = $dateAndTotal['total'];
                $listPengeluaran = $this->getFullTableBy($username, $selectedMonth, $selectedYear, "Pengeluaran");
            }
            

            $bulanList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $listLaporanMingguan = $this->getTableLaporanMingguan($username);

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
                'bulanList' => $bulanList,
                'listLaporanMingguanPemasukan' => $listLaporanMingguan['pemasukan'],
                'listLaporanMingguanPengeluaran' => $listLaporanMingguan['pengeluaran']
            ];
            if (!empty($error)) {
                $data['error'] = $error;
            }
            
            $this->loadView("laporan/laporan", $data);
        }
        
        public function editReport($error = []){

            $tanggal_awal = $_POST['tanggal_awal'];
            $tanggal_akhir = $_POST['tanggal_akhir'];
            $laporan_id = $_POST['laporan_id'];
            $catatan = $_POST['catatan'];

            $data = [
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'laporan_id' => $laporan_id,
                'catatan' => $catatan
            ];

            if (!empty($error)) {
                $data['error'] = $error;
            }

            $this->loadView("laporan/editLaporan", $data);
        }

        public function getTableLaporanMingguan($username){
            $model = $this->loadModel("Laporan");
            $user = $model->getByUsername($username);
            $user_id = $user->user_id;
            $listLaporanMingguanPengeluaran = $model->getDaftarLaporan($user_id, 'pengeluaran');
            $listLaporanMingguanPemasukan = $model->getDaftarLaporan($user_id, 'pemasukan');
            return [
                'pemasukan' => $listLaporanMingguanPemasukan,
                'pengeluaran' => $listLaporanMingguanPengeluaran
            ];
        }

        public function selectDate(){
            $_SESSION['laporan_type'] = 'bulanan';
            $_SESSION['month'] = $_POST['month'];
            $_SESSION['year'] = $_POST['year'];
            header("Location: ?c=LaporanController&m=report");
        }

        public function setLaporanType($laporan_Type, $laporan_id){
            $_SESSION['laporan_type'] = $laporan_Type;
            $_SESSION['laporan_id'] = $laporan_id;
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
            $model = $this->loadModel("Laporan");

            $transactionTable = $model->getTableByTransactionType($reportType, $username, $selectedMonth, $selectedYear);
            return $transactionTable;
        }

        public function getFullTableMingguan($laporan_id, $username, $reportType){
            $model = $this->loadModel("Laporan");

            $transactionTable = $model->getTableByDate($laporan_id, $reportType, $username);
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
            $model = $this->loadModel("Laporan");


            $transactionTable = $model->getPemasukanPengeluaran($username, $selectedMonth, $selectedYear, $reportType);
            $transactionData = [];
            while ($row = $transactionTable->fetch_assoc()) {
                $transactionData[(int)$row['hari']] = $row['total_harian'];
            }
            return $transactionData;
        }

        public function getTransactionMingguan($laporan_id, $username, $reportType) {
            $model = $this->loadModel("Laporan");

            $transactionTable = $model->getLaporanMingguan($laporan_id, $reportType, $username);
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

        public function setDateAndTotalMingguan($transactionData, $laporan_id){
            $model = $this->loadModel("laporan");
            $tabel = $model->getLaporan($laporan_id);
            $hariAwal = (int) date('d', strtotime($tabel->tanggal_awal));
            $hariAkhir = (int) date('d', strtotime($tabel->tanggal_akhir));

            $date = [];
            $total = [];

            for ($i = $hariAwal; $i <= $hariAkhir; $i++) {
                $date[] = $i;
                $total[] = isset($transactionData[$i]) ? (float)$transactionData[$i] : 0;
            }
            return [
                'date' => $date,
                'total' => $total
            ];
        }

        public function deleteLaporan(){
            $username = $_SESSION['user'];
            $username = $username->username;
            $laporan_id = $_POST['laporan_id'];

            $model = $this->loadModel("Laporan");
            $userTable = $model->getByUsername($username);
            $user_id = $userTable->user_id;

            $model->deleteLaporan($user_id, $laporan_id);
            header("Location: ?c=LaporanController&m=report");
        }

        public function editLaporan(){
            $model = $this->loadModel("Laporan");

            $username = $_SESSION['user'];
            $username = $username->username;
            $userTable = $model->getByUsername($username);
            $user_id = $userTable->user_id;

            $laporan_id = $_POST['laporan_id'];
            $tanggal_awal = $_POST['tanggal_awal'];
            $tanggal_akhir = $_POST['tanggal_akhir'];
            $catatan = $_POST['catatan'];

            $errorMsg = $this->cekTanggalLaporan($tanggal_awal, $tanggal_akhir);
            if ($errorMsg) {
                $error['error_editlaporan'] = $errorMsg;
            }
            if(!empty($error)){
                $this->editReport($error);
            }
            else{
                $model->updateLaporan($laporan_id, $tanggal_awal, $tanggal_akhir, $catatan);
                header("Location: ?c=LaporanController&m=report");
            }

            
            
        }
    }