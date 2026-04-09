<?php
require 'includes/config.php';
require_login();
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

            <!-- Section 1 -->
            <div class="section-wrapper">
                <div class="section-label">
                    <div class="section-number">01</div>
                    <div class="section-title">Institutional Records</div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Registry Receipt No</label>
                        <input type="text" name="receipt_no" id="receipt_no" value="(Auto-generated on Save)" required readonly style="font-weight: 800; color: var(--brand-navy);">
                    </div>
                    <div class="field-box">
                        <label>Admission Entry Type</label>
                        <select name="admission_type" id="admission_type" required>
                            <option value="">Select Type</option>
                            <option value="Regular">Regular</option>
                            <option value="Lateral">Lateral Entry</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="section-wrapper">
                <div class="section-label">
                    <div class="section-number">02</div>
                    <div class="section-title">Personal Information</div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Full Student Name *</label>
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
                        <label>Birth Date *</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" required>
                    </div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Father's Name *</label>
                        <input type="text" name="father_name" id="father_name" required>
                    </div>
                    <div class="field-box">
                        <label>Father's Pursuit *</label>
                        <input type="text" name="father_occupation" id="father_occupation" required placeholder="Occupation">
                    </div>
                </div>

                <div class="input-row">
                    <div class="field-box">
                        <label>Mother's Name *</label>
                        <input type="text" name="mother_name" id="mother_name" required>
                    </div>
                    <div class="field-box">
                        <label>Mother's Pursuit *</label>
                        <input type="text" name="mother_occupation" id="mother_occupation" required>
                    </div>
                </div>

                <div class="input-row" style="grid-template-columns: 2fr 1fr 1fr;">
                    <div class="field-box">
                        <label>Contact Address</label>
                        <textarea name="address" id="address" rows="1" placeholder="Current address details"></textarea>
                    </div>
                    <div class="field-box">
                        <label>City / Location</label>
                        <input type="text" name="city" id="city">
                    </div>
                    <div class="field-box">
                        <label>Postal Code</label>
                        <input type="number" name="pincode" id="pincode" placeholder="6 digits">
                    </div>
                </div>

                <div class="input-row">
                    <div class="field-box">
                        <label>Primary Mobile *</label>
                        <input type="number" name="cell_1" id="cell_1" required placeholder="10 Digits">
                    </div>
                    <div class="field-box">
                        <label>Religion</label>
                        <select name="religion" id="religion">
                            <option value="">Select Religion</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Muslim">Muslim</option>
                            <option value="Christian">Christian</option>
                            <option value="Sikh">Sikh</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>Community Group</label>
                        <input type="text" name="community" id="community" placeholder="e.g. BC / MBC / SC">
                    </div>
                </div>
            </div>

            <!-- Section 3 -->
            <div class="section-wrapper">
                <div class="section-label">
                    <div class="section-number">03</div>
                    <div class="section-title">Academic Allotment</div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Central Application No *</label>
                        <input type="text" name="application_no" id="application_no" value="(Auto-generated on Save)" required readonly style="font-weight: 800;">
                    </div>
                    <div class="field-box">
                        <label>Program / Degree *</label>
                        <select name="degree" id="degree" required>
                            <option value="">Select Degree</option>
                            <option value="B.Tech">B.Tech</option>
                            <option value="B.E">B.E</option>
                        </select>
                    </div>
                    <div class="field-box">
                        <label>Academic Department *</label>
                        <select name="department" id="department" required>
                            <option value="">Select Branch</option>
                            <option value="AI&DS">AI&DS Engineering</option>
                            <option value="CSE">Computer Science & Engg</option>
                            <option value="IT">Information Technology</option>
                            <option value="ECE">Electronics & Communication</option>
                            <option value="EEE">Electrical & Electronics</option>
                            <option value="MECHANICAL">Mechanical Engineering</option>
                            <option value="CIVIL">Civil Engineering</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-row">
                    <div class="field-box">
                        <label>Scheduled Joining Date *</label>
                        <input type="date" name="date_of_joining" id="date_of_joining" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="field-box">
                        <label>Student Quota</label>
                        <select name="quota" id="quota">
                            <option value="Merit">Merit (Anna University)</option>
                            <option value="Management">Management Quota</option>
                            <option value="Sports">Sports Scholarship</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="action-strip no-print">
                <button type="button" class="btn-designer btn-ghost" onclick="setMode('add')">ADD NEW</button>
                <button type="button" class="btn-designer btn-accent-designer" onclick="enableModify()">SEARCH / MODIFY</button>
                <button type="submit" class="btn-designer btn-primary-designer" id="saveBtn">SUBMIT REGISTRATION</button>
                <button type="button" class="btn-designer btn-success-designer" onclick="window.print()">PRINT RECORD</button>
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
</script>
<script src="assets/js/script.js"></script>
</body>
</html>
