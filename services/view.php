<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Service.php';
require_once '../models/Provider.php';
require_once '../models/Review.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if service ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('error', 'Invalid service ID');
    redirect('/services/search.php');
}

// Get service ID from URL
$service_id = $_GET['id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$service = new Service($db);
$provider = new Provider($db);
$review = new Review($db);

// Get service details
if (!$service->readOne($service_id)) {
    setFlashMessage('error', 'Service not found');
    redirect('/services/search.php');
}

// Get provider details
$provider->readOne($service->provider_id);

// Get reviews for this service
$reviews = $review->getByServiceId($service_id, 5, 0);
$total_reviews = $reviews->rowCount();

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <!-- Service Details -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
            <div class="p-6">
                <!-- Breadcrumbs -->
                <div class="text-sm text-gray-600 mb-4">
                    <a href="<?php echo BASE_URL; ?>/" class="hover:text-primary-600">Home</a> &raquo; 
                    <a href="<?php echo BASE_URL; ?>/services/search.php" class="hover:text-primary-600">Services</a> &raquo; 
                    <a href="<?php echo BASE_URL; ?>/services/search.php?category=<?= $service->category_id ?>" class="hover:text-primary-600"><?= $service->category_name ?></a> &raquo; 
                    <span class="text-gray-900"><?= $service->title ?></span>
                </div>
                
                <!-- Service Header -->
                <div class="md:flex justify-between items-start">
                    <div>
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium mb-2">
                            <?= $service->category_name ?>
                        </span>
                        <h1 class="text-3xl font-bold mb-2"><?= $service->title ?></h1>
                        
                        <div class="flex items-center mb-4">
                            <div class="text-yellow-400 flex">
                                <?= generateStarRating($service->avg_rating ?? 0) ?>
                            </div>
                            <span class="text-gray-600 ml-2">
                                <?= number_format($service->avg_rating ?? 0, 1) ?> 
                                (<?= $service->total_reviews ?? 0 ?> reviews)
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 md:mt-0">
                        <div class="text-2xl font-bold text-primary-600 mb-2">
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
                        
                        <a href="<?php echo BASE_URL; ?>/bookings/create.php?service_id=<?= $service->id ?>" class="btn btn-primary">
                            Book Now
                        </a>
                    </div>
                </div>
                
                <hr class="my-6">
                
                <!-- Service Description -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Service Description</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700">
                            <?= nl2br(htmlspecialchars($service->description)) ?>
                        </p>
                    </div>
                </div>
                
                <!-- Provider Information -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-semibold mb-4">About the Provider</h2>
                    
                    <div class="flex items-start">
                        <img src="<?= !empty($provider->profile_image) ? '/uploads/profiles/' . $provider->profile_image : 'https://via.placeholder.com/100' ?>" 
                             alt="<?= $provider->name ?>" 
                             class="h-20 w-20 rounded-full object-cover mr-4">
                        
                        <div>
                            <h3 class="text-lg font-semibold"><?= $provider->name ?></h3>
                            
                            <div class="flex items-center text-sm text-gray-600 mt-1 mb-2">
                                <div class="text-yellow-400 flex">
                                    <?= generateStarRating($provider->avg_rating ?? 0) ?>
                                </div>
                                <span class="ml-2">
                                    <?= number_format($provider->avg_rating ?? 0, 1) ?> 
                                    (<?= $provider->total_reviews ?? 0 ?> reviews)
                                </span>
                                
                                <?php if ($provider->is_verified): ?>
                                    <span class="ml-3 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($provider->city) && !empty($provider->state)): ?>
                                <div class="flex items-center text-gray-600 text-sm mb-2">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span><?= $provider->city . ', ' . $provider->state ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php echo BASE_URL; ?>/providers/profile.php?id=<?= $provider->id ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                View Full Profile <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <?php if (!empty($provider->bio)): ?>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($provider->bio)) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Reviews Section -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">
                            Reviews 
                            <span class="text-gray-500 font-normal">(<?= $total_reviews ?>)</span>
                        </h2>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer'): ?>
                            <a href="<?php echo BASE_URL; ?>/reviews/create.php?service_id=<?= $service->id ?>" class="btn btn-outline text-sm">
                                Write a Review
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($total_reviews > 0): ?>
                        <div class="space-y-6">
                            <?php while ($review_item = $reviews->fetch(PDO::FETCH_ASSOC)): ?>
                                <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium"><?= $review_item['customer_name'] ?></div>
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
                        
                        <?php if ($total_reviews > 5): ?>
                            <div class="mt-6 text-center">
                                <a href="<?php echo BASE_URL; ?>/reviews/list.php?service_id=<?= $service->id ?>" class="btn btn-outline">
                                    View All Reviews
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-600 mb-4">This service hasn't received any reviews yet.</p>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer'): ?>
                                <a href="<?php echo BASE_URL; ?>/reviews/create.php?service_id=<?= $service->id ?>" class="btn btn-outline">
                                    Be the first to review
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Booking Call to Action -->
        <div class="bg-primary-600 text-white rounded-lg shadow-sm p-8 text-center">
            <h2 class="text-2xl font-bold mb-4">Ready to Book This Service?</h2>
            <p class="text-primary-100 max-w-2xl mx-auto mb-6">Check availability and secure your booking today. It only takes a few minutes to complete.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="<?php echo BASE_URL; ?>/bookings/create.php?service_id=<?= $service->id ?>" class="btn bg-white text-primary-600 hover:bg-primary-50">
                    Book Now
                </a>
                
                <a href="<?php echo BASE_URL; ?>/contact.php" class="btn bg-primary-700 text-white hover:bg-primary-800">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>