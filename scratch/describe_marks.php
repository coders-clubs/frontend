<?php
require 'connection/connection.php';
try {
    $stmt = $pdo->query('DESCRIBE marks');
    while($row = $stmt->fetch()){
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
