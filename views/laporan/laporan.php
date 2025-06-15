<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Grafik</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="views/styles/dashboard.css" /> -->
    <link rel="stylesheet" href="views/styles/dashboard.css">
    <!-- <link rel="stylesheet" href="views/styles/styleReport.css" /> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="container my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 header-date-row">
            <h2 class="fw-bold text-center text-md-start mb-3 mb-md-0">Laporan</h2>
        </div>
        
        <div class="laporan-wrapper my-3">

            <div class="laporan-card card shadow-sm border-0">
                    <div class="card-header bg-success text-white fw-semibold">
                        Tambah Laporan Mingguan
                    </div>
                    <div class="card-body my-3">
                        <form action="?c=LaporanController&m=addLaporan" method="post" class="row g-3">
                            <!-- <div class="col-12">
                                <label for="jenis_laporan" class="form-label">Jenis Laporan</label>
                                <select class="form-select" name="jenis_laporan" id="jenis_laporan" required>
                                    <option disabled selected value="">Pilih jenis laporan</option>
                                    <option value="bulanan">Bulanan</option>
                                    <option value="mingguan">Mingguan</option>
                                </select>
                            </div> -->
                            <input type="hidden" value="mingguan" name="jenis_laporan">

                            <!-- <div id="tanggal_range" class="col-12 d-none row">
                                <div class="col-md-6">
                                    <label for="bulan" class="form-label">Bulan</label>
                                    <select name="bulan" id="month" class="form-select">
                                        <?php foreach ($bulanList as $num => $name): ?>
                                            <option value="<?= $num ?>" <?= $selectedMonth == $num ? 'selected' : '' ?>>
                                                <?= $name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <input type="number" name="tahun" id="year" min="1970" class="form-control" value="<?= $selectedYear ?>">
                                </div>
                            </div> -->

                            <div id="tanggal_manual" class="row">
                                <div class="col-md-6">
                                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control">
                                </div>
                                <div class="my-3">
                                    <label for="catatan" class="form-label">Catatan Laporan (optional)</label>
                                    <input type="text" name="catatan" class="form-control">
                                </div>
                                
                            </div>
                                
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100">Submit</button>
                            </div>
                        </form>

                        
                    </div>
                    <?php
                        if(isset($error['error'])){
                            echo "<p class=\"m-3\">*{$error['error']}</p>";
                        }
                        if(isset($error['error_addlaporan'])){
                            echo "<p class=\"m-3\">*{$error['error_addlaporan']}</p>";
                        }
                    ?>
            </div>
        </div>
        
        <div class="card mt-4 shadow-sm border-0 rounded-4">
            <div class="card-body text-center">
                <div class="row g-3 justify-content-center">
                    <div class="col-md-5 col-sm-6">
                        <button 
                            class="btn btn-outline-success w-100 py-2 shadow-sm" 
                            onclick="toggleLaporan('mingguan')"
                        >
                            Laporan Mingguan
                        </button>
                    </div>
                    <div class="col-md-5 col-sm-6">
                        <button 
                            class="btn btn-outline-success w-100 py-2 shadow-sm" 
                            onclick="toggleLaporan('bulanan')"
                        >
                            Laporan Bulanan
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="filter_div col mt-4" id="laporan_bulanan_section">
            <div class="card shadow-sm">
                <div class="header_input_div card-header text-white bg-success">
                    Filter Laporan Bulanan
                </div>
                <div class="card-body">
                    <form method="post" action="?c=LaporanController&m=selectDate" class="row g-3">
                        <div class="col-md-6">
                            <label for="month" class="form-label">Bulan</label>
                            <select name="month" id="month" class="form-select">
                                <?php foreach ($bulanList as $num => $name): ?>
                                    <option value="<?= $num ?>" <?= $selectedMonth == $num ? 'selected' : '' ?>>
                                        <?= $name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="year" class="form-label">Tahun</label>
                            <input type="number" name="year" id="year" min="1970" class="form-control" value="<?= $selectedYear ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-success btn w-100">Tampilkan</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById("jenis_laporan").addEventListener("change", toggleTanggal);
                toggleTanggal();
            });

            function toggleTanggal() {
                const jenis = document.getElementById('jenis_laporan').value;
                const tanggalManual = document.getElementById('tanggal_manual');
                const tanggalRange = document.getElementById('tanggal_range');

                console.log("jenis laporan:", jenis);
                console.log("toggleTanggal dijalankan");

                if (jenis === "bulanan") { // Mingguan
                    tanggalManual.classList.add('d-none');
                    tanggalRange.classList.remove('d-none');
                } else if (jenis === "mingguan") { // Bulanan
                    tanggalRange.classList.add('d-none');
                    tanggalManual.classList.remove('d-none');
                } else {
                    tanggalRange.classList.add('d-none');
                    tanggalManual.classList.add('d-none');
                }
            }
        </script>


        

        <div id="laporan_mingguan_section" class="mt-4">
            <div class="card shadow-sm">
                <div class="header_input_div card-header text-white bg-success">
                    Laporan Mingguan
                </div>
                <div class="card-body scrollable-table">
                    <table>
                        <tr>
                            <th>Tanggal Awal</th>
                            <th>Tanggal Akhir</th>
                            <th>Total Pemasukan</th>
                            <th>Total Pengeluaran</th>
                            <th>Catatan</th>
                            <th colspan="3">aksi</th>
                        </tr>
                        <?php
                            // $tabel;
                            if(isset($listLaporanMingguanPengeluaran) && isset($listLaporanMingguanPemasukan)){
                                while (true) {
                                    $tabelPengeluaran = $listLaporanMingguanPengeluaran->fetch_object();
                                    $tabelPemasukan = $listLaporanMingguanPemasukan->fetch_object();

                                    if (!$tabelPengeluaran || !$tabelPemasukan) {
                                        break;
                                    }

                                    printf("<tr>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td>
                                            <form action=\"?c=LaporanController&m=deleteLaporan\" method=\"post\">
                                                <input type=\"hidden\" name=\"laporan_id\" value=\"%s\">
                                                <button type=\"submit\" class=\"btn btn-sm btn-outline-danger\">Hapus</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action=\"?c=LaporanController&m=editReport\" method=\"post\">
                                                <input type=\"hidden\" name=\"laporan_id\" value=\"%s\">
                                                <input type=\"hidden\" name=\"tanggal_awal\" value=\"%s\">
                                                <input type=\"hidden\" name=\"tanggal_akhir\" value=\"%s\">
                                                <input type=\"hidden\" name=\"catatan\" value=\"%s\">
                                                <button type=\"submit\" class=\"btn btn-sm btn-outline-primary\">Edit</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action=\"?c=LaporanController&m=report\" method=\"post\">
                                                <input type=\"hidden\" name=\"laporan_id\" value=\"%s\">
                                                <input type=\"hidden\" name=\"laporan_type\" value=\"mingguan\">
                                                <button type=\"submit\" class=\"btn btn-sm btn-outline-success\">Grafik</button>
                                            </form>
                                        </td>
                                    </tr>", 
                                        $tabelPengeluaran->tanggal_awal, 
                                        $tabelPengeluaran->tanggal_akhir, 
                                        $tabelPemasukan->jumlah, 
                                        $tabelPengeluaran->jumlah,
                                        $tabelPengeluaran->catatan,
                                        $tabelPemasukan->laporan_id,
                                        $tabelPemasukan->laporan_id,
                                        $tabelPengeluaran->tanggal_awal, 
                                        $tabelPengeluaran->tanggal_akhir,
                                        $tabelPengeluaran->catatan,
                                        $tabelPemasukan->laporan_id
                                    );
                                }
                            }                 
                        ?>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="my-5">
            <h3 class="mb-4">Grafik Keuangan: <span class="text-success"><?= $_SESSION['user']->username ?></span></h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm">
                        <h5 class="text-info">Pemasukan</h5>
                        <canvas id="pemasukan"></canvas>
                        <!-- <h6>Rata-rata pemasukan bulanan: <?php echo isset($avrPemasukan) ? $avrPemasukan : 0;?></h6> -->
                        <table>
                            <tr>
                                <th>Tanggal</th>
                                <th>IDR</th>
                                <th>Deskripsi</th>
                            </tr>
                            <?php
                                if(isset($listPemasukan)){
                                    while ($tabel = $listPemasukan->fetch_object()) {
                                        printf("<tr>
                                            <th>%s</th>
                                            <th>%s</th>
                                            <th>%s</th>
                                        </tr>", $tabel->tanggal_transaksi, $tabel->jumlah, $tabel->keterangan);
                                    }
                                }
                                
                            ?>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm">
                        <h5 class="text-danger">Pengeluaran</h5>
                        <canvas id="pengeluaran"></canvas>
                        <!-- <h6>Rata-rata pengeluaran bulanan: <?php echo isset($avrPengeluaran) ? $avrPengeluaran : 0;?></h6> -->
                        <table>
                            <tr>
                                <th>Tanggal</th>
                                <th>IDR</th>
                                <th>Deskripsi</th>
                            </tr>
                            <?php
                                if(isset($listPengeluaran)){
                                    while ($tabel = $listPengeluaran->fetch_object()) {
                                        printf("<tr>
                                            <th>%s</th>
                                            <th>%s</th>
                                            <th>%s</th>
                                        </tr>", $tabel->tanggal_transaksi, $tabel->jumlah, $tabel->keterangan);
                                    }
                                }
                                
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- <div class="row row-cols-1 row-cols-lg-2 g-4">

            <div class="input_transaksi_div col">
                <div class="card shadow-sm">
                    <div class="header_input_div card-header text-white">
                        Tambah Transaksi
                    </div>
                    <div class="card-body">
                        <form action="?c=LaporanController&m=addTransaction" method="post" class="row g-3">
                            <div class="col-12">
                                <label for="transaction_type" class="form-label">Jenis Transaksi</label>
                                <select class="form-select" name="transaction_type" id="transaction_type">
                                    <option value="Pengeluaran">Pengeluaran</option>
                                    <option value="Pemasukan">Pemasukan</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="IDR" class="form-label">Jumlah (IDR)</label>
                                <input type="number" name="IDR" id="IDR" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <input type="text" name="description" id="description" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-success btn w-100">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div> -->

        
    <?php include_once __DIR__ . '/../footer.php'; ?>

    <script>
        function toggleLaporan(jenis) {
            const mingguan = document.getElementById('laporan_mingguan_section');
            const bulanan = document.getElementById('laporan_bulanan_section');

            if (jenis === 'mingguan') {
                mingguan.classList.remove('d-none');
                bulanan.classList.add('d-none');
            } else {
                bulanan.classList.remove('d-none');
                mingguan.classList.add('d-none');
            }
        }

        // Opsional: munculkan default ke mingguan/bulanan
        document.addEventListener("DOMContentLoaded", () => {
            toggleLaporan('mingguan'); // atau 'bulanan'
        });
    </script>

    <script>
        const datePemasukan = <?= json_encode($datePemasukan) ?>;
        const totalPemasukan = <?= json_encode($totalPemasukan) ?>;

        const datePengeluaran = <?= json_encode($datePengeluaran) ?>;
        const totalPengeluaran = <?= json_encode($totalPengeluaran) ?>;
    </script>
    <script src="views/scripts/laporan.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
