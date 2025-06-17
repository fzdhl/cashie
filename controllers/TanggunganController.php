<?php

class TanggunganController extends Controller
{
    private $model; // Properti model tidak digunakan di constructor atau isAdmin, jadi bisa dihapus atau diinisialisasi di method

    public function __construct()
    {
        session_start();
        // Memastikan sesi 'user' ada untuk kedua peran (user biasa dan admin)
        if (!isset($_SESSION['user'])) {
            header("Location: ?c=UserController&m=loginView");
            exit();
        }
    }

    // Metode bantuan untuk memeriksa apakah pengguna adalah admin
    private function isAdmin()
    {
        // Asumsi: Ada properti 'privilege' di objek $_SESSION['user']
        // Berdasarkan petunjuk tabel, ada kolom 'privilege' di tabel user.
        // Jika $_SESSION['user']->privilege ada dan nilainya 'admin', maka adalah admin.
        return isset($_SESSION['user']->privilege) && $_SESSION['user']->privilege === 'admin';
    }

    public function index()
    {
        $model = $this->loadModel("Tanggungan");
        $kategoriModel = $this->loadModel('Kategori');

        if ($this->isAdmin()) {
            // Admin bisa melihat semua tanggungan
            $tanggungan = $model->getAll();
            // Asumsi admin bisa melihat semua kategori, bukan hanya kategori milik user tertentu.
            // Metode getAllCategories() di model Kategori tidak ada dalam file yang diberikan,
            // jadi kita asumsikan ada atau gunakan getAllCategoriesByUser() dengan ID admin jika itu logikanya.
            // Untuk amannya, jika getAllCategories() tidak ada, Anda perlu membuatnya.
            // Jika KategoriController sudah memiliki getAllCategoriesByUser(null), itu bisa dipakai.
            // Untuk saat ini, asumsikan ini akan mengambil semua kategori dari tabel 'kategori'.
            // Kalau tidak, mungkin perlu ditambahkan di model Kategori.
            $categories = $kategoriModel->getAllCategoriesByUser(null); // Atau buat metode getAllCategories() jika admin bisa melihat semua.
            // JikagetAllCategoriesByUser(null) tidak bekerja, maka harus ada metode getAllCategories di model Kategori.
            // Contoh implementasi di Kategori.php:
            // public function getAllCategories() { return $this->dbconn->query("SELECT * FROM kategori")->fetch_all(MYSQLI_ASSOC); }
        } else {
            // User biasa hanya bisa melihat tanggungan miliknya
            $id_user = $_SESSION['user']->user_id;
            $tanggungan = $model->getByUser($id_user);
            $categories = $kategoriModel->getAllCategoriesByUser($id_user);
        }

        $this->loadView("tanggungan", [
            'tanggungan' => $tanggungan,
            'categories' => $categories,
            'isAdmin' => $this->isAdmin() // Kirim status admin ke view
        ]);
    }

    public function insert()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(["isSuccess" => false, "info" => "Metode request tidak diizinkan."]);
            exit();
        }

        $model = $this->loadModel("Tanggungan");

        // Dapatkan user_id. Jika admin dan ada user_id di POST, gunakan itu. Jika bukan, gunakan user_id dari sesi.
        $id_user_input = $_POST['user_id'] ?? null;
        $id_user = $this->isAdmin() ? $id_user_input : $_SESSION['user']->user_id;

        $tanggungan = $_POST['tanggungan'] ?? '';
        $jadwal_pembayaran = $_POST['jadwal_pembayaran'] ?? '';
        $kategori_id = $_POST['kategori_id'] ?? null;
        $jumlah = $_POST['jumlah'] ?? 0;
        $status = "Belum dibayar"; // Status default saat insert

        // Validasi input umum
        if (empty($tanggungan) || empty($jadwal_pembayaran) || empty($kategori_id) || !is_numeric($jumlah) || $jumlah <= 0) {
            header('Content-Type: application/json');
            echo json_encode(["isSuccess" => false, "info" => "Data yang dikirim tidak lengkap atau tidak valid."]);
            exit();
        }

        // Validasi khusus admin untuk user_id
        if ($this->isAdmin() && (empty($id_user) || !is_numeric($id_user))) {
             header('Content-Type: application/json');
            echo json_encode(["isSuccess" => false, "info" => "ID Pengguna harus ditentukan dan berupa angka oleh admin."]);
            exit();
        }
        
        $kategori_id = (int) $kategori_id;
        $jumlah = (int) $jumlah;
        $id_user = (int) $id_user; // Pastikan id_user juga di-cast ke int

        $data = [
            $id_user,
            $tanggungan,
            $jadwal_pembayaran,
            $kategori_id,
            $jumlah,
            $status
        ];

        $result = $model->insert($data);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    public function update()
    {
        header('Content-Type: application/json');

        $model = $this->loadModel("Tanggungan");

        $tanggungan_id = $_POST['tanggungan_id'] ?? null;
        $tanggungan = $_POST['tanggungan'] ?? '';
        $jadwal_pembayaran = $_POST['jadwal_pembayaran'] ?? '';
        $kategori_id = $_POST['kategori_id'] ?? null;
        $jumlah = $_POST['jumlah'] ?? 0;
        $status = $_POST['status'] ?? null; // Dapatkan status jika ada (admin yang mengirim)
        $user_id_from_post = $_POST['user_id'] ?? null; // Dapatkan user_id jika dikirim oleh admin

        // Validasi input
        if (empty($tanggungan_id) || empty($tanggungan) || empty($jadwal_pembayaran) || empty($kategori_id) || !is_numeric($jumlah) || $jumlah <= 0) {
            echo json_encode(["isSuccess" => false, "info" => "Data update tidak lengkap atau tidak valid."]);
            exit();
        }

        $kategori_id = (int) $kategori_id;
        $jumlah = (int) $jumlah;
        $tanggungan_id = (int) $tanggungan_id;

        $data = [
            'tanggungan' => $tanggungan,
            'jadwal_pembayaran' => $jadwal_pembayaran,
            'kategori_id' => $kategori_id,
            'jumlah' => $jumlah,
        ];

        if ($this->isAdmin()) {
            if ($status !== null) {
                $data['status'] = $status;
            }
            // Jika admin mengirim user_id untuk update (misal mengalihkan tanggungan ke user lain)
            if ($user_id_from_post !== null) {
                $data['user_id'] = (int)$user_id_from_post;
            }
        } else {
            // Jika bukan admin, pastikan tanggungan ini milik user yang sedang login
            // dan tambahkan user_id sesi ke data untuk filter di model update()
            $id_user_sesi = $_SESSION['user']->user_id;
            $existingTanggungan = $model->getByIdAndUser($tanggungan_id, $id_user_sesi);
            if (!$existingTanggungan) {
                echo json_encode(["isSuccess" => false, "info" => "Anda tidak memiliki izin untuk memperbarui tanggungan ini."]);
                exit();
            }
            $data['user_id'] = $id_user_sesi; // Penting: Tambahkan user_id sesi ke data
        }

        if ($tanggungan_id) {
            if ($this->isAdmin()) {
                $result = $model->updateById($tanggungan_id, $data);
            } else {
                $result = $model->update($tanggungan_id, $data);
            }
            
            if ($result) {
                echo json_encode(["isSuccess" => true, "info" => "Tanggungan berhasil diperbarui."]);
            } else {
                echo json_encode(["isSuccess" => false, "info" => "Gagal memperbarui tanggungan di database. Pastikan semua input valid."]);
            }
        } else {
            echo json_encode(["isSuccess" => false, "info" => "ID Tanggungan tidak ditemukan."]);
        }
        exit();
    }

    public function resetAwalBulan()
    {
        // Hanya admin atau user yang memiliki tanggungan yang dapat mereset
        if (!$this->isAdmin()) {
            $id_user = $_SESSION['user']->user_id;
            $model = $this->loadModel("Tanggungan");
            $model->resetStatus($id_user);
        } else {
            // Admin bisa mereset semua status tanggungan di sistem
            $model = $this->loadModel("Tanggungan");
            $model->resetAllStatuses();
        }

        header('Location: ?c=TanggunganController&m=index');
        exit();
    }

    public function sinkronDariCatatan()
    {
        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");
        $pengeluaran = $model->getPengeluaranUser($id_user); 

        foreach ($pengeluaran as $item) {
            $model->updateStatusSelesai(
                $id_user,
                $item['keterangan'],
                $item['jumlah']
            );
        }

        header('Location: ?c=TanggunganController&m=index');
        exit();
    }

    public function hapus()
    {
        header('Content-Type: application/json');

        $id_tanggungan = $_POST['id'] ?? null;
        $id_user_sesi = $_SESSION['user']->user_id; // User yang sedang login
        $model = $this->loadModel("Tanggungan");

        if ($id_tanggungan) {
            if ($this->isAdmin()) {
                // Admin bisa menghapus tanggungan apa pun tanpa peduli user_id
                if ($model->deleteAnyById($id_tanggungan)) {
                    echo json_encode(["isSuccess" => true, "info" => "Tanggungan berhasil dihapus oleh admin."]);
                } else {
                    echo json_encode(["isSuccess" => false, "info" => "Gagal menghapus tanggungan oleh admin."]);
                }
            } else {
                // User biasa hanya bisa menghapus tanggungannya sendiri
                if ($model->deleteById($id_tanggungan, $id_user_sesi)) {
                    echo json_encode(["isSuccess" => true, "info" => "Tanggungan berhasil dihapus."]);
                } else {
                    echo json_encode(["isSuccess" => false, "info" => "Gagal menghapus tanggungan."]);
                }
            }
        } else {
            echo json_encode(["isSuccess" => false, "info" => "ID Tanggungan tidak ditemukan untuk dihapus."]);
        }
        exit();
    }

    // Contoh: Melihat tanggungan untuk user tertentu (opsional, bisa dihandle di index jika ada parameter)
    public function viewUserTanggungan($userId) {
        if (!$this->isAdmin()) {
            header("Location: ?c=UserController&m=loginView"); // Atau halaman error/access denied
            exit();
        }

        $model = $this->loadModel("Tanggungan");
        $kategoriModel = $this->loadModel('Kategori');

        $tanggungan = $model->getByUser($userId);
        $categories = $kategoriModel->getAllCategoriesByUser($userId); // Atau semua kategori

        $this->loadView("tanggungan_admin_view", [ // Mungkin perlu view khusus admin
            'tanggungan' => $tanggungan,
            'categories' => $categories,
            'viewingUserId' => $userId,
            'isAdmin' => true
        ]);
    }

}

?>