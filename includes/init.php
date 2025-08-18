<?php
// Ensure session is started at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default timezone (CRITICAL: Replace with your actual timezone)
date_default_timezone_set('Asia/Kolkata'); // Example: 'America/New_York' or 'Europe/London'

// Error Reporting for development (adjust for production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define BASE_URL (CRITICAL: Replace with your actual project URL)
// Example: if your project is at http://localhost/sdms/, set it to 'http://localhost/sdms/'
// Make sure it ends with a slash if it points to a directory.
define('BASE_URL', 'http://localhost/sdms/'); 

// Include core files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/helpers.php'; // Ensure helpers are included For isActiveLink, etc
require_once __DIR__ . '/UserManager.php'; // <--- NEW: Include UserManager

// Instantiate common classes and establish database connection
$database = new Database();
$db_conn = null;
try {
    $db_conn = $database->connect(); // Get the PDO connection object
} catch (Exception $e) {
    error_log("CRITICAL: Database connection failed in init.php - " . $e->getMessage());
    die("<h1>Application Error</h1><p>A critical system error occurred. Please try again later or contact support.</p>");
}

// Instantiate Auth class, injecting the database connection
$auth = new Auth($db_conn); 

// --- THIS LINE IS CRUCIAL ---
$user_manager = new UserManager($db_conn); // Ensure this line exists and is uncommented

// Session activity update for logged-in users to prevent premature timeout
if ($auth->isLoggedIn()) {
    $_SESSION['last_activity'] = time();
    
    // You could also fetch and store profile_photo in session upon login if not already
    // For now, we'll assume it's set elsewhere or handled by the default.
    // Example (if 'photo_url' exists in 'users' table or linked profile table):
    // if (isset($_SESSION['user_id'])) {
    //     $stmt = $db_conn->prepare("SELECT photo_url FROM users WHERE user_id = :user_id");
    //     $stmt->execute([':user_id' => $_SESSION['user_id']]);
    //     $user_photo = $stmt->fetchColumn();
    //     if ($user_photo) {
    //         $_SESSION['profile_photo'] = $user_photo;
    //     }
    // }
}
?>