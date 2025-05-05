<?php
// Include necessary files
require_once '../config/database.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-12">
    <div class="container-custom">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">How It Works - For Service Providers</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Learn how to grow your business by joining our platform as a service provider.</p>
        </div>

        <!-- Steps Section -->
        <div class="max-w-4xl mx-auto">
            <div class="grid gap-8">
                <!-- Step 1 -->
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="flex items-start">
                        <div class="bg-primary-100 text-primary-600 w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold mr-6 flex-shrink-0">
                            1
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-3">Create Your Account</h3>
                            <p class="text-gray-600 mb-4">
                                Sign up as a service provider with your basic information. Complete your profile with:
                            </p>
                            <ul class="space-y-2 text-gray-600">
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Professional photo</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Detailed bio and experience</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Service area and location</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Contact information</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="flex items-start">
                        <div class="bg-primary-100 text-primary-600 w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold mr-6 flex-shrink-0">
                            2
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-3">List Your Services</h3>
                            <p class="text-gray-600 mb-4">
                                Add the services you offer with detailed descriptions and pricing:
                            </p>
                            <ul class="space-y-2 text-gray-600">
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Choose service categories</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Set competitive pricing</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Define service scope</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Add service photos</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="flex items-start">
                        <div class="bg-primary-100 text-primary-600 w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold mr-6 flex-shrink-0">
                            3
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-3">Manage Your Schedule</h3>
                            <p class="text-gray-600 mb-4">
                                Set your availability and manage bookings efficiently:
                            </p>
                            <ul class="space-y-2 text-gray-600">
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Set working hours</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Block out unavailable times</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Receive booking notifications</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Confirm or reschedule appointments</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="flex items-start">
                        <div class="bg-primary-100 text-primary-600 w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold mr-6 flex-shrink-0">
                            4
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-3">Deliver Great Service</h3>
                            <p class="text-gray-600 mb-4">
                                Provide excellent service and build your reputation:
                            </p>
                            <ul class="space-y-2 text-gray-600">
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Communicate professionally</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Complete jobs on time</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Collect reviews and ratings</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> Build your client base</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-center mb-8">Benefits of Joining Our Platform</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Reach More Customers</h3>
                    <p class="text-gray-600">Connect with customers actively looking for your services in your area.</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Easy Scheduling</h3>
                    <p class="text-gray-600">Manage your bookings and schedule efficiently through our platform.</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Build Your Reputation</h3>
                    <p class="text-gray-600">Collect reviews and ratings to showcase your quality service.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="mt-16 bg-primary-600 rounded-lg p-8 text-center text-white">
            <h2 class="text-2xl font-bold mb-4">Ready to Grow Your Business?</h2>
            <p class="text-primary-100 mb-6">Join our community of successful service providers today.</p>
            <div class="flex justify-center space-x-4">
                <a href="<?php echo BASE_URL; ?>/auth/register.php?role=provider" class="btn bg-white text-primary-600 hover:bg-primary-50">
                    Register as Provider
                </a>
                <a href="<?php echo BASE_URL; ?>/contact.php" class="btn bg-primary-700 text-white hover:bg-primary-800">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>