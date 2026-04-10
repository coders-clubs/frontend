<?php
session_start();

// Ultra-Resilient Cloud Environment Detection
function get_db_var($name, $default = '') {
    return $_ENV[$name] ?? getenv($name) ?? $_SERVER[$name] ?? $default;
}

$host = get_db_var('DB_HOST', '127.0.0.1');
$db   = get_db_var('DB_NAME', 'nscet_admission_2026');
$user = get_db_var('DB_USER', 'root');
$pass = get_db_var('DB_PASS', '');
$charset = 'utf8mb4';

// Secure Dynamic DSN Generation
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Define $pdo as null initially to prevent "Undefined" errors
$pdo = null;

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("<h1>System Connection Error</h1><p><b>Host detected:</b> $host</p><p><b>Error:</b> " . $e->getMessage() . "</p><p><i>Verify your Vercel Environment Variables!</i></p>");
}

function require_login() {
    if (!isset($_SESSION['faculty_email'])) {
        header("Location: login.php");
        exit;
    }
}
?>
