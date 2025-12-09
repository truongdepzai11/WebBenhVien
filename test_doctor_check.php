<?php
session_start();
require_once 'config/database.php';
require_once 'app/Helpers/Auth.php';
require_once 'app/Models/Doctor.php';

// Get current user from session
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    echo "User ID: $userId\n";
    echo "Is Doctor: " . (Auth::isDoctor() ? 'YES' : 'NO') . "\n";
    echo "Is Admin: " . (Auth::isAdmin() ? 'YES' : 'NO') . "\n";
    echo "Is Patient: " . (Auth::isPatient() ? 'YES' : 'NO') . "\n";
    
    // Check database
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare('SELECT id, role FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nDatabase User:\n";
    var_dump($user);
} else {
    echo "No user logged in\n";
}
?>
