<?php
require 'includes/config.php';

try {
    // Connect to MySQL root
    $pdo = new PDO("mysql:host=127.0.0.1", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS college_admission");
    $pdo->exec("USE college_admission");

    // Create table with all required fields
    $sql = "CREATE TABLE IF NOT EXISTS admissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        receipt_no VARCHAR(50),
        admission_type VARCHAR(50),
        student_name VARCHAR(100),
        gender VARCHAR(10),
        date_of_birth DATE,
        father_name VARCHAR(100),
        father_occupation VARCHAR(100),
        mother_name VARCHAR(100),
        mother_occupation VARCHAR(100),
        address TEXT,
        city VARCHAR(50),
        pincode VARCHAR(10),
        cell_1 VARCHAR(15),
        cell_2 VARCHAR(15),
        religion VARCHAR(50),
        community VARCHAR(50),
        caste VARCHAR(50),
        application_no VARCHAR(50),
        department VARCHAR(50),
        quota VARCHAR(50),
        concession VARCHAR(100),
        admission_no VARCHAR(50),
        date_of_joining DATE,
        degree VARCHAR(50),
        hostel VARCHAR(10),
        bus_stop VARCHAR(100),
        bus_route_no VARCHAR(50),
        faculty_email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    echo "Database and table created successfully in phpMyAdmin!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
