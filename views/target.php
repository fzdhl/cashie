<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rencana Keuangan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/cashie/views/styles/target.css">
</head>
<body>
  <?php include_once "header.php" ?>

  <main class="container py-4">
    <h1 class="mb-4">Target</h1>

    <!-- Form Tambah Goal -->
    <div class="card p-4 mb-4 shadow-sm">
      <h5 class="mb-3">Tambah Target Baru</h5>
      <form id="targetForm">
        <div class="row g-3">
          <div class="col-md-5">
            <input name="target" type="text" class="form-control" placeholder="Nama Target (misal: Tabungan Liburan)">
          </div>
          <div class="col-md-5">
            <input name="amount" type="number" class="form-control" placeholder="Target Nominal (Rp)">
          </div>
          <!-- <div class="col-md-3">
            <input type="date" class="form-control" placeholder="Tenggat Waktu">
          </div> -->
          <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-success">Tambah</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Daftar Goals -->
    <div class="row row-cols-1 row-cols-md-2 g-4">
      <!-- Goal Card -->
      <div class="col">
        <div class="card shadow-sm p-3">
          <div class="d-flex justify-content-between">
            <h5 class="mb-2">Dana Darurat</h5>
            <div>
              <button class="btn btn-sm btn-outline-primary me-1">✏️</button>
              <button class="btn btn-sm btn-outline-danger">🗑</button>
            </div>
          </div>
          <p class="text-muted mb-2">Target: Rp 10.000.000</p>
          <div class="progress mb-2" style="height: 15px;">
            <div class="progress-bar bg-success" style="width: 60%;">60%</div>
          </div>
          <small class="text-muted">Rp 6.000.000 dari Rp 10.000.000</small>
        </div>
      </div>

      <!-- Tambah lebih banyak card di sini -->
    </div>
  </main>
  <?php include_once "header.php" ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/views/scripts/target.js"></script>
</body>
</html>
