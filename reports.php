<?php
require 'connection/connection.php';
require_login();

if (!isset($_SESSION['selected_center'])) {
    header("Location: dashboard.php");
    exit;
}

$active_center = $_SESSION['selected_center'];
$active_center_name = $_SESSION['selected_center_name'];
$today = date('Y-m-d');
$today_display = date('d F Y');

// DATA FETCHING (Filtered by Active Center)
// 1. Today's Counts
$stmtToday = $pdo->prepare("SELECT department, degree, COUNT(*) as count FROM admissions WHERE receipt_date = ? AND center = ? GROUP BY department, degree");
$stmtToday->execute([$today, $active_center]);
$today_rows = $stmtToday->fetchAll();

// 2. Cumulative Counts
$stmtCumul = $pdo->prepare("SELECT department, degree, COUNT(*) as count FROM admissions WHERE center = ? GROUP BY department, degree");
$stmtCumul->execute([$active_center]);
$cumul_rows = $stmtCumul->fetchAll();

// 3. Management Quota
$stmtMQ = $pdo->prepare("SELECT department, COUNT(*) as count FROM admissions WHERE quota = 'Management' AND center = ? GROUP BY department");
$stmtMQ->execute([$active_center]);
$mq_data = $stmtMQ->fetchAll(PDO::FETCH_KEY_PAIR);

// Helpers
function getVal($data, $dept) {
    foreach($data as $row) {
        // Soft match to handle case or whitespace differences
        if (trim(strtolower($row['department'])) == trim(strtolower($dept))) return $row['count'];
        // Also check if dept is part of the stored value (e.g. "B.E CSE" vs "CSE")
        if (stripos($row['department'], $dept) !== false) return $row['count'];
    }
    return 0;
}

// Fixed Dept List (Matching typical NSCET standards)
$depts = [
    'B.E CSE', 'B.E Mech', 'B.E ECE', 'B.E CIVIL', 'B.E EEE', 'B.Tech IT', 'B.Tech AI & DS', 'M.E Structural'
];

$todayUG = 0; $todayPG = 0;
$totalUG = 0; $totalPG = 0;

foreach($cumul_rows as $r) {
    if(stripos($r['department'], 'M.E') !== false) $totalPG += $r['count'];
    else $totalUG += $r['count'];
}
foreach($today_rows as $r) {
    if(stripos($r['department'], 'M.E') !== false) $todayPG += $r['count'];
    else $todayUG += $r['count'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission Progress Report | NSCET</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; }
        
        /* PRINT SPECIFIC STYLES - PROFESSIONAL GRADE */
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .app-container { display: block !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
            .dash-container { max-width: 100% !important; border: none !important; box-shadow: none !important; background: white !important; padding: 0 !important; }
            
            .formal-header { display: flex !important; flex-direction: column; align-items: center; text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
            .formal-header h1 { font-size: 22pt !important; margin: 0; color: #000; }
            .formal-header p { font-size: 10pt !important; margin: 5px 0; color: #333; }
            
            .report-title-box { display: block !important; text-align: center; margin-bottom: 30px; }
            .report-title-box h2 { text-decoration: underline; font-size: 16pt; margin-bottom: 10px; }
            
            .formal-table { display: table !important; width: 100%; border-collapse: collapse; margin-bottom: 30px; }
            .formal-table th, .formal-table td { border: 1px solid #000; padding: 8px 12px; font-size: 10pt; text-align: center; }
            .formal-table th { background: #f0f0f0 !important; font-weight: bold; }
            .formal-table .text-left { text-align: left; }
            
            .signature-section { display: flex !important; justify-content: space-between; margin-top: 80px; }
            .sig-box { text-align: center; width: 200px; border-top: 1px solid #000; padding-top: 10px; font-weight: bold; font-size: 10pt; }
            
            .summary-cards-print { display: block !important; margin-bottom: 20px; }
            .summary-print-item { display: inline-block; width: 24%; border: 1px solid #ddd; padding: 10px; text-align: center; }

            /* Hide Web-only Insights */
            .web-only { display: none !important; }
        }

        /* WEB VIEW STYLES */
        .report-card { background: white; border-radius: 20px; padding: 40px; box-shadow: var(--shadow-premium); border: 1px solid #e2e8f0; }
        .formal-header, .report-title-box, .formal-table, .signature-section { display: none; }
        
        .insight-row { display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #f1f5f9; }
        .dept-label { font-weight: 700; color: var(--brand-navy); }
        .count-bubble { background: #f1f5f9; padding: 5px 12px; border-radius: 10px; font-weight: 800; }

    </style>
</head>
<body class="designer-animate">
<div class="app-container">
    <div class="no-print">
        <?php include 'sidebar.php'; ?>
    </div>
    
    <main class="main-content">
        <div class="dash-container">
            <?php include 'branding.php'; ?>
            
            <div class="report-title-box text-center">
                <h2>ADMISSION SOLD STATUS REPORT</h2>
                <p style="font-weight: bold;">Admission Center: <?= htmlspecialchars($active_center_name) ?></p>
                <p>Report Date: <?= $today_display ?></p>
            </div>

            <!-- WEB UI VIEW -->
            <div class="report-card web-only">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div>
                        <h1 style="font-size: 2rem; color: var(--brand-navy);">Admission Sold Status</h1>
                        <p style="color: #64748b; font-weight: 600;">Campus Context: <?= htmlspecialchars($active_center_name) ?></p>
                    </div>
                    <button class="btn-designer btn-primary-designer no-print" onclick="window.print()">🖨️ PRINT FORMAL REPORT</button>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
                    <div style="background: var(--brand-navy); color: white; padding: 25px; border-radius: 20px;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; opacity: 0.7; letter-spacing: 1px;">Today UG</span>
                        <h2 style="font-size: 2.5rem;"><?= $todayUG ?></h2>
                    </div>
                    <div style="background: var(--brand-navy); color: white; padding: 25px; border-radius: 20px;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; opacity: 0.7; letter-spacing: 1px;">Today PG</span>
                        <h2 style="font-size: 2.5rem;"><?= $todayPG ?></h2>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 25px; border-radius: 20px;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; color: #64748b; letter-spacing: 1px;">Cumulative Total</span>
                        <h2 style="font-size: 2.5rem; color: var(--brand-navy);"><?= ($totalUG + $totalPG) ?></h2>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <div>
                        <h3 style="font-size: 1rem; margin-bottom: 20px;">Dept-wise Pulse (Today)</h3>
                        <?php foreach($depts as $d): $v = getVal($today_rows, $d); ?>
                            <div class="insight-row">
                                <span class="dept-label"><?= $d ?></span>
                                <span class="count-bubble" style="<?= $v > 0 ? 'background: #dcfce7; color: #166534;' : '' ?>"><?= $v ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; margin-bottom: 20px;">Management Quota</h3>
                        <?php foreach($mq_data as $dept => $count): ?>
                            <div class="insight-row">
                                <span class="dept-label"><?= $dept ?></span>
                                <span class="count-bubble" style="background: #fef9c3; color: #854d0e;"><?= $count ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- FORMAL TABLE (Print Only) -->
            <table class="formal-table">
                <thead>
                    <tr>
                        <th rowspan="2">S.No</th>
                        <th rowspan="2" class="text-left">Department Name</th>
                        <th colspan="2">Today Status (<?= $active_center_name ?>)</th>
                        <th colspan="2">Cumulative Status</th>
                        <th rowspan="2">Mgmt Quota</th>
                    </tr>
                    <tr>
                        <th>UG</th>
                        <th>PG</th>
                        <th>UG</th>
                        <th>PG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sn = 1;
                    foreach($depts as $d): 
                        $tRow = 0; // Logic for row-wise split if needed
                        $cRow = getVal($cumul_rows, $d);
                        $tRow = getVal($today_rows, $d);
                        $isPG = (stripos($d, 'M.E') !== false);
                    ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td class="text-left"><?= $d ?></td>
                        <td><?= !$isPG ? $tRow : '-' ?></td>
                        <td><?= $isPG ? $tRow : '-' ?></td>
                        <td><?= !$isPG ? $cRow : '-' ?></td>
                        <td><?= $isPG ? $cRow : '-' ?></td>
                        <td><?= $mq_data[$d] ?? '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight: bold; background: #eee;">
                        <td colspan="2">TOTAL</td>
                        <td><?= $todayUG ?></td>
                        <td><?= $todayPG ?></td>
                        <td><?= $totalUG ?></td>
                        <td><?= $totalPG ?></td>
                        <td><?= array_sum($mq_data) ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="signature-section">
                <div class="sig-box">Admission Coordinator</div>
                <div class="sig-box">Verified by (AO)</div>
                <div class="sig-box">PRINCIPAL</div>
            </div>

        </div>
    </main>
</div>
</body>
</html>
