<?php
// We use direct PDO here because the database in config.php doesn't exist yet
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. CREATE THE 2026 DATABASE
    $pdo->exec("CREATE DATABASE IF NOT EXISTS nscet_admission_2026");
    $pdo->exec("USE nscet_admission_2026");

    // 2. CREATE ADMISSIONS TABLE
    $pdo->exec("CREATE TABLE IF NOT EXISTS admissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        receipt_no VARCHAR(50),
        application_no VARCHAR(50),
        admission_type VARCHAR(50) DEFAULT 'Regular',
        student_name VARCHAR(100),
        gender VARCHAR(10),
        date_of_birth DATE,
        father_name VARCHAR(100),
        father_occupation VARCHAR(100),
        mother_name VARCHAR(100),
        mother_occupation VARCHAR(100),
        address TEXT,
        place VARCHAR(100),
        city VARCHAR(100),
        state VARCHAR(100) DEFAULT 'Tamil Nadu',
        pincode VARCHAR(10),
        cell_1 VARCHAR(20),
        cell_2 VARCHAR(20),
        religion VARCHAR(50),
        community VARCHAR(50),
        caste VARCHAR(50),
        degree VARCHAR(100),
        department VARCHAR(100),
        date_of_joining DATE,
        receipt_date DATE,
        quota VARCHAR(50) DEFAULT 'Merit',
        hostel VARCHAR(10) DEFAULT 'No',
        concession VARCHAR(100),
        bus_stop VARCHAR(100),
        reg_no VARCHAR(50),
        school_name VARCHAR(200),
        percentage DECIMAL(5,2),
        reference VARCHAR(100),
        reference_name VARCHAR(100),
        uravinmurai_letter VARCHAR(10) DEFAULT 'No',
        fees_name VARCHAR(100),
        amount DECIMAL(10,2),
        bill_type VARCHAR(20) DEFAULT 'Cash',
        faculty_email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. CREATE MARKS TABLE
    $pdo->exec("CREATE TABLE IF NOT EXISTS marks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admission_id INT,
        subject_name VARCHAR(100),
        max_marks INT DEFAULT 100,
        marks_obtained INT,
        grade VARCHAR(10),
        FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE CASCADE
    )");

    // 4. CREATE USERS TABLE
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100) UNIQUE,
        password VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. SEED INITIAL ADMIN USER
    $admin_email = 'admin@nscet.edu.in';
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$admin_email]);
    if (!$check->fetch()) {
        $hashed_pass = password_hash('nscet2026', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)")
            ->execute(['NSCET Admin', $admin_email, $hashed_pass]);
        $seeded = true;
    } else {
        $seeded = false;
    }

    echo "<h1>2026 Academic Database Created!</h1>";
    echo "<p>Database <b>nscet_admission_2026</b> is now live and perfectly optimized.</p>";
    if ($seeded) {
        echo "<div style='background: #ecfdf5; padding: 20px; border-radius: 12px; border: 2px solid #a7f3d0;'>";
        echo "<h3>✅ Admin Account Created!</h3>";
        echo "<p><b>Login Email:</b> " . $admin_email . "</p>";
        echo "<p><b>Temp Password:</b> nscet2026</p>";
        echo "<p><i>Please log in and change your password for security.</i></p>";
        echo "</div>";
    }

} catch (PDOException $e) {
    die("Deployment Failed: " . $e->getMessage());
}
?>
