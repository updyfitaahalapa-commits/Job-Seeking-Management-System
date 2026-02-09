<?php
require_once 'includes/db.php';

$username = 'admin';
$password = 'admin123';
$email = 'admin@example.com';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        // Update password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        echo "Admin password has been reset to: <strong>$password</strong><br>";
    } else {
        // Insert admin
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$username, $hashed_password, $email]);
        echo "Admin user created with password: <strong>$password</strong><br>";
    }
    echo "Click <a href='login.php'>here</a> to login.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
