<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../utils/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
requireLogin();

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = false;
    
    // Validate current password
    if (empty(trim($_POST["current_password"]))) {
        setFlashMessage('error', 'Please enter your current password');
        $error = true;
    }
    
    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        setFlashMessage('error', 'Please enter a new password');
        $error = true;
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        setFlashMessage('error', 'Password must have at least 6 characters');
        $error = true;
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        setFlashMessage('error', 'Please confirm your new password');
        $error = true;
    } elseif (trim($_POST["new_password"]) != trim($_POST["confirm_password"])) {
        setFlashMessage('error', 'Passwords do not match');
        $error = true;
    }
    
    if (!$error) {
        // Get user data
        $user->readOne($_SESSION['user_id']);
        // Verify current password
        if (password_verify($_POST["current_password"], $user->password)) {
            // Update password
            if ($user->updatePassword($_POST["new_password"])) {
                setFlashMessage('success', 'Password updated successfully');
                redirect('/account/profile.php');
            } else {
                setFlashMessage('error', 'Error updating password');
            }
        } else {
            setFlashMessage('error', 'Current password is incorrect');
        }
    }
}

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <a href="<?php echo BASE_URL; ?>/account/profile.php" class="text-gray-500 hover:text-gray-700 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold">Change Password</h1>
                </div>
                
                <?= displayFlashMessage() ?>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" class="space-y-4">
                    <div>
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-input">
                    </div>
                    
                    <div>
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-input">
                        <p class="text-sm text-gray-500 mt-1">Must be at least 6 characters long</p>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input">
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary w-full">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>