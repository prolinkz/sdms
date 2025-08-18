<?php
require_once __DIR__ . '/includes/init.php'; // Include init to get the $auth object

$auth->logout();
header("Location: " . BASE_URL . "index.php?logged_out=true");
exit();
?>