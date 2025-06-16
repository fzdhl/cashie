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

    public function index()
    {
        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");
        $tanggungan = $model->getByUser($id_user);

        $kategoriModel = $this->loadModel('Kategori');
        $categories = $kategoriModel->getAllCategoriesByUser($id_user);

        $this->loadView("tanggungan", [
            'tanggungan' => $tanggungan,
            'categories' => $categories
        ]);
    }

    public function insert()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(["isSuccess" => false, "info" => "Metode request tidak diizinkan."]);
            exit();
        }

        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");

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

        $kategori_id = (int) $kategori_id;
        $jumlah = (int) $jumlah;

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

        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");

        $tanggungan_id = $_POST['tanggungan_id'] ?? null;
        $tanggungan = $_POST['tanggungan'] ?? '';
        $jadwal_pembayaran = $_POST['jadwal_pembayaran'] ?? '';
        $kategori_id = $_POST['kategori_id'] ?? null;
        $jumlah = $_POST['jumlah'] ?? 0;

        // validasi input
        if (empty($tanggungan_id) || empty($tanggungan) || empty($jadwal_pembayaran) || empty($kategori_id) || !is_numeric($jumlah) || $jumlah <= 0) {
            echo json_encode(["isSuccess" => false, "info" => "Data update tidak lengkap atau tidak valid."]);
            exit();
        }

        $kategori_id = (int) $kategori_id;
        $jumlah = (int) $jumlah;

        if ($tanggungan_id) {
            $data = [
                'user_id' => $id_user,
                'tanggungan' => $tanggungan,
                'jadwal_pembayaran' => $jadwal_pembayaran,
                'kategori_id' => $kategori_id,
                'jumlah' => $jumlah,
            ];

            if ($model->update($tanggungan_id, $data)) {
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
        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");
        $model->resetStatus($id_user);

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
        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");

        if ($id_tanggungan) {
            if ($model->deleteById($id_tanggungan, $id_user)) {
                echo json_encode(["isSuccess" => true, "info" => "Tanggungan berhasil dihapus."]);
            } else {
                echo json_encode(["isSuccess" => false, "info" => "Gagal menghapus tanggungan. Mungkin tanggungan sudah selesai atau permanen."]);
            }
        } else {
            echo json_encode(["isSuccess" => false, "info" => "ID Tanggungan tidak ditemukan untuk dihapus."]);
        }
        exit();
    }

}

?>