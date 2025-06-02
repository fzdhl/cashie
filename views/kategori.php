<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Categories</title>
    <link rel="stylesheet" href="views/styles/kategori.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <!-- Header Template -->
    <?php include_once "header.php" ?>

    <main class="container main-content">
        <div class="categories-header">
            <h2>Transaction Categories</h2>
            <button id="addCategoryBtn" class="btn-primary">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>

        <!-- Category List -->
        <div class="categories-list" id="categoriesList">
            <!-- Categories will be loaded here by JavaScript -->
        </div>

        <!-- Add/Edit Category Modal -->
        <div class="modal" id="categoryModal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h3 id="modalTitle">Add New Category</h3>
                <form id="categoryForm">
                    <input type="hidden" id="categoryId">
                    <div class="form-group">
                        <label for="categoryName">Category Name</label>
                        <input type="text" id="categoryName" required>
                    </div>
                    <div class="form-group">
                        <label>Category Type</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="categoryType" value="income" checked>
                                <span>Income</span>
                            </label>
                            <label>
                                <input type="radio" name="categoryType" value="expense">
                                <span>Expense</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Select Icon</label>
                        <div class="icons-grid" id="iconsGrid">
                            <!-- Icons will be loaded here by JavaScript -->
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
                        <button type="submit" class="btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal" id="deleteModal">
            <div class="modal-content small">
                <h3>Delete Category</h3>
                <p>Are you sure you want to delete this category? This action cannot be undone.</p>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelDeleteBtn">Cancel</button>
                    <button type="button" class="btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Template -->
    <?php include_once "footer.php" ?>

    <script src="views/scripts/kategori.js"></script>
</body>
</html>