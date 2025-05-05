<?php
// Start the session
session_start();

require_once __DIR__ . '/../config/baseurl.php';

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
header("location:". BASE_URL);
exit;
?>