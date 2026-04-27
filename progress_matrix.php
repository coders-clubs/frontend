<?php
require 'connection/connection.php';
require_login();
require_admin();

// Matrix Configuration
$target_depts = [
    'B.E CSE', 'B.E Mech', 'B.E ECE', 'B.E CIVIL', 'B.E EEE', 
    'B.Tech IT', 'B.Tech AI & DS', 'M.E Structural'
];

// 1. FETCH MATRIX DATA
$stmtMatrix = $pdo->query("SELECT center, department, COUNT(*) as total 
                         FROM admissions 
                         GROUP BY center, department");
$matrix_raw = $stmtMatrix->fetchAll();

$matrix_data = [];
foreach($matrix_raw as $row) {
    // Find the closest matching dept from our official list
    $matched_dept = null;
    foreach($target_depts as $td) {
        if (stripos($row['department'], trim(str_ireplace(['B.E', 'B.Tech', 'M.E'], '', $td))) !== false) {
            $matched_dept = $td;
            break;
        }
    }
    if($matched_dept) {
        $matrix_data[$row['center']][$matched_dept] = ($matrix_data[$row['center']][$matched_dept] ?? 0) + $row['total'];
    }
}

// 2. FETCH HOURLY PULSE (Today's velocity)
$stmtVelocity = $pdo->query("SELECT center, COUNT(*) as today 
                           FROM admissions 
                           WHERE receipt_date = CURRENT_DATE 
                           GROUP BY center");
$velocity_data = $stmtVelocity->fetchAll(PDO::FETCH_KEY_PAIR);

// 3. GLOBAL STATS
$stmtTotal = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN degree LIKE 'B.%' THEN 1 ELSE 0 END) as ug_total,
    SUM(CASE WHEN degree LIKE 'M.%' THEN 1 ELSE 0 END) as pg_total,
    SUM(CASE WHEN quota = 'Management' THEN 1 ELSE 0 END) as mq_total
    FROM admissions");
$global = $stmtTotal->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Matrix | NSCET Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .matrix-container { width: 100%; overflow-x: auto; background: white; border-radius: 24px; padding: 30px; box-shadow: var(--shadow-premium); border: 1px solid #e2e8f0; }
        .matrix-table { width: 100%; border-collapse: separate; border-spacing: 4px; }
        .matrix-table th { padding: 15px; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; background: #f8fafc; border-radius: 10px; }
        .matrix-table td { padding: 15px; text-align: center; border-radius: 10px; transition: all 0.3s ease; border: 1px solid #f1f5f9; }
        
        .matrix-cell { position: relative; cursor: pointer; }
        .matrix-cell:hover { transform: scale(1.05); z-index: 10; box-shadow: 0 10px 20px rgba(0,0,0,0.1); border-color: var(--brand-navy); }
        .cell-val { font-family: 'Outfit'; font-weight: 800; font-size: 1.2rem; display: block; }
        .cell-label { font-size: 0.55rem; text-transform: uppercase; opacity: 0.6; font-weight: 700; }
        
        /* Intensity Colors */
        .intensity-0 { background: #fff; color: #cbd5e1; }
        .intensity-low { background: #f0f9ff; color: #0369a1; }
        .intensity-med { background: #e0f2fe; color: #0284c7; }
        .intensity-high { background: #bae6fd; color: #0369a1; border-color: #7dd3fc; }
        .intensity-elite { background: var(--brand-navy); color: var(--brand-gold); }

        .stat-card-mini { background: white; padding: 20px; border-radius: 20px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: flex-start; gap: 5px; }
        .stat-card-mini .label { font-size: 0.6rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card-mini .val { font-size: 1.8rem; font-weight: 800; color: var(--brand-navy); font-family: 'Outfit'; }
    </style>
</head>
<body class="designer-animate">
<div class="app-container">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <div class="dash-container">
            <?php include 'branding.php'; ?>

            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
                <div>
                    <h1 style="font-size: 2.2rem; color: var(--brand-navy); letter-spacing: -1px;">Interconnected Progress Matrix</h1>
                    <p style="color: var(--text-secondary); font-weight: 600;">Real-time simultaneous tracking across all centers and departments</p>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="stat-card-mini" style="border-left: 4px solid var(--brand-navy);">
                        <span class="label">UG Cumulative</span>
                        <span class="val"><?= $global['ug_total'] ?></span>
                    </div>
                    <div class="stat-card-mini" style="border-left: 4px solid var(--brand-gold);">
                        <span class="label">PG Cumulative</span>
                        <span class="val"><?= $global['pg_total'] ?></span>
                    </div>
                    <div class="stat-card-mini" style="border-left: 4px solid #10b981;">
                        <span class="label">Total Admissions</span>
                        <span class="val"><?= $global['total'] ?></span>
                    </div>
                </div>
            </div>

            <div class="matrix-container">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="text-align: left; background: white;">Department</th>
                            <?php foreach($centers_list as $id => $name): ?>
                            <th>
                                <div style="max-width: 80px; margin: 0 auto; line-height: 1.2;">
                                    <?= htmlspecialchars($name) ?>
                                    <span style="display: block; font-size: 0.55rem; color: #10b981; margin-top: 5px;">+<?= $velocity_data[$id] ?? 0 ?> TODAY</span>
                                </div>
                            </th>
                            <?php endforeach; ?>
                            <th style="background: var(--brand-gold-soft); color: var(--brand-gold-bright);">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $dept_totals = [];
                        foreach($target_depts as $dept): 
                            $row_total = 0;
                        ?>
                        <tr>
                            <td style="text-align: left; font-weight: 800; color: var(--brand-navy); font-size: 0.8rem; background: #f8fafc; border-radius: 10px; padding-left: 20px;">
                                <?= $dept ?>
                            </td>
                            <?php foreach($centers_list as $cid => $cname): 
                                $val = $matrix_data[$cid][$dept] ?? 0;
                                $row_total += $val;
                                $intensity = 'intensity-0';
                                if($val > 20) $intensity = 'intensity-elite';
                                elseif($val > 10) $intensity = 'intensity-high';
                                elseif($val > 5) $intensity = 'intensity-med';
                                elseif($val > 0) $intensity = 'intensity-low';
                            ?>
                            <td class="matrix-cell <?= $intensity ?>" onclick="window.location='application_records.php?filter_center=<?= $cid ?>&filter_dept=<?= urlencode($dept) ?>'">
                                <span class="cell-val"><?= $val ?></span>
                                <span class="cell-label">Sold</span>
                            </td>
                            <?php endforeach; ?>
                            <td style="background: var(--brand-gold-soft); font-weight: 800; color: var(--brand-navy);">
                                <span class="cell-val"><?= $row_total ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="border-top: 2px solid #e2e8f0;">
                            <td style="text-align: left; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; color: #64748b;">Center Totals</td>
                            <?php 
                            $grand_total = 0;
                            foreach($centers_list as $cid => $cname): 
                                $c_total = 0;
                                foreach($target_depts as $dept) $c_total += ($matrix_data[$cid][$dept] ?? 0);
                                $grand_total += $c_total;
                            ?>
                            <td style="font-weight: 800; font-family: 'Outfit'; font-size: 1.1rem; color: var(--brand-navy); background: #f1f5f9;">
                                <?= $c_total ?>
                            </td>
                            <?php endforeach; ?>
                            <td style="background: var(--brand-navy); color: var(--brand-gold); font-weight: 800; font-size: 1.2rem;">
                                <?= $grand_total ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div style="margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div class="designer-card" style="padding: 30px;">
                    <h3 style="margin-bottom: 20px; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                        <span>📶</span> Registration Velocity Insight
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach($velocity_data as $cid => $today_count): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;"><?= $centers_list[$cid] ?></span>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="height: 6px; width: 100px; background: #f1f5f9; border-radius: 10px; overflow: hidden;">
                                    <div style="height: 100%; width: <?= min(100, $today_count * 10) ?>%; background: #10b981;"></div>
                                </div>
                                <span style="font-weight: 800; color: #10b981; font-size: 0.9rem;">+<?= $today_count ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="designer-card" style="padding: 30px; background: var(--brand-navy); color: white;">
                    <h3 style="margin-bottom: 20px; font-size: 1.1rem; color: var(--brand-gold);">
                        🎯 Quota Interaction Study
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px;">
                            <span style="font-size: 0.6rem; text-transform: uppercase; color: var(--brand-gold); font-weight: 800; letter-spacing: 1px;">Management Total</span>
                            <div style="font-size: 2rem; font-weight: 800; font-family: 'Outfit';"><?= $global['mq_total'] ?></div>
                            <div style="font-size: 0.65rem; opacity: 0.6; margin-top: 5px;">Overall Institutional Load</div>
                        </div>
                        <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px;">
                            <span style="font-size: 0.6rem; text-transform: uppercase; color: var(--brand-gold); font-weight: 800; letter-spacing: 1px;">Merit/General</span>
                            <div style="font-size: 2rem; font-weight: 800; font-family: 'Outfit';"><?= $global['total'] - $global['mq_total'] ?></div>
                            <div style="font-size: 0.65rem; opacity: 0.6; margin-top: 5px;">Counselling & Government</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>
</body>
</html>
