<?php
require 'connection/connection.php';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - <?= htmlspecialchars($receipt_no) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=Inter:wght@400;600;700;800&display=swap');
        
        :root { 
            --brand-navy: #0f172a; 
            --text-secondary: #475569; 
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            background: #f1f5f9; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            min-height: 100vh; 
            font-family: 'Inter', sans-serif; 
            font-size: 11pt;
        }

        .a4-page {
            width: 210mm;
            height: 297mm;
            background: white;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 0 50px rgba(0,0,0,0.1);
        }

        .receipt-slip {
            width: 100%;
            height: 148.5mm; /* Exact Half A4 */
            padding: 8mm 12mm; /* Compact padding */
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .tear-spacer {
            height: 0;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .tear-line {
            width: 100%;
            border-top: 1.5px dashed #cbd5e1;
            position: absolute;
            top: -1px;
        }

        /* HEADER - COMPACT */
        .receipt-header { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 12px; 
            border-bottom: 2.5px solid var(--brand-navy); 
            padding-bottom: 8px; 
            margin-bottom: 15px; 
        }
        .logo-img { height: 50px; }
        .header-text { text-align: center; }
        .header-text h1 { font-family: 'Outfit'; font-size: 1.2rem; color: var(--brand-navy); font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .header-text p { font-size: 0.55rem; color: var(--text-secondary); margin: 2px 0; font-weight: 700; line-height: 1; }

        .receipt-title { 
            text-align: center; 
            font-family: 'Outfit'; 
            font-weight: 800; 
            font-size: 0.95rem; 
            margin: 10px 0; 
            color: var(--brand-navy); 
            text-decoration: underline; 
            text-underline-offset: 3px; 
            text-transform: uppercase;
        }
        
        /* INFORMATION GRID - SMALL FONT */
        .info-grid { 
            display: grid; 
            grid-template-columns: 1.1fr 0.9fr; 
            gap: 8px 30px; 
            margin-bottom: 15px; 
            font-size: 0.75rem; 
        }
        .info-item { display: flex; align-items: baseline; }
        .info-label { width: 95px; font-weight: 700; color: #475569; }
        .info-value { border-bottom: 1px dotted #94a3b8; flex: 1; font-weight: 800; color: #000; padding-bottom: 1px; }

        /* TABLE - COMPACT */
        .fee-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .fee-table th { background: #f8fafc; color: var(--brand-navy); padding: 6px 10px; font-size: 0.65rem; text-align: left; text-transform: uppercase; letter-spacing: 1px; border: 1.5px solid #e2e8f0; }
        .fee-table td { border: 1.5px solid #e2e8f0; padding: 10px 12px; font-size: 0.75rem; font-weight: 700; }
        .total-row td { background: #f8fafc; font-weight: 900; border-top: 2px solid var(--brand-navy); color: var(--brand-navy); font-size: 0.85rem; }

        .payment-mode-line {
            font-size: 0.7rem;
            font-weight: 800;
            margin-top: 15px;
            color: #1e293b;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; margin: 0; }
            .a4-page { box-shadow: none; border: none; width: 210mm; height: 297mm; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; gap: 10px;">
    <a href="admission_entry.php" style="text-decoration: none; color: #fff; font-weight: 700; font-size: 0.75rem; background: var(--text-secondary); padding: 8px 15px; border-radius: 4px;">← BACK</a>
    <button style="padding: 8px 25px; background: #10b981; color: white; border: none; border-radius: 4px; font-weight: 900; cursor: pointer; font-size: 0.75rem;" onclick="window.print()">PRINT</button>
</div>

<div class="a4-page">
    <?php for($i=0; $i<2; $i++): ?>
    
    <div class="receipt-slip">
        <!-- HEADER -->
        <div class="receipt-header">
            <img src="assets/logo.png" class="logo-img">
            <div class="header-text">
                <h1 style="font-size: 1.3rem; letter-spacing: -0.5px;">NADAR SARASWATHI COLLEGE OF ENGINEERING & TECHNOLOGY</h1>
                <p style="font-size: 0.65rem; color: #475569; font-weight: 800; margin-top: 2px;">Vadapudupatti, Annanji (PO), Theni - 625 531, Tamil Nadu.</p>
                <p style="font-size: 0.5rem; opacity: 0.9; font-weight: 700; color: #b22222; margin-top: 5px;">
                    Approved by AICTE | Affiliated to Anna University | Accredited by NAAC with "A" Grade
                </p>
            </div>
        </div>

        <div class="receipt-title">APPLICATION FORM FEE RECEIPT</div>

        <!-- INFO GRID -->
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Receipt No</span><span class="info-value">: <?= htmlspecialchars($r['receipt_no'] ?? '') ?></span></div>
            <div class="info-item"><span class="info-label">Date</span><span class="info-value">: <?= date('d-m-Y', strtotime($r['receipt_date'] ?? $r['created_at'] ?? 'now')) ?></span></div>
            <div class="info-item"><span class="info-label">Application No</span><span class="info-value">: <?= htmlspecialchars($r['application_no'] ?? '') ?></span></div>
            <div class="info-item"><span class="info-label">Student Name</span><span class="info-value">: <?= strtoupper(htmlspecialchars($r['student_name'] ?? '')) ?></span></div>
            <div class="info-item" style="grid-column: span 2;"><span class="info-label">Course Applied</span><span class="info-value">: <?= htmlspecialchars(($r['degree'] ?? '') . " - " . ($r['department'] ?? '')) ?></span></div>
            <div class="info-item"><span class="info-label">Academic Year</span><span class="info-value">: <?= date('Y') ?> - <?= (date('Y') + 4) ?></span></div>
            <div class="info-item"><span class="info-label">Admission Center</span><span class="info-value">: <?= htmlspecialchars($centers_list[$r['center']] ?? $r['center'] ?? 'General') ?></span></div>
        </div>

        <!-- TABLE -->
        <table class="fee-table">
            <thead>
                <tr>
                    <th style="width: 10%;">S.No</th>
                    <th style="width: 65%;">Particulars</th>
                    <th style="width: 25%; text-align: right;">Amount (Rs)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>Application form fee</td>
                    <td style="text-align: right;">300.00</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;">TOTAL AMOUNT PAID</td>
                    <td style="text-align: right;">300.00</td>
                </tr>
            </tbody>
        </table>

    </div>
    
    <!-- DIVIDER -->
    <?php if($i == 0): ?>
        <div class="tear-spacer">
            <div class="tear-line"></div>
        </div>
    <?php endif; ?>

    <?php endfor; ?>
</div>

<script>
    window.onload = () => { setTimeout(() => { window.print(); }, 1200); };
</script>

</body>
</html>
