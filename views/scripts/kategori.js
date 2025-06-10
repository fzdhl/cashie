document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const categoriesList = document.getElementById('categoriesList');
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const categoryModal = document.getElementById('categoryModal');
    const deleteModal = document.getElementById('deleteModal');
    const categoryForm = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('modalTitle');
    const categoryIdInput = document.getElementById('categoryId'); // Hidden input for category_id
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

    // Fetch and render categories from the server
    async function fetchAndRenderCategories() {
        try {
            // Memanggil KategoriController::index() untuk mendapatkan data JSON
            const response = await fetch('?c=KategoriController&m=index');
            const data = await response.json(); // Mengurai respons sebagai JSON

            if (data.status === 'success') {
                categoriesList.innerHTML = ''; // Hapus kategori yang ada
                if (data.categories.length === 0) {
                    categoriesList.innerHTML = '<p class="no-categories">No categories found. Add your first category!</p>';
                } else {
                    data.categories.forEach(category => {
                        const categoryItem = document.createElement('div');
                        categoryItem.className = 'category-item';
                        categoryItem.setAttribute('data-id', category.kategori_id); // Menggunakan kategori_id
                        categoryItem.draggable = true; // Make items draggable

                        categoryItem.innerHTML = `
                            <div class="category-item-left">
                                <div class="category-icon">
                                    <i class="fas fa-${category.icon}"></i>
                                </div>
                                <div class="category-info">
                                    <h3>${category.kategori}</h3>
                                    <span>${category.tipe.charAt(0).toUpperCase() + category.tipe.slice(1)}</span>
                                </div>
                            </div>
                            <div class="category-actions">
                                <button class="action-btn edit" data-id="${category.kategori_id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete" data-id="${category.kategori_id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        categoriesList.appendChild(categoryItem);
                    });
                }
                setupDragAndDrop(); // Re-apply drag and drop after rendering
            } else {
                categoriesList.innerHTML = `<p class="text-danger">Error: ${data.message || 'Gagal memuat kategori.'}</p>`;
            }
        } catch (error) {
            console.error('Error fetching categories:', error);
            categoriesList.innerHTML = '<p class="text-danger">Network error or server unavailable.</p>';
        }
    }

    // Render icons in the modal
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

    // Setup event listeners
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

        // Event delegation for edit and delete buttons
        categoriesList.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit');
            const deleteBtn = e.target.closest('.delete');

            if (editBtn) {
                const categoryId = parseInt(editBtn.getAttribute('data-id'));
                openEditCategoryModal(categoryId);
            } else if (deleteBtn) {
                const categoryId = parseInt(deleteBtn.getAttribute('data-id'));
                openDeleteModal(categoryId);
            }
        });
    }

    // Setup drag and drop for categories
    function setupDragAndDrop() {
        const categoryItems = document.querySelectorAll('.category-item');

        categoryItems.forEach(item => {
            item.addEventListener('dragstart', function() {
                this.classList.add('dragging');
            });

            item.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                // You would typically save the new order to your database here
                // For this example, we're not implementing reordering persistence
            });
        });

        categoriesList.addEventListener('dragover', function(e) {
            e.preventDefault();
            const draggingItem = document.querySelector('.dragging');
            const afterElement = getDragAfterElement(this, e.clientY);

            if (afterElement) {
                this.insertBefore(draggingItem, afterElement);
            } else {
                this.appendChild(draggingItem);
            }
        });
    }

    // Helper function for drag and drop
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.category-item:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Open add category modal
    function openAddCategoryModal() {
        currentAction = 'create';
        modalTitle.textContent = 'Add New Category';
        categoryForm.reset();
        selectedIcon = '';
        document.querySelectorAll('.icon-option').forEach(icon => {
            icon.classList.remove('selected');
        });
        document.querySelector('input[name="tipe"][value="pemasukan"]').checked = true; // Menggunakan 'tipe'
        categoryModal.style.display = 'flex';
    }

    // Open edit category modal
    async function openEditCategoryModal(id) {
        currentAction = 'edit';
        currentCategoryId = id;
        modalTitle.textContent = 'Edit Category';

        try {
            const response = await fetch(`?c=KategoriController&m=getCategory&id=${id}`);
            const data = await response.json();

            if (data.status === 'success') {
                const category = data.data;
                categoryIdInput.value = category.kategori_id; // Set hidden ID
                categoryNameInput.value = category.kategori;
                document.querySelector(`input[name="tipe"][value="${category.tipe}"]`).checked = true; // Menggunakan 'tipe'
                selectedIcon = category.icon;

                document.querySelectorAll('.icon-option').forEach(iconEl => {
                    iconEl.classList.remove('selected');
                    if (iconEl.getAttribute('data-icon') === category.icon) {
                        iconEl.classList.add('selected');
                    }
                });
                categoryModal.style.display = 'flex';
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error fetching category for edit:', error);
            alert('Gagal memuat detail kategori.');
        }
    }

    // Handle form submission (Add/Edit)
    async function handleFormSubmit(e) {
        e.preventDefault();

        const kategori = categoryNameInput.value.trim();
        const tipe = document.querySelector('input[name="tipe"]:checked').value; // Menggunakan 'tipe'

        if (!kategori) {
            alert('Please enter a category name.');
            return;
        }

        if (!selectedIcon) {
            alert('Please select an icon.');
            return;
        }

        const formData = new FormData();
        formData.append('kategori', kategori);
        formData.append('tipe', tipe); // Menggunakan 'tipe'
        formData.append('icon', selectedIcon);

        let url = '';
        let method = 'POST';

        if (currentAction === 'create') {
            url = '?c=KategoriController&m=addCategory';
        } else {
            url = '?c=KategoriController&m=updateCategory';
            formData.append('kategori_id', currentCategoryId);
        }

        try {
            const response = await fetch(url, { method: method, body: formData });
            const data = await response.json();

            if (data.status === 'success') {
                alert(data.message);
                closeModal();
                fetchAndRenderCategories(); // Reload categories to show changes
            } else {
                alert('Error: ' + data.message);
            }
        }
        catch (error) {
            console.error('Error submitting form:', error);
            alert('Gagal menyimpan kategori. Terjadi kesalahan jaringan.');
        }
    }

    // Open delete confirmation modal
    function openDeleteModal(id) {
        currentCategoryId = id;
        deleteModal.style.display = 'flex';
    }

    // Close delete modal
    function closeDeleteModal() {
        deleteModal.style.display = 'none';
    }

    // Confirm delete
    async function confirmDelete() {
        const formData = new FormData();
        formData.append('kategori_id', currentCategoryId);

        try {
            const response = await fetch('?c=KategoriController&m=deleteCategory', { method: 'POST', body: formData });
            const data = await response.json();

            if (data.status === 'success') {
                alert(data.message);
                closeDeleteModal();
                fetchAndRenderCategories(); // Reload categories
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            alert('Gagal menghapus kategori. Terjadi kesalahan jaringan.');
        }
    }

    // Close main category modal
    function closeModal() {
        categoryModal.style.display = 'none';
    }

    function init() {
        fetchAndRenderCategories(); // Memuat kategori saat DOM siap
        renderIcons();
        setupEventListeners();
    }
});