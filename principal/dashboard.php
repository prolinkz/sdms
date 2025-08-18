<?php
require_once __DIR__ . '/../includes/init.php';

if (!$auth->isLoggedIn() || $_SESSION['role'] !== 'Principal') {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Dashboard | SDMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css"> 
    <style> /* Add specific styles for Principal dashboard if needed */ </style>
</head>
<body>
    <div class="dashboard-container">
        <form action="<?= BASE_URL ?>logout.php" method="POST">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
        <h1>Welcome, Principal Dashboard</h1>
        <p class="welcome-message">Hello, <?= htmlspecialchars($_SESSION['username']) ?>! You are logged in as the Principal.</p>
        
        <div class="dashboard-links">
            <a href="#">View All Reports</a>
            <a href="#">Approve Leaves</a>
            <a href="#">Manage Staff</a>
            <!-- Add more principal-specific links here -->
        </div>
    </div>
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>