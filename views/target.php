<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rencana Keuangan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="views/styles/dashboard.css" rel="stylesheet">
</head>
<body>
  <?php include_once "header.php" ?>

  <main class="container py-4">
    <h2 class="mb-4 text-center text-start text-md-start fw-bold">Target</h2>

    <!-- Form Tambah Target -->
    <div class="card p-4 mb-4 shadow-sm">
      <h5 class="mb-3 text-center text-start text-md-start fw-semibold" >Tambah Target Baru</h5>
      <form id="targetForm" method="POST">
        <div class="row g-3">
          <div class="col-md-5">
            <input name="target" type="text" class="form-control" placeholder="Nama Target (misal: Tabungan Liburan)" required>
          </div>
          <div class="col-md-5">
            <input name="amount" id="nominalAdd" type="text" class="form-control" placeholder="Nominal Target dalam Rupiah (misal: 10.000.000)" required>
          </div>
          <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-success">Tambah</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Daftar Target -->
    <?php if ($targets): ?>
      <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php include "views/targetCards.php"; ?>
      </div>
    <?php else: ?>
      <div class="row-cols-1 g-4">
        <p class="text-center">Belum ada target yang ditambahkan.</p>
      </div>
    <?php endif; ?>
      <!-- Card tambahan akan muncul di sini, dengan DOM injection melalui ajax -->
  </main>
  <?php include_once "footer.php" ?>

  <!-- For Edit Modal -->
  <div class="modal fade" id="editTargetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="editTargetForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Target</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="target_id" id="edit-target-id">
          <div class="mb-3">
            <label for="edit-target-name">Nama Target</label>
            <input type="text" name="target" class="form-control" id="edit-target-name" required>
          </div>
          <div class="mb-3">
            <label for="edit-target-amount">Nominal Target</label>
            <input type="text" name="amount" class="form-control" id="edit-target-amount" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan Perubahan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="views/scripts/target.js"></script>
</body>
</html>
