<?php
require 'connection/connection.php';
require_login();

$today = date('Y-m-d');
$today_display = date('d F Y');

// DATA FETCHING
// 1. Today's Counts
$stmtToday = $pdo->prepare("SELECT department, degree, COUNT(*) as count FROM admissions WHERE receipt_date = ? GROUP BY department, degree");
$stmtToday->execute([$today]);
$today_rows = $stmtToday->fetchAll();

// 2. Cumulative Counts
$stmtCumul = $pdo->query("SELECT department, degree, COUNT(*) as count FROM admissions GROUP BY department, degree");
$cumul_rows = $stmtCumul->fetchAll();

// 3. Management Quota
$stmtMQ = $pdo->query("SELECT department, COUNT(*) as count FROM admissions WHERE quota = 'Management' GROUP BY department");
$mq_data = $stmtMQ->fetchAll(PDO::FETCH_KEY_PAIR);

// Helpers
function getVal($data, $dept) {
    foreach($data as $row) {
        if (trim(strtolower($row['department'])) == trim(strtolower($dept))) return $row['count'];
    }
    return 0;
}

$depts = [
    'B.E. CSE', 'B.E. Mech', 'B.E. ECE', 'B.E. Civil', 'B.E. EEE', 'B.Tech IT', 'B.Tech AI&DS', 'M.E. Structural'
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
    <title>Application Status | NSCET</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --report-bg: #fdfdfd;
            --card-dark: #27272a;
            --text-muted: #71717a;
        }
        body { background: var(--report-bg); }
        .report-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .report-header h1 { font-size: 2.2rem; color: #18181b; margin-bottom: 5px; opacity: 0.9; }
        .report-header p { color: var(--text-muted); font-weight: 600; font-size: 1.1rem; }
        
        .live-tag { background: #eff6ff; color: #2563eb; padding: 6px 16px; border-radius: 20px; font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border: 1px solid #dbeafe; }

        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
        .summary-card { background: var(--card-dark); color: #fff; padding: 30px; border-radius: 20px; position: relative; }
        .summary-card span { display: block; font-size: 0.9rem; font-weight: 600; opacity: 0.7; margin-bottom: 10px; }
        .summary-card h2 { font-size: 3rem; font-weight: 800; margin-bottom: 5px; }
        .summary-card p { font-size: 0.9rem; opacity: 0.5; font-weight: 500; }

        .insights-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; }
        .insight-box { background: #3f3f46; color: #fff; padding: 40px; border-radius: 24px; }
        .insight-box h3 { font-size: 0.8rem; font-weight: 800; letter-spacing: 1px; margin-bottom: 30px; opacity: 0.6; text-transform: uppercase; }

        .progress-row { display: flex; align-items: center; gap: 20px; margin-bottom: 18px; }
        .dept-name { width: 140px; font-weight: 600; font-size: 0.95rem; opacity: 0.9; }
        .progress-bar { flex: 1; height: 6px; background: #27272a; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 10px; transition: width 1s ease; }
        .val-pill { width: 30px; text-align: right; font-weight: 800; font-size: 1.1rem; opacity: 0.8; }

        .mq-card { background: #3f3f46; color: #fff; padding: 40px; border-radius: 24px; }
        .mq-item { display: flex; justify-content: space-between; padding: 20px 0; border-bottom: 1px solid #52525b; align-items: center; }
        .mq-item:last-child { border-bottom: none; }
        .mq-label { font-size: 1.1rem; font-weight: 600; max-width: 250px; line-height: 1.2; }
        .mq-badge { background: #fff; color: #27272a; height: 36px; width: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; }
    </style>
</head>
<body class="designer-animate">
<div class="app-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <div class="dash-container" style="max-width: 1100px;">
            <div class="report-header">
                <div>
                    <h1>Application sold status</h1>
                    <p>Academic year 2026-27 &middot; Today: <?= $today_display ?></p>
                </div>
                <div class="live-tag">Live report</div>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <span>Today UG</span>
                    <h2><?= str_pad($todayUG, 2, "0", STR_PAD_LEFT) ?></h2>
                    <p>applications</p>
                </div>
                <div class="summary-card">
                    <span>Today PG</span>
                    <h2><?= str_pad($todayPG, 2, "0", STR_PAD_LEFT) ?></h2>
                    <p>applications</p>
                </div>
                <div class="summary-card">
                    <span>Total UG</span>
                    <h2><?= str_pad($totalUG, 2, "0", STR_PAD_LEFT) ?></h2>
                    <p>cumulative</p>
                </div>
                <div class="summary-card">
                    <span>Total PG</span>
                    <h2><?= str_pad($totalPG, 2, "0", STR_PAD_LEFT) ?></h2>
                    <p>cumulative</p>
                </div>
            </div>

            <div class="insights-grid">
                <!-- Today Insights -->
                <div class="insight-box">
                    <h3>Today's UG Applications</h3>
                    <?php foreach($depts as $d): 
                        $val = getVal($today_rows, $d);
                        $w = min(100, ($val * 20)); // Visualization scale
                    ?>
                    <div class="progress-row">
                        <div class="dept-name"><?= $d ?></div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $w ?>%; background: #3b82f6;"></div>
                        </div>
                        <div class="val-pill"><?= $val ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Cumulative Insights -->
                <div class="insight-box">
                    <h3>Cumulative UG + PG</h3>
                    <?php foreach($depts as $d): 
                        $val = getVal($cumul_rows, $d);
                        $w = min(100, ($val * 2)); // Visualization scale
                    ?>
                    <div class="progress-row">
                        <div class="dept-name"><?= $d ?></div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $w ?>%; background: #10b981;"></div>
                        </div>
                        <div class="val-pill"><?= $val ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mq-card">
                <h3 style="font-size: 0.8rem; font-weight: 800; letter-spacing: 1px; margin-bottom: 20px; opacity: 0.6; text-transform: uppercase;">Management Quota Admissions</h3>
                <?php foreach($mq_data as $dept => $count): ?>
                <div class="mq-item">
                    <div class="mq-label"><?= $dept ?></div>
                    <div class="mq-badge"><?= $count ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 40px; text-align: center;">
                <button class="btn-designer btn-primary-designer no-print" onclick="window.print()">GENERATE PDF REPORT</button>
            </div>
        </div>
    </main>
</div>
</body>
</html>
