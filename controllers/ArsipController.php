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
        // ini mencegah error "Undefined variable" dan "Attempt to read property num_rows on null"
        $arsipList = $model->getByUser($_SESSION['user']->user_id); 
        $this->loadView("arsip", ['arsipList' => $arsipList]);
    }

    // Create: Mengunggah atau memasukkan data struk
    public function upload() {
        // Memastikan file diupload dan tidak ada error
        if (isset($_FILES['struk']) && $_FILES['struk']['error'] === UPLOAD_ERR_OK) {
            $fileName = uniqid('arsip_') . '_' . basename($_FILES['struk']['name']);
            $uploadDir = 'uploads/arsip/';
            $targetPath = $uploadDir . $fileName;

            // Buat direktori jika belum ada, dengan izin baca/tulis penuh
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Pindahkan file yang diupload dari lokasi sementara ke lokasi target
            if (move_uploaded_file($_FILES['struk']['tmp_name'], $targetPath)) {
                $desc = $_POST['description'] ?? ''; // Ambil deskripsi, default string kosong jika tidak ada
                $model = $this->loadModel("Arsip");
                $insertSuccess = $model->insert($_SESSION['user']->user_id, $targetPath, $desc);
                
                if (!$insertSuccess) {
                    // Jika insert ke DB gagal, hapus file yang sudah diupload
                    if (file_exists($targetPath)) {
                        unlink($targetPath);
                    }
                    error_log("Failed to insert arsip data into DB for user ID: " . $_SESSION['user']->user_id);
                    // Anda bisa menambahkan pesan error ke session untuk ditampilkan di view
                    // $_SESSION['error_message'] = "Gagal menyimpan data arsip ke database.";
                }
            } else {
                error_log("Failed to move uploaded file to: " . $targetPath . " - Error: " . $_FILES['struk']['error']);
                // $_SESSION['error_message'] = "Gagal mengunggah file. Pastikan folder 'uploads/arsip' dapat ditulis.";
            }
        } else if (isset($_FILES['struk'])) {
            // Tangani error upload file PHP (misal: ukuran terlalu besar)
            error_log("File upload error code: " . $_FILES['struk']['error']);
            // $_SESSION['error_message'] = "Terjadi masalah saat mengunggah file. Kode error: " . $_FILES['struk']['error'];
        } else {
            // $_SESSION['error_message'] = "Tidak ada file yang diunggah.";
        }
        
        header("Location: ?c=ArsipController&m=index");
        exit(); // Penting: Hentikan eksekusi setelah redirect
    }

    // Update: Mengedit informasi struk yang sudah tersimpan
    public function update() {
        // Pastikan ini adalah request POST dan data yang diperlukan ada
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['description'])) {
            $id = $_POST['id'];
            $desc = $_POST['description'];
            $model = $this->loadModel("Arsip");
            $success = $model->updateDescription($id, $desc, $_SESSION['user']->user_id);
            
            if ($success) {
                echo "success"; // Beri respons "success" ke AJAX
            } else {
                http_response_code(500); // Set status kode error HTTP 500
                echo "error: Failed to update database"; // Beri pesan error ke AJAX
            }
        } else {
            http_response_code(400); // Bad Request
            echo "error: Invalid request data";
        }
        exit(); // Penting: Hentikan eksekusi
    }

    // Delete: Menghapus arsip struk dengan konfirmasi terlebih dahulu
    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadModel("Arsip");
            $arsip = $model->getById($id, $_SESSION['user']->user_id);
            
            if ($arsip) {
                // Hapus file fisik dari server jika ada dan file_path valid
                if (file_exists($arsip->file_path) && $arsip->file_path != '') {
                    unlink($arsip->file_path);
                }
                // Hapus entri dari database
                $model->delete($id, $_SESSION['user']->user_id);
            }
            // Jika arsip tidak ditemukan atau bukan milik user ini, tidak ada yang dilakukan.
            // Bisa juga ditambahkan error logging atau pesan ke user.
        }
        header("Location: ?c=ArsipController&m=index");
        exit(); // Penting: Hentikan eksekusi setelah redirect
    }
}