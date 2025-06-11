<?php
class TanggunganController extends Controller
{
    private $model;

    // konstruktor dipanggil otomatis setiap kali class dibuat
    public function __construct()
    {
        // agar bisa memakai session untuk menyimpan atau membaca data
        session_start();

        // mengecek apakah user sudah login
        if (!isset($_SESSION['user'])) {
            // jika user belum login maka diarahkan ke halaman login
            header("Location: ?c=UserController&m=loginView");
            // program berhenti dan tidak lanjut menjalankan method lainnya
            exit();
        }

    }

    // menampilkan seluruh daftar tanggungan milik user aktif ke halaman view
    public function index()
    {
        // mengambil id user yang login
        $id_user = $_SESSION['user']->user_id;
        // membuat objek baru agar bisa memanggil method di model tanggungan
        $model = $this->loadModel("Tanggungan");
        // mengambil semua data dari tabel tanggungan berdasarkan user_id 
        $tanggungan = $model->getByUser($id_user);

        // load model kategori dan ambil data kategori dari halaman kategori
        $kategoriModel = $this->loadModel('Kategori');
        $categories = $kategoriModel->getAllCategoriesByUser($id_user);

        // menampilkan data hasik query ke tampilan html (ditampilkan ke browser user)
        $this->loadView("tanggungan", [
            'tanggungan' => $tanggungan,
            'categories' => $categories
        ]);
    }

    // menyimpan entri tagihan yang dikirim dari form ke database dan mengunci
    public function simpanPermanen()
    {
        // mengambil id user yang sedang login dari session
        $id_user = $_SESSION['user']->user_id;

        // mengambil input dari form yang dikirim via method post 
        // disimopan di array
        $namaList = $_POST['nama'] ?? [];
        $jadwalList = $_POST['jadwal'] ?? [];
        $jenisList = $_POST['jenis'] ?? [];
        $jumlahList = $_POST['jumlah'] ?? [];
        // data periode sesuai dengan data saat ini
        // $periode = date('Y-m-01');

        // inisialisasi status keberhasilan
        $berhasil = true;

        // memuat model agar bisa memanggil method insert() dan lainnya
        // TANYA INI PERLU ATAU NGGA???
        $model = $this->loadModel("Tanggungan");

        // menyimpan banyak data tanggungan sekaligus ke database
        for ($i = 0; $i < count($namaList); $i++) {
            $data = [
                // menyusun data menjadi array 1 dimensi, urut sesuai kolom database
                $id_user,
                $namaList[$i],
                $jadwalList[$i],
                $jenisList[$i],
                (int) $jumlahList[$i],
                'Belum dibayar',
                // $periode
            ];

            // menyimpan ke database 
            if (!$model->insert($data)) {
                // jika salah satu gagal maka berhasil akan diubah menjadi false
                $berhasil = false;
                break;
            }
        }

        // mengecek apakah menyimpan data berhasil?
        // if ($berhasil) {
        //     // jika berhasil maka tanggungan user akan di-update di kolom permanen
        //     // permanen = 1, artinya tidak bisa diedit atau dihapus lagi
        //     $model->setPermanen($id_user);
        // }

        // setelah selesai akan diarahkan ke halaman utama untuk melihat hasil akhirnya
        header('Location: ?c=TanggunganController&m=index');
        exit();
    }

    // mengatur ulang semua data tagihan tanggungan milik user setiap awal bulan
    public function resetAwalBulan()
    {
        // mengambil id user dari session
        $id_user = $_SESSION['user']->user_id;
        // memanggil model dan menjalankan reset
        $model = $this->loadModel("Tanggungan");
        // resetStatus($id_user) akan mengeksekusi query update seperti:
        // UPDATE tanggungan 
        // SET status = 'Belum dibayar', permanen = 0 
        // WHERE user_id = ?
        $model->resetStatus($id_user);

        // mengarahkan kembali ke tampilan daftar tanggungan
        header('Location: ?c=TanggunganController&m=index');
        exit();
    }

    // menyamakan data pengeluaran dengan status tagihan di tabel tanggungan
    // jika ada catatan pengeluaran yang cocok nama dan jumlah tanggungannya maka status akan diubah menjadi selesai
    public function sinkronDariCatatan()
    {
        // mengambil id user agar sinkronisasi hanya terjadi pada data user tertentu
        $id_user = $_SESSION['user']->user_id;
        // memanggil model dan mengambil data pengeluaran
        $model = $this->loadModel("Tanggungan");
        // ambil daftar catatan pengeluaran (jenis_transaksi = 'pengeluaran') dari tabel catatan_keuangan milik user tersebut.
        $pengeluaran = $model->getPengeluaranUser($id_user);

        // membandingkan dan memperbarui status tanggungan
        foreach ($pengeluaran as $item) {
            // status akan diupdate menjadi selesai jika
            $model->updateStatusSelesai(
                // di data user tertentu terdapat
                $id_user,
                // nama pengeluaran dan jumlah yang sama dengan isi di tabel tanggungan
                $item['keterangan'],
                $item['jumlah']
            );
        }

        // diarahkan ke halaman daftar tanggungan
        header('Location: ?c=TanggunganController&m=index');
        exit();
    }

    // menghapus data tanggungan tertentu asal belum disimpan permanen di database
    public function hapus()
    {
        $id = $_GET['id'] ?? null;
        // memastikan yang menghapus data adalah pemilik data
        $id_user = $_SESSION['user']->user_id;

        // menginisialisasi model agar bisa mengakses fungsi
        $model = $this->loadModel("Tanggungan");
        // menjalankan hapus jika id tersedia dan permanen = 0
        if ($id) {
            // query di model 
            // DELETE FROM tanggungan WHERE id = ? AND user_id = ? AND permanen = 0
            $model->deleteById($id, $id_user);
        }

        // mengarahkan kembali ke halaman indeks
        header('Location: ?c=TanggunganController&m=index');
        exit();
    }
}
?>