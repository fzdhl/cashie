<?php
class TanggunganController extends Controller
{
    private $model;

    // konstruktor dipanggil otomatis setiap kali class dibuat
    public function __construct()
    {
        session_start();
        // mengecek apakah user sudah login
        if (!isset($_SESSION['user'])) {
            header("Location: ?c=UserController&m=loginView");
            exit();
        }
    }

    // menampilkan seluruh daftar tanggungan milik user aktif ke halaman view
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

    // menyimpan entri tagihan yang dikirim dari form ke database dan mengunci
    public function simpanPermanen()
    {
        $id_user = $_SESSION['user']->user_id;
        $namaList = $_POST['nama'] ?? [];
        $jadwalList = $_POST['jadwal'] ?? [];
        $jenisList = $_POST['jenis'] ?? [];
        $jumlahList = $_POST['jumlah'] ?? [];

        $berhasil = true;
        $model = $this->loadModel("Tanggungan");

        // menyimpan banyak data tanggungan sekaligus ke database
        for ($i = 0; $i < count($namaList); $i++) {
            $data = [
                $id_user,
                $namaList[$i],
                $jadwalList[$i],
                $jenisList[$i],
                (int) $jumlahList[$i],
                'Belum dibayar',
            ];

            // menyimpan ke database 
            if (!$model->insert($data)) {
                $berhasil = false;
                break;
            }
        }
        
        // mengecek apakah menyimpan data berhasil?
        if ($berhasil) {
            $model->setPermanen($id_user);
        }
        header('Location: ?c=TanggunganController&m=index');
        exit();
    }

    // mengatur ulang semua data tagihan tanggungan milik user setiap awal bulan
    public function resetAwalBulan()
    {
        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");
        $model->resetStatus($id_user);

        header('Location: ?c=TanggunganController&m=index');
        exit();
    }

    // menyamakan data pengeluaran dengan status tagihan di tabel tanggungan
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

    // menghapus data tanggungan tertentu asal belum disimpan permanen di database
    public function hapus()
    {
        $id = $_GET['id'] ?? null;
        $id_user = $_SESSION['user']->user_id;
        $model = $this->loadModel("Tanggungan");
        if ($id) {
            $model->deleteById($id, $id_user);
        }

        header('Location: ?c=TanggunganController&m=index');
        exit();
    }
}
?>