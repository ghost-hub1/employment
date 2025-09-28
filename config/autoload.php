<?php
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'database.php';

// Include Auth class
require_once 'includes/auth.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create Auth instance
$auth = new Auth($db);
?>