<?php
require_once __DIR__ . '/../config/baseurl.php';

/**
 * Collection of helper functions for the application
 */

/**
 * Sanitize input data
 * 
 * @param string $data
 * @return string
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email format
 * 
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Redirect to another page
 * 
 * @param string $url
 * @return void
 */
function redirect($url) {
    header("Location: " . BASE_URL . ltrim($url, '/'));
    exit;
}

/**
 * Set flash message in session
 * 
 * @param string $type (success, error, info, warning)
 * @param string $message
 * @return void
 */
function setFlashMessage($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Display flash message and clear from session
 * 
 * @return string HTML for flash message
 */
function displayFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message']['message'];
        $type = $_SESSION['flash_message']['type'];
        
        // Clear the flash message
        unset($_SESSION['flash_message']);
        
        // Define classes based on message type
        $bgColor = 'bg-blue-100 border-blue-500 text-blue-700';
        $icon = '<i class="fas fa-info-circle"></i>';
        
        if ($type === 'success') {
            $bgColor = 'bg-green-100 border-green-500 text-green-700';
            $icon = '<i class="fas fa-check-circle"></i>';
        } elseif ($type === 'error') {
            $bgColor = 'bg-red-100 border-red-500 text-red-700';
            $icon = '<i class="fas fa-exclamation-circle"></i>';
        } elseif ($type === 'warning') {
            $bgColor = 'bg-yellow-100 border-yellow-500 text-yellow-700';
            $icon = '<i class="fas fa-exclamation-triangle"></i>';
        }
        
        return '<div class="flash-message border-l-4 p-4 ' . $bgColor . '" role="alert">
                <div class="flex items-center">
                    <div class="mr-2">' . $icon . '</div>
                    <div>' . $message . '</div>
                </div>
            </div>';
    }
    
    return '';
}

/**
 * Redirect with a flash message
 *
 * @param string $url
 * @param string $type (success, error, info, warning)
 * @param string $message
 * @return void
 */
function redirectWithMessage($url, $type, $message) {
    setFlashMessage($type, $message);
    redirect($url);
}

/**
 * Check if user is logged in, if not redirect to login page
 * 
 * @param string $redirectTo
 * @return void
 */
function requireLogin($redirectTo = '/auth/login.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        setFlashMessage('error', 'Please log in to access this page');
        redirect($redirectTo);
    }
}

/**
 * Check if user has specific role, if not redirect
 * 
 * @param array|string $roles
 * @param string $redirectTo
 * @return void
 */
function requireRole($roles, $redirectTo = '/unauthorized.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Convert to array if string
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        redirect($redirectTo);
    }
}

/**
 * Format a date in a human-readable way
 * 
 * @param string $dateString
 * @param string $format
 * @return string
 */
function formatDate($dateString, $format = 'M j, Y') {
    $date = new DateTime($dateString);
    return $date->format($format);
}

/**
 * Format a time in a human-readable way
 * 
 * @param string $timeString
 * @param string $format
 * @return string
 */
function formatTime($timeString, $format = 'g:i A') {
    $time = new DateTime($timeString);
    return $time->format($format);
}

/**
 * Get a list of US states for form dropdowns
 * 
 * @return array
 */
function getStatesList() {
    return [
        'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas',
        'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware',
        'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho',
        'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas',
        'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
        'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi',
        'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
        'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
        'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma',
        'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
        'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah',
        'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia',
        'WI' => 'Wisconsin', 'WY' => 'Wyoming'
    ];
}

/**
 * Generate star rating HTML
 * 
 * @param float $rating
 * @param int $maxRating
 * @return string
 */
function generateStarRating($rating, $maxRating = 5) {
    $fullStar = '<i class="fas fa-star text-yellow-400"></i>';
    $halfStar = '<i class="fas fa-star-half-alt text-yellow-400"></i>';
    $emptyStar = '<i class="far fa-star text-yellow-400"></i>';
    
    $output = '';
    
    // Calculate full, half, and empty stars
    $fullStars = floor($rating);
    $halfStars = ceil($rating - $fullStars);
    $emptyStars = $maxRating - $fullStars - $halfStars;
    
    // Output full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $output .= $fullStar;
    }
    
    // Output half star if there is one
    if ($halfStars) {
        $output .= $halfStar;
    }
    
    // Output empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $output .= $emptyStar;
    }
    
    return $output;
}

/**
 * Calculate distance between two points using Haversine formula
 * 
 * @param float $lat1
 * @param float $lon1
 * @param float $lat2
 * @param float $lon2
 * @return float Distance in miles
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 3958.8; // Radius of the Earth in miles
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;
    
    return round($distance, 1);
}

/**
 * Get day name from day of week number
 * 
 * @param int $dayNum (0 = Sunday, 1 = Monday, etc.)
 * @return string
 */
function getDayName($dayNum) {
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return $days[$dayNum] ?? '';
}
?>