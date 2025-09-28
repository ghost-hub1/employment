<?php
require_once __DIR__ . '/includes/auth.php';

$auth = new Auth($db);
$auth->logout();
?>
