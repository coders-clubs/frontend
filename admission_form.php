<?php
require 'config.php';
require_login();

// Generate unique ID utility for new forms
$new_receipt_no = 'REC-' . date('YmdHis') . '-' . rand(100, 999);
$new_application_no = 'APP-' . date('YmdHis') . '-' . rand(100, 999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission Entry Form</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo">College Admissions</div>
    <div class="user-info">
        <?= htmlspecialchars($_SESSION['faculty_name'] ?? $_SESSION['faculty_email']) ?>
        <a href="dashboard.php" class="logout-btn" style="background-color: #5a6268;">Dashboard</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<div class="container">
    <div class="form-card" id="printable-area">
        <h2 style="text-align: center; margin-bottom: 30px;">Admission Entry Form</h2>

        <!-- Message mapping if redirect comes back with status -->
        <?php if(isset($_GET['msg'])): ?>
            <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:20px; border-radius:5px;">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <form id="admissionForm" action="admission_handler.php" method="POST">
            <!-- Hidden inputs -->
            <input type="hidden" name="action_type" id="action_type" value="save">
            <input type="hidden" name="existing_id" id="existing_id" value="">

            <!-- Basic Information -->
            <div class="form-section">
                <h3>Basic Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Receipt No *</label>
                        <input type="text" name="receipt_no" id="receipt_no" value="<?= $new_receipt_no ?>" required readonly>
                    </div>
                    <div class="form-group">
                        <label>Admission Type *</label>
                        <select name="admission_type" id="admission_type" required>
                            <option value="">Select Type</option>
                            <option value="Regular">Regular</option>
                            <option value="Lateral">Lateral Entry</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Personal Details -->
            <div class="form-section">
                <h3>Personal Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Student Name *</label>
                        <input type="text" name="student_name" id="student_name" required>
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <select name="gender" id="gender" required>
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth *</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Father's Name</label>
                        <input type="text" name="father_name" id="father_name">
                    </div>
                    <div class="form-group">
                        <label>Father's Occupation</label>
                        <input type="text" name="father_occupation" id="father_occupation">
                    </div>
                    <div class="form-group">
                        <label>Mother's Name</label>
                        <input type="text" name="mother_name" id="mother_name">
                    </div>
                    <div class="form-group">
                        <label>Mother's Occupation</label>
                        <input type="text" name="mother_occupation" id="mother_occupation">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" id="address" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" id="city">
                    </div>
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" id="pincode">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Cell 1 *</label>
                        <input type="text" name="cell_1" id="cell_1" required>
                    </div>
                    <div class="form-group">
                        <label>Cell 2</label>
                        <input type="text" name="cell_2" id="cell_2">
                    </div>
                    <div class="form-group">
                        <label>Religion</label>
                        <input type="text" name="religion" id="religion">
                    </div>
                    <div class="form-group">
                        <label>Community</label>
                        <input type="text" name="community" id="community">
                    </div>
                    <div class="form-group">
                        <label>Caste</label>
                        <input type="text" name="caste" id="caste">
                    </div>
                </div>
            </div>

            <!-- Academic Details -->
            <div class="form-section">
                <h3>Academic Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Application No *</label>
                        <input type="text" name="application_no" id="application_no" value="<?= $new_application_no ?>" required readonly>
                    </div>
                    <div class="form-group">
                        <label>Department *</label>
                        <select name="department" id="department" required>
                            <option value="">Select Dept</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Mechanical">Mechanical</option>
                            <option value="Electrical">Electrical</option>
                            <option value="Civil">Civil</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quota *</label>
                        <select name="quota" id="quota" required>
                            <option value="">Select Quota</option>
                            <option value="Merit">Merit</option>
                            <option value="Management">Management</option>
                            <option value="Sports">Sports</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Concession</label>
                        <input type="text" name="concession" id="concession">
                    </div>
                </div>
            </div>

            <!-- Admission Details -->
            <div class="form-section">
                <h3>Admission Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Admission No</label>
                        <input type="text" name="admission_no" id="admission_no">
                    </div>
                    <div class="form-group">
                        <label>Date of Joining *</label>
                        <input type="date" name="date_of_joining" id="date_of_joining" required>
                    </div>
                    <div class="form-group">
                        <label>Degree</label>
                        <input type="text" name="degree" id="degree">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Hostel Needed?</label>
                        <select name="hostel" id="hostel">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Bus Stop</label>
                        <input type="text" name="bus_stop" id="bus_stop">
                    </div>
                    <div class="form-group">
                        <label>Bus Route No</label>
                        <input type="text" name="bus_route_no" id="bus_route_no">
                    </div>
                </div>
            </div>

            <div class="form-actions no-print">
                <button type="button" class="btn btn-primary" onclick="setMode('add')">Add</button>
                <button type="button" class="btn btn-warning" onclick="enableModify()">Modify</button>
                <button type="button" class="btn btn-danger" onclick="setMode('delete')">Delete</button>
                <button type="submit" class="btn btn-success" id="saveBtn">Save / Update</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('admissionForm').reset()">Cancel</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
                <a href="admission_form.php" class="btn btn-primary">New Form</a>
            </div>
            
            <div id="modify-search-bar" style="display:none; margin-top:20px; padding:15px; background:#f9f9f9; border:1px solid #ccc; border-radius:5px;" class="no-print">
                <label><strong>Enter Application No to Fetch:</strong></label>
                <input type="text" id="search_app_no" style="padding:8px; width:200px; margin-left:10px;">
                <button type="button" class="btn btn-primary" onclick="fetchRecord()">Fetch Record</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/script.js"></script>
<!-- Initializing values for JS resets -->
<script>
    const INIT_RECEIPT_NO = "<?= $new_receipt_no ?>";
    const INIT_APP_NO = "<?= $new_application_no ?>";
</script>
</body>
</html>
