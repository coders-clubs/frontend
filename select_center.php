<?php
session_start();
require 'connection/config.php';
require_login();

$centers = [
    'uravinmurai_office' => 'Uravinmurai Office',
    'tmhnu' => 'TMHNU',
    'main_campus' => 'Main Campus',
    'south_branch' => 'South Branch'
];

if (isset($_GET['id']) && array_key_exists($_GET['id'], $centers)) {
    $_SESSION['selected_center'] = $_GET['id'];
    $_SESSION['selected_center_name'] = $centers[$_GET['id']];
} elseif (isset($_GET['clear'])) {
    unset($_SESSION['selected_center']);
    unset($_SESSION['selected_center_name']);
}

header("Location: dashboard.php");
exit;
