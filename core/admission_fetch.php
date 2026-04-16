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
        // 2. Fetch associated marks from the relational table
        $mStmt = $pdo->prepare("SELECT subject_name as subject, max_marks as max, marks_obtained as obt, grade FROM marks WHERE admission_id = ?");
        $mStmt->execute([$record['id']]);
        $marks = $mStmt->fetchAll();
        
        echo json_encode(['status' => 'success', 'record' => $record, 'marks' => $marks]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record not found.']);
    }
}
?>
