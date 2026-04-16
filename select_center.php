<?php
session_start();
require 'connection/connection.php';
require_login();

global $centers_list;
$centers = $centers_list;

if (isset($_GET['id']) && array_key_exists($_GET['id'], $centers)) {
    $_SESSION['selected_center'] = $_GET['id'];
    $_SESSION['selected_center_name'] = $centers[$_GET['id']];
} elseif (isset($_GET['clear'])) {
    unset($_SESSION['selected_center']);
    unset($_SESSION['selected_center_name']);
}

header("Location: dashboard.php");
exit;
