<?php

class TanggunganController extends Controller
{
    private $model;

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
        // Asumsi: Ada properti 'role' di objek $_SESSION['user']
        // Anda perlu memastikan bahwa objek user yang disimpan di sesi memiliki properti 'role'
        // Misalnya, 'admin' atau 'user'. Sesuaikan dengan struktur data user Anda.
        return isset($_SESSION['user']->role) && $_SESSION['user']->role === 'admin';
    }

    public function index()
    {
        $model = $this->loadModel("Tanggungan");
        $kategoriModel = $this->loadModel('Kategori');

        if ($this->isAdmin()) {
            // Admin bisa melihat semua tanggungan
            $tanggungan = $model->getAll(); // Asumsi ada metode getAll() di model Tanggungan
            $categories = $kategoriModel->getAllCategories(); // Admin bisa melihat semua kategori
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

        $id_user_input = $_POST['user_id'] ?? null; // Admin bisa menentukan user_id
        $tanggungan = $_POST['tanggungan'] ?? '';
        $jadwal_pembayaran = $_POST['jadwal_pembayaran'] ?? '';
        $kategori_id = $_POST['kategori_id'] ?? null;
        $jumlah = $_POST['jumlah'] ?? 0;
        $status = "Belum dibayar"; // Status default saat insert

        // Jika admin, gunakan user_id dari input form. Jika bukan, gunakan user_id dari sesi.
        $id_user = $this->isAdmin() ? $id_user_input : $_SESSION['user']->user_id;

        // Validasi tambahan untuk admin: Pastikan user_id_input tidak kosong jika admin
        if ($this->isAdmin() && empty($id_user)) {
             header('Content-Type: application/json');
            echo json_encode(["isSuccess" => false, "info" => "ID Pengguna harus ditentukan oleh admin."]);
            exit();
        }

        if (empty($tanggungan) || empty($jadwal_pembayaran) || empty($kategori_id) || !is_numeric($jumlah) || $jumlah <= 0) {
            header('Content-Type: application/json');
            echo json_encode(["isSuccess" => false, "info" => "Data yang dikirim tidak lengkap atau tidak valid."]);
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
        $status = $_POST['status'] ?? null; // Tambahan: Admin bisa mengubah status

        // Validasi input
        if (empty($tanggungan_id) || empty($tanggungan) || empty($jadwal_pembayaran) || empty($kategori_id) || !is_numeric($jumlah) || $jumlah <= 0) {
            echo json_encode(["isSuccess" => false, "info" => "Data update tidak lengkap atau tidak valid."]);
            exit();
        }

        $kategori_id = (int) $kategori_id;
        $jumlah = (int) $jumlah;
        $tanggungan_id = (int) $tanggungan_id; // Pastikan tanggungan_id juga di-cast ke int

        $data = [
            'tanggungan' => $tanggungan,
            'jadwal_pembayaran' => $jadwal_pembayaran,
            'kategori_id' => $kategori_id,
            'jumlah' => $jumlah,
        ];

        // Jika admin, tambahkan status ke data update
        if ($this->isAdmin() && $status !== null) {
            $data['status'] = $status;
        }

        // Untuk update, user_id tidak perlu ada di data update,
        // tapi perlu diverifikasi kepemilikan jika bukan admin.
        if (!$this->isAdmin()) {
            // Jika bukan admin, pastikan tanggungan ini milik user yang sedang login
            $id_user = $_SESSION['user']->user_id;
            $existingTanggungan = $model->getByIdAndUser($tanggungan_id, $id_user); // Asumsi ada metode ini
            if (!$existingTanggungan) {
                echo json_encode(["isSuccess" => false, "info" => "Anda tidak memiliki izin untuk memperbarui tanggungan ini."]);
                exit();
            }
        }

        if ($tanggungan_id) {
            // Admin bisa update tanpa filter user_id, user biasa update dengan filter user_id
            if ($this->isAdmin()) {
                $result = $model->updateById($tanggungan_id, $data); // Asumsi ada metode updateById di model
            } else {
                $result = $model->update($tanggungan_id, $data); // Ini adalah metode update yang ada, asumsikan sudah memfilter user_id
            }
            

            if ($result) {
                echo json_encode(["isSuccess" => true, "info" => "Tanggungan berhasil diperbarui."]);
            } else {
                echo json_encode(["isSuccess" => false, "info" => "Gagal memperbarui tanggungan di database."]);
            }
        } else {
            echo json_encode(["isSuccess" => false, "info" => "ID Tanggungan tidak ditemukan."]);
        }
        exit();
    }

    public function resetAwalBulan()
    {
        // Hanya admin atau user yang memiliki tanggungan yang dapat mereset
        // Jika hanya user yang boleh mereset tanggungan miliknya, tidak perlu ada perubahan.
        // Jika admin juga bisa mereset semua tanggungan, perlu penyesuaian di model.
        if (!$this->isAdmin()) {
            $id_user = $_SESSION['user']->user_id;
            $model = $this->loadModel("Tanggungan");
            $model->resetStatus($id_user);
        } else {
            // Admin bisa mereset semua status tanggungan atau tanggungan user tertentu
            // Ini membutuhkan penyesuaian di model Tanggungan.
            // Contoh: $model->resetAllStatuses();
            // Atau Anda bisa menambahkan input user_id di form jika admin ingin mereset spesifik user.
            $model = $this->loadModel("Tanggungan");
            $model->resetAllStatuses(); // Contoh metode baru untuk admin
        }

        header('Location: ?c=TanggunganController&m=index');
        exit();
    }

    public function sinkronDariCatatan()
    {
        // Fungsionalitas ini tampaknya lebih spesifik untuk user individu
        // Karena menyinkronkan pengeluaran user dengan tanggungan mereka.
        // Jika admin juga perlu fungsionalitas serupa untuk semua user,
        // maka logika di dalam loop foreach perlu disesuaikan agar bisa bekerja lintas user.
        // Untuk saat ini, asumsikan ini tetap fungsionalitas khusus user.
        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");
        $pengeluaran = $model->getPengeluaranUser($id_user); // Ini harusnya mengambil pengeluaran hanya untuk user ini

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
                if ($model->deleteAnyById($id_tanggungan)) { // Asumsi ada metode deleteAnyById di model
                    echo json_encode(["isSuccess" => true, "info" => "Tanggungan berhasil dihapus oleh admin."]);
                } else {
                    echo json_encode(["isSuccess" => false, "info" => "Gagal menghapus tanggungan oleh admin."]);
                }
            } else {
                // User biasa hanya bisa menghapus tanggungannya sendiri
                if ($model->deleteById($id_tanggungan, $id_user_sesi)) {
                    echo json_encode(["isSuccess" => true, "info" => "Tanggungan berhasil dihapus."]);
                } else {
                    echo json_encode(["isSuccess" => false, "info" => "Gagal menghapus tanggungan. Mungkin tanggungan sudah selesai atau permanen."]);
                }
            }
        } else {
            echo json_encode(["isSuccess" => false, "info" => "ID Tanggungan tidak ditemukan untuk dihapus."]);
        }
        exit();
    }

    // --- Metode Tambahan Khusus Admin ---
    // Anda bisa menambahkan metode khusus admin, atau mengintegrasikan ke metode yang sudah ada.
    // Untuk saat ini, saya integrasikan. Jika ingin metode terpisah, bisa ditambahkan di sini.

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

