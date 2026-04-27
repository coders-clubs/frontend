<?php
require '../connection/connection.php';
require_login(); // Allow both Admin and Faculty

$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$center = $_GET['center'] ?? 'all';
$faculty = $_GET['faculty'] ?? 'all';

$query = "SELECT a.receipt_no, a.student_name, a.department, a.center, u.name as staff_name, a.date_of_joining, a.created_at 
          FROM admissions a 
          LEFT JOIN users u ON a.faculty_email = u.email 
          WHERE 1=1";
$params = [];

// If Faculty, restrict to their own records
if (!is_admin()) {
    $query .= " AND a.faculty_email = ?";
    $params[] = $_SESSION['faculty_email'];
    $faculty = $_SESSION['faculty_email'];
} else {
    if ($faculty !== 'all') {
        $query .= " AND a.faculty_email = ?";
        $params[] = $faculty;
    }
}

if (!empty($startDate)) { $query .= " AND DATE(a.created_at) >= ?"; $params[] = $startDate; }
if (!empty($endDate)) { $query .= " AND DATE(a.created_at) <= ?"; $params[] = $endDate; }
if ($center !== 'all') { $query .= " AND a.center = ?"; $params[] = $center; }

$query .= " ORDER BY a.created_at ASC";

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
fputcsv($output, ['RECEIPT NO', 'STUDENT NAME', 'DEPARTMENT', 'ADMISSION CENTER', 'REGISTERED BY', 'DATE OF JOINING', 'REGISRATION DATE']);

// Data
foreach ($records as $row) {
    global $centers_list;
    $row['center'] = $centers_list[$row['center']] ?? $row['center'];
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
