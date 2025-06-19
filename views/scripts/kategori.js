// views/scripts/kategori.js

document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const categoriesList = document.getElementById('categoriesList');
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const categoryModal = document.getElementById('categoryModal');
    const deleteModal = document.getElementById('deleteModal');
    const categoryForm = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('modalTitle');
    const categoryIdInput = document.getElementById('categoryId');
    const categoryNameInput = document.getElementById('categoryName');
    const iconsGrid = document.getElementById('iconsGrid');
    const closeBtn = document.querySelector('#categoryModal .close-btn');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Available icons for categories (Font Awesome classes)
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

    // Variables for current action
    let currentAction = 'create'; // 'create' or 'edit'
    let currentCategoryId = null;
    let selectedIcon = '';

    // Initialize the app
    init();

    // Renders available icons in the modal's icon grid
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

    // Sets up all event listeners for buttons and form submissions
    function setupEventListeners() {
        addCategoryBtn.addEventListener('click', openAddCategoryModal);
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

        // Event delegation for edit and delete buttons (karena categoriesList di-render PHP)
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

    // Handles drag and drop functionality for category items (for potential reordering)
    function setupDragAndDrop() {
        const categoryItems = document.querySelectorAll('.category-item');
        categoryItems.forEach(item => {
            item.addEventListener('dragstart', function() { this.classList.add('dragging'); });
            item.addEventListener('dragend', function() { this.classList.remove('dragging'); });
        });
        categoriesList.addEventListener('dragover', function(e) {
            e.preventDefault();
            const draggingItem = document.querySelector('.dragging');
            const afterElement = getDragAfterElement(this, e.clientY);
            if (afterElement) { this.insertBefore(draggingItem, afterElement); }
            else { this.appendChild(draggingItem); }
        });
    }

    // Helper function for drag and drop to determine element position
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.category-item:not(.dragging)')];
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) { return { offset: offset, element: child }; }
            else { return closest; }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Opens the modal for adding a new category
    function openAddCategoryModal() {
        currentAction = 'create';
        modalTitle.textContent = 'Tambah Kategori Baru';
        categoryForm.reset();
        selectedIcon = '';
        document.querySelectorAll('.icon-option').forEach(icon => { icon.classList.remove('selected'); });
        document.querySelector('input[name="tipe"][value="pemasukan"]').checked = true;
        categoryModal.style.display = 'flex';
    }

    // Opens the modal for editing an existing category
    async function openEditCategoryModal(id) {
        currentAction = 'edit';
        currentCategoryId = id;
        modalTitle.textContent = 'Edit Kategori';
        console.log('--- openEditCategoryModal: Membuka untuk ID:', id, '---');
        try {
            const response = await fetch(`?c=KategoriController&m=getCategory&id=${id}`);
            const data = await response.json();

            if (data.status === 'success') {
                const category = data.data;
                categoryIdInput.value = category.kategori_id;
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

    // Handles form submission for both adding and editing categories
    async function handleFormSubmit(e) {
        e.preventDefault();
        console.log('--- handleFormSubmit: Form disubmit ---');

        const kategori = categoryNameInput.value.trim();
        const tipe = document.querySelector('input[name="tipe"]:checked').value;

        if (!kategori) { alert('Silakan masukkan nama kategori.'); return; }
        if (!selectedIcon) { alert('Silakan pilih ikon.'); return; }

        const formData = new FormData();
        formData.append('kategori', kategori);
        formData.append('tipe', tipe);
        formData.append('icon', selectedIcon);
        if (currentAction === 'edit') { formData.append('kategori_id', currentCategoryId); }

        let url = currentAction === 'create' ? '?c=KategoriController&m=addCategory' : '?c=KategoriController&m=updateCategory';

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
                location.reload(); // PENTING: Reload halaman untuk menampilkan data terbaru
            } else {
                alert('Error: ' + data.message);
                console.error('handleFormSubmit Error: Status server tidak sukses:', data.message);
            }
        } catch (error) {
            console.error('handleFormSubmit Fatal Error:', error);
            alert('Gagal menyimpan kategori. Terjadi kesalahan jaringan.');
        }
    }

    // Opens the delete confirmation modal
    function openDeleteModal(id) {
        currentCategoryId = id;
        deleteModal.style.display = 'flex';
    }

    // Closes the delete confirmation modal
    function closeDeleteModal() {
        deleteModal.style.display = 'none';
    }

    // Confirms and executes category deletion
    async function confirmDelete() {
        console.log('--- confirmDelete: Mengkonfirmasi penghapusan ID:', currentCategoryId, '---');
        const formData = new FormData();
        formData.append('kategori_id', currentCategoryId);

        try {
            const response = await fetch('?c=KategoriController&m=deleteCategory', { method: 'POST', body: formData });

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
                location.reload(); // PENTING: Reload halaman untuk menampilkan data terbaru
            } else {
                alert('Error: ' + data.message);
                console.error('confirmDelete Error: Status server tidak sukses:', data.message);
            }
        } catch (error) {
            console.error('confirmDelete Fatal Error:', error);
            alert('Gagal menghapus kategori. Terjadi kesalahan jaringan.');
        }
    }

    // Closes the main category modal
    function closeModal() {
        categoryModal.style.display = 'none';
        // Pastikan juga modal delete tertutup jika entah bagaimana terbuka
        deleteModal.style.display = 'none';
    }

    // Initializes the application on page load
    function init() {
        // --- TAMBAH BARIS INI UNTUK MEMASTIKAN MODAL TERSEMBUNYI SAAT INIT ---
        categoryModal.style.display = 'none'; 
        deleteModal.style.display = 'none';
        // ------------------------------------------------------------------

        renderIcons();
        setupEventListeners();
        setupDragAndDrop(); // Pastikan drag and drop di-setup untuk elemen yang sudah ada
    }
});