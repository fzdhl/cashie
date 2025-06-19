<?php
    class AdminTargetController extends Controller {
        public function __construct() {
            session_start();
            // Redirect if user is not logged in or doesn't have admin privilege
            if (!isset($_SESSION['user']) || $_SESSION['user']->privilege != 'admin') {
                header('Location: ?c=DashboardController&m=index');
                exit;
            }
        }

        public function index() {
            $targetModel = $this->loadModel('Target');
            $userModel = $this->loadModel('User'); // Load User model
            
            $targets = $targetModel->getAllTargetsWithUsernames();
            $users = $userModel->getAll(); // Get all users for the dropdown

            $this->loadView('adminTarget', [
                'targets' => $targets,
                'users' => $users // Pass all users to the view
            ]);
        }

        public function getTarget() { // This method might become redundant with inline editing, but we'll keep it for now.
            header('Content-Type: application/json');
            if (!isset($_GET['id'])) {
                echo json_encode(["status" => "error", "message" => "ID Target tidak disediakan."]);
                exit();
            }
            $targetId = $_GET['id'];
            $targetModel = $this->loadModel('Target');
            $target = $targetModel->getTargetById($targetId);

            if ($target) {
                echo json_encode(["status" => "success", "data" => $target]);
            } else {
                echo json_encode(["status" => "error", "message" => "Target tidak ditemukan."]);
            }
            exit();
        }

        public function update() { // This method is for single updates, can be reused by bulkUpdate or kept separate
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['target_id'])) {
                echo json_encode(["status" => "error", "message" => "Permintaan tidak valid."]);
                exit();
            }

            $model = $this->loadModel('Target');
            $targetId = $_POST['target_id'];
            $userId = $_POST['user_id'];
            $targetName = $_POST['target'];
            $amount = $_POST['jumlah'];

            // Basic validation
            if (empty($targetName) || !is_numeric($amount) || $amount <= 0 || !is_numeric($userId) || $userId <= 0) {
                echo json_encode(["status" => "error", "message" => "Data tidak lengkap atau tidak valid."]);
                exit();
            }

            if ($model->updateTargetAdmin($targetId, $userId, $targetName, $amount)) {
                echo json_encode(["status" => "success", "message" => "Target berhasil diperbarui."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal memperbarui target. Pastikan User ID valid dan tidak ada target duplikat untuk user ini."]);
            }
            exit();
        }

        public function bulkUpdate() {
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['updates'])) {
                echo json_encode(["status" => "error", "message" => "Tidak ada data update yang diterima."]);
                exit();
            }

            $updates = json_decode($_POST['updates'], true); // Decode the JSON string
            if (!is_array($updates)) {
                echo json_encode(["status" => "error", "message" => "Format data update tidak valid."]);
                exit();
            }

            $model = $this->loadModel('Target');
            $successCount = 0;
            $errorMessages = [];

            foreach ($updates as $data) {
                $targetId = $data['target_id'] ?? null;
                $userId = $data['user_id'] ?? null;
                $targetName = $data['target'] ?? null;
                $amount = $data['jumlah'] ?? null;

                // Basic validation for each item
                if (empty($targetId) || empty($targetName) || !is_numeric($amount) || $amount <= 0 || !is_numeric($userId) || $userId <= 0) {
                    $errorMessages[] = "Data tidak lengkap atau tidak valid untuk target ID: " . ($targetId ?? 'N/A');
                    continue;
                }

                if ($model->updateTargetAdmin($targetId, $userId, $targetName, $amount)) {
                    $successCount++;
                } else {
                    $errorMessages[] = "Gagal memperbarui target ID: " . $targetId;
                }
            }

            if ($successCount === count($updates)) {
                echo json_encode(["status" => "success", "message" => "Semua target berhasil diperbarui."]);
            } elseif ($successCount > 0) {
                echo json_encode(["status" => "partial_success", "message" => "Beberapa target berhasil diperbarui, namun ada kesalahan: " . implode(", ", $errorMessages)]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal memperbarui target: " . implode(", ", $errorMessages)]);
            }
            exit();
        }

        public function delete() {
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['target_id'])) {
                echo json_encode(["status" => "error", "message" => "Permintaan tidak valid."]);
                exit();
            }

            $model = $this->loadModel('Target');
            $targetId = $_POST['target_id'];

            $result = $model->delete($targetId);

            if ($result === "Hapus berhasil!") {
                echo json_encode(["status" => "success", "message" => $result]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal menghapus target: " . $result]);
            }
            exit();
        }
    }
?>