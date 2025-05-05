<?php
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../models/User.php';
require_once '../utils/helpers.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auth check
requireLogin();
requireRole('customer');

// DB & models
$db = (new Database())->getConnection();
$booking = new Booking($db);
$user = new User($db);
$user->readOne($_SESSION['user_id']);

// Handle search/filter
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

// Fetch bookings
$all_bookings = $booking->getByCustomer($_SESSION['user_id'], $status_filter);

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">My Bookings</h1>
                <a href="<?= BASE_URL ?>/services/search.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> New Booking
                </a>
            </div>

            <?= displayFlashMessage(); ?>

            <!-- Filters -->
            <form method="GET" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status:</label>
                <select name="status" onchange="this.form.submit()" class="form-select w-48">
                    <option value="">All</option>
                    <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?= $status_filter == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="completed" <?= $status_filter == 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </form>

            <?php if ($all_bookings->rowCount() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Provider</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($b = $all_bookings->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?= $b['service_title'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $b['category_name'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= $b['provider_name'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?= formatDate($b['booking_date']) ?><br>
                                        <span class="text-sm text-gray-500"><?= formatTime($b['start_time']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <?php
                                            $colors = [
                                                'pending' => 'bg-blue-100 text-blue-800',
                                                'confirmed' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-purple-100 text-purple-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $status_class = $colors[$b['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold <?= $status_class ?>">
                                            <?= ucfirst($b['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">$<?= number_format($b['total_price'], 2) ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?= BASE_URL ?>/bookings/view.php?id=<?= $b['id'] ?>" class="text-primary-600 hover:text-primary-900 mr-3">View</a>
                                        <?php if ($b['status'] === 'pending'): ?>
                                            <a href="<?= BASE_URL ?>/bookings/cancel.php?id=<?= $b['id'] ?>" class="text-red-600 hover:text-red-900 mr-3">Cancel</a>
                                        <?php endif; ?>
                                        <?php if ($b['status'] === 'completed'): ?>
                                            <a href="<?= BASE_URL ?>/bookings/review.php?booking_id=<?= $b['id'] ?>" class="text-green-600 hover:text-green-900">Review</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="bg-gray-50 p-6 rounded-lg text-center text-gray-600">
                    <p>No bookings found<?= $status_filter ? ' for "' . htmlspecialchars($status_filter) . '"' : '' ?>.</p>
                    <a href="<?= BASE_URL ?>/services/search.php" class="btn btn-primary mt-4">Find Services</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
