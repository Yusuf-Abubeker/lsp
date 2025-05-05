<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../config/baseurl.php';


// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Service Finder</title>
    <link rel="stylesheet" href="/lsp/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-white shadow-sm">
        <nav class="container-custom py-4" x-data="{ mobileMenuOpen: false }">
            <div class="flex justify-between items-center">
                <a href="<?php echo BASE_URL; ?>/" class="flex items-center space-x-2">
                    <span class="text-primary-600 text-2xl"><i class="fas fa-search-location"></i></span>
                    <span class="font-bold text-xl text-gray-900">LocalServices</span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="<?php echo BASE_URL; ?>" class="text-gray-700 hover:text-primary-600 transition-colors duration-200">Home</a>
                    <a href="<?php echo BASE_URL; ?>/services/search.php" class="text-gray-700 hover:text-primary-600 transition-colors duration-200">Find Services</a>
                    <a href="<?php echo BASE_URL; ?>/about.php" class="text-gray-700 hover:text-primary-600 transition-colors duration-200">About</a>
                    
                    <?php if(isLoggedIn()): ?>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-primary-600 focus:outline-none">
                                <span>My Account</span>
                                <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-lg z-20">
                                <?php if(getUserRole() === 'customer'): ?>
                                    <a href="<?php echo BASE_URL; ?>/customer/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <a href="<?php echo BASE_URL; ?>/customer/bookings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Bookings</a>
                                <?php elseif(getUserRole() === 'provider'): ?>
                                    <a href="<?php echo BASE_URL; ?>/provider/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <a href="<?php echo BASE_URL; ?>/provider/services.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Services</a>
                                    <a href="<?php echo BASE_URL; ?>/provider/bookings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Booking Requests</a>
                                <?php elseif(getUserRole() === 'admin'): ?>
                                    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                <?php endif; ?>
                                <a href="<?php echo BASE_URL; ?>/account/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Settings</a>
                                <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/auth/login.php" class="text-gray-700 hover:text-primary-600 transition-colors duration-200">Login</a>
                        <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-6 w-6" x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" class="md:hidden mt-4 pb-4">
                <a href="<?php echo BASE_URL; ?>/" class="block py-2 text-gray-700 hover:text-primary-600">Home</a>
                <a href="<?php echo BASE_URL; ?>/services/search.php" class="block py-2 text-gray-700 hover:text-primary-600">Find Services</a>
                <a href="<?php echo BASE_URL; ?>/about.php" class="block py-2 text-gray-700 hover:text-primary-600">About</a>
                
                <?php if(isLoggedIn()): ?>
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <p class="py-2 text-sm text-gray-500">My Account</p>
                        
                        <?php if(getUserRole() === 'customer'): ?>
                            <a href="<?php echo BASE_URL; ?>/customer/dashboard.php" class="block py-2 text-gray-700 hover:text-primary-600">Dashboard</a>
                            <a href="<?php echo BASE_URL; ?>/customer/bookings.php" class="block py-2 text-gray-700 hover:text-primary-600">My Bookings</a>
                        <?php elseif(getUserRole() === 'provider'): ?>
                            <a href="<?php echo BASE_URL; ?>/provider/dashboard.php" class="block py-2 text-gray-700 hover:text-primary-600">Dashboard</a>
                            <a href="<?php echo BASE_URL; ?>/provider/services.php" class="block py-2 text-gray-700 hover:text-primary-600">My Services</a>
                            <a href="<?php echo BASE_URL; ?>/provider/bookings.php" class="block py-2 text-gray-700 hover:text-primary-600">Booking Requests</a>
                        <?php elseif(getUserRole() === 'admin'): ?>
                            <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="block py-2 text-gray-700 hover:text-primary-600">Admin Dashboard</a>
                        <?php endif; ?>
                        
                        <a href="<?php echo BASE_URL; ?>/account/profile.php" class="block py-2 text-gray-700 hover:text-primary-600">Profile Settings</a>
                        <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="block py-2 text-red-600 hover:text-red-700">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <a href="<?php echo BASE_URL; ?>/auth/login.php" class="block py-2 text-gray-700 hover:text-primary-600">Login</a>
                        <a href="<?php echo BASE_URL; ?>/auth/register.php" class="block py-2 text-primary-600 font-medium hover:text-primary-700">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="flex-grow">