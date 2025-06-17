<?php

class TanggunganController extends Controller
{
    private $model;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: ?c=UserController&m=loginView");
            exit();
        }
    }

    private function isAdmin()
    {
        return isset($_SESSION['user']->privilege) && $_SESSION['user']->privilege === 'admin';
    }

    public function index()
    {
        $model = $this->loadModel("Tanggungan");
        $kategoriModel = $this->loadModel('Kategori');

        if ($this->isAdmin()) {
            $tanggungan = $model->getAll();
            $categories = $kategoriModel->getAllCategoriesByUser(null);
        } else {
            $id_user = $_SESSION['user']->user_id;
            $tanggungan = $model->getByUser($id_user);
            $categories = $kategoriModel->getAllCategoriesByUser($id_user);
        }

        $this->loadView("tanggungan", [
            'tanggungan' => $tanggungan,
            'categories' => $categories,
            'isAdmin' => $this->isAdmin()
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

        $id_user_input = $_POST['user_id'] ?? null;
        $id_user = $this->isAdmin() ? $id_user_input : $_SESSION['user']->user_id;

        $tanggungan = $_POST['tanggungan'] ?? '';
        $jadwal_pembayaran = $_POST['jadwal_pembayaran'] ?? '';
        $kategori_id = $_POST['kategori_id'] ?? null;
        $jumlah = $_POST['jumlah'] ?? 0;
        $status = "Belum dibayar";

        if (empty($tanggungan) || empty($jadwal_pembayaran) || empty($kategori_id) || !is_numeric($jumlah) || $jumlah <= 0) {
            header('Content-Type: application/json');
            echo json_encode(["isSuccess" => false, "info" => "Data yang dikirim tidak lengkap atau tidak valid."]);
            exit();
        }

        if ($this->isAdmin() && (empty($id_user) || !is_numeric($id_user))) {
            header('Content-Type: application/json');
            echo json_encode(["isSuccess" => false, "info" => "ID Pengguna harus ditentukan dan berupa angka oleh admin."]);
            exit();
        }

        $kategori_id = (int) $kategori_id;
        $jumlah = (int) $jumlah;
        $id_user = (int) $id_user;

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
        $status = $_POST['status'] ?? null;
        $user_id_from_post = $_POST['user_id'] ?? null;

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
            if ($user_id_from_post !== null) {
                $data['user_id'] = (int) $user_id_from_post;
            }
        } else {
            $id_user_sesi = $_SESSION['user']->user_id;
            $existingTanggungan = $model->getByIdAndUser($tanggungan_id, $id_user_sesi);
            if (!$existingTanggungan) {
                echo json_encode(["isSuccess" => false, "info" => "Anda tidak memiliki izin untuk memperbarui tanggungan ini."]);
                exit();
            }
            $data['user_id'] = $id_user_sesi;
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
        if (!$this->isAdmin()) {
            $id_user = $_SESSION['user']->user_id;
            $model = $this->loadModel("Tanggungan");
            $model->resetStatus($id_user);
        } else {
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
        $id_user_sesi = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");

        if ($id_tanggungan) {
            if ($this->isAdmin()) {
                if ($model->deleteAnyById($id_tanggungan)) {
                    echo json_encode(["isSuccess" => true, "info" => "Tanggungan berhasil dihapus oleh admin."]);
                } else {
                    echo json_encode(["isSuccess" => false, "info" => "Gagal menghapus tanggungan oleh admin."]);
                }
            } else {
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

    public function viewUserTanggungan($userId)
    {
        if (!$this->isAdmin()) {
            header("Location: ?c=UserController&m=loginView");
            exit();
        }

        $model = $this->loadModel("Tanggungan");
        $kategoriModel = $this->loadModel('Kategori');

        $tanggungan = $model->getByUser($userId);
        $categories = $kategoriModel->getAllCategoriesByUser($userId);

        $this->loadView("tanggungan_admin_view", [
            'tanggungan' => $tanggungan,
            'categories' => $categories,
            'viewingUserId' => $userId,
            'isAdmin' => true
        ]);
    }

}

?>