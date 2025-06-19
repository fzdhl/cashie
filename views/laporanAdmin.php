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
            <h2 class="fw-bold text-center text-md-start mb-3 mb-md-0">Laporan Admin</h2>
        </div>
        
        <div class="laporan-wrapper my-3">
            <div class="laporan-card card shadow-sm border-0">
                    <div class="card-header bg-success text-white fw-semibold">
                        Tambah Laporan Mingguan
                    </div>
                    <div class="card-body my-3">
                        <form id="addReport" method="post" class="row g-3">
                            <input type="hidden" value="mingguan" name="jenis_laporan">

                            <div id="tanggal_manual" class="row">
                                <div class="col-md-4">
                                    <label for="user_id" class="form-label">ID User</label>
                                    <input type="number" name="user_id" id="user_id" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control">
                                </div>
                                <div class="col-md-4">
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
                        if(isset($error['error_userID'])){
                            echo "<p class=\"mx-3\">*{$error['error_userID']}</p>";
                        }
                    ?>
                    <p id="errorReport" class="m-3"></p>
            </div>
        </div>
        
        <div id="listReport">
            <?php include_once __DIR__ . '/./listLaporanAdmin.php'; ?>
        </div>

    </div>

        
    <?php include_once __DIR__ . '/./footer.php'; ?>

    <script src="views/scripts/laporanAdmin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
