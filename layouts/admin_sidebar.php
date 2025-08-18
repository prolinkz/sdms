<?php
// admin_sidebar.php
// Assumes BASE_URL and session are available from init.php
// Assumes $current_page_relative_path is defined in admin_header.php

// Define menu items for the admin sidebar
$admin_menu_items = [
    ['name' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'link' => 'admin/dashboard.php'],
    ['name' => 'User', 'icon' => 'fa-users', 'link' => 'admin/users/index.php'],
    ['name' => 'Student', 'icon' => 'fa-graduation-cap', 'link' => 'admin/students/index.php'],
    ['name' => 'Staff', 'icon' => 'fa-chalkboard-teacher', 'link' => 'admin/staff/index.php'],
    ['name' => 'Parent', 'icon' => 'fa-user-tie', 'link' => 'admin/parents/index.php'],
    ['name' => 'Academic', 'icon' => 'fa-book-open', 'link' => 'admin/academic/index.php'],
    ['name' => 'Attendance', 'icon' => 'fa-clipboard-check', 'link' => 'admin/attendance/index.php'],
    ['name' => 'Fee', 'icon' => 'fa-dollar-sign', 'link' => 'admin/fees/index.php'],
    ['name' => 'Examination', 'icon' => 'fa-file-alt', 'link' => 'admin/exams/index.php'],
    ['name' => 'Notifications', 'icon' => 'fa-bell', 'link' => 'admin/notifications/index.php'],
    ['name' => 'Reports', 'icon' => 'fa-chart-bar', 'link' => 'admin/reports/index.php'],
    ['name' => 'System Settings', 'icon' => 'fa-cogs', 'link' => 'admin/settings/index.php'],
    ['name' => 'Logs', 'icon' => 'fa-history', 'link' => 'admin/logs/index.php'],
];
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Admin Panel</h3>
        <button class="sidebar-toggle-btn" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <!-- <p>Student Database Management</p> Removed for now to align with image -->
    </div>
    <ul class="sidebar-menu">
        <?php foreach ($admin_menu_items as $item): ?>
            <li>
                <a href="<?= BASE_URL . $item['link'] ?>" class="<?= isActiveLink($item['link'], $current_page_relative_path) ?>">
                    <i class="fas <?= $item['icon'] ?>"></i>
                    <span><?= $item['name'] ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="sidebar-footer">
        <form action="<?= BASE_URL ?>logout.php" method="POST">
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </button>
        </form>
    </div>
</div>