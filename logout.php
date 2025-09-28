<?php
include 'includes/auth.php';

$auth = new Auth($db);
$auth->logout();
?>
