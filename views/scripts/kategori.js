document.addEventListener('DOMContentLoaded', function() {
    // Sample data for categories
    let categories = [
        { id: 1, name: 'Salary', type: 'income', icon: 'money-bill-wave' },
        { id: 2, name: 'Freelance', type: 'income', icon: 'laptop-code' },
        { id: 3, name: 'Groceries', type: 'expense', icon: 'shopping-basket' },
        { id: 4, name: 'Transport', type: 'expense', icon: 'bus' },
        { id: 5, name: 'Entertainment', type: 'expense', icon: 'film' }
    ];

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
    const closeBtn = document.querySelector('.close-btn');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Available icons for categories
    const availableIcons = [
        'money-bill-wave', 'shopping-cart', 'home', 'utensils', 'bus', 'car',
        'film', 'music', 'laptop-code', 'shopping-basket', 'tshirt', 'book',
        'graduation-cap', 'heartbeat', 'plane', 'gift', 'coins', 'piggy-bank',
        'wallet', 'credit-card', 'chart-line', 'university', 'hand-holding-usd'
    ];

    // Variables for current action
    let currentAction = 'create';
    let currentCategoryId = null;
    let selectedIcon = '';

    // Initialize the app
    function init() {
        renderCategories();
        renderIcons();
        setupEventListeners();
    }

    // Render categories list
    function renderCategories() {
        categoriesList.innerHTML = '';
        
        if (categories.length === 0) {
            categoriesList.innerHTML = '<p class="no-categories">No categories found. Add your first category!</p>';
            return;
        }
        
        categories.forEach(category => {
            const categoryItem = document.createElement('div');
            categoryItem.className = 'category-item';
            categoryItem.setAttribute('data-id', category.id);
            categoryItem.draggable = true;
            
            categoryItem.innerHTML = `
                <div class="category-item-left">
                    <div class="category-icon">
                        <i class="fas fa-${category.icon}"></i>
                    </div>
                    <div class="category-info">
                        <h3>${category.name}</h3>
                        <span>${category.type === 'income' ? 'Income' : 'Expense'}</span>
                    </div>
                </div>
                <div class="category-actions">
                    <button class="action-btn edit" data-id="${category.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" data-id="${category.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            categoriesList.appendChild(categoryItem);
        });
        
        // Add drag and drop event listeners
        setupDragAndDrop();
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
                // Remove selected class from all icons
                document.querySelectorAll('.icon-option').forEach(iconEl => {
                    iconEl.classList.remove('selected');
                });
                
                // Add selected class to clicked icon
                this.classList.add('selected');
                selectedIcon = icon;
            });
            
            iconsGrid.appendChild(iconOption);
        });
    }

    // Setup event listeners
    function setupEventListeners() {
        // Add category button
        addCategoryBtn.addEventListener('click', openAddCategoryModal);
        
        // Close modal buttons
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        cancelDeleteBtn.addEventListener('click', closeDeleteModal);
        
        // Form submission
        categoryForm.addEventListener('submit', handleFormSubmit);
        
        // Delete confirmation
        confirmDeleteBtn.addEventListener('click', confirmDelete);
        
        // Close modals when clicking outside
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
            if (e.target.closest('.edit')) {
                const categoryId = parseInt(e.target.closest('.edit').getAttribute('data-id'));
                openEditCategoryModal(categoryId);
            }
            
            if (e.target.closest('.delete')) {
                const categoryId = parseInt(e.target.closest('.delete').getAttribute('data-id'));
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
                // Here you would typically save the new order to your database
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
        document.querySelector('input[name="categoryType"][value="income"]').checked = true;
        categoryModal.style.display = 'flex';
    }

    // Open edit category modal
    function openEditCategoryModal(id) {
        currentAction = 'edit';
        currentCategoryId = id;
        modalTitle.textContent = 'Edit Category';
        
        const category = categories.find(cat => cat.id === id);
        if (category) {
            categoryNameInput.value = category.name;
            document.querySelector(`input[name="categoryType"][value="${category.type}"]`).checked = true;
            selectedIcon = category.icon;
            
            // Select the icon in the grid
            document.querySelectorAll('.icon-option').forEach(icon => {
                icon.classList.remove('selected');
                if (icon.getAttribute('data-icon') === category.icon) {
                    icon.classList.add('selected');
                }
            });
            
            categoryModal.style.display = 'flex';
        }
    }

    // Handle form submission
    function handleFormSubmit(e) {
        e.preventDefault();
        
        const name = categoryNameInput.value.trim();
        const type = document.querySelector('input[name="categoryType"]:checked').value;
        
        if (!name) {
            alert('Please enter a category name');
            return;
        }
        
        if (!selectedIcon) {
            alert('Please select an icon');
            return;
        }
        
        if (currentAction === 'create') {
            // Create new category
            const newId = categories.length > 0 ? Math.max(...categories.map(cat => cat.id)) + 1 : 1;
            const newCategory = {
                id: newId,
                name,
                type,
                icon: selectedIcon
            };
            categories.push(newCategory);
        } else {
            // Update existing category
            const index = categories.findIndex(cat => cat.id === currentCategoryId);
            if (index !== -1) {
                categories[index] = {
                    ...categories[index],
                    name,
                    type,
                    icon: selectedIcon
                };
            }
        }
        
        renderCategories();
        closeModal();
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
    function confirmDelete() {
        categories = categories.filter(cat => cat.id !== currentCategoryId);
        renderCategories();
        closeDeleteModal();
    }

    // Close modal
    function closeModal() {
        categoryModal.style.display = 'none';
    }

    // Initialize the app
    init();
});