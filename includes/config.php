<?php
session_start();

// Environment-Aware High-Performance Logic
$isVercel = isset($_SERVER['VERCEL_URL']);

// Cloud Database Orchestration (Vercel Variables)
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db   = $_ENV['DB_NAME'] ?? 'nscet_admission_2026';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$charset = 'utf8mb4';

// Secure Dynamic DSN Generation
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If we are live, we need to know WHY it failed
     die("<h1>System Connection Error</h1><p>Our academic server could not reach the database.</p><p><b>Error:</b> " . $e->getMessage() . "</p><p><i>Check your Vercel Environment Variables!</i></p>");
}

function require_login() {
    if (!isset($_SESSION['faculty_email'])) {
        header("Location: login.php");
        exit;
    }
}
?>
