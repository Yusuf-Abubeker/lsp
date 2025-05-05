<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/User.php';
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

// Initialize User object
$user = new User($db);

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'delete' && $user_id > 0) {
    // Prevent deleting own account
    if ($user_id === $_SESSION['user_id']) {
        setFlashMessage('error', 'You cannot delete your own account');
    } else {
        if ($user->delete($user_id)) {
            setFlashMessage('success', 'User deleted successfully');
        } else {
            setFlashMessage('error', 'Error deleting user');
        }
    }
    redirect('/admin/users.php');
}

// Get users list
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$users = $user->readAll($role_filter);

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Manage Users</h1>
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-outline text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>

            <?= displayFlashMessage() ?>

            <!-- Filters -->
            <div class="mb-6">
                <div class="flex gap-2">
                    <a href="<?php echo BASE_URL; ?>/admin/users.php" 
                       class="px-4 py-2 rounded-lg <?= empty($role_filter) ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        All Users
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/users.php?role=customer" 
                       class="px-4 py-2 rounded-lg <?= $role_filter === 'customer' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Customers
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/users.php?role=provider" 
                       class="px-4 py-2 rounded-lg <?= $role_filter === 'provider' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Providers
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/users.php?role=admin" 
                       class="px-4 py-2 rounded-lg <?= $role_filter === 'admin' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Admins
                    </a>
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
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
                        <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" 
                                                 src="<?= !empty($row['profile_image']) ? '/uploads/profiles/' . $row['profile_image'] : 'https://via.placeholder.com/40' ?>" 
                                                 alt="<?= $row['name'] ?>">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?= $row['name'] ?></div>
                                            <?php if (!empty($row['phone'])): ?>
                                                <div class="text-sm text-gray-500"><?= $row['phone'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['email'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $role_color = 'bg-gray-100 text-gray-800';
                                    if ($row['role'] === 'provider') {
                                        $role_color = 'bg-green-100 text-green-800';
                                    } elseif ($row['role'] === 'admin') {
                                        $role_color = 'bg-purple-100 text-purple-800';
                                    } elseif ($row['role'] === 'customer') {
                                        $role_color = 'bg-blue-100 text-blue-800';
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $role_color ?>">
                                        <?= ucfirst($row['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDate($row['created_at']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo BASE_URL; ?>/admin/users.php?action=view&id=<?= $row['id'] ?>" 
                                       class="text-primary-600 hover:text-primary-900 mr-3">View</a>
                                    
                                    <?php if ($row['id'] !== $_SESSION['user_id']): ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/users.php?action=edit&id=<?= $row['id'] ?>" 
                                           class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <a href="<?php echo BASE_URL; ?>/admin/users.php?action=delete&id=<?= $row['id'] ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                    <?php endif; ?>
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