<?php
require '../connection/connection.php';
require_login();

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Convert dd-mm-yyyy or dd/mm/yyyy to yyyy-mm-dd for DB matching
    $db_date_search = $search;
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $search, $matches)) {
        $db_date_search = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
    }

    // 1. Fetch main student records by multiple fields
    $stmt = $pdo->prepare("SELECT * FROM admissions WHERE receipt_no = ? OR application_no = ? OR cell_1 = ? OR date_of_birth = ? OR student_name LIKE ? OR father_name LIKE ? ORDER BY id DESC");
    $stmt->execute([$search, $search, $search, $db_date_search, "%$search%", "%$search%"]);
    $records = $stmt->fetchAll();

    if (count($records) == 1) {
        $record = $records[0];
        // 2. Fetch ultra-optimized marks
        $mStmt = $pdo->prepare("SELECT * FROM marks WHERE admission_id = ?");
        $mStmt->execute([$record['id']]);
        $row = $mStmt->fetch();
        
        $marks = [];
        if ($row) {
            for ($i=1; $i<=5; $i++) {
                if (!empty($row["s{$i}_name"])) {
                    $marks[] = [
                        'subject' => $row["s{$i}_name"],
                        'obt' => $row["s{$i}_obt"],
                        'max' => 100 // Visual default
                    ];
                }
            }
            $record['marks_total'] = $row['total_obt'];
            $record['cutoff'] = $row['cutoff'];
        }
        
        echo json_encode(['status' => 'success', 'record' => $record, 'marks' => $marks]);
    } elseif (count($records) > 1) {
        $summary = [];
        foreach($records as $r) {
            $summary[] = [
                'receipt_no' => $r['receipt_no'], 
                'student_name' => $r['student_name'], 
                'department' => $r['department'], 
                'date_of_birth' => $r['date_of_birth']
            ];
        }
        echo json_encode(['status' => 'multiple', 'records' => $summary]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record not found.']);
    }
}
?>
