<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori Transaksi - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="views/styles/kategori.css">
    <link rel="stylesheet" href="views/styles/admin.css"> </head>
<body>
    <?php include_once "header.php" ?>

    <main class="container main-content">
        <div class="categories-header">
            <h2 class="fw-bold text-center text-md-start mb-3 mb-md-0">Kelola Kategori Transaksi <small class="text-muted">(Admin)</small></h2>
            </div>

        <div class="categories-list" id="categoriesList">
            <?php if (empty($categories)): ?>
                <p class="no-categories">Belum ada kategori yang ditambahkan.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID Pengguna</th> <th>Pengguna</th>
                                <th>ID Kategori</th>
                                <th>Kategori</th>
                                <th>Tipe</th>
                                <th>Ikon</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr data-id="<?= htmlspecialchars($category['kategori_id']) ?>">
                                    <td><?= htmlspecialchars($category['user_id']) ?></td> <td><?= htmlspecialchars($category['username']) ?></td>
                                    <td><?= htmlspecialchars($category['kategori_id']) ?></td>
                                    <td><?= htmlspecialchars($category['kategori']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($category['tipe'])) ?></td>
                                    <td><i class="fas fa-<?= htmlspecialchars($category['icon']) ?>"></i></td>
                                    <td class="text-center">
                                        <button class="action-btn edit" data-id="<?= htmlspecialchars($category['kategori_id']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete" data-id="<?= htmlspecialchars($category['kategori_id']) ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="modal" id="categoryModal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h3 id="modalTitle">Edit Kategori</h3> <form id="categoryForm">
                    <input type="hidden" id="categoryId" name="kategori_id">
                    <div class="form-group">
                        <label for="userId">ID Pengguna</label>
                        <input type="number" id="userId" name="user_id" required>
                    </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriesList = document.getElementById('categoriesList');
            // const addCategoryBtn = document.getElementById('addCategoryBtn'); // Dihapus atau dikomentari
            const categoryModal = document.getElementById('categoryModal');
            const deleteModal = document.getElementById('deleteModal');
            const categoryForm = document.getElementById('categoryForm');
            const modalTitle = document.getElementById('modalTitle');
            const categoryIdInput = document.getElementById('categoryId');
            const userIdInput = document.getElementById('userId'); 
            const categoryNameInput = document.getElementById('categoryName');
            const iconsGrid = document.getElementById('iconsGrid');
            const closeBtn = document.querySelector('#categoryModal .close-btn');
            const cancelBtn = document.getElementById('cancelBtn');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            const availableIcons = [
                'money-bill-wave', 'shopping-cart', 'home', 'utensils', 'bus', 'car',
                'film', 'music', 'laptop-code', 'shopping-basket', 'tshirt', 'book',
                'graduation-cap', 'heartbeat', 'plane', 'gift', 'coins', 'piggy-bank',
                'wallet', 'credit-card', 'chart-line', 'university', 'hand-holding-usd',
                'bell', 'bolt', 'camera', 'cloud', 'coffee', 'cogs', 'cut', 'envelope',
                'fire', 'flask', 'gamepad', 'gavel', 'gem', 'globe', 'handshake', 'headphones',
                'lightbulb', 'map', 'moon', 'palette', 'pizza-slice', 'robot', 'rocket', 'shield-alt',
                'smile', 'snowflake', 'star', 'tree', 'trophy', 'umbrella', 'wine-glass', 'wrench'
            ];

            let currentAction = 'edit'; // Default action is now 'edit'
            let currentCategoryId = null;
            let selectedIcon = '';

            init();

            function renderIcons() {
                iconsGrid.innerHTML = '';
                availableIcons.forEach(icon => {
                    const iconOption = document.createElement('div');
                    iconOption.className = 'icon-option';
                    iconOption.innerHTML = `<i class="fas fa-${icon}"></i>`;
                    iconOption.setAttribute('data-icon', icon);

                    iconOption.addEventListener('click', function() {
                        document.querySelectorAll('.icon-option').forEach(iconEl => {
                            iconEl.classList.remove('selected');
                        });
                        this.classList.add('selected');
                        selectedIcon = icon;
                    });
                    iconsGrid.appendChild(iconOption);
                });
            }

            function setupEventListeners() {
                // addCategoryBtn.addEventListener('click', openAddCategoryModal); // Dihapus atau dikomentari
                closeBtn.addEventListener('click', closeModal);
                cancelBtn.addEventListener('click', closeModal);
                cancelDeleteBtn.addEventListener('click', closeDeleteModal);
                categoryForm.addEventListener('submit', handleFormSubmit);
                confirmDeleteBtn.addEventListener('click', confirmDelete);

                window.addEventListener('click', function(event) {
                    if (event.target === categoryModal) {
                        closeModal();
                    }
                    if (event.target === deleteModal) {
                        closeDeleteModal();
                    }
                });

                // Event delegation for edit and delete buttons
                categoriesList.addEventListener('click', function(e) {
                    const editBtn = e.target.closest('.edit');
                    const deleteBtn = e.target.closest('.delete');

                    if (editBtn) {
                        const categoryId = parseInt(editBtn.getAttribute('data-id'));
                        openEditCategoryModal(categoryId);
                    }
                    if (deleteBtn) {
                        const categoryId = parseInt(deleteBtn.getAttribute('data-id'));
                        openDeleteModal(categoryId);
                    }
                });
            }

            async function openEditCategoryModal(id) {
                currentAction = 'edit';
                currentCategoryId = id;
                modalTitle.textContent = 'Edit Kategori';
                console.log('--- openEditCategoryModal: Membuka untuk ID:', id, '---');
                try {
                    const response = await fetch(`?c=AdminKategoriController&m=getCategory&id=${id}`);
                    const data = await response.json();

                    if (data.status === 'success') {
                        const category = data.data;
                        categoryIdInput.value = category.kategori_id;
                        userIdInput.value = category.user_id; 
                        categoryNameInput.value = category.kategori;
                        document.querySelector(`input[name="tipe"][value="${category.tipe}"]`).checked = true;
                        selectedIcon = category.icon;

                        document.querySelectorAll('.icon-option').forEach(iconEl => {
                            iconEl.classList.remove('selected');
                            if (iconEl.getAttribute('data-icon') === category.icon) {
                                iconEl.classList.add('selected');
                            }
                        });
                        categoryModal.style.display = 'flex';
                        console.log('openEditCategoryModal: Data kategori dimuat:', category);
                    } else {
                        alert('Error: ' + data.message);
                        console.error('openEditCategoryModal Error:', data.message);
                    }
                } catch (error) {
                    console.error('openEditCategoryModal Fatal Error:', error);
                    alert('Gagal memuat detail kategori. Periksa konsol browser.');
                }
            }

            async function handleFormSubmit(e) {
                e.preventDefault();
                console.log('--- handleFormSubmit: Form disubmit ---');

                const kategori = categoryNameInput.value.trim();
                const tipe = document.querySelector('input[name="tipe"]:checked').value;
                const userId = userIdInput.value; 

                if (!userId) { alert('Silakan masukkan ID Pengguna.'); return; }
                if (!kategori) { alert('Silakan masukkan nama kategori.'); return; }
                if (!selectedIcon) { alert('Silakan pilih ikon.'); return; }

                const formData = new FormData();
                formData.append('user_id', userId); 
                formData.append('kategori', kategori);
                formData.append('tipe', tipe);
                formData.append('icon', selectedIcon);
                // currentAction will always be 'edit' now
                formData.append('kategori_id', currentCategoryId); 

                let url = '?c=AdminKategoriController&m=updateCategory'; // Hanya ada fungsi update

                try {
                    console.log('handleFormSubmit: Mengirim data ke:', url, 'Data:', Object.fromEntries(formData));
                    const response = await fetch(url, { method: 'POST', body: formData });
                    
                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        const textResponse = await response.text();
                        console.error('handleFormSubmit Error: Server tidak mengembalikan JSON. Respons:', textResponse);
                        alert('Server tidak mengembalikan JSON. Periksa log server untuk error saat pengiriman form.');
                        return;
                    }

                    const data = await response.json();
                    console.log('handleFormSubmit: Respons diterima:', data);

                    if (data.status === 'success') {
                        alert(data.message);
                        closeModal();
                        location.reload(); 
                    } else {
                        alert('Error: ' + data.message);
                        console.error('handleFormSubmit Error: Status server tidak sukses:', data.message);
                    }
                } catch (error) {
                    console.error('handleFormSubmit Fatal Error:', error);
                    alert('Gagal menyimpan kategori. Terjadi kesalahan jaringan.');
                }
            }

            function openDeleteModal(id) {
                currentCategoryId = id;
                deleteModal.style.display = 'flex';
            }

            function closeDeleteModal() {
                deleteModal.style.display = 'none';
            }

            async function confirmDelete() {
                console.log('--- confirmDelete: Mengkonfirmasi penghapusan ID:', currentCategoryId, '---');
                const formData = new FormData();
                formData.append('kategori_id', currentCategoryId);

                try {
                    const response = await fetch('?c=AdminKategoriController&m=deleteCategory', { method: 'POST', body: formData });

                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        const textResponse = await response.text();
                        console.error('confirmDelete Error: Server tidak mengembalikan JSON. Respons:', textResponse);
                        alert('Server tidak mengembalikan JSON. Periksa log server untuk error saat penghapusan.');
                        return;
                    }

                    const data = await response.json();
                    console.log('confirmDelete: Respons diterima:', data);

                    if (data.status === 'success') {
                        alert(data.message);
                        closeDeleteModal();
                        location.reload(); 
                    } else {
                        alert('Error: ' + data.message);
                        console.error('confirmDelete Error: Status server tidak sukses:', data.message);
                    }
                } catch (error) {
                    console.error('confirmDelete Fatal Error:', error);
                    alert('Gagal menghapus kategori. Terjadi kesalahan jaringan.');
                }
            }

            function closeModal() {
                categoryModal.style.display = 'none';
            }

            function init() {
                renderIcons();
                setupEventListeners();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>