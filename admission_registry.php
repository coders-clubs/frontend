<?php
require 'connection/connection.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission Registry | NSCET</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .marks-table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .marks-table th { background: var(--brand-navy); color: #fff; padding: 12px; font-size: 0.8rem; text-transform: uppercase; }
        .marks-table td { padding: 8px; border: 1px solid #f1f5f9; }
        .marks-table input { width: 100%; border: none; padding: 10px; background: transparent; text-align: center; font-weight: 600; }
        .marks-table input:focus { background: var(--brand-gold-soft); outline: none; }
        .total-area { background: var(--brand-navy); color: #fff; padding: 20px; border-radius: 16px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="designer-animate">

<header class="no-print">
    <div class="nav-actions"><a href="dashboard.php" class="nav-btn btn-ghost">← DASHBOARD</a></div>
    <div class="branding-center">
        <img src="assets/logo.png" alt="NSCET" class="logo-main" style="height: 120px; max-width: auto;">
        <div class="college-title" style="margin-top: 10px;">NADAR SARASWATHI COLLEGE OF ENGINEERING & TECHNOLOGY</div>
    </div>
    <div class="nav-actions"><a href="logout.php" class="nav-btn btn-danger-soft">LOGOUT</a></div>
</header>

<div class="dash-container" style="margin-top: 40px; margin-bottom: 20px;">
    <div class="hero-section no-print" style="margin-bottom: 40px;">
        <h1 style="font-size: 3rem;">Registry Master</h1>
        <p style="color: var(--text-secondary); font-weight: 600;">Verification and Final Academic Orchestration</p>
    </div>

    <div class="designer-card">
        <!-- Elite Search Bar -->
        <div class="search-panel no-print" style="margin-bottom: 40px; padding: 30px; background: #f1f5f9; border-radius: 24px; border: 1px solid #e2e8f0;">
            <div style="flex: 1;">
                <label style="font-weight: 800; color: var(--brand-navy); font-size: 0.7rem; letter-spacing: 1px;">DATABASE QUERY: RECEIPT NO</label>
                <div style="display: flex; gap: 15px; margin-top: 10px;">
                    <input type="text" id="search_receipt" placeholder="ENTER NS-XXXXX" style="flex: 1; font-weight: 800; letter-spacing: 2px;">
                    <button type="button" class="btn-designer btn-primary-designer" onclick="fetchAdvancedRecord()">SCAN & SYNC</button>
                </div>
            </div>
        </div>

        <form id="advancedAdmissionForm" action="core/admission_handler.php" method="POST">
            <input type="hidden" name="action_type" value="update_advanced">
            <input type="hidden" name="existing_id" id="advanced_id" value="">

            <div class="input-row" style="grid-template-columns: 1fr 1fr;">
                 <!-- Left Column -->
                 <div class="left-col">
                    <div class="section-wrapper">
                        <div class="section-label"><div class="section-number">01</div><div class="section-title">Admission Context</div></div>
                        <div class="input-row">
                            <div class="field-box">
                                <label>Admission Type</label>
                                <div style="display:flex; gap:20px; padding: 10px; <?= !is_admin() ? 'pointer-events: none; opacity: 0.7;' : '' ?>">
                                    <label style="font-size:0.8rem;"><input type="radio" name="admission_type" value="Regular" checked> Fresh Entry</label>
                                    <label style="font-size:0.8rem;"><input type="radio" name="admission_type" value="Lateral"> Lateral Entry</label>
                                </div>
                            </div>
                            <div class="field-box">
                                <label>Receipt No</label>
                                <input type="text" name="receipt_no" id="receipt_no" readonly style="font-weight:800; color:var(--brand-navy);">
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="field-box">
                                <label>Receipt Date</label>
                                <input type="date" name="receipt_date" id="receipt_date" value="<?= date('Y-m-d') ?>" <?= !is_admin() ? 'readonly' : '' ?> style="<?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>">
                            </div>
                            <div class="field-box">
                                <label>Degree</label>
                                <select name="degree" id="degree" <?= !is_admin() ? 'style="pointer-events: none; background: #f1f5f9;"' : '' ?>>
                                    <option value="B.E">B.E</option>
                                    <option value="B.Tech">B.Tech</option>
                                    <option value="M.E">M.E</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="section-wrapper">
                        <div class="section-label"><div class="section-number">02</div><div class="section-title">Student Profile</div></div>
                        <div class="field-box" style="margin-bottom:20px;">
                            <label>Student Name</label>
                            <input type="text" name="student_name" id="student_name" <?= !is_admin() ? 'readonly' : '' ?> style="<?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>">
                        </div>
                        <div class="input-row">
                            <div class="field-box">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" <?= !is_admin() ? 'readonly' : '' ?> style="<?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>">
                            </div>
                            <div class="field-box">
                                <label>Gender</label>
                                <select name="gender" id="gender" <?= !is_admin() ? 'style="pointer-events: none; background: #f1f5f9;"' : '' ?>><option value="Male">Male</option><option value="Female">Female</option></select>
                            </div>
                        </div>
                        <div class="field-box" style="margin-bottom:20px;">
                            <label>Father's Name</label>
                            <input type="text" name="father_name" id="father_name">
                        </div>
                        <div class="input-row">
                            <div class="field-box">
                                <label>Caste</label>
                                <input type="text" name="caste" id="caste">
                            </div>
                            <div class="field-box">
                                <label>State</label>
                                <input type="text" name="state" id="state" value="Tamil Nadu">
                            </div>
                        </div>
                        <div class="field-box" style="margin-bottom:20px;">
                            <label>Full Address</label>
                            <textarea name="address" id="address" rows="2"></textarea>
                        </div>
                        <div class="input-row">
                            <div class="field-box"><label>Place</label><input type="text" name="place" id="place"></div>
                            <div class="field-box"><label>Cell / Mobile</label><input type="text" name="cell_1" id="cell_1"></div>
                        </div>
                    </div>
                 </div>

                 <!-- Right Column -->
                 <div class="right-col">
                    <div class="section-wrapper">
                        <div class="section-label"><div class="section-number">03</div><div class="section-title">Academic & Marks</div></div>
                        <div class="input-row">
                            <div class="field-box">
                                <label>Department</label>
                                <input type="text" name="department" id="department" readonly>
                            </div>
                            <div class="field-box">
                                <label>Reg No (Board/Univ)</label>
                                <input type="text" name="reg_no" id="reg_no">
                            </div>
                        </div>
                        
                        <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-top: 15px; display: block;">MARK DETAILS</label>
                        <table class="marks-table">
                            <thead><tr><th>Subjects</th><th>Max. Marks</th><th>Obtained</th><th>Grade</th></tr></thead>
                            <tbody id="marks-body">
                                <tr><td><input type="text" name="subject[]" value="Physics"></td><td><input type="number" name="max[]" value="100" class="max-m"></td><td><input type="number" name="obt[]" class="obt-m"></td><td><input type="text" name="grade[]"></td></tr>
                                <tr><td><input type="text" name="subject[]" value="Chemistry"></td><td><input type="number" name="max[]" value="100" class="max-m"></td><td><input type="number" name="obt[]" class="obt-m"></td><td><input type="text" name="grade[]"></td></tr>
                                <tr><td><input type="text" name="subject[]" value="Mathematics"></td><td><input type="number" name="max[]" value="100" class="max-m"></td><td><input type="number" name="obt[]" class="obt-m"></td><td><input type="text" name="grade[]"></td></tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn-designer btn-ghost" style="margin-top:10px; width:100%; border: 1px dashed #cbd5e1;" onclick="addMarkRow()">+ ADD SUBJECT</button>

                        <div class="total-area">
                            <span>AGGREGATE TOTAL</span>
                            <span id="grand-total" style="font-size: 1.5rem; font-weight: 800;">0 / 300</span>
                        </div>
                    </div>

                    <div class="section-wrapper">
                        <div class="section-label"><div class="section-number">04</div><div class="section-title">Others & Fees</div></div>
                        <div class="input-row">
                            <div class="field-box"><label>School Name</label><input type="text" name="school_name" id="school_name"></div>
                            <div class="field-box"><label>Percentage (%)</label><input type="text" name="percentage" id="percentage"></div>
                        </div>
                        <div class="input-row">
                            <div class="field-box"><label>Reference</label><input type="text" name="reference" id="reference"></div>
                            <div class="field-box"><label>Ref. Name</label><input type="text" name="reference_name" id="reference_name"></div>
                        </div>
                        <div class="input-row">
                            <div class="field-box">
                                <label>Quota</label>
                                <input type="text" name="quota" id="quota" <?= !is_admin() ? 'readonly' : '' ?> style="<?= !is_admin() ? 'background: #f1f5f9; cursor: not-allowed;' : '' ?>">
                            </div>
                            <div class="field-box">
                                <label>Uravinmurai Letter</label>
                                <select name="uravinmurai_letter" id="uravinmurai_letter" <?= !is_admin() ? 'style="pointer-events: none; background: #f1f5f9;"' : '' ?>><option value="No">No</option><option value="Yes">Yes</option></select>
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="field-box">
                                <label>Bus Stop</label>
                                <input type="text" name="bus_stop" id="bus_stop">
                            </div>
                            <div class="field-box">
                                <label>Hostel</label>
                                <select name="hostel" id="hostel"><option value="No">No</option><option value="Yes">Yes</option></select>
                            </div>
                        </div>
                         <div class="input-row" id="payment_row">
                            <div class="field-box"><label>Fees Name</label><input type="text" name="fees_name" id="fees_name"></div>
                            <div class="field-box"><label>Amount</label><input type="number" name="amount" id="amount"></div>
                            <div class="field-box">
                                <label>Concession Note / Amt</label>
                                <input type="text" name="concession" id="concession" placeholder="e.g. Merit 10% / Staff Child">
                            </div>
                            <div class="field-box">
                                <label>Bill Type</label>
                                <select name="bill_type" id="bill_type" onchange="toggleRegistryPayment(this.value)">
                                    <option value="Cash">Cash</option>
                                    <option value="Online">Online / UPI</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div id="registry_online_stuff" style="display: none; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px; align-items: center; gap: 20px;">
                            <div class="field-box" style="align-items: center;">
                                <label style="font-size: 0.6rem;">Scan to Pay</label>
                                <img src="assets/image.png" alt="Payment QR" style="height: 180px; border: 5px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-radius: 8px;">
                            </div>
                            <div class="field-box" style="flex: 1;">
                                <label>Transaction Ref / UTR No *</label>
                                <input type="text" name="reference" id="reference" placeholder="Enter Reference Number">
                            </div>
                        </div>
                    </div>
                 </div>
            </div>

            <div class="action-strip no-print">
                <button type="button" class="btn-designer btn-ghost" onclick="window.location.reload()">ADD</button>
                <button type="button" class="btn-designer btn-accent-designer" onclick="enableModify()">MODIFY</button>
                <button type="button" class="btn-designer btn-danger-soft" onclick="setMode('delete')">DELETE</button>
                <button type="submit" class="btn-designer btn-primary-designer" id="saveBtn">SAVE RECORDS</button>
                <button type="button" class="btn-designer btn-success-designer" onclick="window.print()">PRINT</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/script.js"></script>
<script>
    function addMarkRow() {
        const tbody = document.getElementById('marks-body');
        const row = document.createElement('tr');
        row.innerHTML = '<td><input type="text" name="subject[]"></td><td><input type="number" name="max[]" value="100" class="max-m"></td><td><input type="number" name="obt[]" class="obt-m"></td><td><input type="text" name="grade[]"></td>';
        tbody.appendChild(row);
        attachMarkListeners();
    }

    function attachMarkListeners() {
        document.querySelectorAll('.obt-m, .max-m').forEach(input => {
            input.oninput = () => {
                let totalObt = 0, totalMax = 0;
                document.querySelectorAll('.obt-m').forEach(i => totalObt += (Number(i.value) || 0));
                document.querySelectorAll('.max-m').forEach(i => totalMax += (Number(i.value) || 0));
                document.getElementById('grand-total').innerText = totalObt + ' / ' + totalMax;
            };
        });
    }
    attachMarkListeners();

    function fetchAdvancedRecord() {
        const receiptInput = document.getElementById('search_receipt');
        const receipt = receiptInput.value.trim();
        if(!receipt) return alert("Please enter a Receipt No.");
        
        fetch(`core/admission_fetch.php?search=${receipt}`)
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const r = data.record;
                    const marks = data.marks || [];
                    
                    document.getElementById('advanced_id').value = r.id;
                    const fields = ['receipt_no', 'student_name', 'date_of_birth', 'gender', 'father_name', 'caste', 'state', 'address', 'place', 'cell_1', 'department', 'school_name', 'percentage', 'reference', 'reference_name', 'hostel', 'uravinmurai_letter', 'fees_name', 'amount', 'bill_type', 'reg_no', 'receipt_date', 'concession', 'degree', 'quota', 'bus_stop'];
                    fields.forEach(f => {
                         const el = document.getElementById(f);
                         if(el) el.value = r[f] || '';
                    });

                    // Build Marks Table
                    const tbody = document.getElementById('marks-body');
                    tbody.innerHTML = ''; // Clear
                    if (marks.length === 0) {
                        addMarkRow(); addMarkRow(); addMarkRow();
                    } else {
                        marks.forEach(m => {
                            const row = document.createElement('tr');
                            row.innerHTML = `<td><input type="text" name="subject[]" value="${m.subject}"></td>
                                             <td><input type="number" name="max[]" value="${m.max}" class="max-m"></td>
                                             <td><input type="number" name="obt[]" value="${m.obt}" class="obt-m"></td>
                                             <td><input type="text" name="grade[]" value="${m.grade}"></td>`;
                            tbody.appendChild(row);
                        });
                    }
                    attachMarkListeners();
                    // Trigger first calculation
                    const firstObt = document.querySelector('.obt-m');
                    if (firstObt) firstObt.dispatchEvent(new Event('input'));
                    
                    alert("Student details & marks fetched! You can now verify or update.");
                } else { alert("Receipt not found: " + receipt); }
            });
    }

    // --- INSTANT FETCH LOGIC ---
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const fetchReceipt = urlParams.get('fetch');
        if (fetchReceipt) {
            document.getElementById('search_receipt').value = fetchReceipt;
            fetchAdvancedRecord();
        }
    });

    function toggleRegistryPayment(val) {
        const container = document.getElementById('registry_online_stuff');
        container.style.display = (val === 'Online') ? 'flex' : 'none';
    }
</script>
</body>
</html>
