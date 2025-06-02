<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Struk - Cashie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="views/styles/dashboard.css" rel="stylesheet">
</head>
<body>
    <?php include_once "header.php" ?>

    <main class="container my-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 header-date-row">
            <h2 class="fw-bold text-center text-md-start mb-3 mb-md-0">Arsip Struk</h2>
            <div class="d-flex gap-3 w-100 w-md-auto">
                <input type="text" class="form-control" placeholder="Cari..." id="searchInput">
                <select class="form-select" id="filterSelect">
                    <option>Semua</option>
                    <option>Terbaru</option>
                    <option>Transaksi Tertinggi</option>
                </select>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            <div class="col-md-8">
                <div class="bg-white p-4 rounded shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama File</th>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal Upload</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <?php foreach ($arsip as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['nama_file']) ?></td>
                                    <td>#<?= htmlspecialchars($item['transaksi_id']) ?></td>
                                    <td><?= date('d M Y', strtotime($item['tanggal_upload'])) ?></td>
                                    <td>
                                        <a href="/arsip/edit/<?= $item['arsip_id'] ?>" 
                                            class="btn btn-sm btn-outline-primary me-2">✏ Edit</a>
                                        <form method="POST" 
                                                action="/arsip/delete/<?= $item['arsip_id'] ?>" 
                                                class="d-inline"
                                                onsubmit="return confirm('Hapus arsip ini?')">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">🗑 Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="profile-card mb-3">
                    <h6 class="fw-bold mb-3">📁 Statistik Arsip</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Arsip</span>
                        <!-- <strong><?= count($arsip) ?></strong> -->
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ukuran Total</span>
                        <strong>15.2 MB</strong>
                    </div>
                </div>

                <div class="profile-card">
                    <h6 class="fw-bold mb-3">⬆ Upload Baru</h6>
                    <form method="POST" action="/arsip/store" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="number" 
                                    class="form-control mb-2" 
                                    name="transaksi_id" 
                                    placeholder="ID Transaksi" 
                                    required>
                            <input type="file" 
                                    class="form-control" 
                                    name="file" 
                                    accept=".pdf,.jpg,.png" 
                                    required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Upload Struk</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include_once "footer.php" ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // AJAX Filter
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchValue = this.value;
            fetch(/arsip/filter?search=${encodeURIComponent(searchValue)})
                .then(response => response.json())
                .then(data => updateTable(data));
        });

        function updateTable(data) {
            const tbody = document.querySelector('tbody');
            tbody.innerHTML = data.map(item => `
                <tr>
                    <td>${item.nama_file}</td>
                    <td>#${item.transaksi_id}</td>
                    <td>${new Date(item.tanggal_upload).toLocaleDateString('id-ID')}</td>
                    <td>
                        <a href="/arsip/edit/${item.arsip_id}" 
                            class="btn btn-sm btn-outline-primary me-2">✏ Edit</a>
                        <form method="POST" 
                                action="/arsip/delete/${item.arsip_id}" 
                                class="d-inline"
                                onsubmit="return confirm('Hapus arsip ini?')">
                            <button type="submit" class="btn btn-sm btn-outline-danger">🗑 Hapus</button>
                        </form>
                    </td>
                </tr>
            `).join('');
        }
    </script>
</body>
</html>