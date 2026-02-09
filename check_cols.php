<?php
require_once 'includes/db.php';

try {
    $stmt = $pdo->query("DESCRIBE jobs");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in jobs table: " . implode(", ", $columns);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
