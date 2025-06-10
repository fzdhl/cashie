<?php
class ArsipController extends Controller {
    public function __construct() {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: ?c=UserController&m=loginView");
            exit;
        }
    }

    public function index() {
        $model = $this->loadModel("Arsip");
        // Pastikan $arsipList selalu didefinisikan di sini.
        // getByUser akan mengembalikan objek mysqli_result, yang memiliki properti num_rows.
        $arsipList = $model->getByUser($_SESSION['user']->user_id); 
        $this->loadView("arsip", ['arsipList' => $arsipList]);
    }

    public function upload() {
        // Cek apakah ada file yang diupload dan tidak ada error
        if (isset($_FILES['struk']) && $_FILES['struk']['error'] === UPLOAD_ERR_OK) {
            $fileName = uniqid('arsip_') . '_' . basename($_FILES['struk']['name']);
            $uploadDir = 'uploads/arsip/';
            $targetPath = $uploadDir . $fileName;

            // Buat direktori jika belum ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Pindahkan file yang diupload
            if (move_uploaded_file($_FILES['struk']['tmp_name'], $targetPath)) {
                $desc = $_POST['description'] ?? ''; // Ambil deskripsi, default kosong jika tidak ada
                $model = $this->loadModel("Arsip");
                $model->insert($_SESSION['user']->user_id, $targetPath, $desc);
            } else {
                // Penanganan error jika gagal memindahkan file
                // Anda bisa log error ini atau menampilkan pesan ke user
                error_log("Failed to move uploaded file to: " . $targetPath);
                // header("Location: ?c=ArsipController&m=index&error=upload_failed");
            }
        } else {
            // Penanganan error jika ada masalah dengan file upload
            // error_log("File upload error: " . ($_FILES['struk']['error'] ?? 'No file uploaded'));
            // header("Location: ?c=ArsipController&m=index&error=file_error");
        }
        header("Location: ?c=ArsipController&m=index");
        exit(); // Penting untuk menghentikan eksekusi setelah header redirect
    }

    public function update() {
        // Pastikan ini adalah request POST dan data yang diperlukan ada
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['description'])) {
            $id = $_POST['id'];
            $desc = $_POST['description'];
            $model = $this->loadModel("Arsip");
            $success = $model->updateDescription($id, $desc, $_SESSION['user']->user_id);
            
            if ($success) {
                echo "success"; // Beri respons success ke AJAX
            } else {
                http_response_code(500); // Set status kode error
                echo "error"; // Beri respons error ke AJAX
            }
        } else {
            http_response_code(400); // Bad Request
            echo "Invalid request";
        }
        exit(); // Penting untuk menghentikan eksekusi
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadModel("Arsip");
            $arsip = $model->getById($id, $_SESSION['user']->user_id);
            
            if ($arsip) {
                // Hapus file fisik jika ada
                if (file_exists($arsip->file_path)) {
                    unlink($arsip->file_path);
                }
                // Hapus entri dari database
                $model->delete($id, $_SESSION['user']->user_id);
            }
        }
        header("Location: ?c=ArsipController&m=index");
        exit(); // Penting untuk menghentikan eksekusi setelah header redirect
    }
}