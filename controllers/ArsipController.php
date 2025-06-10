<?php
class ArsipController extends Controller {
    public function __construct() {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: ?c=UserController&m=loginView");
            exit;
        }
    }

    // Read: Melihat daftar struk
    public function index() {
        $model = $this->loadModel("Arsip");
        // Pastikan $arsipList selalu didefinisikan sebagai objek mysqli_result
        // getByUser() mengembalikan objek mysqli_result yang memiliki properti num_rows,
        // bahkan jika tidak ada baris yang ditemukan (num_rows akan 0).
        $arsipList = $model->getByUser($_SESSION['user']->user_id);
        $this->loadView("arsip", ['arsipList' => $arsipList]);
    }

    // Create: Mengunggah atau memasukkan data struk
    public function upload() {
        if (isset($_FILES['struk']) && $_FILES['struk']['error'] === UPLOAD_ERR_OK) {
            $fileName = uniqid('arsip_') . '_' . basename($_FILES['struk']['name']);
            $uploadDir = 'uploads/arsip/';
            $targetPath = $uploadDir . $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['struk']['tmp_name'], $targetPath)) {
                $desc = $_POST['description'] ?? '';
                $model = $this->loadModel("Arsip");
                $insertSuccess = $model->insert($_SESSION['user']->user_id, $targetPath, $desc);
                
                if (!$insertSuccess) {
                    if (file_exists($targetPath)) {
                        unlink($targetPath);
                    }
                    error_log("Failed to insert arsip data into DB for user ID: " . $_SESSION['user']->user_id);
                }
            } else {
                error_log("Failed to move uploaded file to: " . $targetPath . " - Error: " . $_FILES['struk']['error']);
            }
        } else if (isset($_FILES['struk'])) {
            error_log("File upload error code: " . $_FILES['struk']['error']);
        }
        
        header("Location: ?c=ArsipController&m=index");
        exit();
    }

    // Update: Mengedit informasi struk yang sudah tersimpan
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['description'])) {
            $id = $_POST['id'];
            $desc = $_POST['description'];
            $model = $this->loadModel("Arsip");
            $success = $model->updateDescription($id, $desc, $_SESSION['user']->user_id);
            
            if ($success) {
                echo "success";
            } else {
                http_response_code(500);
                echo "error: Failed to update database";
            }
        } else {
            http_response_code(400);
            echo "error: Invalid request data";
        }
        exit();
    }

    // Delete: Menghapus arsip struk dengan konfirmasi terlebih dahulu
    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadModel("Arsip");
            $arsip = $model->getById($id, $_SESSION['user']->user_id);
            
            if ($arsip) {
                if (file_exists($arsip->file_path) && $arsip->file_path != '') {
                    unlink($arsip->file_path);
                }
                $model->delete($id, $_SESSION['user']->user_id);
            }
        }
        header("Location: ?c=ArsipController&m=index");
        exit();
    }
}