<?php
require '../connection/connection.php';
require_admin();

$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$center = $_GET['center'] ?? '';
$faculty = $_GET['faculty'] ?? '';

$query = "SELECT * FROM admissions WHERE 1=1";
$params = [];

if (!empty($startDate)) {
    $query .= " AND DATE(created_at) >= ?";
    $params[] = $startDate;
}
if (!empty($endDate)) {
    $query .= " AND DATE(created_at) <= ?";
    $params[] = $endDate;
}
if (!empty($center) && $center !== 'all') {
    $query .= " AND center = ?";
    $params[] = $center;
}
if (!empty($faculty) && $faculty !== 'all') {
    $query .= " AND faculty_email = ?";
    $params[] = $faculty;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($records)) {
    echo "<script>alert('No records found for the selected filters.'); window.history.back();</script>";
    exit;
}

$filename = "NSCET_Admissions_" . ($center !== 'all' ? $center : 'AllCenters') . "_" . ($faculty !== 'all' ? $faculty : 'AllStaff') . "_" . date('Ymd_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Headers
if (!empty($records)) {
    fputcsv($output, array_keys($records[0]));
}

// Data
foreach ($records as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
