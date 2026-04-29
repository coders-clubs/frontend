<?php
require '../connection/connection.php';

try {
    // 1. Add dept_choice_2 and dept_choice_3 to admissions if they don't exist
    $pdo->exec("ALTER TABLE admissions ADD COLUMN dept_choice_2 VARCHAR(100) DEFAULT NULL");
    $pdo->exec("ALTER TABLE admissions ADD COLUMN dept_choice_3 VARCHAR(100) DEFAULT NULL");
    
    // 2. Create vocational_marks table
    $pdo->exec("CREATE TABLE IF NOT EXISTS vocational_marks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admission_id INT NOT NULL,
        receipt_no VARCHAR(50),
        student_name VARCHAR(150),
        maths_obt DECIMAL(5,2) DEFAULT 0,
        theory_1_name VARCHAR(100) DEFAULT 'Theory 1',
        theory_1_obt DECIMAL(5,2) DEFAULT 0,
        practical_1_name VARCHAR(100) DEFAULT 'Practical 1',
        practical_1_obt DECIMAL(5,2) DEFAULT 0,
        total_obt DECIMAL(5,2) DEFAULT 0,
        cutoff DECIMAL(5,2) DEFAULT 0,
        FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE CASCADE
    )");

    echo "Success";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
