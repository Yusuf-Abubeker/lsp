<?php
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../models/Review.php';
require_once '../models/Provider.php';
require_once '../utils/helpers.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireLogin();
requireRole('customer');

$db = (new Database())->getConnection();
$bookingModel = new Booking($db);
$reviewModel = new Review($db);

// Get booking ID from query string
$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    setFlashMessage('error', 'Booking ID is required.');
    redirect('/customer/bookings.php');
}

// Fetch booking and validate access
$bookingModel->readOne($booking_id);
if (!$bookingModel->id || $bookingModel->customer_id != $_SESSION['user_id']) {
    setFlashMessage('error', 'Unauthorized or invalid booking.');
    redirect('/customer/bookings.php');
}

if ($bookingModel->status !== 'completed') {
    setFlashMessage('error', 'Only completed bookings can be reviewed.');
    redirect('/customer/bookings.php');
}

// Prevent duplicate reviews
if ($reviewModel->bookingHasReview($booking_id)) {
    setFlashMessage('error', 'You have already submitted a review for this booking.');
    redirect('/customer/bookings.php');
}

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5 || empty($comment)) {
        setFlashMessage('error', 'Rating must be between 1-5 and comment is required.');
    } else {
        $reviewModel->booking_id = $booking_id;
        $reviewModel->service_id = $bookingModel->service_id;
        $reviewModel->customer_id = $_SESSION['user_id'];
        $reviewModel->provider_id = $bookingModel->provider_id;
        $reviewModel->rating = $rating;
        $reviewModel->comment = $comment;

        if ($reviewModel->create()) {
            setFlashMessage('success', 'Thank you! Your review has been submitted.');
            redirect('/customer/bookings.php');
        } else {
            setFlashMessage('error', 'Something went wrong. Please try again.');
        }
    }
}

include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-4">Write a Review</h2>
            <?= displayFlashMessage() ?>
            <form method="POST">
                <div class="mb-4">
                    <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                    <select name="rating" id="rating" class="form-select w-full" required>
                        <option value="">Select</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">Comment</label>
                    <textarea name="comment" id="comment" rows="4" class="form-textarea w-full" placeholder="Write your feedback here..." required></textarea>
                </div>
                <div class="flex justify-end">
                    <a href="/customer/bookings.php" class="btn btn-outline mr-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
