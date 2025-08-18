<?php
// helpers.php

// Function to check if a navigation link is currently active
function isActiveLink($page_name, $current_page) {
    // Compare the current script name with the page name for the link
    // basename($_SERVER['PHP_SELF']) gets 'dashboard.php' from '/admin/dashboard.php'
    return basename($current_page) === basename($page_name) ? 'active' : '';
}

// Function to get current page relative path for active link checking
function getCurrentPageRelativePath() {
    // Get the full request URI
    $requestUri = $_SERVER['REQUEST_URI'];
    // Get the base path of the application (e.g., /sdms/)
    $baseUrl = rtrim(parse_url(BASE_URL, PHP_URL_PATH), '/');
    // Remove the base path to get the relative path
    $relativePath = substr($requestUri, strlen($baseUrl));
    // Remove query string parameters
    $relativePath = strtok($relativePath, '?');
    // Ensure it starts with a slash
    if (empty($relativePath) || $relativePath[0] !== '/') {
        $relativePath = '/' . $relativePath;
    }
    return $relativePath;
}
?>