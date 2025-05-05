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
            <h1 class="text-4xl font-bold mb-4">Simple, Transparent Pricing</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Choose the plan that best fits your business needs and start growing with us.</p>
        </div>

        <!-- Pricing Plans -->
        <div class="max-w-5xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Basic Plan -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Basic</h3>
                        <div class="text-gray-600 mb-4">Perfect for getting started</div>
                        <div class="text-3xl font-bold mb-6">
                            $0<span class="text-lg text-gray-600">/month</span>
                        </div>
                        
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>List up to 3 services</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Basic profile</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Standard support</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>10% service fee</span>
                            </li>
                        </ul>
                        
                        <a href="<?php echo BASE_URL; ?>/auth/register.php?role=provider" class="btn btn-outline w-full">Get Started</a>
                    </div>
                </div>

                <!-- Pro Plan -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform scale-105 relative">
                    <div class="absolute top-0 right-0 bg-primary-600 text-white px-4 py-1 rounded-bl-lg text-sm">
                        Popular
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Professional</h3>
                        <div class="text-gray-600 mb-4">For growing businesses</div>
                        <div class="text-3xl font-bold mb-6">
                            $29<span class="text-lg text-gray-600">/month</span>
                        </div>
                        
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Unlimited services</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Featured profile</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Priority support</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>5% service fee</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Analytics dashboard</span>
                            </li>
                        </ul>
                        
                        <a href="<?php echo BASE_URL; ?>/auth/register.php?role=provider" class="btn btn-primary w-full">Get Started</a>
                    </div>
                </div>

                <!-- Premium Plan -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Premium</h3>
                        <div class="text-gray-600 mb-4">For established businesses</div>
                        <div class="text-3xl font-bold mb-6">
                            $99<span class="text-lg text-gray-600">/month</span>
                        </div>
                        
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Everything in Pro</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>3% service fee</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Dedicated account manager</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Marketing tools</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>API access</span>
                            </li>
                        </ul>
                        
                        <a href="<?php echo BASE_URL; ?>/auth/register.php?role=provider" class="btn btn-outline w-full">Get Started</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Comparison -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-center mb-8">Compare Features</h2>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Feature
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Basic
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Professional
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Premium
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Number of Services</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">Up to 3</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">Unlimited</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">Unlimited</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Service Fee</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">10%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">5%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">3%</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Featured Profile</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center"><i class="fas fa-times text-red-500"></i></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center"><i class="fas fa-check text-green-500"></i></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Analytics Dashboard</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center"><i class="fas fa-times text-red-500"></i></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center"><i class="fas fa-check text-green-500"></i></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Support Level</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">Standard</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">Priority</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">Dedicated</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-center mb-8">Frequently Asked Questions</h2>
            
            <div class="max-w-3xl mx-auto space-y-4">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-2">Can I change plans later?</h3>
                    <p class="text-gray-600">Yes, you can upgrade or downgrade your plan at any time. Changes will take effect at the start of your next billing cycle.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-2">What payment methods do you accept?</h3>
                    <p class="text-gray-600">We accept all major credit cards, PayPal, and bank transfers for professional and premium plans.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-2">Is there a contract or commitment?</h3>
                    <p class="text-gray-600">No, all plans are month-to-month with no long-term commitment. You can cancel at any time.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="mt-16 bg-primary-600 rounded-lg p-8 text-center text-white">
            <h2 class="text-2xl font-bold mb-4">Start Growing Your Business Today</h2>
            <p class="text-primary-100 mb-6">Join thousands of successful service providers on our platform.</p>
            <div class="flex justify-center space-x-4">
                <a href="<?php echo BASE_URL; ?>/auth/register.php?role=provider" class="btn bg-white text-primary-600 hover:bg-primary-50">
                    Get Started
                </a>
                <a href="<?php echo BASE_URL; ?>/contact.php" class="btn bg-primary-700 text-white hover:bg-primary-800">
                    Contact Sales
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>