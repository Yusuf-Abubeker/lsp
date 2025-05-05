</main>
    <footer class="bg-gray-800 text-white py-12 mt-8">
        <div class="container-custom">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">LocalServices</h3>
                    <p class="text-gray-300">Find trusted local service providers near you. Home services, professional services, and more.</p>
                    <div class="mt-4 flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="<?php echo BASE_URL; ?>/" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/services/search.php" class="text-gray-300 hover:text-white transition-colors">Find Services</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/about.php" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/contact.php" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">For Providers</h3>
                    <ul class="space-y-2">
                        <li><a href="<?php echo BASE_URL; ?>/auth/register.php?role=provider" class="text-gray-300 hover:text-white transition-colors">Join as Provider</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/provider/how-it-works.php" class="text-gray-300 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/provider/pricing.php" class="text-gray-300 hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/provider/faq.php" class="text-gray-300 hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                            <span>123 Service Street, City, Country</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-2"></i>
                            <span>+1 234 567 890</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>contact@localservices.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> LocalServices. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
    // Add any global JS here
    </script>
</body>
</html>