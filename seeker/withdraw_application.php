<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $app_id = $_GET['id'];
    $seeker_id = $_SESSION['user_id'];

    // Verify ownership and that it's not already processed (optional, but good practice)
    // For now, allow withdrawing any application owned by the user
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE id = ? AND seeker_id = ?");
    $stmt->execute([$app_id, $seeker_id]);
    
    if ($stmt->fetch()) {
        try {
            // Delete the application
            $delete = $pdo->prepare("DELETE FROM applications WHERE id = ?");
            $delete->execute([$app_id]);
            
            // Optional: You could set a session flash message here
            $_SESSION['success_msg'] = "Application withdrawn successfully.";
        } catch (PDOException $e) {
            // Handle valid errors
        }
    }
}

header("Location: my_applications.php");
exit();
?>
