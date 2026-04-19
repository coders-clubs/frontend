<?php
require '../connection/connection.php';
require_login();

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // 1. Fetch main student record
    $stmt = $pdo->prepare("SELECT * FROM admissions WHERE receipt_no = ? OR application_no = ?");
    $stmt->execute([$search, $search]);
    $record = $stmt->fetch();

    if ($record) {
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
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record not found.']);
    }
}
?>
