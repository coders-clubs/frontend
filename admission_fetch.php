<?php
require 'config.php';
require_login();

header('Content-Type: application/json');

$application_no = $_GET['application_no'] ?? '';
$faculty_email = $_SESSION['faculty_email'];

if (empty($application_no)) {
    echo json_encode(['status' => 'error', 'message' => 'Application number is required.']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM admissions WHERE application_no = ? AND faculty_email = ?");
$stmt->execute([$application_no, $faculty_email]);
$record = $stmt->fetch();

if ($record) {
    echo json_encode(['status' => 'success', 'record' => $record]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Record not found or you do not have permission to view it.']);
}
?>
