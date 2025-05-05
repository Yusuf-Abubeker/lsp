<?php
// Home page
require_once 'config/database.php';
require_once 'models/Category.php';
require_once 'models/Provider.php';
require_once 'models/Service.php';
require_once 'utils/helpers.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$category = new Category($db);
$provider = new Provider($db);
$service = new Service($db);

// Get popular categories
$popularCategories = $category->getPopularCategories(6);

// Get top rated providers
$topProviders = $provider->getTopRated(3);

// Get featured services
$featuredServices = $service->getTopServices(6);

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-blue-600 to-primary-700 text-white">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.pexels.com/photos/3760069/pexels-photo-3760069.jpeg?auto=compress&cs=tinysrgb&w=1600'); opacity: 0.2;"></div>
    
    <div class="container-custom relative py-20 md:py-32">
        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Find Local Service Providers You Can Trust</h1>
            <p class="text-xl mb-8 text-blue-100">Connect with skilled professionals for home services, repairs, lessons, and more in your neighborhood.</p>
            
            <form action="<?php echo BASE_URL; ?>/services/search.php" method="GET" class="bg-white p-4 rounded-lg shadow-lg flex flex-col md:flex-row gap-4 md:items-center">
                <div class="flex-1">
                    <label for="category" class="block text-gray-700 text-sm font-medium mb-1">I need help with...</label>
                    <select name="category" id="category" class="form-input bg-gray-50">
                        <option value="">All Services</option>
                        <?php while ($row = $popularCategories->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="flex-1">
                    <label for="location" class="block text-gray-700 text-sm font-medium mb-1">Location</label>
                    <input type="text" id="location" name="location" placeholder="City, State, or ZIP" class="form-input bg-gray-50">
                </div>
                
                <div class="flex-none self-end md:self-auto">
                    <button type="submit" class="btn btn-primary w-full md:w-auto px-8 h-11">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Popular Categories -->
<section class="py-16 bg-gray-50">
    <div class="container-custom">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Browse Popular Categories</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">Find the service you need from our most popular categories</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php 
            // Reset pointer to beginning
            $popularCategories->execute();
            while ($category = $popularCategories->fetch(PDO::FETCH_ASSOC)): 
                $icon = $category['icon'] ?? 'wrench';
            ?>
                <a href="<?php echo BASE_URL; ?>/services/search.php?category=<?= $category['id'] ?>" class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 text-center group">
                    <div class="bg-primary-100 text-primary-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-primary-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-<?= $icon ?> text-2xl"></i>
                    </div>
                    <h3 class="font-semibold mb-1"><?= $category['name'] ?></h3>
                    <p class="text-gray-500 text-sm"><?= $category['service_count'] ?> services</p>
                </a>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-8">
            <a href="<?php echo BASE_URL; ?>/services/categories.php" class="btn btn-outline">View All Categories</a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">How It Works</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">Connect with qualified service providers in just a few simple steps</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-xl font-bold text-primary-600">1</span>
                </div>
                <h3 class="text-xl font-semibold mb-3">Search for a Service</h3>
                <p class="text-gray-600">Browse categories or search for the specific service you need in your area.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-xl font-bold text-primary-600">2</span>
                </div>
                <h3 class="text-xl font-semibold mb-3">Book an Appointment</h3>
                <p class="text-gray-600">Select a provider, choose your preferred date and time, and book directly.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-xl font-bold text-primary-600">3</span>
                </div>
                <h3 class="text-xl font-semibold mb-3">Get the Job Done</h3>
                <p class="text-gray-600">Meet your provider, get excellent service, and leave a review about your experience.</p>
            </div>
        </div>
    </div>
</section>

<!-- Top Rated Providers -->
<section class="py-16 bg-gray-50">
    <div class="container-custom">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Our Top Rated Providers</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">Trusted professionals with the highest ratings from satisfied customers</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php while ($provider = $topProviders->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <img src="<?= !empty($provider['profile_image']) ? '/uploads/profiles/' . $provider['profile_image'] : 'https://via.placeholder.com/150' ?>" 
                                     alt="<?= $provider['name'] ?>" 
                                     class="h-16 w-16 rounded-full object-cover">
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold"><?= $provider['name'] ?></h3>
                                <div class="flex items-center text-yellow-400 mt-1">
                                    <?php 
                                    require_once 'utils/helpers.php';
                                    echo generateStarRating($provider['avg_rating']);
                                    ?>
                                    <span class="text-gray-600 ml-2">(<?= $provider['total_reviews'] ?> reviews)</span>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-gray-600 mb-4 line-clamp-2">
                            <?= !empty($provider['bio']) ? htmlspecialchars($provider['bio']) : 'Professional service provider ready to help with your needs.' ?>
                        </p>
                        
                        <?php if (!empty($provider['city']) && !empty($provider['state'])): ?>
                            <div class="text-gray-600 flex items-center mb-4">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                <?= $provider['city'] . ', ' . $provider['state'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?php echo BASE_URL; ?>/providers/profile.php?id=<?= $provider['id'] ?>" class="btn btn-outline w-full">View Profile</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-8">
            <a href="<?php echo BASE_URL; ?>/providers/list.php" class="btn btn-outline">View All Providers</a>
        </div>
    </div>
</section>

<!-- Featured Services -->
<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Featured Services</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">Discover popular services available in your area</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($service = $featuredServices->fetch(PDO::FETCH_ASSOC)): ?>
                <a href="<?php echo BASE_URL; ?>/services/view.php?id=<?= $service['id'] ?>" class="card hover:translate-y-[-5px] transition-all duration-300">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium mb-2">
                                    <?= $service['category_name'] ?>
                                </span>
                                <h3 class="text-lg font-semibold"><?= $service['title'] ?></h3>
                            </div>
                            <div class="text-lg font-semibold text-primary-600">
                                <?php 
                                $priceDisplay = '$' . number_format($service['price'], 2);
                                if ($service['price_type'] === 'hourly') {
                                    $priceDisplay .= '/hr';
                                } elseif ($service['price_type'] === 'starting_at') {
                                    $priceDisplay = 'From ' . $priceDisplay;
                                }
                                echo $priceDisplay;
                                ?>
                            </div>
                        </div>
                        
                        <div class="flex items-center mb-4">
                            <div class="text-yellow-400 flex">
                                <?php 
                                echo generateStarRating($service['avg_rating'] ?? 0);
                                ?>
                            </div>
                            <span class="text-gray-600 text-sm ml-2">
                                <?= number_format($service['avg_rating'] ?? 0, 1) ?> 
                                (<?= $service['total_reviews'] ?? 0 ?> reviews)
                            </span>
                        </div>
                        
                        <div class="flex items-center text-gray-600 text-sm">
                            <i class="fas fa-user-circle mr-2"></i>
                            <span>Provided by <?= $service['provider_name'] ?></span>
                        </div>
                        
                        <?php if (!empty($service['city']) && !empty($service['state'])): ?>
                            <div class="flex items-center text-gray-600 text-sm mt-1">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span><?= $service['city'] . ', ' . $service['state'] ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-8">
            <a href="<?php echo BASE_URL; ?>/services/search.php" class="btn btn-outline">View All Services</a>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-16 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
    <div class="container-custom">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">What Our Customers Say</h2>
            <p class="text-blue-100 max-w-3xl mx-auto">Read testimonials from satisfied customers who found reliable service providers</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-6">
                <div class="text-yellow-300 flex mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="italic mb-4">"I needed a plumber urgently and found one through this platform in less than an hour. Great service, fair pricing, and professional work. Highly recommended!"</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-200 text-primary-600 flex items-center justify-center font-bold">JD</div>
                    <div class="ml-3">
                        <h4 class="font-semibold">John Doe</h4>
                        <p class="text-blue-200 text-sm">Plumbing Service</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-6">
                <div class="text-yellow-300 flex mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="italic mb-4">"Found a fantastic tutor for my daughter. The booking process was seamless, and the service exceeded our expectations. We've booked weekly sessions now!"</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-200 text-primary-600 flex items-center justify-center font-bold">JS</div>
                    <div class="ml-3">
                        <h4 class="font-semibold">Jane Smith</h4>
                        <p class="text-blue-200 text-sm">Tutoring Service</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-6">
                <div class="text-yellow-300 flex mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="italic mb-4">"As someone who provides cleaning services, this platform has helped me connect with new clients and grow my business. The booking system makes scheduling easy."</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-200 text-primary-600 flex items-center justify-center font-bold">RJ</div>
                    <div class="ml-3">
                        <h4 class="font-semibold">Robert Johnson</h4>
                        <p class="text-blue-200 text-sm">Service Provider</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="bg-gray-50 rounded-2xl shadow-sm p-8 md:p-12">
            <div class="md:flex items-center justify-between">
                <div class="md:w-2/3 mb-6 md:mb-0">
                    <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
                    <p class="text-gray-600">Join our community of customers and service providers today.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo BASE_URL; ?>/auth/register.php?role=customer" class="btn btn-primary text-center">Find a Service</a>
                    <a href="<?php echo BASE_URL; ?>/auth/register.php?role=provider" class="btn btn-outline text-center">Become a Provider</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>