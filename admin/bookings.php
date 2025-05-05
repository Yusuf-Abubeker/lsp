<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Booking.php';
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

// Initialize Booking object
$booking = new Booking($db);

// Handle status updates
if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $booking->readOne($_GET['id']);
    if ($booking->updateStatus($_GET['status'])) {
        setFlashMessage('success', 'Booking status updated successfully');
    } else {
        setFlashMessage('error', 'Error updating booking status');
    }
    redirect('/admin/bookings.php');
}

// Get status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Get all bookings
$bookings = $booking->getAllBookings($status_filter);

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Manage Bookings</h1>
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-outline text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>

            <?= displayFlashMessage() ?>

            <!-- Status Filters -->
            <div class="mb-6">
                <div class="flex gap-2">
                    <a href="<?php echo BASE_URL; ?>/admin/bookings.php" 
                       class="px-4 py-2 rounded-lg <?= empty($status_filter) ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        All Bookings
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/bookings.php?status=pending" 
                       class="px-4 py-2 rounded-lg <?= $status_filter === 'pending' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Pending
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/bookings.php?status=confirmed" 
                       class="px-4 py-2 rounded-lg <?= $status_filter === 'confirmed' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Confirmed
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/bookings.php?status=completed" 
                       class="px-4 py-2 rounded-lg <?= $status_filter === 'completed' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Completed
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/bookings.php?status=cancelled" 
                       class="px-4 py-2 rounded-lg <?= $status_filter === 'cancelled' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Cancelled
                    </a>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Provider
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $bookings->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $row['service_title'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $row['category_name'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['customer_name'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['provider_name'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= formatDate($row['booking_date']) ?></div>
                                    <div class="text-sm text-gray-500"><?= formatTime($row['start_time']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_color = 'bg-blue-100 text-blue-800';
                                    if ($row['status'] === 'confirmed') {
                                        $status_color = 'bg-green-100 text-green-800';
                                    } elseif ($row['status'] === 'cancelled') {
                                        $status_color = 'bg-red-100 text-red-800';
                                    } elseif ($row['status'] === 'completed') {
                                        $status_color = 'bg-purple-100 text-purple-800';
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_color ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    $<?= number_format($row['total_price'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo BASE_URL; ?>/bookings/view.php?id=<?= $row['id'] ?>" 
                                       class="text-primary-600 hover:text-primary-900 mr-3">View</a>
                                    
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/bookings.php?action=update_status&id=<?= $row['id'] ?>&status=confirmed" 
                                           class="text-green-600 hover:text-green-900 mr-3">Confirm</a>
                                        <a href="<?php echo BASE_URL; ?>/admin/bookings.php?action=update_status&id=<?= $row['id'] ?>&status=cancelled" 
                                           class="text-red-600 hover:text-red-900">Cancel</a>
                                    <?php elseif ($row['status'] === 'confirmed'): ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/bookings.php?action=update_status&id=<?= $row['id'] ?>&status=completed" 
                                           class="text-green-600 hover:text-green-900 mr-3">Complete</a>
                                        <a href="<?php echo BASE_URL; ?>/admin/bookings.php?action=update_status&id=<?= $row['id'] ?>&status=cancelled" 
                                           class="text-red-600 hover:text-red-900">Cancel</a>
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
