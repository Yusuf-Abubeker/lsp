<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Provider.php';
require_once '../models/Service.php';
require_once '../models/Booking.php';
require_once '../models/Review.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a provider
requireLogin();
requireRole('provider');

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$user = new User($db);
$provider_model = new Provider($db);
$service = new Service($db);
$booking = new Booking($db);
$review = new Review($db);

// Get user data
$user->readOne($_SESSION['user_id']);

// Get provider profile
$provider = $provider_model->getByUserId($_SESSION['user_id']);

// Get provider's services
$services = $service->getByProviderId($provider->id);
$services_count = $services->rowCount();

// Get provider's bookings (recent ones)
$recent_bookings = $booking->getByProvider($provider->id);
$pending_bookings = $booking->getByProvider($provider->id, 'pending');
$pending_count = $pending_bookings->rowCount();

// Get provider's reviews
$recent_reviews = $review->getByProviderId($provider->id, 5, 0);
$review_count = $review->countByProviderId($provider->id);

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Provider Dashboard</h1>
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
                            <div class="flex items-center">
                                <h2 class="text-xl font-semibold"><?= $user->name ?></h2>
                                <?php if ($provider->is_verified): ?>
                                    <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center">
                                <div class="text-yellow-400 flex mr-1">
                                    <?= generateStarRating($provider->avg_rating ?? 0) ?>
                                </div>
                                <span class="text-gray-600 text-sm">
                                    <?= number_format($provider->avg_rating ?? 0, 1) ?> 
                                    (<?= $provider->total_reviews ?? 0 ?> reviews)
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="<?php echo BASE_URL; ?>/provider/services.php?action=create" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i> Add Service
                        </a>
                        <a href="<?php echo BASE_URL; ?>/provider/bookings.php" class="btn btn-outline">
                            <i class="fas fa-calendar-alt mr-2"></i> View Bookings
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-700">Active Services</h3>
                            <p class="text-3xl font-bold text-blue-800 mt-2"><?= $services_count ?></p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3 text-blue-600 text-xl">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-700">Pending Bookings</h3>
                            <p class="text-3xl font-bold text-yellow-800 mt-2"><?= $pending_count ?></p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3 text-yellow-600 text-xl">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-green-700">Reviews</h3>
                            <p class="text-3xl font-bold text-green-800 mt-2"><?= $review_count ?></p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3 text-green-600 text-xl">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-purple-700">Total Revenue</h3>
                            <p class="text-3xl font-bold text-purple-800 mt-2">
                                <?php
                                $total_revenue = 0;
                                
                                if ($recent_bookings->rowCount() > 0) {
                                    // Reset pointer to beginning
                                    $recent_bookings->execute();
                                    while ($b = $recent_bookings->fetch(PDO::FETCH_ASSOC)) {
                                        if ($b['status'] === 'completed') {
                                            $total_revenue += (float)$b['total_price'];
                                        }
                                    }
                                }
                                
                                echo '$' . number_format($total_revenue, 2);
                                ?>
                            </p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3 text-purple-600 text-xl">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pending Booking Requests -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Pending Booking Requests</h2>
                    <a href="<?php echo BASE_URL; ?>/provider/bookings.php" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <?php if ($pending_count > 0): ?>
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
                                        Date & Time
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
                                while ($b = $pending_bookings->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= $b['service_title'] ?></div>
                                            <div class="text-sm text-gray-500"><?= $b['category_name'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= $b['customer_name'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= formatDate($b['booking_date']) ?></div>
                                            <div class="text-sm text-gray-500"><?= formatTime($b['start_time']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            $<?= number_format($b['total_price'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="<?php echo BASE_URL; ?>/bookings/view.php?id=<?= $b['id'] ?>" class="text-primary-600 hover:text-primary-900 mr-3">
                                                View
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>/provider/bookings.php?action=confirm&id=<?= $b['id'] ?>" class="text-green-600 hover:text-green-900 mr-3">
                                                Confirm
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>/provider/bookings.php?action=reject&id=<?= $b['id'] ?>" class="text-red-600 hover:text-red-900">
                                                Reject
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <p class="text-gray-600">You don't have any pending booking requests.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Your Services -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Your Services</h2>
                    <a href="<?php echo BASE_URL; ?>/provider/services.php" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        Manage Services <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <?php if ($services_count > 0): ?>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php 
                        $count = 0;
                        while ($s = $services->fetch(PDO::FETCH_ASSOC)): 
                            if ($count >= 3) break; // Show only 3 services
                            $count++;
                        ?>
                            <div class="card hover:shadow-md transition-all duration-300">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium mb-2">
                                                <?= $s['category_name'] ?>
                                            </span>
                                            <h3 class="text-lg font-semibold"><?= $s['title'] ?></h3>
                                        </div>
                                        <div class="text-lg font-semibold text-primary-600">
                                            <?php 
                                            $priceDisplay = '$' . number_format($s['price'], 2);
                                            if ($s['price_type'] === 'hourly') {
                                                $priceDisplay .= '/hr';
                                            } elseif ($s['price_type'] === 'starting_at') {
                                                $priceDisplay = 'From ' . $priceDisplay;
                                            }
                                            echo $priceDisplay;
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center mb-4">
                                        <div class="bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-xs font-medium">
                                            <?= $s['booking_count'] ?> bookings
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="<?php echo BASE_URL; ?>/services/view.php?id=<?= $s['id'] ?>" class="btn btn-outline flex-grow text-center text-sm">
                                            View
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/provider/services.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-outline flex-grow text-center text-sm">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php if ($services_count > 3): ?>
                        <div class="mt-4 text-center">
                            <a href="<?php echo BASE_URL; ?>/provider/services.php" class="btn btn-outline">
                                View All Services
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <p class="text-gray-600 mb-4">You haven't added any services yet.</p>
                        <a href="<?php echo BASE_URL; ?>/provider/services.php?action=create" class="btn btn-primary">
                            Add Your First Service
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Recent Reviews -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Recent Reviews</h2>
                    <?php if ($review_count > 0): ?>
                        <a href="<?php echo BASE_URL; ?>/provider/reviews.php" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if ($review_count > 0): ?>
                    <div class="space-y-6">
                        <?php while ($review_item = $recent_reviews->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center">
                                            <div class="font-medium"><?= $review_item['customer_name'] ?></div>
                                            <span class="mx-2">•</span>
                                            <div class="text-sm text-gray-500"><?= $review_item['service_title'] ?></div>
                                        </div>
                                        <div class="text-sm text-gray-500"><?= formatDate($review_item['created_at'], 'F j, Y') ?></div>
                                    </div>
                                    <div class="text-yellow-400 flex">
                                        <?= generateStarRating($review_item['rating']) ?>
                                    </div>
                                </div>
                                <div class="mt-3 text-gray-700">
                                    <?= nl2br(htmlspecialchars($review_item['comment'])) ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <p class="text-gray-600">You haven't received any reviews yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Profile Completion Banner -->
        <?php if (empty($provider->bio) || empty($provider->address) || empty($provider->city)): ?>
            <div class="bg-blue-600 text-white rounded-lg shadow-sm p-8 text-center">
                <h2 class="text-2xl font-bold mb-4">Complete Your Provider Profile</h2>
                <p class="text-blue-100 max-w-2xl mx-auto mb-6">
                    A complete profile helps customers find you and increases your chances of getting bookings.
                </p>
                <a href="<?php echo BASE_URL; ?>/account/profile.php" class="btn bg-white text-blue-600 hover:bg-blue-50">
                    Complete Profile
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>