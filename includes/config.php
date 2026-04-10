<?php
session_start();

// Final "Total Visibility" Detection
function get_db_var($name) {
    if (isset($_ENV[$name])) return $_ENV[$name];
    if (isset($_SERVER[$name])) return $_SERVER[$name];
    if (getenv($name)) return getenv($name);
    return null;
}

$host = get_db_var('DB_HOST');
$db   = get_db_var('DB_NAME');
$user = get_db_var('DB_USER');
$pass = get_db_var('DB_PASS');

$isVercel = isset($_SERVER['VERCEL_URL']);

// Fallback to local only if NOT on Vercel
if (!$host) {
    if ($isVercel) {
        die("<h1>Critical Cloud Error</h1><p>Vercel is not reading your Environment Variables.</p><ul><li>Go to Vercel Settings -> Environment Variables</li><li>Ensure <b>DB_HOST</b>, <b>DB_NAME</b>, <b>DB_USER</b>, and <b>DB_PASS</b> are added.</li><li><b>IMPORTANT:</b> Click 'Redeploy' for changes to take effect!</li></ul>");
    }
    $host = '127.0.0.1';
    $db   = 'nscet_admission_2026';
    $user = 'root';
    $pass = '';
}

// Master Institutional Connection
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (\PDOException $e) {
    die("<h1>System Connection Error</h1><p><b>Target Host:</b> $host</p><p><b>Error:</b> " . $e->getMessage() . "</p><p><i>Verify credentials in Vercel Dashboard!</i></p>");
}

function require_login() {
    if (!isset($_SESSION['faculty_email'])) {
        header("Location: login.php");
        exit;
    }
}
?>
