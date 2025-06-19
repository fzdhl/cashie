<?php
class KategoriController extends Controller {
    public function __construct() {
        // Memulai sesi jika belum ada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Periksa apakah pengguna sudah login
        if (!isset($_SESSION['user'])) {
            header("Location: ?c=UserController&m=loginView");
            exit;
        }
    }

    // Method untuk menampilkan halaman kategori dengan data yang sudah dirender server
    public function index() {
        $model = $this->loadModel('Kategori');
        $userId = $_SESSION['user']->user_id;
        $categories = $model->getAllCategoriesByUser($userId);

        // Langsung tampilkan halaman HTML dengan data kategori
        $this->loadView('kategori', ['categories' => $categories]);
    }

    // Method untuk menambah kategori baru (dipanggil via AJAX POST)
    public function addCategory() {
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']->user_id;
            $kategori = trim($_POST['kategori']);
            $tipe = $_POST['tipe'];
            $icon = $_POST['icon'];

            if (empty($kategori)) {
                $response['message'] = 'Nama kategori tidak boleh kosong.';
            } elseif (empty($icon)) {
                $response['message'] = 'Ikon kategori tidak boleh kosong.';
            } else {
                $model = $this->loadModel('Kategori');
                if ($model->getCategoryByNameAndType($userId, $kategori, $tipe)) {
                    $response['message'] = 'Kategori dengan nama dan tipe yang sama sudah ada.';
                } else {
                    $result = $model->addCategory($userId, $kategori, $tipe, $icon);
                    if ($result['isSuccess']) {
                        $response = ['status' => 'success', 'message' => 'Kategori berhasil ditambahkan.'];
                    } else {
                        $response['message'] = 'Gagal menambahkan kategori: ' . ($result['info'] ?? 'Terjadi kesalahan database.');
                    }
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Method untuk mendapatkan detail kategori tunggal (dipanggil via AJAX GET) - edit kategori
    public function getCategory() {
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];
        if (isset($_GET['id'])) {
            $categoryId = $_GET['id'];
            $userId = $_SESSION['user']->user_id;
            $model = $this->loadModel('Kategori');
            $category = $model->getCategoryById($categoryId, $userId);

            if ($category) {
                $response = ['status' => 'success', 'data' => $category];
            } else {
                $response['message'] = 'Kategori tidak ditemukan atau Anda tidak memiliki akses.';
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Method untuk memperbarui kategori (dipanggil via AJAX POST)
    public function updateCategory() {
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = $_POST['kategori_id'];
            $userId = $_SESSION['user']->user_id;
            $kategori = trim($_POST['kategori']);
            $tipe = $_POST['tipe'];
            $icon = $_POST['icon'];

            if (empty($kategori)) {
                $response['message'] = 'Nama kategori tidak boleh kosong.';
            } elseif (empty($icon)) {
                $response['message'] = 'Ikon kategori tidak boleh kosong.';
            } else {
                $model = $this->loadModel('Kategori');
                $existingCategory = $model->getCategoryByNameAndType($userId, $kategori, $tipe);
                if ($existingCategory && $existingCategory->kategori_id != $categoryId) {
                    $response['message'] = 'Kategori dengan nama dan tipe yang sama sudah ada.';
                } else {
                    $result = $model->updateCategory($categoryId, $userId, $kategori, $tipe, $icon);
                    if ($result) {
                        $response = ['status' => 'success', 'message' => 'Kategori berhasil diperbarui.'];
                    } else {
                        $response['message'] = 'Gagal memperbarui kategori. Pastikan data berbeda dengan yang sudah ada.';
                    }
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Method untuk menghapus kategori (dipanggil via AJAX POST)
    public function deleteCategory() {
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = $_POST['kategori_id'];
            $userId = $_SESSION['user']->user_id;
            $model = $this->loadModel('Kategori');
            $result = $model->deleteCategory($categoryId, $userId);

            if ($result) {
                $response = ['status' => 'success', 'message' => 'Kategori berhasil dihapus.'];
            } else {
                $response['message'] = 'Gagal menghapus kategori. Mungkin kategori tidak ditemukan atau digunakan dalam transaksi.';
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}