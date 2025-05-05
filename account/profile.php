<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Provider.php';
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

// Initialize objects
$user = new User($db);
$provider = null;

// Get user data
$user->readOne($_SESSION['user_id']);

// If user is a provider, get provider data
if ($_SESSION['role'] === 'provider') {
    $provider = new Provider($db);
    $provider->getByUserId($_SESSION['user_id']);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = false;
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        setFlashMessage('error', 'Email is required');
        $error = true;
    } elseif (!isValidEmail($_POST["email"])) {
        setFlashMessage('error', 'Please enter a valid email address');
        $error = true;
    }
    
    if (!$error) {
        // Update user data
        $user->name = sanitizeInput($_POST["name"]);
        $user->email = sanitizeInput($_POST["email"]);
        $user->phone = sanitizeInput($_POST["phone"]);
        
        if ($user->update()) {
            // If user is a provider, update provider data
            if ($provider) {
                $provider->bio = sanitizeInput($_POST["bio"]);
                $provider->address = sanitizeInput($_POST["address"]);
                $provider->city = sanitizeInput($_POST["city"]);
                $provider->state = sanitizeInput($_POST["state"]);
                $provider->zip_code = sanitizeInput($_POST["zip_code"]);
                
                if ($provider->update()) {
                    setFlashMessage('success', 'Profile updated successfully');
                    redirect('/account/profile.php');
                } else {
                    setFlashMessage('error', 'Error updating provider profile');
                }
            } else {
                setFlashMessage('success', 'Profile updated successfully');
                redirect('/account/profile.php');
            }
        } else {
            setFlashMessage('error', 'Error updating profile');
        }
    }
}

// Include header
include '../includes/header.php';
?>

<div class="bg-gray-50 py-8">
    <div class="container-custom">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Profile Settings</h1>
                    <a href="<?php echo BASE_URL; ?>/account/change-password.php" class="btn btn-outline text-sm">
                        <i class="fas fa-key mr-2"></i> Change Password
                    </a>
                </div>
                
                <?= displayFlashMessage() ?>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" class="space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" id="name" class="form-input" value="<?= $user->name ?>">
                            </div>
                            
                            <div>
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-input" value="<?= $user->email ?>">
                            </div>
                            
                            <div>
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" name="phone" id="phone" class="form-input" value="<?= $user->phone ?>">
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($provider): ?>
                        <!-- Provider Information -->
                        <div class="pt-6 border-t border-gray-200">
                            <h2 class="text-lg font-semibold mb-4">Provider Information</h2>
                            
                            <div class="mb-4">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea name="bio" id="bio" rows="4" class="form-input"><?= $provider->bio ?></textarea>
                                <p class="text-sm text-gray-500 mt-1">Tell customers about yourself and your services.</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="address" class="form-label">Street Address</label>
                                    <input type="text" name="address" id="address" class="form-input" value="<?= $provider->address ?>">
                                </div>
                                
                                <div>
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" name="city" id="city" class="form-input" value="<?= $provider->city ?>">
                                </div>
                                
                                <div>
                                    <label for="state" class="form-label">State</label>
                                    <select name="state" id="state" class="form-input">
                                        <option value="">Select State</option>
                                        <?php foreach (getStatesList() as $code => $name): ?>
                                            <option value="<?= $code ?>" <?= $provider->state === $code ? 'selected' : '' ?>>
                                                <?= $name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="zip_code" class="form-label">ZIP Code</label>
                                    <input type="text" name="zip_code" id="zip_code" class="form-input" value="<?= $provider->zip_code ?>">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="pt-6 border-t border-gray-200">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>