<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Transaksi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="views/styles/kategori.css">
</head>
<body>
    <?php include_once "header.php" ?>

    <main class="container main-content">
        <div class="categories-header">
            <h2 class="fw-bold text-center text-md-start mb-3 mb-md-0">Kategori Transaksi</h2>
            <button id="addCategoryBtn" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kategori
            </button>
        </div>

        <div class="categories-list" id="categoriesList">
            <?php if (empty($categories)): // $categories berasal dari KategoriController::index() ?>
                <p class="no-categories">Belum ada kategori. Tambahkan kategori pertama Anda!</p>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <div class="category-item" data-id="<?= htmlspecialchars($category['kategori_id']) ?>" draggable="true">
                        <div class="category-item-left">
                            <div class="category-icon">
                                <i class="fas fa-<?= htmlspecialchars($category['icon']) ?>"></i>
                            </div>
                            <div class="category-info">
                                <h3><?= htmlspecialchars($category['kategori']) ?></h3>
                                <span><?= htmlspecialchars(ucfirst($category['tipe'])) ?></span>
                            </div>
                        </div>
                        <div class="category-actions">
                            <button class="action-btn edit" data-id="<?= htmlspecialchars($category['kategori_id']) ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" data-id="<?= htmlspecialchars($category['kategori_id']) ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="modal" id="categoryModal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h3 id="modalTitle">Tambah Kategori Baru</h3>
                <form id="categoryForm">
                    <input type="hidden" id="categoryId" name="kategori_id">
                    <div class="form-group">
                        <label for="categoryName">Nama Kategori</label>
                        <input type="text" id="categoryName" name="kategori" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Kategori</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="tipe" value="pemasukan" checked>
                                <span>Pemasukan</span>
                            </label>
                            <label>
                                <input type="radio" name="tipe" value="pengeluaran">
                                <span>Pengeluaran</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Pilih Ikon</label>
                        <div class="icons-grid" id="iconsGrid">
                            </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelBtn">Batalkan</button>
                        <button type="submit" class="btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="deleteModal">
            <div class="modal-content small">
                <h3>Hapus Kategori</h3>
                <p>Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelDeleteBtn">Batalkan</button>
                    <button type="button" class="btn-danger" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>
    </main>

    <?php include_once "footer.php" ?>

    <script src="views/scripts/kategori.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>