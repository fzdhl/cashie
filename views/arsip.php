<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arsip Struk - Cashie</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="views/styles/arsip.css" rel="stylesheet">
</head>
<body>
  <?php include_once "header.php" ?>

  <div class="container py-4" id="main">
    <h2 class="mb-4 text-center text-md-start">Arsip Struk</h2>

    <div class="card p-3 mb-4 shadow-sm">
        <h5 class="mb-3">Upload Struk Baru</h5>
        <form action="?c=ArsipController&m=upload" method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label for="strukFile" class="form-label">Pilih File Struk (JPG, PNG, PDF)</label>
                <input type="file" name="struk" id="strukFile" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
            <div class="col-md-6">
                <label for="descriptionInput" class="form-label">Deskripsi Struk</label>
                <input type="text" name="description" id="descriptionInput" class="form-control" placeholder="Contoh: Struk belanja bulanan" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success w-100">Upload Struk</button>
            </div>
        </form>
    </div>

    <div class="card p-3 shadow-sm">
        <h5 class="mb-3">Daftar Arsip Anda</h5>
        <?php if (isset($arsipList) && is_object($arsipList) && $arsipList->num_rows > 0): ?> <div class="table-responsive">
                <table class="table table-hover table-striped mt-3">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">File</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; while ($arsip = $arsipList->fetch_object()): ?>
                        <tr>
                            <th scope="row"><?= $i++ ?></th>
                            <td><a href="<?= htmlspecialchars($arsip->file_path) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Struk</a></td>
                            <td>
                                <form class="deskripsi-form d-flex align-items-center gap-2" data-id="<?= $arsip->id ?>">
                                    <input type="text" name="description" value="<?= htmlspecialchars($arsip->description) ?>" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-primary" title="Simpan Deskripsi"><i class="fas fa-save"></i></button>
                                </form>
                            </td>
                            <td><?= date("d M Y H:i", strtotime($arsip->created_at)) ?></td>
                            <td>
                                <a href="?c=ArsipController&m=delete&id=<?= $arsip->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus arsip ini?')" title="Hapus Arsip">
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
                <p>Mulai unggah struk Anda untuk menyimpan catatan pengeluaran!</p>
            </div>
        <?php endif; ?>
    </div>
  </div>

  <?php include_once "footer.php" ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.deskripsi-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const description = this.querySelector('[name=description]').value;
            const formData = new FormData();
            formData.append('id', id);
            formData.append('description', description);
            
            try {
                const response = await fetch('?c=ArsipController&m=update', { method: 'POST', body: formData });
                if (response.ok) {
                    console.log('Deskripsi berhasil diperbarui.');
                } else {
                    const errorText = await response.text();
                    console.error('Gagal memperbarui deskripsi:', errorText);
                    alert('Gagal memperbarui deskripsi: ' + errorText);
                }
            } catch (error) {
                console.error('Error saat fetch:', error);
                alert('Terjadi kesalahan jaringan atau server tidak merespons.');
            }
        });
    });
  </script>
</body>
</html>