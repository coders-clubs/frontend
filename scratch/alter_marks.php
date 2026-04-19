<?php
require 'connection/connection.php';
try {
    $pdo->exec("ALTER TABLE marks ADD COLUMN student_name VARCHAR(255)");
    $pdo->exec("ALTER TABLE marks ADD COLUMN receipt_no VARCHAR(50)");
    $pdo->exec("ALTER TABLE marks ADD COLUMN department VARCHAR(100)");
    $pdo->exec("ALTER TABLE marks ADD COLUMN school_name VARCHAR(255)");
    echo "Success: Table 'marks' altered successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
