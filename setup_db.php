<?php
// setup_db.php

$host = '127.0.0.1';
$user = 'root'; // default XAMPP/MySQL
$pass = '';

try {
    $pdoTemp = new PDO("mysql:host=$host", $user, $pass);
    $pdoTemp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdoTemp->exec("CREATE DATABASE IF NOT EXISTS college_admissions CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database created successfully or already exists.<br>";

    // Connect to the specific DB
    $pdoTemp->exec("USE college_admissions");

    // Create Users table
    $usersTable = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdoTemp->exec($usersTable);
    echo "Users table created.<br>";

    // Create a default admin/faculty user if none exists
    $stmt = $pdoTemp->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $stmtInsert = $pdoTemp->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
        $stmtInsert->execute(['admin@college.edu', $hashedPassword, 'Admin Faculty']);
        echo "Default faculty user created (admin@college.edu / password123).<br>";
    }

    // Create Admissions table
    $admissionsTable = "
    CREATE TABLE IF NOT EXISTS admissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        receipt_no VARCHAR(50) UNIQUE,
        admission_type VARCHAR(50),
        
        student_name VARCHAR(100),
        gender VARCHAR(10),
        father_name VARCHAR(100),
        mother_name VARCHAR(100),
        address TEXT,
        city VARCHAR(50),
        pincode VARCHAR(10),
        cell_1 VARCHAR(15),
        cell_2 VARCHAR(15),
        community VARCHAR(50),
        religion VARCHAR(50),
        date_of_birth DATE,
        caste VARCHAR(50),
        father_occupation VARCHAR(100),
        mother_occupation VARCHAR(100),
        
        application_no VARCHAR(50) UNIQUE,
        department VARCHAR(100),
        quota VARCHAR(50),
        concession VARCHAR(50),
        
        admission_no VARCHAR(50),
        date_of_joining DATE,
        bus_stop VARCHAR(100),
        bus_route_no VARCHAR(50),
        degree VARCHAR(100),
        hostel VARCHAR(10),

        faculty_email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdoTemp->exec($admissionsTable);
    echo "Admissions table created.<br>";

    echo "<br><b>Database setup complete. <a href='login.php'>Go to login</a></b>";

} catch (PDOException $e) {
    die("DB SETUP FAILED: " . $e->getMessage());
}
?>
