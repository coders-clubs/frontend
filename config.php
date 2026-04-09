<?php
// config.php
session_start();

$host = '127.0.0.1';
$db   = 'college_admissions';
$user = 'root'; // default XAMPP/MySQL
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Note: If you run setup_db.php, it connects without dbname first.
    // For normal app use, this requires the db to exist.
    // We suppress the error here so setup_db can include config for credentials.
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        // App will fail if trying to query, but let setup_db handle creations.
        $pdo = null;
    } else {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}

// Ensure faculty is logged in (unless we're on login pages)
function require_login() {
    if (!isset($_SESSION['faculty_email'])) {
        header('Location: login.php');
        exit;
    }
}
?>
