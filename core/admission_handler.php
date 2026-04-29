<?php
require '../connection/connection.php';
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

            $sql = "INSERT INTO admissions (receipt_no, application_no, admission_type, student_name, gender, date_of_birth, father_name, father_occupation, mother_name, mother_occupation, address, city, pincode, cell_1, cell_2, religion, community, caste, degree, department, date_of_joining, quota, hostel, concession, bus_stop, bill_type, reference, faculty_email, center, reg_no, school_name, percentage, receipt_date, record_type, transaction_id, scheme_7_5, place_of_school, exam_no) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $receipt_no, $application_no, $_POST['admission_type'] ?? 'Regular', $_POST['student_name'] ?? '', $_POST['gender'] ?? '', 
                !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null, 
                $_POST['father_name'] ?? '', $_POST['father_occupation'] ?? '', $_POST['mother_name'] ?? '', $_POST['mother_occupation'] ?? '', 
                $_POST['address'] ?? '', $_POST['city'] ?? '', $_POST['pincode'] ?? '', $_POST['cell_1'] ?? '', $_POST['cell_2'] ?? '', 
                $_POST['religion'] ?? '', $_POST['community'] ?? '', $_POST['caste'] ?? '', $_POST['degree'] ?? '', $_POST['department'] ?? '',
                !empty($_POST['date_of_joining']) ? $_POST['date_of_joining'] : null, 
                $_POST['quota'] ?? 'Merit', $_POST['hostel'] ?? 'No', $_POST['concession'] ?? '', $_POST['bus_stop'] ?? '', 
                $_POST['bill_type'] ?? 'Cash', $_POST['reference'] ?? '',
                $faculty_email, $_SESSION['selected_center'] ?? '',
                $_POST['reg_no'] ?? '', $_POST['school_name'] ?? '', $_POST['percentage'] ?? 0,
                !empty($_POST['receipt_date']) ? $_POST['receipt_date'] : date('Y-m-d'),
                $_POST['record_type'] ?? 'Application', $_POST['transaction_id'] ?? '', $_POST['scheme_7_5'] ?? 'No', $_POST['place_of_school'] ?? '', $_POST['exam_no'] ?? ''
            ]);
            
            $new_student_id = $pdo->lastInsertId();

            if (isset($_POST['subject'])) {
                $sqlM = "INSERT INTO marks (admission_id, receipt_no, student_name, 
                        s1_name, s1_obt, s2_name, s2_obt, s3_name, s3_obt, 
                        s4_name, s4_obt, s5_name, s5_obt, total_obt, cutoff) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $mStmt = $pdo->prepare($sqlM);
                
                $totalObt = 0;
                $sNames = []; $sObts = [];
                for($i=0; $i<5; $i++) {
                    $sNames[$i] = $_POST['subject'][$i] ?? '';
                    $sObts[$i] = intval($_POST['obt'][$i] ?? 0);
                    $totalObt += $sObts[$i];
                }
                
                // Manual Cutoff input from Registry form
                $cutoff = floatval($_POST['cutoff'] ?? 0);
                
                $mBatch = [$new_student_id, $receipt_no, $_POST['student_name'] ?? ''];
                for($i=0; $i<5; $i++) {
                    $mBatch[] = $sNames[$i]; $mBatch[] = $sObts[$i];
                }
                $mBatch[] = $totalObt;
                $mBatch[] = $cutoff;
                $mStmt->execute($mBatch);
            }
            
            $pdo->commit();
            if (($_POST['record_type'] ?? 'Application') === 'Enquiry') {
                header("Location: ../admission_entry.php?msg=" . urlencode("Enquiry Saved Successfully!"));
            } else {
                header("Location: ../print_receipt.php?receipt_no=" . $receipt_no);
            }
            exit;

        } elseif ($action === 'update_advanced' || $action === 'update') {
            $id = $_POST['existing_id'];
            
            $sql = "UPDATE admissions SET 
                    admission_type = ?, student_name = ?, gender = ?, date_of_birth = ?, father_name = ?, mother_name = ?, father_occupation = ?, mother_occupation = ?, caste = ?, 
                    state = ?, address = ?, place = ?, city = ?, pincode = ?, cell_1 = ?, cell_2 = ?, religion = ?, community = ?, reg_no = ?, department = ?, school_name = ?, 
                    percentage = ?, reference = ?, reference_name = ?, hostel = ?, uravinmurai_letter = ?, 
                    fees_name = ?, amount = ?, bill_type = ?, degree = ?, receipt_date = ?, date_of_joining = ?, quota = ?, concession = ?, bus_stop = ?,
                    record_type = ?, transaction_id = ?, scheme_7_5 = ?, place_of_school = ?, exam_no = ?
                    WHERE id = ? AND faculty_email = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['admission_type'] ?? 'Regular', $_POST['student_name'] ?? '', $_POST['gender'] ?? '', 
                !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null, 
                $_POST['father_name'] ?? '', $_POST['mother_name'] ?? '', $_POST['father_occupation'] ?? '', $_POST['mother_occupation'] ?? '', $_POST['caste'] ?? '', $_POST['state'] ?? 'Tamil Nadu', 
                $_POST['address'] ?? '', $_POST['place'] ?? '', $_POST['city'] ?? '', $_POST['pincode'] ?? '',
                $_POST['cell_1'] ?? '', $_POST['cell_2'] ?? '', $_POST['religion'] ?? '', $_POST['community'] ?? '', $_POST['reg_no'] ?? '', $_POST['department'] ?? '', $_POST['school_name'] ?? '', 
                $_POST['percentage'] ?? 0, 
                $_POST['reference'] ?? '', $_POST['reference_name'] ?? '', $_POST['hostel'] ?? 'No', 
                $_POST['uravinmurai_letter'] ?? 'No', 
                $_POST['fees_name'] ?? '', $_POST['amount'] ?? 0, $_POST['bill_type'] ?? 'Cash', $_POST['degree'] ?? '',
                !empty($_POST['receipt_date']) ? $_POST['receipt_date'] : null,
                !empty($_POST['date_of_joining']) ? $_POST['date_of_joining'] : null,
                $_POST['quota'] ?? 'Merit', $_POST['concession'] ?? '', 
                $_POST['bus_stop'] ?? '',
                $_POST['record_type'] ?? 'Application', $_POST['transaction_id'] ?? '', $_POST['scheme_7_5'] ?? 'No', $_POST['place_of_school'] ?? '', $_POST['exam_no'] ?? '',
                $id, $faculty_email
            ]);

            if (isset($_POST['subject'])) {
                $pdo->prepare("DELETE FROM marks WHERE admission_id = ?")->execute([$id]);
                $sqlM = "INSERT INTO marks (admission_id, receipt_no, student_name, 
                        s1_name, s1_obt, s2_name, s2_obt, s3_name, s3_obt, 
                        s4_name, s4_obt, s5_name, s5_obt, total_obt, cutoff) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $mStmt = $pdo->prepare($sqlM);
                
                $totalObt = 0;
                $sNames = []; $sObts = [];
                for($i=0; $i<5; $i++) {
                    $sNames[$i] = $_POST['subject'][$i] ?? '';
                    $sObts[$i] = intval($_POST['obt'][$i] ?? 0);
                    $totalObt += $sObts[$i];
                }
                
                // Manual Cutoff input from Registry form
                $cutoff = floatval($_POST['cutoff'] ?? 0);
                
                $mBatch = [$id, $_POST['receipt_no'] ?? '', $_POST['student_name'] ?? ''];
                for($i=0; $i<5; $i++) {
                    $mBatch[] = $sNames[$i]; $mBatch[] = $sObts[$i];
                }
                $mBatch[] = $totalObt;
                $mBatch[] = $cutoff;
                $mStmt->execute($mBatch);
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
