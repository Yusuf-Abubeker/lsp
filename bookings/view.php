<?php
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../utils/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();

// Validate booking ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('/customer/bookings.php');
}

$booking_id = (int) $_GET['id'];

// Setup database and models
$db = (new Database())->getConnection();
$bookingModel = new Booking($db);
$booking = $bookingModel->readOne($booking_id);

// Check if booking exists
if (!$booking) {
    redirectWithMessage('/customer/bookings.php', 'error', 'Booking not found.');
}

$current_user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch provider ID once (if needed)
$provider_id = null;
if ($role === 'provider') {
    require_once '../models/Provider.php';
    $providerModel = new Provider($db);
    $provider_id = $providerModel->getProviderIdByUserId($current_user_id);
}

$is_customer = $role === 'customer' && $booking->customer_id == $current_user_id;
$is_provider = $role === 'provider' && $provider_id && $booking->provider_id == $provider_id;
$is_admin = $role === 'admin';

if (!$is_customer && !$is_provider && !$is_admin) {
    redirect('/unauthorized.php');
}

// Include header
include '../includes/header.php>';

// Back link based on role
if ($is_customer) {
    $back_link = '/customer/bookings.php';
} elseif ($is_provider) {
    $back_link = '/provider/bookings.php';
} else {
    $back_link = '/admin/bookings.php'; // optional, in case you have an admin section
}

?>

<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-md p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Booking Details</h1>
                <a href="<?= BASE_URL . $back_link ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                    &larr; Back to Bookings
                </a>
            </div>

            <?= displayFlashMessage(); ?>

            <!-- Service Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-3">Service Information</h2>
                    <p class="mb-1"><span class="font-medium">Service:</span> <?= htmlspecialchars($booking->service_title) ?></p>
                    <p class="mb-1"><span class="font-medium">Category:</span> <?= htmlspecialchars($booking->category_name) ?></p>
                    <p class="mb-1"><span class="font-medium">Price:</span> $<?= number_format($booking->service_price, 2) ?> (<?= $booking->service_price_type ?>)</p>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-3">Provider Information</h2>
                    <p class="mb-1"><span class="font-medium">Name:</span> <?= htmlspecialchars($booking->provider_name) ?></p>
                </div>
            </div>

            <!-- Booking & Payment Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-3">Booking Details</h2>
                    <p class="mb-1"><span class="font-medium">Date:</span> <?= formatDate($booking->booking_date) ?></p>
                    <p class="mb-1"><span class="font-medium">Time:</span> <?= formatTime($booking->start_time) ?> - <?= formatTime($booking->end_time) ?></p>
                    <p class="mb-1 flex items-center gap-2">
                        <span class="font-medium">Status:</span>
                        <?php
                        $status_classes = [
                            'pending' => 'bg-blue-100 text-blue-800',
                            'confirmed' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-purple-100 text-purple-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $class = $status_classes[$booking->status] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="inline-block text-xs font-medium px-2 py-1 rounded-full <?= $class ?>">
                            <?= ucfirst($booking->status) ?>
                        </span>
                    </p>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-3">Payment Info</h2>
                    <p class="mb-1"><span class="font-medium">Total:</span> $<?= number_format($booking->total_price, 2) ?></p>
                </div>
            </div>

            <!-- Customer Actions -->
            <?php if ($is_customer): ?>
                <div class="mt-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-3">Actions</h2>
                    <div class="flex flex-wrap gap-4">
                        <?php if ($booking->status === 'pending'): ?>
                            <a href="<?= BASE_URL ?>/bookings/cancel.php?id=<?= $booking_id ?>"
                               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm shadow transition"
                               onclick="return confirm('Are you sure you want to cancel this booking?')">
                                Cancel Booking
                            </a>
                        <?php endif; ?>

                        <?php if ($booking->status === 'completed'): ?>
                            <a href="<?= BASE_URL ?>/reviews/create.php?booking_id=<?= $booking_id ?>"
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm shadow transition">
                                Leave a Review
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
