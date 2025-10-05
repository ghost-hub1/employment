<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$auth->redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Reset all progress fields
    $query = "UPDATE users SET 
        offer_accepted = 0, offer_accepted_at = NULL,
        financial_completed = 0, financial_completed_at = NULL,
        payroll_completed = 0, payroll_completed_at = NULL,
        commitment_completed = 0, commitment_completed_at = NULL,
        equipment_ordered = 0, equipment_ordered_at = NULL
        WHERE id = :user_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    header('Location: candidate-dashboard.php');
    exit;
}
?>
