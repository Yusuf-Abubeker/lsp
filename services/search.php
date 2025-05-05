<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Category.php';
require_once '../models/Service.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$category = new Category($db);
$service = new Service($db);

// Get all categories for the filter
$categories = $category->readAll();

// Get search parameters
$search_category = isset($_GET['category']) ? $_GET['category'] : '';
$search_location = isset($_GET['location']) ? sanitizeInput($_GET['location']) : '';
$search_price_min = isset($_GET['price_min']) ? (float)$_GET['price_min'] : null;
$search_price_max = isset($_GET['price_max']) ? (float)$_GET['price_max'] : null;
$search_rating = isset($_GET['rating']) ? (int)$_GET['rating'] : null;

// Search parameters
$search_params = [
    'category_id' => $search_category,
    'location' => $search_location,
    'price_min' => $search_price_min,
    'price_max' => $search_price_max,
    'rating_min' => $search_rating,
    'limit' => 12,
    'offset' => 0
];

// Perform search
$search_results = $service->search($search_params);

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <!-- Search Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h1 class="text-2xl font-bold mb-4">Find Local Services</h1>
            
            <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="category" class="form-label">Service Category</label>
                    <select name="category" id="category" class="form-input">
                        <option value="">All Categories</option>
                        <?php 
                        // Reset pointer to beginning
                        $categories->execute();
                        while ($cat = $categories->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= $search_category == $cat['id'] ? 'selected' : '' ?>>
                                <?= $cat['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div>
                    <label for="location" class="form-label">Location</label>
                    <input type="text" id="location" name="location" placeholder="City, State, or ZIP" 
                           class="form-input" value="<?= $search_location ?>">
                </div>
                
                <div>
                    <label for="price" class="form-label">Price Range</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" id="price_min" name="price_min" placeholder="Min" 
                               class="form-input" value="<?= $search_price_min ?>">
                        <input type="number" id="price_max" name="price_max" placeholder="Max" 
                               class="form-input" value="<?= $search_price_max ?>">
                    </div>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full h-10">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Mobile Filters Toggle -->
        <div class="lg:hidden mb-4">
            <button class="w-full btn btn-outline" type="button" data-toggle="collapse" data-target="#mobileFilters" 
                    aria-expanded="false" aria-controls="mobileFilters" onclick="document.getElementById('mobileFilters').classList.toggle('hidden')">
                <i class="fas fa-filter mr-2"></i> Show Filters
            </button>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Filters Sidebar -->
            <div id="mobileFilters" class="hidden lg:block lg:w-1/4">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Filter Results</h2>
                    
                    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="GET">
                        <!-- Keep existing search params if any -->
                        <?php if ($search_category): ?>
                            <input type="hidden" name="category" value="<?= $search_category ?>">
                        <?php endif; ?>
                        <?php if ($search_location): ?>
                            <input type="hidden" name="location" value="<?= $search_location ?>">
                        <?php endif; ?>
                        
                        <div class="mb-4">
                            <label class="form-label">Price Range</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="price_min" placeholder="Min $" class="form-input" value="<?= $search_price_min ?>">
                                <input type="number" name="price_max" placeholder="Max $" class="form-input" value="<?= $search_price_max ?>">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Minimum Rating</label>
                            <div class="flex items-center space-x-2 mt-2">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <div class="flex items-center">
                                        <input type="radio" id="rating<?= $i ?>" name="rating" value="<?= $i ?>" 
                                               <?= $search_rating == $i ? 'checked' : '' ?> class="mr-1">
                                        <label for="rating<?= $i ?>" class="text-yellow-400">
                                            <?= str_repeat('<i class="fas fa-star"></i>', $i) ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-full">Apply Filters</button>
                    </form>
                </div>
            </div>
            
            <!-- Search Results -->
            <div class="lg:w-3/4">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold">
                            <?php
                            $result_count = $search_results->rowCount();
                            echo $result_count . ' ' . ($result_count === 1 ? 'Service' : 'Services') . ' Found';
                            
                            if ($search_category || $search_location) {
                                echo ' for ';
                                
                                if ($search_category) {
                                    $cat = new Category($db);
                                    $cat->readOne($search_category);
                                    echo $cat->name;
                                    
                                    if ($search_location) {
                                        echo ' in ';
                                    }
                                }
                                
                                if ($search_location) {
                                    echo $search_location;
                                }
                            }
                            ?>
                        </h2>
                        
                        <div class="text-sm text-gray-600">
                            Sort by: 
                            <select class="border-none bg-transparent focus:ring-0">
                                <option>Relevance</option>
                                <option>Rating: High to Low</option>
                                <option>Price: Low to High</option>
                                <option>Price: High to Low</option>
                            </select>
                        </div>
                    </div>
                    
                    <?php if ($result_count > 0): ?>
                        <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6">
                            <?php while ($result = $search_results->fetch(PDO::FETCH_ASSOC)): ?>
                                <div class="card transition-all duration-300 hover:shadow-md">
                                    <div class="p-6">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium mb-2">
                                                    <?= $result['category_name'] ?>
                                                </span>
                                                <h3 class="text-lg font-semibold">
                                                    <a href="<?php echo BASE_URL; ?>/services/view.php?id=<?= $result['id'] ?>" class="hover:text-primary-600">
                                                        <?= $result['title'] ?>
                                                    </a>
                                                </h3>
                                            </div>
                                            <div class="text-lg font-semibold text-primary-600">
                                                <?php 
                                                $priceDisplay = '$' . number_format($result['price'], 2);
                                                if ($result['price_type'] === 'hourly') {
                                                    $priceDisplay .= '/hr';
                                                } elseif ($result['price_type'] === 'starting_at') {
                                                    $priceDisplay = 'From ' . $priceDisplay;
                                                }
                                                echo $priceDisplay;
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center mb-3">
                                            <div class="text-yellow-400 flex">
                                                <?= generateStarRating($result['avg_rating'] ?? 0) ?>
                                            </div>
                                            <span class="text-gray-600 text-sm ml-2">
                                                <?= number_format($result['avg_rating'] ?? 0, 1) ?> 
                                                (<?= $result['total_reviews'] ?? 0 ?> reviews)
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center text-gray-600 text-sm mb-3">
                                            <i class="fas fa-user-circle mr-2"></i>
                                            <span><?= $result['provider_name'] ?></span>
                                        </div>
                                        
                                        <?php if (!empty($result['city']) && !empty($result['state'])): ?>
                                            <div class="flex items-center text-gray-600 text-sm mb-4">
                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                <span><?= $result['city'] . ', ' . $result['state'] ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="flex space-x-2">
                                            <a href="<?php echo BASE_URL; ?>/services/view.php?id=<?= $result['id'] ?>" class="btn btn-primary flex-grow text-center">View Details</a>
                                            <a href="<?php echo BASE_URL; ?>/bookings/create.php?service_id=<?= $result['id'] ?>" class="btn btn-outline flex-grow text-center">Book Now</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-8 flex justify-center">
                            <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-primary-50 text-sm font-medium text-primary-600">2</a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">3</a>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">8</a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">9</a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </nav>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-search fa-3x"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No services found</h3>
                            <p class="text-gray-600 mb-6">Try adjusting your search criteria or browse all categories.</p>
                            <a href="<?php echo BASE_URL; ?>/services/search.php" class="btn btn-outline">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>