<?php
// Include header
include 'includes/header.php';
?>

<div class="bg-gray-50 py-12">
    <div class="container-custom">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">About Local Service Finder</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Connecting skilled service providers with customers in your local area.</p>
        </div>

        <!-- Mission Section -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-12">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-2xl font-bold mb-6">Our Mission</h2>
                <p class="text-gray-700 mb-6">
                    At Local Service Finder, we believe everyone should have easy access to reliable, professional services. 
                    Our platform connects skilled service providers with customers looking for quality services in their area.
                </p>
                <p class="text-gray-700">
                    Whether you need a plumber, electrician, tutor, or any other service professional, we make it easy to find, 
                    book, and review local service providers you can trust.
                </p>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Easy to Find</h3>
                <p class="text-gray-600">Search and filter services based on your specific needs and location.</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-check text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Easy to Book</h3>
                <p class="text-gray-600">Book services directly through our platform with just a few clicks.</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Verified Reviews</h3>
                <p class="text-gray-600">Read genuine reviews from verified customers to make informed decisions.</p>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-primary-600 text-white rounded-lg shadow-sm p-8 mb-12">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold mb-2">1000+</div>
                    <div class="text-primary-100">Service Providers</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">5000+</div>
                    <div class="text-primary-100">Happy Customers</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">20+</div>
                    <div class="text-primary-100">Service Categories</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">10000+</div>
                    <div class="text-primary-100">Completed Services</div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-center mb-8">Our Leadership Team</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg" 
                         alt="John Smith" 
                         class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold mb-1">John Smith</h3>
                    <div class="text-gray-600 mb-3">CEO & Founder</div>
                    <p class="text-gray-600 text-sm">15+ years experience in technology and marketplace platforms.</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <img src="https://images.pexels.com/photos/3796217/pexels-photo-3796217.jpeg" 
                         alt="Sarah Johnson" 
                         class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold mb-1">Sarah Johnson</h3>
                    <div class="text-gray-600 mb-3">COO</div>
                    <p class="text-gray-600 text-sm">10+ years experience in operations and customer service.</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <img src="https://images.pexels.com/photos/3777943/pexels-photo-3777943.jpeg" 
                         alt="Mike Wilson" 
                         class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold mb-1">Mike Wilson</h3>
                    <div class="text-gray-600 mb-3">CTO</div>
                    <p class="text-gray-600 text-sm">12+ years experience in software development.</p>
                </div>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <h2 class="text-2xl font-bold mb-4">Want to Learn More?</h2>
            <p class="text-gray-600 mb-6">Get in touch with our team to learn more about our platform and services.</p>
            <div class="flex justify-center space-x-4">
                <a href="/contact.php" class="btn btn-primary">Contact Us</a>
                <a href="/auth/register.php?role=provider" class="btn btn-outline">Become a Provider</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>