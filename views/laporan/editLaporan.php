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
    <?php include_once __DIR__ . '/../header.php'; ?>
    <div class="container my-4">
        <div class="d-flex justify-content-center"> <!-- Membuat card di tengah -->
            <div class="w-100" style="max-width: 900px;"> <!-- Maksimal lebar card -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 header-date-row">
                    <h2 class="fw-bold text-center text-md-start mb-3 mb-md-0">Laporan</h2>
                </div>
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-success text-white rounded-top-4">
                        <h5 class="mb-0">Edit Laporan</h5>
                    </div>
                    <div class="card-body">
                        <form action="?c=LaporanController&m=editLaporan" method="post" class="row g-4">
                            <input type="hidden" name="jenis_laporan" value="mingguan">

                            <!-- Kolom Tanggal -->
                            <div class="col-md-6 d-flex flex-column justify-content-between">
                                <div>
                                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                                    <input 
                                        type="date" 
                                        id="tanggal_awal" 
                                        name="tanggal_awal" 
                                        class="form-control shadow-sm mb-3" 
                                        value="<?php echo $tanggal_awal ?>" 
                                        required
                                    >
                                </div>
                                <div>
                                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                    <input 
                                        type="date" 
                                        id="tanggal_akhir" 
                                        name="tanggal_akhir" 
                                        class="form-control shadow-sm" 
                                        value="<?php echo $tanggal_akhir ?>" 
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Kolom Catatan -->
                            <div class="col-md-6">
                                <label for="catatan" class="form-label">Catatan Laporan (optional)</label>
                                <textarea 
                                    name="catatan" 
                                    id="catatan" 
                                    rows="5" 
                                    class="form-control shadow-sm h-80"
                                ><?php echo $catatan ?? null ?></textarea>
                            </div>

                            <input type="hidden" name="laporan_id" value="<?php echo $laporan_id ?>">

                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100 py-2 shadow-sm">
                                    <i class="bi bi-check-circle-fill me-2"></i> Submit
                                </button>
                            </div>
                        </form>

                        <!-- Pesan Error -->

                        <?php if(isset($error['error_editlaporan'])): ?>
                            <?= htmlspecialchars($error['error_editlaporan']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php include_once __DIR__ . '/../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
