<?php
require 'config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$action = $_POST['action_type'] ?? 'save';
$faculty_email = $_SESSION['faculty_email'];

if ($action === 'delete') {
    $existing_id = $_POST['existing_id'] ?? '';
    if ($existing_id) {
        $stmt = $pdo->prepare("DELETE FROM admissions WHERE id = ? AND faculty_email = ?");
        $stmt->execute([$existing_id, $faculty_email]);
        header("Location: admission_form.php?msg=" . urlencode("Record successfully deleted."));
    } else {
        header("Location: admission_form.php?msg=" . urlencode("Error: No record specified for deletion."));
    }
    exit;
}

// Variables collection
$existing_id = $_POST['existing_id'] ?? '';
$receipt_no = $_POST['receipt_no'];
$admission_type = $_POST['admission_type'];
$student_name = $_POST['student_name'];
$gender = $_POST['gender'];
$father_name = $_POST['father_name'];
$mother_name = $_POST['mother_name'];
$address = $_POST['address'];
$city = $_POST['city'];
$pincode = $_POST['pincode'];
$cell_1 = $_POST['cell_1'];
$cell_2 = $_POST['cell_2'];
$community = $_POST['community'];
$religion = $_POST['religion'];
$date_of_birth = $_POST['date_of_birth'];
$caste = $_POST['caste'];
$father_occupation = $_POST['father_occupation'];
$mother_occupation = $_POST['mother_occupation'];

$application_no = $_POST['application_no'];
$department = $_POST['department'];
$quota = $_POST['quota'];
$concession = $_POST['concession'];

$admission_no = $_POST['admission_no'];
$date_of_joining = $_POST['date_of_joining'];
$bus_stop = $_POST['bus_stop'];
$bus_route_no = $_POST['bus_route_no'];
$degree = $_POST['degree'];
$hostel = $_POST['hostel'];

if ($action === 'save') {
    // Insert new
    $sql = "INSERT INTO admissions (
        receipt_no, admission_type, student_name, gender, father_name, mother_name, address, city, pincode,
        cell_1, cell_2, community, religion, date_of_birth, caste, father_occupation, mother_occupation,
        application_no, department, quota, concession, admission_no, date_of_joining, bus_stop, bus_route_no,
        degree, hostel, faculty_email
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?
    )";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            $receipt_no, $admission_type, $student_name, $gender, $father_name, $mother_name, $address, $city, $pincode,
            $cell_1, $cell_2, $community, $religion, $date_of_birth, $caste, $father_occupation, $mother_occupation,
            $application_no, $department, $quota, $concession, $admission_no, $date_of_joining, $bus_stop, $bus_route_no,
            $degree, $hostel, $faculty_email
        ]);
        header("Location: admission_form.php?msg=" . urlencode("Admission record successfully saved."));
    } catch (PDOException $e) {
        header("Location: admission_form.php?msg=" . urlencode("Error saving record: " . $e->getMessage()));
    }

} elseif ($action === 'update' && $existing_id) {
    // Update existing
    // We don't update receipt_no and application_no
    $sql = "UPDATE admissions SET 
        admission_type=?, student_name=?, gender=?, father_name=?, mother_name=?, address=?, city=?, pincode=?,
        cell_1=?, cell_2=?, community=?, religion=?, date_of_birth=?, caste=?, father_occupation=?, mother_occupation=?,
        department=?, quota=?, concession=?, admission_no=?, date_of_joining=?, bus_stop=?, bus_route_no=?,
        degree=?, hostel=?
        WHERE id=? AND faculty_email=?";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            $admission_type, $student_name, $gender, $father_name, $mother_name, $address, $city, $pincode,
            $cell_1, $cell_2, $community, $religion, $date_of_birth, $caste, $father_occupation, $mother_occupation,
            $department, $quota, $concession, $admission_no, $date_of_joining, $bus_stop, $bus_route_no,
            $degree, $hostel, $existing_id, $faculty_email
        ]);
        header("Location: admission_form.php?msg=" . urlencode("Admission record successfully updated."));
    } catch (PDOException $e) {
        header("Location: admission_form.php?msg=" . urlencode("Error updating record: " . $e->getMessage()));
    }
} else {
    header("Location: admission_form.php?msg=" . urlencode("Invalid action."));
}
exit;
?>
