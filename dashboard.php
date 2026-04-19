<?php
require 'connection/connection.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | NSCET Admission</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .dash-container { width: 100%; max-width: 1400px; margin: 0 auto; padding: 40px; }
        .hero-section { text-align: center; margin-bottom: 50px; }
        .hero-section h1 { font-size: 3.5rem; color: var(--brand-navy); letter-spacing: -2px; }
        
        .elite-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; margin-bottom: 40px; }
        .elite-card { 
            background: #fff; border-radius: 28px; padding: 35px; 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            text-decoration: none; border: 1px solid #e2e8f0;
            box-shadow: var(--shadow-premium);
            display: flex; flex-direction: column; align-items: flex-start;
            position: relative; overflow: hidden;
        }
        .elite-card:hover { transform: translateY(-8px); border-color: var(--brand-gold); }
        .elite-card h3 { font-size: 1.4rem; color: var(--brand-navy); margin-bottom: 12px; }
        .elite-card p { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; }
        .card-accent { margin-top: 30px; font-weight: 800; font-size: 0.7rem; color: var(--brand-gold-bright); letter-spacing: 2px; text-transform: uppercase; }
        
        .stat-banner { border-radius: 32px; padding: 30px; display: flex; justify-content: space-around; color: #fff; background: var(--brand-navy); }
        .stat-val { display: block; font-size: 2.2rem; font-weight: 800; font-family: 'Outfit'; color: var(--brand-gold); }
        .stat-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.7; }
    </style>
</head>
<body class="designer-animate">
<div class="app-container">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <div class="dash-container">
            <div class="hero-section stagger-1">
                <h1>Admission Center</h1>
                <p style="font-size: 1.2rem; color: var(--text-secondary); max-width: 600px; margin: 20px auto;">Welcome to the portal for academic excellence and student registration.</p>
            </div>

            <?php if (!isset($_SESSION['selected_center'])): ?>
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: var(--brand-navy);">Select Your Center to Proceed</h2>
            </div>
            <div class="elite-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); width: 100%; margin: 0 auto;">
            <?php
            global $centers_list;
            $centers = $centers_list;
            $delay = 1;
            foreach($centers as $id => $name):
                $delay++;
            ?>
                <a href="select_center.php?id=<?= $id ?>" class="elite-card stagger-<?= $delay ?>" style="align-items: center; text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 15px; color: #2a5298;">🏬</div>
                    <h3><?= htmlspecialchars($name) ?></h3>
                    <div class="card-accent" style="margin-top: 20px;">ENTER PORTAL →</div>
                </a>
            <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; margin-bottom: 40px; background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; display: inline-block; position: relative; left: 50%; transform: translateX(-50%);">
                <span style="color: #64748b; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Active Center</span><br>
                <strong style="color: var(--brand-navy); font-size: 24px;"><?= htmlspecialchars($_SESSION['selected_center_name']) ?></strong>
                <br>
                <a href="select_center.php?clear=1" style="display: inline-block; margin-top: 10px; color: #e11d48; text-decoration: none; font-size: 14px; font-weight: bold; padding: 5px 15px; border-radius: 20px; background: #ffe4e6;">Change Center</a>
            </div>

            <div class="elite-grid">
                <a href="admission_entry.php" class="elite-card stagger-2">
                    <div style="font-size: 2rem; margin-bottom: 15px;">📝</div>
                    <h3>Admission Entry</h3>
                    <p>Initialize student profiles.</p>
                    <div class="card-accent">INITIATE FLOW →</div>
                </a>
                <a href="admission_registry.php" class="elite-card stagger-3">
                    <div style="font-size: 2rem; margin-bottom: 15px;">📋</div>
                    <h3>Registry Master</h3>
                    <p>Master command for marks entry, financial records, and institutional verification.</p>
                    <div class="card-accent">FINALIZATION →</div>
                </a>
                <a href="application_records.php" class="elite-card stagger-4">
                    <div style="font-size: 2rem; margin-bottom: 15px;">📊</div>
                    <h3><?= is_admin() ? 'Master Registry' : 'My applicants' ?></h3>
                    <p><?= is_admin() ? 'Global audit trail across all centers with full data export capabilities.' : 'Full audit trail of all applications.' ?></p>
                    <div class="card-accent"><?= is_admin() ? 'MANAGE ALL →' : 'VIEW ARCHIVE →' ?></div>
                </a>
            </div>

            <!-- REFINED: ADMISSION PULSE SECTION -->
            <?php
            $filter = $_GET['pulse_filter'] ?? 'today';
            $filter_date = $_GET['pulse_date'] ?? date('Y-m-d');
            
            $sqlFilter = "";
            $displayLabel = "";
            
            if ($filter == 'today') {
                $sqlFilter = "WHERE receipt_date = CURRENT_DATE";
                $displayLabel = "Today's Pulse";
            } elseif ($filter == 'yesterday') {
                $sqlFilter = "WHERE receipt_date = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)";
                $displayLabel = "Yesterday's Pulse";
            } elseif ($filter == 'date') {
                $sqlFilter = "WHERE receipt_date = ?";
                $displayLabel = "Pulse for " . date('d M Y', strtotime($filter_date));
            } else {
                $sqlFilter = ""; 
                $displayLabel = "Overall Capacity";
            }
            ?>
            <div class="section-wrapper stagger-5" style="background: #fff; border-radius: 28px; padding: 40px; margin-bottom: 40px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-premium);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h2 style="color: var(--brand-navy); font-size: 1.6rem; letter-spacing: -1px;"><?= $displayLabel ?></h2>
                        <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600;">Velocity & enrollment tracking</p>
                    </div>
                    
                    <form method="GET" style="display: flex; gap: 8px; align-items: center; background: #f1f5f9; padding: 10px; border-radius: 20px; border: 1px solid #e2e8f0;">
                        <button type="submit" name="pulse_filter" value="today" class="btn-designer <?= $filter == 'today' ? 'btn-primary-designer' : 'btn-ghost' ?>" style="padding: 10px 18px; font-size: 0.65rem; border-radius: 14px;">Today</button>
                        <button type="submit" name="pulse_filter" value="yesterday" class="btn-designer <?= $filter == 'yesterday' ? 'btn-primary-designer' : 'btn-ghost' ?>" style="padding: 10px 18px; font-size: 0.65rem; border-radius: 14px;">Yesterday</button>
                        <div style="display: flex; align-items: center; gap: 8px; margin-left: 12px; padding-left: 12px; border-left: 2px solid #cbd5e1;">
                            <input type="date" name="pulse_date" value="<?= $filter_date ?>" style="padding: 8px 12px; border-radius: 12px; border: 1px solid #cbd5e1; font-size: 0.75rem; font-weight: 700;">
                            <button type="submit" name="pulse_filter" value="date" class="btn-designer <?= $filter == 'date' ? 'btn-primary-designer' : 'btn-ghost' ?>" style="padding: 10px 18px; font-size: 0.65rem; border-radius: 14px;">Explore</button>
                        </div>
                        <a href="dashboard.php" class="btn-designer btn-ghost" style="padding: 10px 18px; font-size: 0.65rem; border-radius: 14px;">Reset</a>
                    </form>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px;">
                    <?php
                    $seat_capacity = 60; 
                    $stmtHeat = $pdo->prepare("SELECT department, COUNT(*) as count FROM admissions $sqlFilter GROUP BY department");
                    ($filter == 'date') ? $stmtHeat->execute([$filter_date]) : $stmtHeat->execute();
                    $heat_data = $stmtHeat->fetchAll(PDO::FETCH_KEY_PAIR);
                    
                    $target_depts = ['B.E CSE', 'B.E Mech', 'B.E ECE', 'B.E CIVIL', 'B.E EEE', 'B.Tech IT', 'B.Tech AI & DS', 'M.E Structural'];
                    foreach($target_depts as $dept):
                        $count = $heat_data[$dept] ?? 0;
                        $percent = min(100, round(($count / $seat_capacity) * 100));
                        $color = ($percent > 85) ? '#e11d48' : (($percent > 50) ? '#f59e0b' : '#10b981');
                    ?>
                    <div style="background:#f8fafc; padding:25px; border-radius:22px; border:1px solid #e2e8f0; transition: all 0.3s ease;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
                            <span style="font-weight: 800; font-size: 0.8rem; color: var(--brand-navy);"><?= $dept ?></span>
                            <span style="font-size: 0.75rem; font-weight: 800; color: <?= $color ?>; background: white; padding: 4px 10px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <?= $count ?>
                            </span>
                        </div>
                        <div style="height: 8px; background: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 12px;">
                            <div style="width: <?= $percent ?>%; height: 100%; background: <?= $color ?>; border-radius: 10px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.6rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
                            <span><?= ($filter == 'today' || $filter == 'yesterday' || $filter == 'date') ? 'DAILY VELOCITY' : 'TOTAL LOAD' ?></span>
                            <span><?= $percent ?>%</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="stat-banner stagger-5">
                <div class="stat-item">
                    <span class="stat-val">ACTIVE</span>
                    <span class="stat-label">System Status</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val"><?= strtoupper($_SESSION['role'] ?? 'faculty') ?></span>
                    <span class="stat-label">Your Role</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val"><?= htmlspecialchars($_SESSION['faculty_name'] ?? 'User') ?></span>
                    <span class="stat-label">Logged In</span>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
