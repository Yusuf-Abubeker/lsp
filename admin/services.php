<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Service.php';
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

// Initialize objects
$service = new Service($db);
$category = new Category($db);

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'delete' && $service_id > 0) {
    if ($service->delete()) {
        setFlashMessage('success', 'Service deleted successfully');
    } else {
        setFlashMessage('error', 'Error deleting service');
    }
    redirect('/admin/services.php');
}

// Get all categories for filter
$categories = $category->readAll();

// Get services with filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_params = [
    'category_id' => $category_filter,
    'limit' => 50
];
$services = $service->search($search_params);

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Manage Services</h1>
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-outline text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>

            <?= displayFlashMessage() ?>

            <!-- Filters -->
            <div class="mb-6">
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="GET" class="flex gap-4">
                    <div class="flex-grow">
                        <select name="category" class="form-input" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php 
                            // Reset pointer to beginning
                            $categories->execute();
                            while ($cat = $categories->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                                    <?= $cat['name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Services Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Provider
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rating
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $services->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $row['title'] ?></div>
                                    <div class="text-sm text-gray-500">Created <?= formatDate($row['created_at']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['provider_name'] ?></div>
                                    <?php if (!empty($row['city']) && !empty($row['state'])): ?>
                                        <div class="text-sm text-gray-500"><?= $row['city'] . ', ' . $row['state'] ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= $row['category_name'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php 
                                    $priceDisplay = '$' . number_format($row['price'], 2);
                                    if ($row['price_type'] === 'hourly') {
                                        $priceDisplay .= '/hr';
                                    } elseif ($row['price_type'] === 'starting_at') {
                                        $priceDisplay = 'From ' . $priceDisplay;
                                    }
                                    echo $priceDisplay;
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-yellow-400 flex">
                                            <?= generateStarRating($row['avg_rating'] ?? 0) ?>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">
                                            (<?= $row['total_reviews'] ?? 0 ?>)
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo BASE_URL; ?>/services/view.php?id=<?= $row['id'] ?>" 
                                       class="text-primary-600 hover:text-primary-900 mr-3">View</a>
                                    <a href="<?php echo BASE_URL; ?>/admin/services.php?action=delete&id=<?= $row['id'] ?>" 
                                       class="text-red-600 hover:text-red-900"
                                       onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
```