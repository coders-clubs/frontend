<?php
require 'connection/connection.php';
require_login();

if (!isset($_SESSION['selected_center']) && !is_admin()) {
    header("Location: dashboard.php");
    exit;
}

// 1. HANDLE FILTERS
$active_center = $_SESSION['selected_center'] ?? 'nscet';
$active_center_name = $_SESSION['selected_center_name'] ?? 'Main Campus';

// If admin, they can override center selection via GET; Faculty is locked to their active center
$report_center = (is_admin() && isset($_GET['center'])) ? $_GET['center'] : $active_center;
$is_all_centers = (is_admin() && $report_center === 'all');

// Date Filter
$report_date = $_GET['report_date'] ?? date('Y-m-d');
$report_date_display = date('d F Y', strtotime($report_date));

// Prepare SQL Snippets
$center_where = "";
$params_cumul = [];
if (!$is_all_centers) {
    if ($report_center === 'all') {
         // This shouldn't happen for non-admins, but let's be safe
         $report_center = $active_center;
    }
    $center_where = " AND center = :center ";
    $params_cumul = ['center' => $report_center];
}

// 2. DATA FETCHING
// A. Date-wise Counts (Filtered by selected Date and Center)
$stmtDateWise = $pdo->prepare("SELECT department, degree, COUNT(*) as count FROM admissions WHERE receipt_date = :rdate $center_where GROUP BY department, degree");
$stmtDateWise->execute(array_merge(['rdate' => $report_date], $params_cumul));
$date_rows = $stmtDateWise->fetchAll();

// B. Cumulative Counts (Filtered by Center)
$stmtCumul = $pdo->prepare("SELECT department, degree, COUNT(*) as count FROM admissions WHERE 1=1 $center_where GROUP BY department, degree");
$stmtCumul->execute($params_cumul);
$cumul_rows = $stmtCumul->fetchAll();

// C. Quota-wise Counts (Management vs Counselling)
$stmtQuota = $pdo->prepare("SELECT department, quota, COUNT(*) as count FROM admissions WHERE 1=1 $center_where GROUP BY department, quota");
$stmtQuota->execute($params_cumul);
$quota_raw = $stmtQuota->fetchAll();
$quota_data = [];
foreach($quota_raw as $qr) {
    $quota_data[$qr['department']][$qr['quota']] = $qr['count'];
}

// Helpers
function getVal($data, $displayDept) {
    foreach($data as $row) {
        $dbDept = $row['department'] ?? '';
        if ($dbDept === '') continue;
        // Direct or Substring match
        if (stripos($displayDept, $dbDept) !== false || stripos($dbDept, $displayDept) !== false) return $row['count'];
        // Wildcard / Alias matches
        if (stripos($dbDept, 'MECH') !== false && stripos($displayDept, 'Mech') !== false) return $row['count'];
        if (stripos($dbDept, 'AI') !== false && stripos($displayDept, 'AI') !== false) return $row['count'];
        if (stripos($dbDept, 'STRUCTURAL') !== false && stripos($displayDept, 'Structural') !== false) return $row['count'];
        if (stripos($dbDept, 'MANUFACTURING') !== false && stripos($displayDept, 'Manufacturing') !== false) return $row['count'];
    }
    return 0;
}

function getQuota($quotaData, $displayDept, $type) {
    foreach($quotaData as $dbDept => $types) {
        $dbDept = (string)$dbDept;
        if (stripos($displayDept, $dbDept) !== false || stripos($dbDept, $displayDept) !== false) return $types[$type] ?? 0;
        if (stripos($dbDept, 'MECH') !== false && stripos($displayDept, 'Mech') !== false) return $types[$type] ?? 0;
        if (stripos($dbDept, 'AI') !== false && stripos($displayDept, 'AI') !== false) return $types[$type] ?? 0;
        if (stripos($dbDept, 'STRUCTURAL') !== false && stripos($displayDept, 'Structural') !== false) return $types[$type] ?? 0;
        if (stripos($dbDept, 'MANUFACTURING') !== false && stripos($displayDept, 'Manufacturing') !== false) return $types[$type] ?? 0;
    }
    return 0;
}

// Fixed Dept List
$depts = [
    'B.E CSE', 'B.E Mech', 'B.E ECE', 'B.E CIVIL', 'B.E EEE', 'B.Tech IT', 'B.Tech AI & DS', 'M.E Structural'
];

$dateUG = 0; $datePG = 0;
$totalUG = 0; $totalPG = 0;

foreach($cumul_rows as $r) {
    if(stripos($r['department'], 'M.E') !== false) $totalPG += $r['count'];
    else $totalUG += $r['count'];
}
foreach($date_rows as $r) {
    if(stripos($r['department'], 'M.E') !== false) $datePG += $r['count'];
    else $dateUG += $r['count'];
}

$display_center_name = $is_all_centers ? "All Admission Centers" : ($centers_list[$report_center] ?? $active_center_name);

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
            .formal-header h1 { font-size: 20pt !important; margin: 0; color: #003366; font-family: 'Outfit'; }
            .formal-header p { font-size: 8.5pt !important; margin: 2px 0; color: #444; }
            
            .report-title-box { display: block !important; text-align: center; margin: 30px 0; padding: 10px; border-top: 1px double #000; border-bottom: 1px double #000; }
            .report-title-box h2 { font-size: 14pt; margin-bottom: 4px; color: #000; font-family: 'Outfit'; letter-spacing: 1px; }
            .report-title-box p { font-size: 8.5pt; font-weight: 700; margin: 0; }
            
            .formal-table { display: table !important; width: 100%; border-collapse: collapse; margin-bottom: 30px; }
            .formal-table th, .formal-table td { border: 1px solid #000; padding: 6px 4px; font-size: 8pt; text-align: center; }
            .formal-table th { background: #f1f5f9 !important; font-weight: 800; color: #000; text-transform: uppercase; font-size: 7pt; }
            .formal-table .text-left { text-align: left; padding-left: 12px; }
            
            .signature-section { display: flex !important; justify-content: space-between; margin-top: 80px; padding: 0 40px; }
            .sig-box { text-align: center; width: 170px; border-top: 1.5px solid #000; padding-top: 10px; font-weight: 800; font-size: 8pt; text-transform: uppercase; color: #000; }

            .institutional-branding { margin-bottom: 20px !important; padding: 10px 0 !important; }
            .institutional-branding img { height: 80px !important; }
            .institutional-branding h1 { font-size: 22pt !important; }
            .institutional-branding p { font-size: 8pt !important; }
            
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
                <p style="font-weight: bold;">Admission Center: <?= htmlspecialchars($display_center_name) ?></p>
                <p>Report Context: <?= $report_date_display ?></p>
            </div>

            <!-- SUCCESS BANNER (Post-Registration) -->
            <?php if(isset($_GET['msg'])): ?>
            <div class="no-print" style="background: #10b981; color: white; padding: 30px; border-radius: 20px; margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 20px 40px rgba(16, 185, 129, 0.2);">
                <div>
                    <h3 style="margin: 0; font-size: 1.4rem;">🎉 <?= htmlspecialchars($_GET['msg']) ?></h3>
                    <p style="margin: 5px 0 0; opacity: 0.9; font-weight: 600;">The registration has been reflected in the department-wise report below.</p>
                </div>
                <?php if(isset($_GET['new_receipt'])): ?>
                    <a href="print_receipt.php?receipt_no=<?= htmlspecialchars($_GET['new_receipt']) ?>" target="_blank" class="btn-designer" style="background: white; color: #10b981; font-weight: 800;">
                        🖨️ PRINT RECEIPT: <?= htmlspecialchars($_GET['new_receipt']) ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- WEB UI VIEW -->
            <div class="report-card web-only">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                    <div>
                        <h1 style="font-size: 2rem; color: var(--brand-navy);">Admission Sold Status</h1>
                        <p style="color: #64748b; font-weight: 600;">Viewing: <?= htmlspecialchars($display_center_name) ?> | <?= $report_date_display ?></p>
                    </div>
                    <button class="btn-designer btn-primary-designer no-print" onclick="window.print()">🖨️ PRINT FORMAL REPORT</button>
                </div>

                <!-- FILTER FORM -->
                <form method="GET" class="no-print" style="margin-bottom: 40px; background: #f8fafc; padding: 25px; border-radius: 20px; border: 1px solid #e2e8f0; display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                    <?php if(is_admin()): ?>
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Institutional Center</label>
                        <select name="center" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #cbd5e1; font-weight: 600; font-family: inherit;">
                            <option value="all" <?= $report_center == 'all' ? 'selected' : '' ?>>All Centers (Combined)</option>
                            <?php foreach($centers_list as $id => $name): ?>
                                <option value="<?= $id ?>" <?= $report_center == $id ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Report Date</label>
                        <input type="date" name="report_date" value="<?= $report_date ?>" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #cbd5e1; font-weight: 600; font-family: inherit;">
                    </div>
                    <button type="submit" class="btn-designer btn-primary-designer" style="padding: 14px 30px; border-radius: 14px;">Generate Report</button>
                    <a href="reports.php" class="btn-designer btn-ghost" style="padding: 14px 20px; border-radius: 14px; text-decoration: none; border: 1px solid #e2e8f0; background: white; font-size: 0.8rem;">Reset Filters</a>
                </form>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
                    <div style="background: var(--brand-navy); color: white; padding: 25px; border-radius: 20px;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; opacity: 0.7; letter-spacing: 1px;">Selected Date UG</span>
                        <h2 style="font-size: 2.5rem;"><?= $dateUG ?></h2>
                    </div>
                    <div style="background: var(--brand-navy); color: white; padding: 25px; border-radius: 20px;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; opacity: 0.7; letter-spacing: 1px;">Selected Date PG</span>
                        <h2 style="font-size: 2.5rem;"><?= $datePG ?></h2>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 25px; border-radius: 20px;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; color: #64748b; letter-spacing: 1px;">Cumulative Total</span>
                        <h2 style="font-size: 2.5rem; color: var(--brand-navy);"><?= ($totalUG + $totalPG) ?></h2>
                    </div>
                    <div style="background: #fffbeb; border: 1px solid #fbbf24; padding: 25px; border-radius: 20px;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; color: #b45309; letter-spacing: 1px;">Mgmt Quota Total</span>
                        <h2 style="font-size: 2.5rem; color: #92400e;">
                            <?php 
                                $totalMQ = 0;
                                foreach($quota_data as $qd) $totalMQ += ($qd['Management'] ?? 0);
                                echo $totalMQ;
                            ?>
                        </h2>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 40px;">
                    <div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 20px; color: var(--brand-navy);">Departmental Performance Overview</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                            <?php foreach($depts as $d): 
                                $vDate = getVal($date_rows, $d);
                                $vMQ = getQuota($quota_data, $d, 'Management');
                                $vMerit = getQuota($quota_data, $d, 'Merit');
                            ?>
                                <div class="insight-row" style="background: #f8fafc; padding: 15px 25px; border-radius: 15px; border: 1px solid #e2e8f0; border-bottom: none;">
                                    <span class="dept-label" style="font-size: 0.9rem;"><?= $d ?></span>
                                    <div style="display: flex; gap: 10px;">
                                        <div style="text-align: center;">
                                            <span style="display: block; font-size: 0.6rem; text-transform: uppercase; color: #64748b; font-weight: 800; margin-bottom: 3px;">Date Total</span>
                                            <span class="count-bubble" style="<?= $vDate > 0 ? 'background: var(--brand-navy); color: white;' : '' ?>"><?= $vDate ?></span>
                                        </div>
                                        <div style="text-align: center;">
                                            <span style="display: block; font-size: 0.6rem; text-transform: uppercase; color: #64748b; font-weight: 800; margin-bottom: 3px;">MQ</span>
                                            <span class="count-bubble" style="<?= $vMQ > 0 ? 'background: #fef9c3; color: #854d0e;' : '' ?>"><?= $vMQ ?></span>
                                        </div>
                                        <div style="text-align: center;">
                                            <span style="display: block; font-size: 0.6rem; text-transform: uppercase; color: #64748b; font-weight: 800; margin-bottom: 3px;">Counselling</span>
                                            <span class="count-bubble" style="<?= $vMerit > 0 ? 'background: #dcfce7; color: #166534;' : '' ?>"><?= $vMerit ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FORMAL TABLE (Print Only) -->
            <table class="formal-table">
                <thead>
                    <tr>
                        <th rowspan="2">S.No</th>
                        <th rowspan="2" class="text-left">Department Name</th>
                        <th colspan="2">Status for <?= $report_date_display ?></th>
                        <th colspan="2">Cumulative Status (<?= $display_center_name ?>)</th>
                        <th rowspan="2">Management</th>
                        <th rowspan="2">Counselling</th>
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
                        $cRow = getVal($cumul_rows, $d);
                        $dRow = getVal($date_rows, $d);
                        $isPG = (stripos($d, 'M.E') !== false);
                    ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td class="text-left"><?= $d ?></td>
                        <td><?= !$isPG ? $dRow : '-' ?></td>
                        <td><?= $isPG ? $dRow : '-' ?></td>
                        <td><?= !$isPG ? $cRow : '-' ?></td>
                        <td><?= $isPG ? $cRow : '-' ?></td>
                        <td><?= getQuota($quota_data, $d, 'Management') ?: '-' ?></td>
                        <td><?= getQuota($quota_data, $d, 'Merit') ?: '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight: bold; background: #eee;">
                        <td colspan="2">TOTAL</td>
                        <td><?= $dateUG ?></td>
                        <td><?= $datePG ?></td>
                        <td><?= $totalUG ?></td>
                        <td><?= $totalPG ?></td>
                        <td>
                            <?php 
                                $sumMQ = 0;
                                foreach($quota_data as $qd) $sumMQ += ($qd['Management'] ?? 0);
                                echo $sumMQ;
                            ?>
                        </td>
                        <td>
                            <?php 
                                $sumMerit = 0;
                                foreach($quota_data as $qd) $sumMerit += ($qd['Merit'] ?? 0);
                                echo $sumMerit;
                            ?>
                        </td>
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
