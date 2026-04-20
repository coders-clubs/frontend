<?php
require '../connection/connection.php';
require_login(); // Allow both Admin and Faculty

$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$center = $_GET['center'] ?? 'all';
$faculty = $_GET['faculty'] ?? 'all';

$query = "SELECT receipt_no, student_name, department, center, date_of_joining, created_at FROM admissions WHERE 1=1";
$params = [];

// If Faculty, restrict to their own records
if (!is_admin()) {
    $query .= " AND faculty_email = ?";
    $params[] = $_SESSION['faculty_email'];
    // Reset faculty filter to themselves just in case they tried to tamper with the URL
    $faculty = $_SESSION['faculty_email'];
} else {
    // Admin can filter by any faculty
    if ($faculty !== 'all') {
        $query .= " AND faculty_email = ?";
        $params[] = $faculty;
    }
}

if (!empty($startDate)) {
    $query .= " AND DATE(created_at) >= ?";
    $params[] = $startDate;
}
if (!empty($endDate)) {
    $query .= " AND DATE(created_at) <= ?";
    $params[] = $endDate;
}
if ($center !== 'all') {
    $query .= " AND center = ?";
    $params[] = $center;
}

$query .= " ORDER BY created_at ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($records)) {
    echo "<script>alert('No records found for the selected filters.'); window.history.back();</script>";
    exit;
}

$displayName = !is_admin() ? $_SESSION['faculty_name'] : ($faculty !== 'all' ? $faculty : 'AllStaff');
$displayCenter = ($center !== 'all' ? $center : 'AllCenters');

$filename = "NSCET_Admissions_" . $displayCenter . "_" . $displayName . "_" . date('Ymd_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Professional Headers
fputcsv($output, ['RECEIPT NO', 'STUDENT NAME', 'DEPARTMENT', 'ADMISSION CENTER', 'DATE OF JOINING', 'REGISTRATION DATE']);

// Data
foreach ($records as $row) {
    // Optional: resolve center ID to Name for the CSV if needed?
    // Current export shows 'nscet', maybe show 'NSCET'
    global $centers_list;
    $row['center'] = $centers_list[$row['center']] ?? $row['center'];
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
