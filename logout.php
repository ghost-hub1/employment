<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Fix: Initialize database connection first
$database = new Database();
$db = $database->getConnection();

// Now create Auth instance with the database connection
$auth = new Auth($db);
$auth->logout();
?>
