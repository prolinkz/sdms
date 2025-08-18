<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/helpers.php'; // Ensure helpers are included

// Check if user is logged in AND has the 'Admin' or 'Administrator' role
if (!$auth->isLoggedIn() || !in_array($_SESSION['role'], ['Admin', 'Administrator'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

$page_title = "Admin Dashboard";
$page_heading = "Dashboard"; // Used in the top-navbar
?>

<?php include __DIR__ . '/../layouts/admin_header.php'; ?>

<!-- Main content of the dashboard starts here -->
    <h1>Welcome to Admin Dashboard!</h1>
    <p class="welcome-message">Use the sidebar to navigate through management sections.</p>
    
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Users</h3>
            <p class="value purple">...</p>
        </div>
        <div class="card">
            <h3>Pending Approvals</h3>
            <p class="value red">...</p>
        </div>
        <div class="card">
            <h3>Total Students</h3>
            <p class="value blue">...</p>
        </div>
        <!-- Add more cards as needed -->
    </div>

<?php include __DIR__ . '/../layouts/admin_footer.php'; ?>