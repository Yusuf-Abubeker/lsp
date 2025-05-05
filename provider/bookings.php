<?php
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../models/Provider.php';
require_once '../utils/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();
requireRole('provider');

$db = (new Database())->getConnection();
$bookingModel = new Booking($db);
$providerModel = new Provider($db);

// Get provider ID from user ID
$provider_id = $providerModel->getProviderIdByUserId($_SESSION['user_id']);
if (!$provider_id) {
    redirect('/unauthorized.php');
}

// Handle booking status updates
if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $bookingModel->readOne($_GET['id']);

    // Ensure the booking belongs to the current provider
    if ($bookingModel->provider_id != $provider_id) {
        setFlashMessage('error', 'Unauthorized action.');
    } else {
        if ($bookingModel->updateStatus($_GET['status'])) {
            setFlashMessage('success', 'Booking status updated successfully');
        } else {
            setFlashMessage('error', 'Error updating booking status');
        }
    }
    redirect('/provider/bookings.php');
}

// Get optional status filter
$status_filter = $_GET['status'] ?? null;

// Fetch bookings by provider and optional status
$stmt = $bookingModel->getByProvider($provider_id, $status_filter);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">My Bookings</h1>
                <a href="<?= BASE_URL ?>/provider/dashboard.php" class="btn btn-outline text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>

            <?= displayFlashMessage() ?>

            <!-- Status Filters -->
            <div class="mb-6">
                <div class="flex gap-2">
                    <?php
                    $statuses = ['', 'pending', 'confirmed', 'completed', 'cancelled'];
                    foreach ($statuses as $status) {
                        $label = $status === '' ? 'All Bookings' : ucfirst($status);
                        $active = ($status_filter === $status || ($status_filter === null && $status === ''));
                        $link = BASE_URL . "/provider/bookings.php" . ($status ? "?status=$status" : '');
                        $class = $active ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200';
                        echo "<a href='$link' class='px-4 py-2 rounded-lg $class'>$label</a>";
                    }
                    ?>
                </div>
            </div>

            <!-- Bookings Table -->
            <?php if (empty($bookings)): ?>
                <p class="text-gray-600">No bookings found.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($bookings as $row): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['service_title']) ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($row['category_name']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($row['customer_name']) ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="text-gray-900"><?= formatDate($row['booking_date']) ?></div>
                                        <div class="text-gray-500"><?= formatTime($row['start_time']) ?> - <?= formatTime($row['end_time']) ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $status_class = [
                                            'pending' => 'bg-blue-100 text-blue-800',
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-purple-100 text-purple-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ][$row['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_class ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        $<?= number_format($row['total_price'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <a href="<?= BASE_URL ?>/bookings/view.php?id=<?= $row['id'] ?>" class="text-primary-600 hover:text-primary-900 mr-3">View</a>
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <a href="<?= BASE_URL ?>/provider/bookings.php?action=update_status&id=<?= $row['id'] ?>&status=confirmed"
                                               class="text-green-600 hover:text-green-900 mr-3">Confirm</a>
                                            <a href="<?= BASE_URL ?>/provider/bookings.php?action=update_status&id=<?= $row['id'] ?>&status=cancelled"
                                               class="text-red-600 hover:text-red-900">Cancel</a>
                                        <?php elseif ($row['status'] === 'confirmed'): ?>
                                            <a href="<?= BASE_URL ?>/provider/bookings.php?action=update_status&id=<?= $row['id'] ?>&status=completed"
                                               class="text-green-600 hover:text-green-900 mr-3">Complete</a>
                                            <a href="<?= BASE_URL ?>/provider/bookings.php?action=update_status&id=<?= $row['id'] ?>&status=cancelled"
                                               class="text-red-600 hover:text-red-900">Cancel</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
