<?php
require '../connection/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action_type'] ?? 'save';
    $faculty_email = $_SESSION['faculty_email'];

    try {
        $pdo->beginTransaction();

        if ($action === 'delete') {
            $id = $_POST['existing_id'];
            $stmt = $pdo->prepare("DELETE FROM admissions WHERE id = ? AND faculty_email = ?");
            $stmt->execute([$id, $faculty_email]);
            $pdo->commit();
            header("Location: ../dashboard.php?msg=Record deleted successfully.");
            exit;
        }

        if ($action === 'save') {
            $stmtCount = $pdo->query("SELECT id FROM admissions ORDER BY id DESC LIMIT 1 FOR UPDATE");
            $lastRecord = $stmtCount->fetch();
            $nextId = $lastRecord ? ($lastRecord['id'] + 1) : 1;
            
            $receipt_no = 'NS-' . str_pad($nextId, 5, "0", STR_PAD_LEFT);
            $application_no = 'STUDENT-' . str_pad($nextId, 5, "0", STR_PAD_LEFT);

            $sql = "INSERT INTO admissions (receipt_no, application_no, admission_type, student_name, gender, date_of_birth, father_name, father_occupation, mother_name, mother_occupation, address, city, pincode, cell_1, cell_2, religion, community, caste, degree, department, date_of_joining, quota, hostel, concession, bus_stop, faculty_email) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $receipt_no, $application_no, $_POST['admission_type'] ?? 'Regular', $_POST['student_name'] ?? '', $_POST['gender'] ?? '', 
                !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null, 
                $_POST['father_name'] ?? '', $_POST['father_occupation'] ?? '', $_POST['mother_name'] ?? '', $_POST['mother_occupation'] ?? '', 
                $_POST['address'] ?? '', $_POST['city'] ?? '', $_POST['pincode'] ?? '', $_POST['cell_1'] ?? '', $_POST['cell_2'] ?? '', 
                $_POST['religion'] ?? '', $_POST['community'] ?? '', $_POST['caste'] ?? '', $_POST['degree'] ?? '', $_POST['department'] ?? '',
                !empty($_POST['date_of_joining']) ? $_POST['date_of_joining'] : null, 
                $_POST['quota'] ?? 'Merit', $_POST['hostel'] ?? 'No', $_POST['concession'] ?? '', $_POST['bus_stop'] ?? '', $faculty_email
            ]);
            
            $new_student_id = $pdo->lastInsertId();

            if (isset($_POST['subject'])) {
                $mStmt = $pdo->prepare("INSERT INTO marks (admission_id, subject_name, max_marks, marks_obtained, grade) VALUES (?, ?, ?, ?, ?)");
                foreach($_POST['subject'] as $idx => $subj) {
                    if (!empty($subj)) {
                        $mStmt->execute([
                            $new_student_id, $subj, $_POST['max'][$idx] ?? 100, $_POST['obt'][$idx] ?? 0, $_POST['grade'][$idx] ?? ''
                        ]);
                    }
                }
            }
            
            $pdo->commit();
            header("Location: ../print_receipt.php?receipt_no=" . $receipt_no);
            exit;

        } elseif ($action === 'update_advanced' || $action === 'update') {
            $id = $_POST['existing_id'];
            
            $sql = "UPDATE admissions SET 
                    admission_type = ?, student_name = ?, gender = ?, date_of_birth = ?, father_name = ?, caste = ?, 
                    state = ?, address = ?, place = ?, cell_1 = ?, reg_no = ?, department = ?, school_name = ?, 
                    percentage = ?, reference = ?, reference_name = ?, hostel = ?, uravinmurai_letter = ?, 
                    fees_name = ?, amount = ?, bill_type = ?, degree = ?, receipt_date = ?, date_of_joining = ?, quota = ?, concession = ?
                    WHERE id = ? AND faculty_email = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['admission_type'] ?? 'Regular', $_POST['student_name'] ?? '', $_POST['gender'] ?? '', 
                !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null, 
                $_POST['father_name'] ?? '', $_POST['caste'] ?? '', $_POST['state'] ?? 'Tamil Nadu', 
                $_POST['address'] ?? '', $_POST['place'] ?? '', 
                $_POST['cell_1'] ?? '', $_POST['reg_no'] ?? '', $_POST['department'] ?? '', $_POST['school_name'] ?? '', 
                $_POST['percentage'] ?? 0, 
                $_POST['reference'] ?? '', $_POST['reference_name'] ?? '', $_POST['hostel'] ?? 'No', 
                $_POST['uravinmurai_letter'] ?? 'No', 
                $_POST['fees_name'] ?? '', $_POST['amount'] ?? 0, $_POST['bill_type'] ?? 'Cash', $_POST['degree'] ?? '',
                !empty($_POST['receipt_date']) ? $_POST['receipt_date'] : null,
                !empty($_POST['date_of_joining']) ? $_POST['date_of_joining'] : null,
                $_POST['quota'] ?? 'Merit', $_POST['concession'] ?? '', $id, $faculty_email
            ]);

            if (isset($_POST['subject'])) {
                $pdo->prepare("DELETE FROM marks WHERE admission_id = ?")->execute([$id]);
                $mStmt = $pdo->prepare("INSERT INTO marks (admission_id, subject_name, max_marks, marks_obtained, grade) VALUES (?, ?, ?, ?, ?)");
                foreach($_POST['subject'] as $idx => $subj) {
                    if (!empty($subj)) {
                        $mStmt->execute([
                            $id, $subj, $_POST['max'][$idx] ?? 100, $_POST['obt'][$idx] ?? 0, $_POST['grade'][$idx] ?? ''
                        ]);
                    }
                }
            }

            $pdo->commit();
            header("Location: ../" . ($action === 'update_advanced' ? 'admission_registry.php' : 'admission_entry.php') . "?msg=Record updated successfully.");
            exit;
        }

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Error processing admission: " . $e->getMessage());
    }
}
?>
