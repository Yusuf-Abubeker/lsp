<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a customer
requireLogin();
requireRole('customer');

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Booking object
$booking = new Booking($db);

// Get status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Get user's bookings
$bookings = $booking->getByCustomer($_SESSION['user_id'], $status_filter);

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">My Bookings</h1>
                <a href="<?php echo BASE_URL; ?>/services/search.php" class="btn btn-primary text-sm">
                    <i class="fas fa-plus mr-2"></i> Book New Service
                </a>
            </div>

            <?= displayFlashMessage() ?>

            <!-- Status Filters -->
            <div class="mb-6">
                <div class="flex gap-2">
                    <a href="<?php echo BASE_URL; ?>/customer/bookings.php" 
                       class="px-4 py-2 rounded-lg <?= empty($status_filter) ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        All Bookings
                    </a>
                    <a href="<?php echo BASE_URL; ?>/customer/bookings.php?status=pending" 
                       class="px-4 py-2 rounded-lg <?= $status_filter === 'pending' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Pending
                    </a>
                    <a href="<?php echo BASE_URL; ?>/customer/bookings.php?status=confirmed" 
                       class="px-4 py-2 rounded-lg <?= $status_filter === 'confirmed' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Confirmed
                    </a>
                    <a href="<?php echo BASE_URL; ?>/customer/bookings.php?status=completed" 
                       class="px-4 py-2 rounded-lg <?= $status_filter === 'completed' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Completed
                    </a>
                    <a href="<?php echo BASE_URL; ?>/customer/bookings.php?status=cancelled" 
                       class="px-4 py-2 rounded-lg <?= $status_filter === 'cancelled' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        Cancelled
                    </a>
                </div>
            </div>

            <?php if ($bookings->rowCount() > 0): ?>
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
                            <?php while ($booking = $bookings->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?= $booking['service_title'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $booking['category_name'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= $booking['provider_name'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= formatDate($booking['booking_date']) ?></div>
                                        <div class="text-sm text-gray-500"><?= formatTime($booking['start_time']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $status_color = 'bg-blue-100 text-blue-800';
                                        if ($booking['status'] === 'confirmed') {
                                            $status_color = 'bg-green-100 text-green-800';
                                        } elseif ($booking['status'] === 'cancelled') {
                                            $status_color = 'bg-red-100 text-red-800';
                                        } elseif ($booking['status'] === 'completed') {
                                            $status_color = 'bg-purple-100 text-purple-800';
                                        }
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_color ?>">
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        $<?= number_format($booking['total_price'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo BASE_URL; ?>/bookings/view.php?id=<?= $booking['id'] ?>" 
                                           class="text-primary-600 hover:text-primary-900 mr-3">View</a>
                                        
                                        <?php if ($booking['status'] === 'completed'): ?>
                                            <a href="<?php echo BASE_URL; ?>/bookings/review.php?booking_id=<?= $booking['id'] ?>" 
                                               class="text-green-600 hover:text-green-900">Review</a>
                                        <?php elseif ($booking['status'] === 'pending'): ?>
                                            <a href="<?php echo BASE_URL; ?>/bookings/cancel.php?id=<?= $booking['id'] ?>" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                Cancel
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-calendar-alt fa-3x"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                    <p class="text-gray-600 mb-6">You haven't made any bookings yet.</p>
                    <a href="<?php echo BASE_URL; ?>/services/search.php" class="btn btn-primary">
                        Book a Service
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>