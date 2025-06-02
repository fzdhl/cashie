<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Grafik</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="views/styles/dashboard.css" /> -->
    <link rel="stylesheet" href="views/styles/dashboard.css">
    <!-- <link rel="stylesheet" href="views/styles/styleReport.css" /> -->
</head>
<body>
    <?php include_once "header.php" ?>

    <div class="container my-4">
        <div class="row row-cols-1 row-cols-lg-2 g-4">

            <!-- Form Tambah Transaksi -->
            <div class="input_transaksi_div col">
                <div class="card shadow-sm">
                    <div class="header_input_div card-header text-white">
                        Tambah Transaksi
                    </div>
                    <div class="card-body">
                        <form action="?c=ReportController&m=addTransaction" method="post" class="row g-3">
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

            <!-- Form Filter Tanggal -->
            <div class="filter_div col ">
                <div class="card shadow-sm">
                    <div class="header_input_div card-header text-white">
                        Filter Laporan Bulanan
                    </div>
                    <div class="card-body">
                        <form method="post" action="?c=ReportController&m=selectDate" class="row g-3">
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

        </div>

        <!-- Grafik -->
        <div class="my-5">
            <h3 class="mb-4">Grafik Keuangan: <span class="text-primary"><?= $_SESSION['user']->username ?></span></h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm">
                        <h5 class="text-info">Pemasukan</h5>
                        <canvas id="pemasukan"></canvas>
                        <h6>Rata-rata pemasukan bulanan: <?echo isset($avrPemasukan) ? $avrPemasukan : 0;?></h6>
                        <table>
                            <tr>
                                <th>Tanggal</th>
                                <th>IDR</th>
                                <th>Deskripsi</th>
                            </tr>
                            <?php
                                // $tabel;
                                if(isset($listPemasukan)){
                                    while ($tabel = $listPemasukan->fetch_object()) {
                                        printf("<tr>
                                            <th>%s</th>
                                            <th>%s</th>
                                            <th>%s</th>
                                        </tr>", $tabel->tanggal, $tabel->jumlah, $tabel->keterangan);
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
                        <h6>Rata-rata pengeluaran bulanan: <?echo isset($avrPengeluaran) ? $avrPengeluaran : 0;?></h6>
                        <table>
                            <tr>
                                <th>Tanggal</th>
                                <th>IDR</th>
                                <th>Deskripsi</th>
                            </tr>
                            <?php
                                // $tabel;
                                if(isset($listPengeluaran)){
                                    while ($tabel = $listPengeluaran->fetch_object()) {
                                        printf("<tr>
                                            <th>%s</th>
                                            <th>%s</th>
                                            <th>%s</th>
                                        </tr>", $tabel->tanggal, $tabel->jumlah, $tabel->keterangan);
                                    }
                                }
                                
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once "footer.php"?>

    <script>
        const datePemasukan = <?= json_encode($datePemasukan) ?>;
        const totalPemasukan = <?= json_encode($totalPemasukan) ?>;

        const datePengeluaran = <?= json_encode($datePengeluaran) ?>;
        const totalPengeluaran = <?= json_encode($totalPengeluaran) ?>;
    </script>
    <script src="views/scripts/laporan.js"></script>
</body>
</html>