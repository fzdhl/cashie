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
    <?php include_once __DIR__ . '/./header.php'; ?>

    <div class="container my-4 fullscreen-section">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 header-date-row">
            <h2 class="fw-bold text-center text-md-start mb-3 mb-md-0">Laporan</h2>
        </div>
        
        <div class="laporan-wrapper my-3">

            <div class="laporan-card card shadow-sm border-0">
                    <div class="card-header bg-success text-white fw-semibold">
                        Tambah Laporan Mingguan
                    </div>
                    <div class="card-body my-3">
                        <form method="post" class="row g-3" id="addReport">
                            <input type="hidden" value="mingguan" name="jenis_laporan">

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
                            if(isset($error['error_addlaporan'])){
                                echo "<p class=\"m-3\">*{$error['error_addlaporan']}</p>";
                            }
                        ?>
                    <p id="errorReport" class="m-3"></p>
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
        <div id="listReport">
            <?php include_once __DIR__ . '/./listLaporan.php'; ?>
        </div>
        
        
        <div class="my-5">
            <h3 class="mb-4">Grafik Keuangan: <span class="text-success"><?= htmlspecialchars($_SESSION['user']->username) ?></span></h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm">
                        <h5 class="text-info">Pemasukan</h5>
                        <canvas id="pemasukan"></canvas>
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
                                        </tr>", $tabel->tanggal_transaksi, $tabel->jumlah, htmlspecialchars($tabel->keterangan));
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
                                        </tr>", $tabel->tanggal_transaksi, $tabel->jumlah, htmlspecialchars($tabel->keterangan));
                                    }
                                }
                                
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <?php include_once __DIR__ . '/./footer.php'; ?>

    <script>
        const datePemasukan = <?= json_encode($dataPemasukan['date']) ?>;
        const totalPemasukan = <?= json_encode($dataPemasukan['total']) ?>;

        const datePengeluaran = <?= json_encode($dataPengeluaran['date']) ?>;
        const totalPengeluaran = <?= json_encode($dataPengeluaran['total']) ?>;
    </script>
    <!-- <script src="views/scripts/laporan.js"></script> -->
    <script src="views/scripts/laporan.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
