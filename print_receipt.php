<?php
require 'connection/config.php';
require_login();

if (!isset($_GET['receipt_no'])) {
    header("Location: dashboard.php");
    exit;
}

$receipt_no = $_GET['receipt_no'];
$stmt = $pdo->prepare("SELECT * FROM admissions WHERE receipt_no = ?");
$stmt->execute([$receipt_no]);
$r = $stmt->fetch();

if (!$r) {
    die("Record not found matching Receipt: " . htmlspecialchars($receipt_no));
}

function amountInWords($num) {
    if ($num == 300) return "Rupees Three Hundred Only";
    return "Rupees " . $num . " Only";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - <?= htmlspecialchars($receipt_no) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&family=Inter:wght@400;600;700&display=swap');
        :root { --brand-navy: #0f172a; --text-secondary: #64748b; --accent-blue: #38bdf8; --brand-gold: #f59e0b; }
        
        body { background: #f1f5f9; margin: 0; padding: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }

        /* A5 Slip Container (Half A4) */
        .receipt-slip {
            width: 210mm;
            height: 148.5mm; /* Exact Half A4 Height */
            padding: 15mm;
            box-sizing: border-box;
            background: white;
            font-family: 'Inter', sans-serif;
            position: relative;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .receipt-header { display: flex; align-items: center; justify-content: center; gap: 20px; border-bottom: 2.5px solid var(--brand-navy); padding-bottom: 12px; margin-bottom: 15px; }
        .header-text { text-align: center; }
        .header-text h1 { font-family: 'Outfit'; font-size: 1.4rem; color: var(--brand-navy); margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .header-text p { font-size: 0.65rem; color: var(--text-secondary); margin: 2px 0; font-weight: 600; }

        .receipt-title { text-align: center; font-family: 'Outfit'; font-weight: 800; font-size: 1.15rem; margin: 15px 0; color: var(--brand-navy); text-decoration: underline; text-underline-offset: 5px; }
        
        .info-grid { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 12px; margin-bottom: 20px; font-size: 0.85rem; }
        .info-item { display: flex; align-items: baseline; }
        .info-label { width: 110px; font-weight: 600; color: var(--text-secondary); white-space: nowrap; }
        .info-value { border-bottom: 1.5px dotted #cbd5e1; flex: 1; font-weight: 700; color: #000; padding-bottom: 2px; }

        .fee-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .fee-table th { background: var(--brand-navy); color: #fff; padding: 10px; font-size: 0.75rem; text-align: left; text-transform: uppercase; letter-spacing: 1px; }
        .fee-table td { border: 1.5px solid #e2e8f0; padding: 12px; font-size: 0.9rem; font-weight: 600; }
        .total-row td { background: #f8fafc; font-weight: 900; border-top: 2.5px solid var(--brand-navy); font-size: 1rem; }

        .words-section { font-style: italic; font-size: 0.8rem; margin-bottom: 20px; font-weight: 600; border-left: 4px solid var(--brand-gold); padding-left: 12px; background: #fffcf0; padding: 10px 12px; }
        
        .payment-block { background: #f8fafc; border: 1.5px solid #e2e8f0; padding: 15px; border-radius: 10px; margin-bottom: 40px; }
        .payment-label { font-size: 0.7rem; font-weight: 900; color: var(--brand-navy); margin-bottom: 10px; display: block; text-transform: uppercase; letter-spacing: 1px; }
        .payment-options { display: flex; gap: 30px; align-items: center; }
        .mode-option { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; font-weight: 700; }
        .radio-dot { width: 14px; height: 14px; border: 2.5px solid var(--brand-navy); border-radius: 50%; }
        .radio-dot.active { background: var(--accent-blue); border-color: var(--accent-blue); box-shadow: 0 0 10px rgba(56, 189, 248, 0.4); }

        .sig-container { display: flex; justify-content: space-between; margin-top: 50px; }
        .sig-box { text-align: center; font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); width: 220px; border-top: 2px solid #e2e8f0; padding-top: 8px; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; margin: 0; }
            .receipt-slip { border: none; box-shadow: none; margin: 0; width: 210mm; height: 148.5mm; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="position: fixed; top: 30px; left: 50%; transform: translateX(-50%); z-index: 100;">
    <button style="padding: 16px 45px; background: var(--brand-navy); color: white; border: none; border-radius: 16px; font-weight: 800; font-size: 1rem; cursor: pointer; box-shadow: 0 20px 40px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px;" onclick="window.print()">
        <span>𖡼</span> PRINT ENQUIRY SLIP
    </button>
    <div style="margin-top: 20px; text-align: center;">
        <a href="admission_entry.php" style="text-decoration: none; color: #64748b; font-weight: 700; font-size: 0.9rem; background: rgba(255,255,255,0.8); padding: 8px 20px; border-radius: 30px; backdrop-filter: blur(10px);">← BACK TO ENTRY</a>
    </div>
</div>

<div class="receipt-slip">
    <div class="receipt-header">
        <img src="assets/logo.png" style="height: 70px;">
        <div class="header-text">
            <h1>NADAR SARASWATHI COLLEGE</h1>
            <p>OF ENGINEERING & TECHNOLOGY</p>
            <p>Approved by AICTE, New Delhi & Affiliated to Anna University, Chennai</p>
            <p>Vadapudupatti, Annanji (PO), Theni - 625 531, Tamil Nadu.</p>
        </div>
    </div>

    <div class="receipt-title">APPLICATION FORM FEE RECEIPT</div>

    <div class="info-grid">
        <div class="info-item"><span class="info-label">Receipt No</span><span class="info-value">: <?= htmlspecialchars($r['receipt_no'] ?? '') ?></span></div>
        <div class="info-item"><span class="info-label">Date</span><span class="info-value">: <?= date('d-m-Y', strtotime($r['created_at'] ?? 'now')) ?></span></div>
        <div class="info-item"><span class="info-label">Application No</span><span class="info-value">: <?= htmlspecialchars($r['application_no'] ?? '') ?></span></div>
        <div class="info-item"><span class="info-label">Course</span><span class="info-value">: <?= htmlspecialchars(($r['degree'] ?? '') . " - " . ($r['department'] ?? '')) ?></span></div>
        <div class="info-item"><span class="info-label">Student Name</span><span class="info-value">: <?= htmlspecialchars($r['student_name'] ?? '') ?></span></div>
        <div class="info-item"><span class="info-label">Academic Year</span><span class="info-value">: <?= date('Y') ?> - <?= date('Y') + 4 ?></span></div>
    </div>

    <table class="fee-table">
        <thead><tr><th style="width: 10%;">S.No</th><th style="width: 60%;">Particulars</th><th style="width: 30%;">Amount (Rs)</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Application Form Fee</td><td style="text-align: right;">300.00</td></tr>
            <tr class="total-row"><td colspan="2" style="text-align: right;">Total Amount</td><td style="text-align: right;">300.00</td></tr>
        </tbody>
    </table>

    <div class="words-section">Amount in words: <?= amountInWords(300) ?></div>

    <div class="payment-block">
        <span class="payment-label">Payment Mode</span>
        <div class="payment-options">
            <div class="mode-option"><div class="radio-dot <?= ($r['bill_type'] == 'Cash' || empty($r['bill_type'])) ? 'active' : '' ?>"></div> Cash</div>
            <div class="mode-option"><div class="radio-dot <?= ($r['bill_type'] == 'Online') ? 'active' : '' ?>"></div> Online</div>
            <?php if (!empty($r['reference'])): ?>
                <div style="font-size: 0.8rem; margin-left: auto; font-family: monospace; font-weight: 700; color: var(--brand-navy);">Ref: <?= htmlspecialchars($r['reference']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="sig-container">
        <div class="sig-box">Student / Parent Signature</div>
        <div class="sig-box">Cashier / Authorized Signatory</div>
    </div>
</div>

<script>
    window.onload = () => { setTimeout(() => { window.print(); }, 1000); };
</script>

</body>
</html>
