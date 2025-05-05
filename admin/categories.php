<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Category.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
requireLogin();
requireRole('admin');

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Category object
$category = new Category($db);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $category->name = sanitizeInput($_POST['name']);
        $category->description = sanitizeInput($_POST['description']);
        $category->icon = sanitizeInput($_POST['icon']);
        
        if ($category->create()) {
            setFlashMessage('success', 'Category created successfully');
        } else {
            setFlashMessage('error', 'Error creating category');
        }
    } elseif ($action === 'update' && isset($_POST['id'])) {
        $category->id = $_POST['id'];
        $category->name = sanitizeInput($_POST['name']);
        $category->description = sanitizeInput($_POST['description']);
        $category->icon = sanitizeInput($_POST['icon']);
        
        if ($category->update()) {
            setFlashMessage('success', 'Category updated successfully');
        } else {
            setFlashMessage('error', 'Error updating category');
        }
    }
    
    redirect('/admin/categories.php');
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $category->id = $_GET['id'];
    if ($category->delete()) {
        setFlashMessage('success', 'Category deleted successfully');
    } else {
        setFlashMessage('error', 'Error deleting category');
    }
    redirect('/admin/categories.php');
}

// Get all categories
$categories = $category->readAll();

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Manage Categories</h1>
                <div class="flex gap-2">
                    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-outline text-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                    <button type="button" 
                            class="btn btn-primary text-sm"
                            onclick="document.getElementById('createCategoryModal').classList.remove('hidden')">
                        <i class="fas fa-plus mr-2"></i> Add Category
                    </button>
                </div>
            </div>

            <?= displayFlashMessage() ?>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php while ($row = $categories->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-primary-100 text-primary-600 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-<?= $row['icon'] ?? 'folder' ?> text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold"><?= $row['name'] ?></h3>
                                    <p class="text-sm text-gray-500"><?= $row['service_count'] ?> services</p>
                                </div>
                            </div>
                            
                            <?php if (!empty($row['description'])): ?>
                                <p class="text-gray-600 text-sm mb-4"><?= $row['description'] ?></p>
                            <?php endif; ?>
                            
                            <div class="flex justify-end space-x-2">
                                <button type="button" 
                                        class="text-primary-600 hover:text-primary-700"
                                        onclick="editCategory(<?= htmlspecialchars(json_encode($row)) ?>)">
                                    Edit
                                </button>
                                <a href="<?php echo BASE_URL; ?>/admin/categories.php?action=delete&id=<?= $row['id'] ?>" 
                                   class="text-red-600 hover:text-red-700"
                                   onclick="return confirm('Are you sure you want to delete this category?')">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div id="createCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Add New Category</h2>
                    <button type="button" 
                            class="text-gray-400 hover:text-gray-500"
                            onclick="document.getElementById('createCategoryModal').classList.add('hidden')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-4">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" name="name" id="name" class="form-input" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-input"></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label for="icon" class="form-label">Icon (FontAwesome class name)</label>
                        <input type="text" name="icon" id="icon" class="form-input" placeholder="e.g., wrench">
                    </div>
                    
                    <div class="flex justify-end gap-2">
                        <button type="button" 
                                class="btn btn-outline"
                                onclick="document.getElementById('createCategoryModal').classList.add('hidden')">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Edit Category</h2>
                    <button type="button" 
                            class="text-gray-400 hover:text-gray-500"
                            onclick="document.getElementById('editCategoryModal').classList.add('hidden')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-4">
                        <label for="edit_name" class="form-label">Category Name</label>
                        <input type="text" name="name" id="edit_name" class="form-input" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" rows="3" class="form-input"></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label for="edit_icon" class="form-label">Icon (FontAwesome class name)</label>
                        <input type="text" name="icon" id="edit_icon" class="form-input" placeholder="e.g., wrench">
                    </div>
                    
                    <div class="flex justify-end gap-2">
                        <button type="button" 
                                class="btn btn-outline"
                                onclick="document.getElementById('editCategoryModal').classList.add('hidden')">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editCategory(category) {
    document.getElementById('edit_id').value = category.id;
    document.getElementById('edit_name').value = category.name;
    document.getElementById('edit_description').value = category.description;
    document.getElementById('edit_icon').value = category.icon;
    document.getElementById('editCategoryModal').classList.remove('hidden');
}
</script>

<?php include '../includes/footer.php'; ?>