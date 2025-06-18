<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Arsip Struk - Cashie Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="views/styles/arsip.css" rel="stylesheet"> <link href="views/styles/admin.css" rel="stylesheet"> </head>
<body>
  <?php include_once "header-admin.php" ?> <div class="container py-4" id="main">
    <h2 class="mb-4 text-center text-md-start">Kelola Arsip Struk <small class="text-muted">(Admin)</small></h2>
    <div class="card p-3 shadow-sm">
        <h5 class="mb-3">Daftar Semua Arsip</h5>
        <?php if (isset($arsipList) && is_object($arsipList) && $arsipList->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped mt-3">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ID Arsip</th>
                            <th scope="col">Pengguna</th>
                            <th scope="col">File</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Tanggal Unggah</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; while ($arsip = $arsipList->fetch_object()): ?>
                        <tr>
                            <th scope="row"><?= $i++ ?></th>
                            <td><?= htmlspecialchars($arsip->id) ?></td>
                            <td><?= htmlspecialchars($arsip->username) ?></td> <td><a href="<?= htmlspecialchars($arsip->file_path) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Struk</a></td>
                            <td>
                                <form class="deskripsi-form d-flex align-items-center gap-2" data-id="<?= htmlspecialchars($arsip->id) ?>">
                                    <input type="text" name="description" value="<?= htmlspecialchars($arsip->description) ?>" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-primary" title="Simpan Deskripsi"><i class="fas fa-save"></i></button>
                                </form>
                            </td>
                            <td><?= date("d M Y H:i", strtotime($arsip->created_at)) ?></td>
                            <td>
                                <a href="?c=AdminArsipController&m=delete&id=<?= htmlspecialchars($arsip->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus arsip ini?')" title="Hapus Arsip">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                <p class="mb-0">Belum ada arsip struk yang diunggah.</p>
            </div>
        <?php endif; ?>
    </div>
  </div>

  <?php include_once "footer.php" ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // JavaScript for AJAX update of description
    document.querySelectorAll('.deskripsi-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const description = this.querySelector('[name="description"]').value;
            const formData = new FormData();
            formData.append('id', id);
            formData.append('description', description);
            
            try {
                const response = await fetch('?c=AdminArsipController&m=update', { method: 'POST', body: formData });
                const result = await response.json(); // Expect JSON response
                
                if (response.ok && result.status === 'success') {
                    alert(result.message);
                    // No need to reload, the input value is already updated by the user
                } else {
                    alert('Gagal memperbarui deskripsi: ' + (result.message || 'Terjadi kesalahan tidak dikenal.'));
                    console.error('Gagal memperbarui deskripsi:', result);
                }
            } catch (error) {
                console.error('Error saat fetch:', error);
                alert('Terjadi kesalahan jaringan atau server tidak merespons.');
            }
        });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>