<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Provider.php';
require_once '../models/Service.php';
require_once '../models/Booking.php';
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
$user_model = new User($db);
$provider_model = new Provider($db);
$service_model = new Service($db);
$booking_model = new Booking($db);
$category_model = new Category($db);

// Get counts
$total_customers = $user_model->countUsers('customer');
$total_providers = $user_model->countUsers('provider');
$pending_bookings = $booking_model->countByStatus('pending');

// Get categories
$categories = $category_model->readAll();

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                <div>
                    <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-outline text-sm mr-2">
                        <i class="fas fa-users mr-2"></i> Manage Users
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="btn btn-primary text-sm">
                        <i class="fas fa-folder mr-2"></i> Manage Categories
                    </a>
                </div>
            </div>
            
            <?= displayFlashMessage() ?>
            
            <!-- Dashboard Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-700">Total Customers</h3>
                            <p class="text-3xl font-bold text-blue-800 mt-2"><?= $total_customers ?></p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3 text-blue-600 text-xl">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-green-700">Service Providers</h3>
                            <p class="text-3xl font-bold text-green-800 mt-2"><?= $total_providers ?></p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3 text-green-600 text-xl">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-700">Pending Bookings</h3>
                            <p class="text-3xl font-bold text-yellow-800 mt-2"><?= $pending_bookings ?></p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3 text-yellow-600 text-xl">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-purple-700">Service Categories</h3>
                            <p class="text-3xl font-bold text-purple-800 mt-2"><?= $categories->rowCount() ?></p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3 text-purple-600 text-xl">
                            <i class="fas fa-th-list"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Users -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Recent Users</h2>
                    <a href="<?php echo BASE_URL; ?>/admin/users.php" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joined
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php 
                            $recent_users = $user_model->readAll();
                            $count = 0;
                            while ($user = $recent_users->fetch(PDO::FETCH_ASSOC)): 
                                if ($count >= 5) break; // Show only 5 recent users
                                $count++;
                            ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" 
                                                     src="<?= !empty($user['profile_image']) ? '/uploads/profiles/' . $user['profile_image'] : 'https://via.placeholder.com/40' ?>" 
                                                     alt="<?= $user['name'] ?>">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?= $user['name'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= $user['email'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $role_color = 'bg-gray-100 text-gray-800';
                                        
                                        if ($user['role'] === 'provider') {
                                            $role_color = 'bg-green-100 text-green-800';
                                        } elseif ($user['role'] === 'admin') {
                                            $role_color = 'bg-purple-100 text-purple-800';
                                        } elseif ($user['role'] === 'customer') {
                                            $role_color = 'bg-blue-100 text-blue-800';
                                        }
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $role_color ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= formatDate($user['created_at']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo BASE_URL; ?>/admin/users.php?action=view&id=<?= $user['id'] ?>" class="text-primary-600 hover:text-primary-900 mr-3">
                                            View
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/admin/users.php?action=edit&id=<?= $user['id'] ?>" class="text-green-600 hover:text-green-900 mr-3">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Service Categories -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Service Categories</h2>
                    <a href="<?php echo BASE_URL; ?>/admin/categories.php?action=create" class="btn btn-primary text-sm">
                        <i class="fas fa-plus mr-2"></i> Add Category
                    </a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php 
                    while ($category = $categories->fetch(PDO::FETCH_ASSOC)): 
                        $icon = $category['icon'] ?? 'wrench';
                    ?>
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-4">
                                <div class="flex items-center">
                                    <div class="bg-primary-100 text-primary-600 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-<?= $icon ?>"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold"><?= $category['name'] ?></h3>
                                        <p class="text-sm text-gray-500"><?= $category['service_count'] ?> services</p>
                                    </div>
                                </div>
                                <div class="mt-3 flex justify-end space-x-2">
                                    <a href="<?php echo BASE_URL; ?>/admin/categories.php?action=edit&id=<?= $category['id'] ?>" class="text-sm text-primary-600 hover:text-primary-900">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Statistics</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Users:</span>
                            <span class="font-semibold"><?= $total_customers + $total_providers + 1 // +1 for admin ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Customers:</span>
                            <span class="font-semibold"><?= $total_customers ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Service Providers:</span>
                            <span class="font-semibold"><?= $total_providers ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Admins:</span>
                            <span class="font-semibold">1</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Service Categories:</span>
                            <span class="font-semibold"><?= $categories->rowCount() ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Administrative Tools</h3>
                    <div class="space-y-3">
                        <a href="<?php echo BASE_URL; ?>/admin/users.php" class="flex justify-between items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                            <span class="font-medium">User Management</span>
                            <i class="fas fa-arrow-right text-gray-400"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="flex justify-between items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                            <span class="font-medium">Category Management</span>
                            <i class="fas fa-arrow-right text-gray-400"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/admin/services.php" class="flex justify-between items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                            <span class="font-medium">Service Moderation</span>
                            <i class="fas fa-arrow-right text-gray-400"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/admin/bookings.php" class="flex justify-between items-center p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                            <span class="font-medium">Booking Management</span>
                            <i class="fas fa-arrow-right text-gray-400"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>