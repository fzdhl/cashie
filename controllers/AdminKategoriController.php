<?php
class AdminKategoriController extends Controller {
    public function __construct() {
        session_start();
        // Redirect if user is not logged in or doesn't have admin privilege
        if (!isset($_SESSION['user']) || $_SESSION['user']->privilege != 'admin') {
            header('Location: ?c=DashboardController&m=index');
            exit;
        }
    }

    public function index() {
        $model = $this->loadModel('Kategori'); // Menggunakan model Kategori yang sudah ada
        // Admin bisa melihat semua kategori, jadi pass null agar getAllCategoriesByUser mengambil semua
        $categories = $model->getAllCategoriesByUser(null); 
        $this->loadView('adminKategori', ['categories' => $categories]);
    }

    // Metode addCategory ini tidak lagi diperlukan atau diubah agar tidak bisa digunakan
    // Saya akan mengomentarinya atau mengarahkan ke halaman error/index admin jika diakses.
    // Untuk tujuan ini, saya akan menghapus fungsionalitas penambahan kategori dari view.
    // Jika ada permintaan POST ke addCategory dari URL, itu akan dianggap permintaan tidak valid.
    public function addCategory() {
        $response = ['status' => 'error', 'message' => 'Fungsi tambah kategori tidak tersedia untuk admin.'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function getCategory() {
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];
        if (isset($_GET['id'])) {
            $categoryId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Sanitasi input
            if (empty($categoryId)) {
                $response['message'] = 'ID Kategori tidak valid.';
            } else {
                $model = $this->loadModel('Kategori');
                // Admin bisa mendapatkan kategori tanpa membatasi user_id (karena bisa milik user mana saja)
                $category = $model->getCategoryByIdAdmin($categoryId); 

                if ($category) {
                    $response = ['status' => 'success', 'data' => $category];
                } else {
                    $response['message'] = 'Kategori tidak ditemukan.';
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function updateCategory() {
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = filter_input(INPUT_POST, 'kategori_id', FILTER_VALIDATE_INT); // Sanitasi input
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT); // Sanitasi input
            $kategori = trim(filter_input(INPUT_POST, 'kategori', FILTER_SANITIZE_STRING)); // Sanitasi input
            $tipe = filter_input(INPUT_POST, 'tipe', FILTER_SANITIZE_STRING); // Sanitasi input
            $icon = filter_input(INPUT_POST, 'icon', FILTER_SANITIZE_STRING); // Sanitasi input

            // Validasi input dasar
            if (empty($categoryId) || !is_numeric($categoryId)) {
                $response['message'] = 'ID Kategori tidak boleh kosong dan harus berupa angka.';
            } elseif (empty($userId) || !is_numeric($userId)) {
                $response['message'] = 'ID Pengguna tidak boleh kosong dan harus berupa angka.';
            } elseif (empty($kategori)) {
                $response['message'] = 'Nama kategori tidak boleh kosong.';
            } elseif (empty($icon)) {
                $response['message'] = 'Ikon kategori tidak boleh kosong.';
            } elseif ($tipe !== 'pemasukan' && $tipe !== 'pengeluaran') {
                 $response['message'] = 'Tipe kategori tidak valid.';
            } else {
                $model = $this->loadModel('Kategori');
                // Cek duplikasi untuk user_id baru (jika user_id diubah) dan nama/tipe kategori
                $existingCategory = $model->getCategoryByNameAndType($userId, $kategori, $tipe);
                if ($existingCategory && $existingCategory->kategori_id != $categoryId) {
                    $response['message'] = 'Kategori dengan nama dan tipe yang sama sudah ada untuk pengguna ini.';
                } else {
                    // Gunakan metode update khusus admin yang menerima user_id
                    $result = $model->updateCategoryAdmin($categoryId, $userId, $kategori, $tipe, $icon);
                    if ($result) {
                        $response = ['status' => 'success', 'message' => 'Kategori berhasil diperbarui.'];
                    } else {
                        $response['message'] = 'Gagal memperbarui kategori. Pastikan data valid dan User ID ada.';
                    }
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function deleteCategory() {
        $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = filter_input(INPUT_POST, 'kategori_id', FILTER_VALIDATE_INT); // Sanitasi input
            if (empty($categoryId)) {
                $response['message'] = 'ID Kategori tidak valid.';
            } else {
                $model = $this->loadModel('Kategori');
                // Admin bisa menghapus kategori siapa saja
                $result = $model->deleteCategoryAdmin($categoryId);

                if ($result) {
                    $response = ['status' => 'success', 'message' => 'Kategori berhasil dihapus.'];
                } else {
                    $response['message'] = 'Gagal menghapus kategori. Mungkin kategori tidak ditemukan atau digunakan dalam transaksi.';
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}