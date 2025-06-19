<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Target - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="views/styles/tanggungan.css"> <!-- Reusing tanggungan.css for table styling -->
  <style>
    .editable-row.editing .display-mode,
    .editable-row:not(.editing) .edit-mode {
      display: none;
    }
    .editable-row.editing .edit-mode {
      display: table-cell; /* Or block, inline-block as needed */
    }
    .editable-row .display-mode,
    .editable-row .edit-mode {
      width: 100%; /* Ensure inputs/selects take full width */
    }
  </style>
</head>

<body class="d-flex flex-column">
  <?php include_once "header.php"; ?>
  <div class="container-fluid py-5 flex-grow-1 px-5">
    <h2 class="fw-bold mb-4">Kelola Target <small class="text-muted">(Admin View)</small></h2>
    
    <form id="targetAdminForm">
        <div class="table-responsive mt-4">
            <table class="table table-bordered align-middle bg-white">
              <thead>
                <tr>
                  <th scope="col" class="text-center" style="width: 5%;">ID Target</th>
                  <th scope="col" style="width: 15%;">Pengguna</th>
                  <th scope="col" style="width: 30%;">Nama Target</th>
                  <th scope="col" class="text-end" style="width: 20%;">Jumlah Target</th>
                  <th scope="col" style="width: 20%;">Tanggal Dibuat</th>
                  <th scope="col" class="text-center" style="width: 10%;">Aksi</th>
                </tr>
              </thead>

              <tbody id="tabelDaftarTarget">
                <?php if (!empty($targets)): ?>
                    <?php foreach ($targets as $target): ?>
                        <tr data-target-id="<?= htmlspecialchars($target['target_id']) ?>" class="editable-row">
                            <td class="text-center"><?= htmlspecialchars($target['target_id']) ?></td>
                            <td>
                                <!-- Display mode for User -->
                                <span class="display-mode"><?= htmlspecialchars($target['username']) ?></span>
                                <!-- Edit mode for User (Dropdown) -->
                                <select name="user_id_<?= htmlspecialchars($target['target_id']) ?>" class="form-select form-select-sm edit-mode">
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= htmlspecialchars($user['user_id']) ?>" <?= ($user['user_id'] == $target['user_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <!-- Display mode for Target Name -->
                                <span class="display-mode"><?= htmlspecialchars($target['target']) ?></span>
                                <!-- Edit mode for Target Name -->
                                <input type="text" name="target_name_<?= htmlspecialchars($target['target_id']) ?>" class="form-control form-control-sm edit-mode" value="<?= htmlspecialchars($target['target']) ?>">
                            </td>
                            <td>
                                <!-- Display mode for Jumlah Target -->
                                <span class="display-mode text-end">Rp<?= number_format($target['jumlah'], 2, ',', '.') ?></span>
                                <!-- Edit mode for Jumlah Target -->
                                <input type="number" name="jumlah_<?= htmlspecialchars($target['target_id']) ?>" class="form-control form-control-sm text-end edit-mode" value="<?= htmlspecialchars($target['jumlah']) ?>" min="0">
                            </td>
                            <td><?= htmlspecialchars(date('d M Y', strtotime($target['created_at']))) ?></td>
                            <td class="text-center">
                                <div class="d-flex flex-column gap-1">
                                    <button type="button" class="btn btn-sm btn-primary w-100 toggle-edit-btn">Edit</button>
                                    <button type="button" class="btn btn-sm btn-danger w-100 delete-btn" onclick="deleteTarget(this)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada target yang tercatat dari pengguna manapun.
                        </td>
                    </tr>
                <?php endif; ?>
              </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-success" id="simpanSemuaBtn">Simpan Perubahan Data</button>
        </div>
    </form>
  </div>

  <?php include_once "footer.php"; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="views/scripts/targetAdmin.js"></script>
</body>
</html>