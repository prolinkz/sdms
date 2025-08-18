<?php
// Ensure BASE_URL and session are defined.
// This file assumes init.php has already been included.
$current_page_relative_path = getCurrentPageRelativePath();

// Get profile image path
$profile_image_path = BASE_URL . 'assets/images/default_profile.png'; // Default image
if (isset($_SESSION['profile_photo']) && !empty($_SESSION['profile_photo'])) {
    // Assuming profile_photo in session stores relative path from project root
    // Example: $_SESSION['profile_photo'] = 'uploads/profile_pics/user_123.jpg';
    // You'd need to set this when user uploads a photo.
    $profile_image_path = BASE_URL . htmlspecialchars($_SESSION['profile_photo']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> | SDMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <div class="main-content" id="mainContent">
            <div class="top-navbar">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="page-title"><?= $page_heading ?? 'Dashboard' ?></h2>
                <div class="user-info settings-dropdown" id="settingsDropdown">
                    <div class="user-info-toggle">
                        <img src="<?= $profile_image_path ?>" alt="User Profile" class="profile-picture">
                        <div class="user-details">
                            <span class="username"><?= htmlspecialchars($_SESSION['username']) ?></span>
                            <span class="role"><?= htmlspecialchars($_SESSION['role']) ?></span>
                        </div>
                        <i class="fas fa-caret-down dropdown-toggle-icon"></i>
                    </div>
                    
                    <div class="settings-dropdown-menu">
                        <a href="<?= BASE_URL ?>profile.php">Profile Info</a>
                        <a href="<?= BASE_URL ?>change-password.php">Change Password</a>
                        <div class="dark-mode-switch">
                            <i class="fas fa-moon"></i> Dark Mode
                            <label class="switch" style="margin-left: auto;">
                                <input type="checkbox" id="darkModeToggle">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <form action="<?= BASE_URL ?>logout.php" method="POST">
                            <button type="submit">
                                <i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i> Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <!-- Main content for the page will go here -->