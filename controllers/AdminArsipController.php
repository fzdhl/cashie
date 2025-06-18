<?php
    class AdminArsipController extends Controller {
        public function __construct() {
            session_start();
            // Redirect if user is not logged in or doesn't have admin privilege
            if (!isset($_SESSION['user']) || $_SESSION['user']->privilege != 'admin') {
                header('Location: ?c=DashboardController&m=index');
                exit;
            }
        }

        public function index() {
            $model = $this->loadModel('AdminArsip'); // Load AdminArsip model
            $arsipList = $model->getAll(); // Get all arsip entries for admin view
            $this->loadView('adminArsip', ['arsipList' => $arsipList]); // Load adminArsip view
        }

        // Metode upload dihapus karena admin tidak lagi bisa mengupload data.

        public function update() {
            header('Content-Type: application/json'); // Ensure JSON response
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['description'])) {
                $id = $_POST['id'];
                $desc = $_POST['description'];

                $model = $this->loadModel('AdminArsip');
                $success = $model->updateDescription($id, $desc); // Update description

                if ($success) {
                    echo json_encode(['status' => 'success', 'message' => 'Deskripsi berhasil diperbarui.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui deskripsi di database.']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid. Data tidak lengkap.']);
            }
            exit();
        }

        public function delete() {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $model = $this->loadModel('AdminArsip');
                $arsip = $model->getById($id); // Get arsip details

                if ($arsip) {
                    // Delete the physical file if it exists and is not empty
                    if (file_exists($arsip->file_path) && $arsip->file_path != '') {
                        unlink($arsip->file_path);
                    }
                    $model->delete($id); // Delete from database
                }
            }
            header("Location: ?c=AdminArsipController&m=index");
            exit();
        }
    }
?>