<?php
require 'connection/connection.php';
require_login();

if (!isset($_SESSION['selected_center'])) {
    header("Location: dashboard.php");
    exit;
}

// Auto-generate Receipt No
$stmtCount = $pdo->query("SELECT id FROM admissions ORDER BY id DESC LIMIT 1");
$lastRecord = $stmtCount->fetch();
$nextId = $lastRecord ? ($lastRecord['id'] + 1) : 1;
$auto_receipt_no = 'NS-' . str_pad($nextId, 5, "0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enquiry Entry | NSCET</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="designer-animate">
<div class="app-container">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <div class="dash-container">
            <?php include 'branding.php'; ?>
            
            <div class="page-hero no-print" style="margin-bottom: 40px;">
                <h1>Student Enquiry</h1>
            </div>

<div class="form-container">
    <div class="designer-card">
        
        <?php if(isset($_GET['msg'])): ?>
            <div style="background:#ecfdf5; color:#065f46; padding:20px; margin-bottom:40px; border-radius:18px; border: 2px solid #a7f3d0; text-align: center; font-weight: 700; font-family: 'Outfit', sans-serif;">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <form id="admissionForm" action="core/admission_handler.php" method="POST">
            <input type="hidden" name="action_type" id="action_type" value="save">
            <input type="hidden" name="existing_id" id="existing_id" value="">

            <!-- SECTION 01: INSTITUTIONAL CONTEXT (HEADER) -->
            <div class="section-wrapper section-institutional">
                <div class="section-label">
                    <div class="section-number">01</div>
                    <div class="section-title">Institutional Context</div>
                </div>
                <input type="hidden" name="receipt_no" id="receipt_no" value="<?= $auto_receipt_no ?>">
                <div class="input-row">
                    <div class="field-box">
                        <label>Record Type</label>
                        <input type="text" value="Enquiry" readonly style="font-weight: 700; background: #f1f5f9; cursor: not-allowed;">
                        <input type="hidden" name="record_type" id="record_type" value="Enquiry">
                    </div>
                    <div class="field-box">
                        <label>Receipt Date</label>
                        <input type="text" name="receipt_date" id="receipt_date" value="<?= date('Y-m-d') ?>" required readonly style="font-weight: 700; background: #f1f5f9; cursor: not-allowed;">
                    </div>
                    <div class="field-box">
                        <label>Admission Type</label>
                        <select name="admission_type" id="admission_type" required>
                            <option value="">Select Type</option>
                            <option value="Regular">Regular</option>
                            <option value="Lateral">Lateral Entry</option>
                            <option value="Transfer">Transfer</option>
                            <option value="PG">PG</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 02: PERSONAL INFORMATION -->
            <div class="section-wrapper section-personal">
                <div class="section-label">
                    <div class="section-number">02</div>
                    <div class="section-title">Personal Information</div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>First Name *</label>
                        <input type="text" id="first_name" required oninput="this.value = this.value.toUpperCase(); updateFullName()">
                    </div>
                    <div class="field-box">
                        <label>Middle Name</label>
                        <input type="text" id="middle_name" oninput="this.value = this.value.toUpperCase(); updateFullName()">
                    </div>
                    <div class="field-box">
                        <label>Last Name (Initial) *</label>
                        <input type="text" id="last_name" required oninput="this.value = this.value.toUpperCase(); updateFullName()">
                    </div>
                    <input type="hidden" name="student_name" id="student_name">
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Gender *</label>
                        <select name="gender" id="gender" required>
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>Date of Birth *</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" required>
                    </div>
                    <div class="field-box">
                        <label>Exam No. (Reg No)</label>
                        <input type="text" name="exam_no" id="exam_no" oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Father's Name *</label>
                        <input type="text" name="father_name" id="father_name" required>
                    </div>
                    <div class="field-box">
                        <label>Mother's Name *</label>
                        <input type="text" name="mother_name" id="mother_name" required>
                    </div>
                </div>

                <div class="input-row">
                    <div class="field-box">
                        <label>Father's Occupation</label>
                        <input type="text" name="father_occupation" id="father_occupation" placeholder="Occupation">
                    </div>
                    <div class="field-box">
                        <label>Mother's Occupation</label>
                        <input type="text" name="mother_occupation" id="mother_occupation">
                    </div>
                </div>

                <div class="input-row" style="grid-template-columns: 2fr 1fr 1fr;">
                    <div class="field-box">
                        <label>Contact Address</label>
                        <input type="text" name="address" id="address" placeholder="Door No, Street Name">
                    </div>
                    <div class="field-box">
                        <label>City</label>
                        <input type="text" name="city" id="city" placeholder="City/Place" oninput="autoFillPincode()">
                    </div>
                    <div class="field-box">
                        <label>Pincode</label>
                        <input type="text" name="pincode" id="pincode" <?= !is_admin() ? 'readonly' : '' ?> style="<?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>">
                    </div>
                </div>

                <div class="input-row">
                    <div class="field-box">
                        <label>Phone Number 1 (Primary) *</label>
                        <input type="text" name="cell_1" id="cell_1" required pattern="[0-9]{10}" maxlength="10" placeholder="10 Digit Number">
                    </div>
                    <div class="field-box">
                        <label>Phone Number 2 (Secondary)</label>
                        <input type="text" name="cell_2" id="cell_2" pattern="[0-9]{10}" maxlength="10" placeholder="10 Digit Number">
                    </div>
                </div>

                <div class="input-row">
                    <div class="field-box">
                        <label>Religion</label>
                        <select name="religion" id="religion">
                            <option value="">Select</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Muslim">Muslim</option>
                            <option value="Christian">Christian</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>Community</label>
                        <select name="community" id="community">
                            <option value="">Select</option>
                            <option value="OC">OC</option>
                            <option value="BC">BC</option>
                            <option value="MBC/DNC">MBC/DNC</option>
                            <option value="SC/ST">SC/ST</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>Caste</label>
                        <input type="text" name="caste" id="caste">
                    </div>
                </div>
            </div>

            <!-- SECTION 03: ACADEMIC INFORMATION -->
            <div class="section-wrapper section-academic">
                <div class="section-label">
                    <div class="section-number">03</div>
                    <div class="section-title">Academic Information</div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Application No</label>
                        <input type="text" name="application_no" id="application_no" value="(Auto-generated)" readonly style="background: #f1f5f9; cursor: not-allowed;">
                    </div>
                    <div class="field-box">
                        <label>Degree *</label>
                        <select name="degree" id="degree" required onchange="updateDepts()">
                            <option value="">Select Degree</option>
                            <option value="B.E">B.E (UG)</option>
                            <option value="B.Tech">B.Tech (UG)</option>
                            <option value="M.E">M.E (PG)</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>Dept (Branch) *</label>
                        <select name="department" id="department" required>
                            <option value="">Select Degree First</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-row">
                    <div class="field-box" style="flex: 2;">
                        <label>School Name</label>
                        <input type="text" name="school_name" id="school_name" placeholder="Last studied institution">
                    </div>
                    <div class="field-box" style="flex: 1;">
                        <label>Place of School</label>
                        <input type="text" name="place_of_school" id="place_of_school">
                    </div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Quota</label>
                        <select name="quota" id="quota">
                            <option value="Merit">Counselling</option>
                            <option value="Management">Management</option>
                            <option value="Sports">Sports</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>7.5% Scheme</label>
                        <select name="scheme_7_5" id="scheme_7_5">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>First Graduate</label>
                        <select name="first_graduate" id="first_graduate">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 04: OTHER DETAILS & FACILITIES -->
            <div class="section-wrapper section-other">
                <div class="section-label">
                    <div class="section-number">04</div>
                    <div class="section-title">Other Details & Facilities</div>
                </div>
                <div class="input-row">
                    <div class="field-box">
                        <label>Date of Joining</label>
                        <input type="date" name="date_of_joining" id="date_of_joining" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="input-row">
                    <div class="field-box">
                        <label>Hostel Required?</label>
                        <select name="hostel" id="hostel">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>Bus Stop</label>
                        <input type="text" name="bus_stop" id="bus_stop">
                    </div>
                    <div class="field-box">
                        <label>Bus Route No</label>
                        <input type="text" name="bus_route_no" id="bus_route_no">
                    </div>
                </div>
            </div>

            <div class="action-strip no-print">
                <button type="button" class="btn-designer btn-ghost" onclick="setMode('add')">ADD NEW</button>
                <button type="button" class="btn-designer btn-accent-designer" onclick="enableModify()">SEARCH / MODIFY</button>
                <button type="submit" class="btn-designer btn-primary-designer" id="saveBtn">SAVE ENQUIRY</button>
            </div>

            <div id="modify-search-bar" style="display:none; margin-top:40px; border-top: 2px dashed #e2e8f0; padding-top: 40px;" class="no-print">
                 <div class="field-box" style="max-width: 500px; margin: 0 auto; text-align: center;">
                    <label>Enter Receipt or App No to Fetch Student Details</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="search_app_no" placeholder="Search by NS-xxxx or STUDENT-xxxx" style="flex: 1;">
                        <button type="button" class="btn-designer btn-primary-designer" onclick="fetchRecord()" style="padding: 10px 20px;">FETCH NOW</button>
                    </div>
                 </div>
            </div>
        </form>
    </div>
</div>

<script>
    function updateFullName() {
        const f = document.getElementById('first_name').value;
        const m = document.getElementById('middle_name').value;
        const l = document.getElementById('last_name').value;
        document.getElementById('student_name').value = [f, m, l].filter(Boolean).join(' ');
    }

    function toggleRecordType() {
        // Obsolete function, removed
    }

    const AUTO_FETCH = "<?= htmlspecialchars($_GET['fetch'] ?? '') ?>";
    function printOfficialReceipt() {
        const receiptNo = document.getElementById('receipt_no').value;
        if (!receiptNo || receiptNo.includes('Auto-generated')) {
            alert("Please submit the registration first to generate a Receipt No.");
            return;
        }
        window.open('print_receipt.php?receipt_no=' + receiptNo, '_blank');
    }


    const deptMap = {
        'B.E': ['CSE', 'ECE', 'MECHANICAL', 'CIVIL', 'EEE'],
        'B.Tech': ['AI&DS', 'IT'],
        'M.E': ['Manufacturing Engineering', 'Structural Engineering']
    };

    function updateDepts() {
        const degree = document.getElementById('degree').value;
        const deptSelect = document.getElementById('department');
        deptSelect.innerHTML = '<option value="">Select Branch</option>';
        if (deptMap[degree]) {
            deptMap[degree].forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d;
                deptSelect.appendChild(opt);
            });
        }
    }

    const pincodeMap = {
        'Theni': '625531',
        'Bodinayakanur': '625513',
        'Periyakulam': '625523',
        'Cumbum': '625516',
        'Andipatti': '625512',
        'Chinnamanur': '625515',
        'Uthamapalayam': '625533'
    };

    function autoFillPincode() {
        const city = document.getElementById('city').value.trim();
        // Case-insensitive lookup
        const found = Object.keys(pincodeMap).find(k => k.toLowerCase() === city.toLowerCase());
        if (found) {
            document.getElementById('pincode').value = pincodeMap[found];
        }
    }
</script>
<script src="assets/js/script.js"></script>
        </div>
    </main>
</div>
</body>
</html>
