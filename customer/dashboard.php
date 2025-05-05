<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/User.php';
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

// Initialize objects
$user = new User($db);
$booking = new Booking($db);

// Get user data
$user->readOne($_SESSION['user_id']);

// Get user's bookings (recent ones)
$recent_bookings = $booking->getByCustomer($_SESSION['user_id']);

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Customer Dashboard</h1>
                <a href="<?php echo BASE_URL; ?>/account/profile.php" class="btn btn-outline text-sm">
                    <i class="fas fa-user-edit mr-2"></i> Edit Profile
                </a>
            </div>
            
            <?= displayFlashMessage() ?>
            
            <div class="bg-primary-50 rounded-lg p-6 mb-8">
                <div class="md:flex justify-between items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="flex-shrink-0">
                            <img src="<?= !empty($user->profile_image) ? '/uploads/profiles/' . $user->profile_image : 'https://via.placeholder.com/60' ?>" 
                                 alt="<?= $user->name ?>" 
                                 class="h-16 w-16 rounded-full object-cover">
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold"><?= $user->name ?></h2>
                            <p class="text-gray-600"><?= $user->email ?></p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="<?php echo BASE_URL; ?>/services/search.php" class="btn btn-primary">
                            <i class="fas fa-search mr-2"></i> Find Services
                        </a>
                        <a href="<?php echo BASE_URL; ?>/customer/bookings.php" class="btn btn-outline">
                            <i class="fas fa-calendar-alt mr-2"></i> My Bookings
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-700">Active Bookings</h3>
                            <p class="text-3xl font-bold text-blue-800 mt-2">
                                <?php
                                $pending_count = 0;
                                $confirmed_count = 0;
                                
                                if ($recent_bookings->rowCount() > 0) {
                                    // Reset pointer to beginning
                                    $recent_bookings->execute();
                                    while ($b = $recent_bookings->fetch(PDO::FETCH_ASSOC)) {
                                        if ($b['status'] === 'pending') {
                                            $pending_count++;
                                        } elseif ($b['status'] === 'confirmed') {
                                            $confirmed_count++;
                                        }
                                    }
                                }
                                
                                echo $pending_count + $confirmed_count;
                                ?>
                            </p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3 text-blue-600 text-xl">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-green-700">Completed Services</h3>
                            <p class="text-3xl font-bold text-green-800 mt-2">
                                <?php
                                $completed_count = 0;
                                
                                if ($recent_bookings->rowCount() > 0) {
                                    // Reset pointer to beginning
                                    $recent_bookings->execute();
                                    while ($b = $recent_bookings->fetch(PDO::FETCH_ASSOC)) {
                                        if ($b['status'] === 'completed') {
                                            $completed_count++;
                                        }
                                    }
                                }
                                
                                echo $completed_count;
                                ?>
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3 text-green-600 text-xl">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-purple-700">Total Spent</h3>
                            <p class="text-3xl font-bold text-purple-800 mt-2">
                                <?php
                                $total_spent = 0;
                                
                                if ($recent_bookings->rowCount() > 0) {
                                    // Reset pointer to beginning
                                    $recent_bookings->execute();
                                    while ($b = $recent_bookings->fetch(PDO::FETCH_ASSOC)) {
                                        if ($b['status'] === 'completed') {
                                            $total_spent += (float)$b['total_price'];
                                        }
                                    }
                                }
                                
                                echo '$' . number_format($total_spent, 2);
                                ?>
                            </p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3 text-purple-600 text-xl">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Bookings -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Recent Bookings</h2>
                    <a href="<?php echo BASE_URL; ?>/customer/bookings.php" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <?php if ($recent_bookings->rowCount() > 0): ?>
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
                                <?php 
                                // Reset pointer to beginning
                                $recent_bookings->execute();
                                $count = 0;
                                while ($b = $recent_bookings->fetch(PDO::FETCH_ASSOC)): 
                                    if ($count >= 5) break; // Show only 5 recent bookings
                                    $count++;
                                ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= $b['service_title'] ?></div>
                                            <div class="text-sm text-gray-500"><?= $b['category_name'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= $b['provider_name'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= formatDate($b['booking_date']) ?></div>
                                            <div class="text-sm text-gray-500"><?= formatTime($b['start_time']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $status_color = 'bg-blue-100 text-blue-800';
                                            
                                            if ($b['status'] === 'confirmed') {
                                                $status_color = 'bg-green-100 text-green-800';
                                            } elseif ($b['status'] === 'cancelled') {
                                                $status_color = 'bg-red-100 text-red-800';
                                            } elseif ($b['status'] === 'completed') {
                                                $status_color = 'bg-purple-100 text-purple-800';
                                            }
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_color ?>">
                                                <?= ucfirst($b['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            $<?= number_format($b['total_price'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="<?php echo BASE_URL; ?>/bookings/view.php?id=<?= $b['id'] ?>" class="text-primary-600 hover:text-primary-900 mr-3">
                                                View
                                            </a>
                                            
                                            <?php if ($b['status'] === 'completed'): ?>
                                                <a href="<?php echo BASE_URL; ?>/reviews/create.php?booking_id=<?= $b['id'] ?>" class="text-green-600 hover:text-green-900">
                                                    Review
                                                </a>
                                            <?php elseif ($b['status'] === 'pending'): ?>
                                                <a href="<?php echo BASE_URL; ?>/bookings/cancel.php?id=<?= $b['id'] ?>" class="text-red-600 hover:text-red-900">
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
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <p class="text-gray-600 mb-4">You haven't made any bookings yet.</p>
                        <a href="<?php echo BASE_URL; ?>/services/search.php" class="btn btn-primary">
                            Find Services
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Search Section -->
        <div class="bg-primary-600 rounded-lg shadow-sm p-8 text-white">
            <div class="md:flex justify-between items-center">
                <div class="mb-6 md:mb-0 md:mr-6">
                    <h2 class="text-2xl font-bold mb-2">Ready to Find a Service?</h2>
                    <p class="text-primary-100">Browse our top categories or search for the specific service you need.</p>
                </div>
                
                <form action="/services/search.php" method="GET" class="bg-white p-3 rounded-lg shadow-sm flex flex-col md:flex-row gap-3 md:items-center">
                    <div class="flex-1">
                        <input type="text" name="search" placeholder="What service do you need?" class="form-input bg-gray-50 border-none w-full">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>