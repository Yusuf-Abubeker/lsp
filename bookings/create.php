<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Service.php';
require_once '../models/Provider.php';
require_once '../models/Booking.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a customer
requireLogin();
requireRole('customer');

// Check if service ID is provided
if (!isset($_GET['service_id']) || !is_numeric($_GET['service_id'])) {
    setFlashMessage('error', 'Invalid service ID');
    redirect('/services/search.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$service = new Service($db);
$provider = new Provider($db);
$booking = new Booking($db);

// Get service details
if (!$service->readOne($_GET['service_id'])) {
    setFlashMessage('error', 'Service not found');
    redirect('/services/search.php');
}

// Get provider details
$provider->readOne($service->provider_id);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = false;
    
    // Validate date
    if (empty($_POST["booking_date"])) {
        setFlashMessage('error', 'Please select a date');
        $error = true;
    } else {
        $booking_date = date('Y-m-d', strtotime($_POST["booking_date"]));
        if ($booking_date < date('Y-m-d')) {
            setFlashMessage('error', 'Please select a future date');
            $error = true;
        }
    }
    
    // Validate time
    if (empty($_POST["booking_time"])) {
        setFlashMessage('error', 'Please select a time');
        $error = true;
    }
    
    if (!$error) {
        // Check if time slot is available
        $booking_time = date('H:i:s', strtotime($_POST["booking_time"]));
        
        if ($booking->isTimeAvailable($service->provider_id, $booking_date, $booking_time)) {
            // Calculate total price
            $total_price = $service->price;
            if ($service->price_type === 'hourly') {
                $hours = isset($_POST["duration"]) ? (int)$_POST["duration"] : 1;
                $total_price *= $hours;
            }
            
            // Create booking
            $booking->service_id = $service->id;
            $booking->customer_id = $_SESSION['user_id'];
            $booking->booking_date = $booking_date;
            $booking->start_time = $booking_time;
            $booking->status = 'pending';
            $booking->total_price = $total_price;
            $booking->notes = sanitizeInput($_POST["notes"] ?? '');
            
            if ($booking->create()) {
                setFlashMessage('success', 'Booking request sent successfully');
                redirect('/customer/bookings.php');
            } else {
                setFlashMessage('error', 'Error creating booking');
            }
        } else {
            setFlashMessage('error', 'Selected time slot is not available');
        }
    }
}

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <a href="<?php echo BASE_URL; ?>/services/view.php?id=<?= $service->id ?>" class="text-gray-500 hover:text-gray-700 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold">Book Service</h1>
                </div>
                
                <?= displayFlashMessage() ?>
                
                <!-- Service Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-grow">
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium mb-2">
                                <?= $service->category_name ?>
                            </span>
                            <h2 class="text-lg font-semibold mb-1"><?= $service->title ?></h2>
                            <p class="text-gray-600 text-sm mb-2">by <?= $provider->name ?></p>
                            <div class="text-primary-600 font-semibold">
                                <?php 
                                $priceDisplay = '$' . number_format($service->price, 2);
                                if ($service->price_type === 'hourly') {
                                    $priceDisplay .= '/hr';
                                } elseif ($service->price_type === 'starting_at') {
                                    $priceDisplay = 'From ' . $priceDisplay;
                                }
                                echo $priceDisplay;
                                ?>
                            </div>
                        </div>
                        
                        <div class="flex-none">
                            <div class="text-yellow-400 flex">
                                <?= generateStarRating($provider->avg_rating ?? 0) ?>
                            </div>
                            <div class="text-sm text-gray-600 text-right mt-1">
                                <?= $provider->total_reviews ?? 0 ?> reviews
                            </div>
                        </div>
                    </div>
                </div>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>?service_id=<?= $service->id ?>" method="post" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="booking_date" class="form-label">Select Date</label>
                            <input type="date" name="booking_date" id="booking_date" class="form-input" 
                                   min="<?= date('Y-m-d') ?>" value="<?= $_POST['booking_date'] ?? '' ?>">
                        </div>
                        
                        <div>
                            <label for="booking_time" class="form-label">Select Time</label>
                            <select name="booking_time" id="booking_time" class="form-input">
                                <option value="">Choose a time</option>
                                <?php
                                $start = strtotime('9:00');
                                $end = strtotime('17:00');
                                for ($time = $start; $time <= $end; $time += 1800) {
                                    $value = date('H:i', $time);
                                    $display = date('g:i A', $time);
                                    $selected = ($_POST['booking_time'] ?? '') === $value ? 'selected' : '';
                                    echo "<option value=\"$value\" $selected>$display</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <?php if ($service->price_type === 'hourly'): ?>
                            <div>
                                <label for="duration" class="form-label">Duration (hours)</label>
                                <select name="duration" id="duration" class="form-input">
                                    <?php for ($i = 1; $i <= 8; $i++): ?>
                                        <option value="<?= $i ?>" <?= ($_POST['duration'] ?? 1) == $i ? 'selected' : '' ?>>
                                            <?= $i ?> hour<?= $i > 1 ? 's' : '' ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="3" class="form-input"><?= $_POST['notes'] ?? '' ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">Add any special requirements or instructions for the provider.</p>
                    </div>
                    
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-600">Service Price:</span>
                            <span class="font-semibold">
                                <?php
                                $price = $service->price;
                                if ($service->price_type === 'hourly') {
                                    $hours = isset($_POST["duration"]) ? (int)$_POST["duration"] : 1;
                                    $price *= $hours;
                                    echo '$' . number_format($price, 2) . ' (' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' @ $' . number_format($service->price, 2) . '/hr)';
                                } else {
                                    echo '$' . number_format($price, 2);
                                }
                                ?>
                            </span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-full">
                            Confirm Booking
                        </button>
                        
                        <p class="text-sm text-gray-500 text-center mt-4">
                            By confirming this booking, you agree to our terms of service and cancellation policy.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>