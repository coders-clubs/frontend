<?php
// Set session cookie parameters: lifetime 0 means it expires when the browser is closed.
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false, // Set to true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
date_default_timezone_set('Asia/Kolkata');

// Add inactivity timeout (e.g., 2 hours)
$timeout_duration = 7200; 
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
$_SESSION['last_activity'] = time();

$host = '127.0.0.1';
$db   = 'nscet_admission_2026';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

function require_login() {
    if (!isset($_SESSION['faculty_email'])) {
        header("Location: login.php");
        exit;
    }
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        die("Access Denied: Administrative privileges required.");
    }
}

$centers_list = [
    'nscet' => 'NSCET',
    'uravinmurai' => 'Uravinmurai',
    'tmhnu_boys' => 'TMHNU Boys school',
    'tmhnu_girls' => 'TMHNU girls school',
    'bodinayakanur' => 'Bodinayakanur',
    'usilampatti' => 'Usilampatti',
    'andipatti' => 'Andipatti',
    'periyakulam' => 'Periyakulam',
    'batlagundu' => 'Batlagundu',
    'chinnamanur' => 'Chinnamanur'
];
?>
