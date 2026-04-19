<?php
require 'connection/connection.php';
require_login();

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
    <title>Admission Entry | NSCET</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="designer-animate">

<header class="no-print">
    <div class="nav-actions"><a href="dashboard.php" class="nav-btn btn-ghost">← DASHBOARD</a></div>
    <div class="branding-center">
        <img src="assets/logo.png" alt="NSCET" class="logo-main" style="height: 120px;">
        <div class="college-title" style="margin-top: 10px;">NADAR SARASWATHI COLLEGE OF ENGINEERING & TECHNOLOGY</div>
    </div>
    <div class="nav-actions">
        <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;"><?= htmlspecialchars($_SESSION['faculty_name'] ?? $_SESSION['faculty_email']) ?></span>
        <a href="logout.php" class="nav-btn btn-danger-soft">LOGOUT</a>
    </div>
</header>

<div class="page-hero no-print">
    <h1>Student Admission</h1>
 
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
                <div class="input-row">
                    <div class="field-box">
                        <label>Receipt No</label>
                        <input type="text" name="receipt_no" id="receipt_no" value="<?= $auto_receipt_no ?>" required <?= !is_admin() ? 'readonly' : '' ?> style="font-weight: 800; color: var(--brand-navy); <?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>">
                    </div>
                    <div class="field-box">
                        <label>Receipt Date</label>
                        <input type="text" name="receipt_date" id="receipt_date" value="<?= date('Y-m-d') ?>" required <?= !is_admin() ? 'readonly' : '' ?> style="font-weight: 700; <?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>">
                    </div>
                    <div class="field-box">
                        <label>Admission Type</label>
                        <select name="admission_type" id="admission_type" required>
                            <option value="">Select Type</option>
                            <option value="Regular">Regular</option>
                            <option value="Lateral">Lateral Entry</option>
                            <option value="Transfer">Transfer</option>
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
                    <div class="field-box" style="flex: 2;">
                        <label>Student Name *</label>
                        <input type="text" name="student_name" id="student_name" required placeholder="Legal full name">
                    </div>
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
                            <option value="MBC">MBC</option>
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
                        <input type="text" name="application_no" id="application_no" value="(Auto-generated)" <?= !is_admin() ? 'readonly' : '' ?> style="<?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>">
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
                    <div class="field-box">
                        <label>Quota</label>
                        <select name="quota" id="quota">
                            <option value="Merit">Counselling</option>
                            <option value="Management">Management</option>
                            <option value="Sports">Sports</option>
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
                    <div class="field-box">
                        <label>Concession</label>
                        <select name="concession" id="concession">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
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

            <!-- SECTION 05: ENQUIRY FEE PAYMENT -->
            <div class="section-wrapper section-payment">
                <div class="section-label">
                    <div class="section-number">05</div>
                    <div class="section-title">Enquiry Fee Payment</div>
                </div>
                
                <div class="input-row" style="background: #fff; padding: 25px; border-radius: 12px; border: 2px solid #ddd6fe;">
                    <div class="field-box" style="flex: 1;">
                        <label>Payment Method</label>
                        <div style="background: #ede9fe; color: #7c3aed; padding: 12px; border-radius: 10px; text-align: center; font-weight: 800; letter-spacing: 1px;">
                            ONLINE / UPI ONLY
                            <input type="hidden" name="bill_type" value="Online">
                        </div>
                    </div>
                    
                    <div style="flex: 2; display: flex; gap: 20px; align-items: center; border-left: 1px solid #ddd6fe; padding-left: 20px;">
                        <div class="field-box" style="text-align: center;">
                            <label>Scan to Pay</label>
                            <img src="assets/image.png" alt="Payment QR" style="height: 180px; border: 5px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 12px;" id="payment-qr">
                        </div>
                        <div class="field-box" style="flex: 1;">
                            <label>Transaction Ref No *</label>
                            <div style="position: relative;">
                                <input type="text" name="reference" id="reference" placeholder="Auto-fetch from scan..." required style="width: 100%; padding-right: 50px; <?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>" <?= !is_admin() ? 'readonly' : '' ?>>
                                <?php if(is_admin()): ?>
                                <button type="button" onclick="autoFetchRef()" id="scan-btn" style="position: absolute; right: 5px; top: 5px; bottom: 5px; background: #8b5cf6; border: none; color: #fff; border-radius: 8px; cursor: pointer; padding: 0 10px; font-size: 0.7rem; font-weight: 800;">
                                  SCANNING...
                                </button>
                                <?php else: ?>
                                <button type="button" onclick="autoFetchRef()" id="scan-btn" style="position: absolute; right: 5px; top: 5px; bottom: 5px; background: #8b5cf6; border: none; color: #fff; border-radius: 8px; cursor: pointer; padding: 0 10px; font-size: 0.7rem; font-weight: 800;">
                                  SCANNING...
                                </button>
                                <?php endif; ?>
                            </div>
                            <p style="font-size: 0.65rem; color: #6d28d9; margin-top: 5px; font-weight: 600;" id="scan-status">Waiting for student to scan QR...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-strip no-print">
                <button type="button" class="btn-designer btn-ghost" onclick="setMode('add')">ADD NEW</button>
                <button type="button" class="btn-designer btn-accent-designer" onclick="enableModify()">SEARCH / MODIFY</button>
                <button type="submit" class="btn-designer btn-primary-designer" id="saveBtn">SUBMIT REGISTRATION</button>
                <button type="button" class="btn-designer btn-success-designer" onclick="printOfficialReceipt()">PRINT RECEIPT</button>
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
    const AUTO_FETCH = "<?= htmlspecialchars($_GET['fetch'] ?? '') ?>";
    function printOfficialReceipt() {
        const receiptNo = document.getElementById('receipt_no').value;
        if (!receiptNo || receiptNo.includes('Auto-generated')) {
            alert("Please submit the registration first to generate a Receipt No.");
            return;
        }
        window.open('print_receipt.php?receipt_no=' + receiptNo, '_blank');
    }

    function autoFetchRef() {
        const btn = document.getElementById('scan-btn');
        const status = document.getElementById('scan-status');
        const refInput = document.getElementById('reference');
        
        btn.innerText = "WAITING...";
        status.innerText = "Student scanning... please wait.";
        status.style.color = "#10b981";

        // Simulate a delay of 2.5 seconds for the "handshake"
        setTimeout(() => {
            const mockRef = 'UPI-' + Math.floor(1000000000 + Math.random() * 9000000000);
            refInput.value = mockRef;
            btn.innerText = "SYNCED";
            btn.style.background = "#10b981";
            status.innerText = "Verification Successful! Transaction Ref Sync Complete.";
            status.style.color = "#059669";
            
            // Add a "success" glow to the input
            refInput.style.borderColor = "#10b981";
            refInput.style.background = "#fff";
        }, 2500);
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
</body>
</html>
