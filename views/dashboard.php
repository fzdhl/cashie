<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Cashie</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="views/styles/dashboard.css" rel="stylesheet">
  
</head>
<body>
  <?php include_once "header.php" ?>

  <main class="container my-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 header-date-row">
      <h2 class="fw-bold text-center text-md-start mb-3 mb-md-0">Dashboard</h2>
    </div>

    <div class="row g-3 g-md-4 mb-4">
      <div class="col-md-4">
        <div class="text-center p-4 summary-card">
          <h5 class="text-muted">Pengeluaran</h5>
          <p class="text-danger fw-bold">Rp2.000.000</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center p-4 summary-card">
          <h5 class="text-muted">Pemasukan</h5>
          <p class="text-success fw-bold">Rp5.000.000</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center p-4 summary-card">
          <h5 class="text-muted">Saldo</h5>
          <p class="text-warning fw-bold">Rp3.000.000</p>
        </div>
      </div>
    </div>

    <div class="row g-3 g-md-4">
      <div class="col-lg-8">
        <div class="bg-white p-4 rounded shadow-sm">
          <h5 class="mb-3">Hari ini, 18 Maret</h5>
          <p class="text-muted">Tidak ada catatan hari ini</p>
          <h5 class="mt-4 mb-3">Senin, 17 Maret</h5>
          <div class="d-flex justify-content-between border-bottom py-2">
            <span>🚗 Transportasi</span>
            <span class="text-danger">- Rp12.000</span>
          </div>
          <div class="d-flex justify-content-between py-2">
            <span>🍽️ Makan</span>
            <span class="text-danger">- Rp24.000</span>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card border-0 p-3">
          <div class="d-flex justify-content-between">
            <strong>Target</strong><strong>Total: Rp<?=number_format($target['total']['total'], 2, ',', '.')?></strong>
          </div>
          <ul>
            <?php foreach ($target['data'] as $target1): ?>
            <li class="d-flex justify-content-between">
              <p><?=$target1['target']?></p><p>Rp<?=number_format($target1['jumlah'], 2, ',', '.')?></p>
            </li>
            <?php endforeach;?>
          </ul>
        </div>
      </div>
    </div>
  </main>

  <?php include_once "footer.php" ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
