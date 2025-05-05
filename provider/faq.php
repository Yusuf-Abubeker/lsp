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
            <h1 class="text-4xl font-bold mb-4">Frequently Asked Questions</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Find answers to common questions about providing services on our platform.</p>
        </div>

        <!-- FAQ Categories -->
        <div class="max-w-4xl mx-auto">
            <!-- Getting Started -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6">Getting Started</h2>
                
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">How do I become a service provider?</h3>
                        <p class="text-gray-600">
                            To become a service provider, simply:
                            <ol class="list-decimal ml-5 mt-2 space-y-1">
                                <li>Register for a provider account</li>
                                <li>Complete your profile with required information</li>
                                <li>Add your services and pricing</li>
                                <li>Set your availability</li>
                                <li>Start accepting bookings</li>
                            </ol>
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">What documents do I need to register?</h3>
                        <p class="text-gray-600">
                            Required documents may include:
                            <ul class="list-disc ml-5 mt-2 space-y-1">
                                <li>Government-issued ID</li>
                                <li>Professional certifications (if applicable)</li>
                                <li>Business registration (if applicable)</li>
                                <li>Insurance documentation</li>
                            </ul>
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">How long does verification take?</h3>
                        <p class="text-gray-600">
                            Verification typically takes 1-2 business days. We'll review your documents and profile information to ensure everything meets our standards.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pricing & Payments -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6">Pricing & Payments</h2>
                
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">How much can I charge for my services?</h3>
                        <p class="text-gray-600">
                            You have full control over your pricing. You can set fixed rates or hourly rates based on your services and expertise. We recommend researching market rates in your area to stay competitive.
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">When and how do I get paid?</h3>
                        <p class="text-gray-600">
                            Payments are processed within 24-48 hours after service completion. We offer direct deposit to your bank account or PayPal transfers. You can track all your earnings in your provider dashboard.
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">What fees do you charge?</h3>
                        <p class="text-gray-600">
                            Our service fee varies by plan:
                            <ul class="list-disc ml-5 mt-2 space-y-1">
                                <li>Basic Plan: 10% per transaction</li>
                                <li>Professional Plan: 5% per transaction</li>
                                <li>Premium Plan: 3% per transaction</li>
                            </ul>
                            View our <a href="<?php echo BASE_URL; ?>/provider/pricing.php" class="text-primary-600 hover:text-primary-700">pricing page</a> for more details.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bookings & Schedule -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6">Bookings & Schedule</h2>
                
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">How do I manage my availability?</h3>
                        <p class="text-gray-600">
                            Use our calendar tool to:
                            <ul class="list-disc ml-5 mt-2 space-y-1">
                                <li>Set your regular working hours</li>
                                <li>Block out vacation time</li>
                                <li>Manage appointment slots</li>
                                <li>Handle booking requests</li>
                            </ul>
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">What happens if I need to cancel a booking?</h3>
                        <p class="text-gray-600">
                            While we encourage maintaining all appointments, we understand emergencies happen. You can cancel a booking through your dashboard, but please provide as much notice as possible. Frequent cancellations may affect your profile rating.
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">How far in advance can customers book?</h3>
                        <p class="text-gray-600">
                            You can set your own booking window, from same-day appointments up to several months in advance. Most providers allow bookings 2-4 weeks ahead.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account & Profile -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6">Account & Profile</h2>
                
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">How can I improve my profile ranking?</h3>
                        <p class="text-gray-600">
                            To improve your visibility:
                            <ul class="list-disc ml-5 mt-2 space-y-1">
                                <li>Complete your profile 100%</li>
                                <li>Maintain high ratings and reviews</li>
                                <li>Respond quickly to inquiries</li>
                                <li>Keep your calendar up to date</li>
                                <li>Provide excellent service</li>
                            </ul>
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">Can I offer multiple services?</h3>
                        <p class="text-gray-600">
                            Yes! You can list multiple services under your profile. The number of services you can list depends on your subscription plan:
                            <ul class="list-disc ml-5 mt-2 space-y-1">
                                <li>Basic: Up to 3 services</li>
                                <li>Professional & Premium: Unlimited services</li>
                            </ul>
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-2">How do I handle customer reviews?</h3>
                        <p class="text-gray-600">
                            We encourage responding to all reviews professionally. Thank customers for positive reviews and address any concerns raised in negative reviews constructively. Our support team can help mediate any disputes.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Still Have Questions -->
        <div class="mt-16 bg-white rounded-lg shadow-sm p-8 text-center">
            <h2 class="text-2xl font-bold mb-4">Still Have Questions?</h2>
            
            <p class="text-gray-600 mb-6">Our support team is here to help you succeed on our platform.</p>
            <div class="flex justify-center space-x-4">
                <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-primary">
                    Contact Support
                </a>
                <a href="<?php echo BASE_URL; ?>/provider/how-it-works.php" class="btn btn-outline">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>