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

// Get the role from the URL parameter
$role = isset($_GET['role']) ? $_GET['role'] : 'customer';

// Validate role
if (!in_array($role, ['customer', 'provider'])) {
    $role = 'customer';
}

// Database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Define variables and set to empty values
$name = $email = $password = $confirm_password = $phone = '';
$name_err = $email_err = $password_err = $confirm_password_err = $phone_err = '';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name";
    } else {
        $name = sanitizeInput($_POST["name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email";
    } else {
        $email = sanitizeInput($_POST["email"]);
        if (!isValidEmail($email)) {
            $email_err = "Please enter a valid email address";
        } else {
            // Check if email exists
            $user->email = $email;
            if ($user->emailExists()) {
                $email_err = "This email is already registered";
            }
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Passwords do not match";
        }
    }
    
    // Validate phone (optional)
    if (!empty(trim($_POST["phone"]))) {
        $phone = sanitizeInput($_POST["phone"]);
    }
    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        
        // Set user properties
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->role = $role;
        $user->phone = $phone;
        
        // Create the user
        if ($user->register()) {
            // Set session variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['name'] = $user->name;
            $_SESSION['email'] = $user->email;
            $_SESSION['role'] = $user->role;
            
            // Redirect based on role
            if ($role === 'provider') {
                setFlashMessage('success', 'Account created successfully! Complete your provider profile to get started.');
                redirect('/provider/profile.php');
            } else {
                setFlashMessage('success', 'Account created successfully! You can now browse and book services.');
                redirect('/customer/dashboard.php');
            }
        } else {
            setFlashMessage('error', 'Something went wrong. Please try again later.');
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
                    <h1 class="text-2xl font-bold">Create an Account</h1>
                    <p class="text-gray-600 mt-2">Join as a <?= $role === 'provider' ? 'Service Provider' : 'Customer' ?></p>
                </div>
                
                <?= displayFlashMessage() ?>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) . '?role=' . $role ?>" method="post" class="space-y-4">
                    <div>
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-input <?= !empty($name_err) ? 'border-red-500' : '' ?>" value="<?= $name ?>">
                        <?php if (!empty($name_err)): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $name_err ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-input <?= !empty($email_err) ? 'border-red-500' : '' ?>" value="<?= $email ?>">
                        <?php if (!empty($email_err)): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $email_err ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="phone" class="form-label">Phone Number (Optional)</label>
                        <input type="tel" name="phone" id="phone" class="form-input <?= !empty($phone_err) ? 'border-red-500' : '' ?>" value="<?= $phone ?>">
                        <?php if (!empty($phone_err)): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $phone_err ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-input <?= !empty($password_err) ? 'border-red-500' : '' ?>">
                        <?php if (!empty($password_err)): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $password_err ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input <?= !empty($confirm_password_err) ? 'border-red-500' : '' ?>">
                        <?php if (!empty($confirm_password_err)): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $confirm_password_err ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary w-full">Create Account</button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Already have an account? 
                        <a href="<?php echo BASE_URL; ?>/auth/login.php" class="text-primary-600 hover:text-primary-700 font-medium">Log In</a>
                    </p>
                </div>
                
                <?php if ($role === 'customer'): ?>
                    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                        <p class="text-gray-600 mb-2">Want to offer your services?</p>
                        <a href="<?php echo BASE_URL; ?>/auth/register.php?role=provider" class="btn btn-outline">Register as a Provider</a>
                    </div>
                <?php else: ?>
                    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                        <p class="text-gray-600 mb-2">Looking for services instead?</p>
                        <a href="<?php echo BASE_URL; ?>/auth/register.php?role=customer" class="btn btn-outline">Register as a Customer</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>