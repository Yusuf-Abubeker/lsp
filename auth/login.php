<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
    redirect('/');
}

// Database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Define variables and set to empty values
$email = $password = '';
$email_err = $password_err = $login_err = '';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email";
    } else {
        $email = sanitizeInput($_POST["email"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
        // Attempt to log in
        if ($user->login($email, $password)) {
            // Login successful
            // Set session variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['name'] = $user->name;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $user->role;
            
            // Redirect based on role
            if ($user->role === 'admin') {
                redirect('/admin/dashboard.php');
            } elseif ($user->role === 'provider') {
                redirect('/provider/dashboard.php');
            } else {
                redirect('/customer/dashboard.php');
            }
        } else {
            // Login failed
            $login_err = "Invalid email or password";
        }
    }
}

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-12">
    <div class="container-custom">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold">Log In to Your Account</h1>
                    <p class="text-gray-600 mt-2">Welcome back! Please enter your credentials.</p>
                </div>
                
                <?= displayFlashMessage() ?>
                
                <?php if (!empty($login_err)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p><?= $login_err ?></p>
                    </div>
                <?php endif; ?>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" class="space-y-4">
                    <div>
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-input <?= !empty($email_err) ? 'border-red-500' : '' ?>" value="<?= $email ?>">
                        <?php if (!empty($email_err)): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $email_err ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-input <?= !empty($password_err) ? 'border-red-500' : '' ?>">
                        <?php if (!empty($password_err)): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $password_err ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-primary-600 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        
                        <div>
                            <a href="<?php echo BASE_URL; ?>/auth/forgot-password.php" class="text-sm text-primary-600 hover:text-primary-700">Forgot your password?</a>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary w-full">Log In</button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Don't have an account? 
                        <a href="<?php echo BASE_URL; ?>/auth/register.php" class="text-primary-600 hover:text-primary-700 font-medium">Sign Up</a>
                    </p>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <p class="text-gray-600 mb-2">Quick Access (Demo)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="document.getElementById('email').value='admin@example.com';document.getElementById('password').value='admin123';" 
                                class="text-sm btn btn-outline py-1">Admin Demo</button>
                        <button onclick="document.getElementById('email').value='provider@example.com';document.getElementById('password').value='password123';" 
                                class="text-sm btn btn-outline py-1">Provider Demo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>