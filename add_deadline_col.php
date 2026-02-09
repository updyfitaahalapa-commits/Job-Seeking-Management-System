<?php
require_once 'includes/db.php';

try {
    $sql = "ALTER TABLE jobs ADD COLUMN deadline DATETIME NULL AFTER created_at";
    $pdo->exec($sql);
    echo "Column 'deadline' added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
