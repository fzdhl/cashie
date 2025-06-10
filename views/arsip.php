<?php include_once "header.php" ?>
<div class="container py-4">
    <h2 class="mb-4">Arsip Struk</h2>

    <!-- Form Upload -->
    <div class="card p-3 mb-4">
        <form action="?c=ArsipController&m=upload" method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <input type="file" name="struk" class="form-control" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="description" class="form-control" placeholder="Deskripsi" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success w-100">Upload</button>
            </div>
        </form>
    </div>

    <!-- Daftar Arsip -->
    <div class="card p-3">
        <h5>Daftar Arsip</h5>
        <?php if ($arsipList->num_rows > 0): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr><th>#</th><th>File</th><th>Deskripsi</th><th>Tanggal</th><th>Hapus</th></tr>
                </thead>
                <tbody>
                <?php $i = 1; while ($arsip = $arsipList->fetch_object()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><a href="<?= $arsip->file_path ?>" target="_blank">Lihat</a></td>
                        <td>
                            <form class="deskripsi-form d-flex gap-2" data-id="<?= $arsip->id ?>">
                                <input type="text" name="description" value="<?= htmlspecialchars($arsip->description) ?>" class="form-control">
                                <button type="submit" class="btn btn-sm btn-primary">💾</button>
                            </form>
                        </td>
                        <td><?= date("d M Y", strtotime($arsip->created_at)) ?></td>
                        <td>
                            <a href="?c=ArsipController&m=delete&id=<?= $arsip->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus arsip ini?')">🗑</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Belum ada arsip.</p>
        <?php endif; ?>
    </div>
</div>
<?php include_once "footer.php" ?>
<script>
    document.querySelectorAll('.deskripsi-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const description = this.querySelector('[name=description]').value;
            const formData = new FormData();
            formData.append('id', id);
            formData.append('description', description);
            await fetch('?c=ArsipController&m=update', { method: 'POST', body: formData });
        });
    });
</script>