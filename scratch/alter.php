<?php
require '../connection/connection.php';

$columns = [
    'record_type' => "VARCHAR(50) DEFAULT 'Application'",
    'transaction_id' => "VARCHAR(100) NULL",
    'scheme_7_5' => "VARCHAR(10) DEFAULT 'No'",
    'place_of_school' => "VARCHAR(255) NULL",
    'exam_no' => "VARCHAR(100) NULL"
];

foreach ($columns as $col => $def) {
    try {
        $pdo->exec("ALTER TABLE admissions ADD COLUMN $col $def");
        echo "Added $col\n";
    } catch (PDOException $e) {
        if ($e->getCode() == '42S21') {
            echo "Column $col already exists\n";
        } else {
            echo "Error adding $col: " . $e->getMessage() . "\n";
        }
    }
}
?>
