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
        .dash-container { width: 100%; max-width: 1400px; margin: 0 auto; padding: 20px 40px; }
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
    <?php if (isset($_SESSION['selected_center'])): ?>
        <?php include 'sidebar.php'; ?>
    <?php endif; ?>
    
    <main class="main-content" style="<?= !isset($_SESSION['selected_center']) ? 'margin-left: 0; width: 100%; max-width: 100%;' : '' ?>">
        <?php if (!isset($_SESSION['selected_center'])): ?>
            <div style="position: absolute; top: 30px; right: 40px; z-index: 100;">
                <a href="logout.php" class="btn-designer btn-ghost" style="color: #ef4444; font-size: 0.7rem; border-radius: 12px; background: #fff; border: 1px solid #fee2e2;">
                    <span>🚪</span> Logout
                </a>
            </div>
        <?php endif; ?>

        <div class="dash-container">
            <?php include 'branding.php'; ?>
            
            <!-- HERO WELCOME SECTION -->
            <div class="hero-section stagger-1">
                <span style="color: var(--brand-gold); font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 3px; display: block; margin-bottom: 10px;">Institutional Portal</span>
                <h1>Welcome, <?= htmlspecialchars($_SESSION['faculty_name'] ?? 'Colleague') ?></h1>
                <p style="font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 15px auto;">
                    <?= isset($_SESSION['selected_center']) ? 'Manage institutional registrations and student records for ' . htmlspecialchars($_SESSION['selected_center_name']) . '.' : 'Please choose a campus location to begin managing admissions.' ?>
                </p>
                
                <?php if (isset($_SESSION['selected_center'])): ?>
                <div style="display: flex; justify-content: center; margin-bottom: 50px;">
                    <div style="text-align: center; background: #fff; padding: 25px 50px; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-premium); position: relative;">
                        <span style="color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px;">Active Center</span>
                        <h2 style="color: var(--brand-navy); font-size: 2rem; margin: 10px 0; font-weight: 800;"><?= htmlspecialchars($_SESSION['selected_center_name']) ?></h2>
                        <a href="select_center.php?clear=1" class="btn-designer btn-ghost" style="color: #ef4444; font-size: 0.75rem; font-weight: 800; border: 1px solid #fee2e2; background: #fffafb; border-radius: 14px; padding: 8px 20px;">
                             🔄 Change Center
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!isset($_SESSION['selected_center'])): ?>
                <!-- CENTER SELECTION VIEW -->
                <div class="elite-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); width: 100%; max-width: 1100px; margin: 0 auto; gap: 25px;">
                <?php
                global $centers_list;
                $centers = $centers_list;
                $delay = 1;
                foreach($centers as $id => $name):
                    $delay++;
                ?>
                    <a href="select_center.php?id=<?= $id ?>" class="elite-card stagger-<?= $delay ?>" style="align-items: center; text-align: center; border-radius: 24px; padding: 40px 20px; background: #fff; border: 1px solid #f1f5f9;">
                        <div style="width: 70px; height: 70px; background: #f8fafc; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 25px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                            🏢
                        </div>
                        <h3 style="font-size: 1.2rem; min-height: 3rem; display: flex; align-items: center; justify-content: center;"><?= htmlspecialchars($name) ?></h3>
                        <div class="card-accent" style="margin-top: 15px; background: var(--brand-navy); color: #fff; padding: 8px 15px; border-radius: 12px; font-size: 0.65rem;">SELECT CENTER</div>
                    </a>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- MAIN DASHBOARD FEATURES -->
                <div class="elite-grid">
                    <a href="admission_entry.php" class="elite-card stagger-2">
                        <div style="font-size: 2rem; margin-bottom: 15px;">📝</div>
                        <h3>Admission Entry</h3>
                        <p>Initialize student profiles.</p>
                        <div class="card-accent">INITIATE FLOW →</div>
                    </a>
                    <a href="enquiry_entry.php" class="elite-card stagger-2">
                        <div style="font-size: 2rem; margin-bottom: 15px;">🔍</div>
                        <h3>Enquiry Form</h3>
                        <p>Capture initial student enquiries.</p>
                        <div class="card-accent">NEW ENQUIRY →</div>
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
                
                <!-- GLOBAL CENTER PULSE (Admin Only) -->
                <?php if(is_admin()): ?>
                <div class="section-wrapper stagger-4" style="background: var(--brand-navy); border-radius: 28px; padding: 40px; margin-bottom: 40px; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                        <div>
                            <h2 style="color: var(--brand-gold); font-size: 1.8rem; letter-spacing: -1px;">Institutional Center Pulse</h2>
                            <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem; font-weight: 600;">Real-time velocity tracking across all 10 admission centers</p>
                        </div>
                        <?php
                        $stmtGlobal = $pdo->query("
                            SELECT 
                                center, 
                                COUNT(*) as total, 
                                SUM(CASE WHEN receipt_date = CURRENT_DATE THEN 1 ELSE 0 END) as today,
                                SUM(CASE WHEN hostel = 'Yes' AND gender = 'Male' THEN 1 ELSE 0 END) as h_male,
                                SUM(CASE WHEN hostel = 'Yes' AND gender = 'Female' THEN 1 ELSE 0 END) as h_female
                            FROM admissions 
                            WHERE record_type = 'Application' 
                            GROUP BY center
                        ");
                        $global_stats = [];
                        $grand_h_male = 0;
                        $grand_h_female = 0;
                        while($row = $stmtGlobal->fetch()) { 
                            $global_stats[$row['center']] = $row; 
                            $grand_h_male += $row['h_male'];
                            $grand_h_female += $row['h_female'];
                        }
                        ?>
                        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                            <div style="background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); font-size: 0.7rem; font-weight: 800; letter-spacing: 1px;">
                                GLOBAL OVERVIEW
                            </div>
                            <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
                                <span style="font-size: 0.7rem; font-weight: 800; color: var(--brand-gold); text-transform: uppercase;">ALL CENTERS HOSTEL</span>
                                <div style="display: flex; gap: 10px;">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8);">Boys: <b style="color: white;"><?= $grand_h_male ?? 0 ?></b></span>
                                    <span style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8);">Girls: <b style="color: white;"><?= $grand_h_female ?? 0 ?></b></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 50px;">
                        <?php foreach($centers_list as $id => $name): 
                            $stats = $global_stats[$id] ?? ['total' => 0, 'today' => 0, 'h_male' => 0, 'h_female' => 0];
                            $max_goal = 200; // Arbitrary center goal for visualization
                            $pct = min(100, round(($stats['total'] / $max_goal) * 100));
                        ?>
                        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 20px; border-radius: 20px; transition: all 0.3s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--brand-gold); max-width: 120px;"><?= htmlspecialchars($name) ?></span>
                                <span style="background: var(--brand-gold); color: var(--brand-navy); font-size: 0.6rem; padding: 3px 8px; border-radius: 8px; font-weight: 900;">+<?= $stats['today'] ?> TODAY</span>
                            </div>
                            <div style="font-size: 1.8rem; font-weight: 800; font-family: 'Outfit'; margin-bottom: 5px;"><?= $stats['total'] ?></div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.65rem; color: rgba(255,255,255,0.6); font-weight: 700; text-transform: uppercase;">
                                <span>Hostel Boys: <b style="color: white;"><?= $stats['h_male'] ?></b></span>
                                <span>Hostel Girls: <b style="color: white;"><?= $stats['h_female'] ?></b></span>
                            </div>
                            <div style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; margin-top: 10px;">
                                <div style="width: <?= $pct ?>%; height: 100%; background: var(--brand-gold); border-radius: 10px;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>


                </div>
                <?php endif; ?>

                <!-- ADMISSION PULSE SECTION -->
                <?php
                $filter = $_GET['pulse_filter'] ?? 'today';
                $filter_date = $_GET['pulse_date'] ?? date('Y-m-d');
                
                $sqlFilter = "WHERE center = :center AND record_type = 'Application'";
                $displayLabel = "";
                $params = ['center' => $_SESSION['selected_center']];
                
                if ($filter == 'today') {
                    $sqlFilter .= " AND receipt_date = CURRENT_DATE";
                    $displayLabel = "Today's Pulse";
                } elseif ($filter == 'yesterday') {
                    $sqlFilter .= " AND receipt_date = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)";
                    $displayLabel = "Yesterday's Pulse";
                } elseif ($filter == 'date') {
                    $sqlFilter .= " AND receipt_date = :date";
                    $params['date'] = $filter_date;
                    $displayLabel = "Pulse for " . date('d M Y', strtotime($filter_date));
                } else {
                    $displayLabel = "Overall Capacity";
                }
                ?>
                <div class="section-wrapper stagger-5" style="background: #fff; border-radius: 28px; padding: 40px; margin-bottom: 40px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-premium);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; flex-wrap: wrap; gap: 20px;">
                        <div>
                            <h2 style="color: var(--brand-navy); font-size: 1.6rem; letter-spacing: -1px;"><?= $displayLabel ?></h2>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600;">Velocity & enrollment tracking for <?= htmlspecialchars($_SESSION['selected_center_name']) ?></p>
                        </div>
                        
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <!-- Interactive Tabs -->
                            <div style="background: #f1f5f9; padding: 5px; border-radius: 12px; display: flex; gap: 5px; border: 1px solid #e2e8f0;">
                                <button type="button" class="btn-designer tab-btn active" onclick="switchPulse('overall')" id="btn-overall" style="padding: 10px 20px; font-size: 0.65rem; border-radius: 8px;">📊 OVERALL</button>
                                <button type="button" class="btn-designer tab-btn" onclick="switchPulse('mq')" id="btn-mq" style="padding: 10px 20px; font-size: 0.65rem; border-radius: 8px; color: #b45309;">🏢 MANAGEMENT</button>
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
                    </div>

                    <?php
                    // Calculate Summary for the current pulse filter
                    $stmtSum = $pdo->prepare("
                        SELECT 
                            SUM(CASE WHEN quota IN ('Government', 'Counselling') THEN 1 ELSE 0 END) as gq_count,
                            SUM(CASE WHEN quota = 'Management' THEN 1 ELSE 0 END) as mq_count,
                            SUM(CASE WHEN hostel = 'Yes' AND gender = 'Male' THEN 1 ELSE 0 END) as hostel_male,
                            SUM(CASE WHEN hostel = 'Yes' AND gender = 'Female' THEN 1 ELSE 0 END) as hostel_female
                        FROM admissions $sqlFilter
                    ");
                    $stmtSum->execute($params);
                    $pulse_sum = $stmtSum->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <div style="display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap;">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 25px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
                            <span style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">Counselling</span>
                            <span style="font-size: 1.4rem; font-weight: 800; color: var(--brand-navy);"><?= $pulse_sum['gq_count'] ?? 0 ?></span>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 25px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
                            <span style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">Management</span>
                            <span style="font-size: 1.4rem; font-weight: 800; color: var(--brand-navy);"><?= $pulse_sum['mq_count'] ?? 0 ?></span>
                        </div>
                        <div style="background: #f0f9ff; border: 1px solid #bae6fd; padding: 12px 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px; box-shadow: 0 2px 4px rgba(14,165,233,0.05);">
                            <span style="font-size: 0.75rem; font-weight: 800; color: #0284c7; text-transform: uppercase; letter-spacing: 1px; border-right: 2px solid #bae6fd; padding-right: 20px;">Hostels</span>
                            <div style="display: flex; gap: 15px;">
                                <span style="font-size: 0.8rem; font-weight: 700; color: #0369a1;">Boys: <b style="font-size:1.2rem; color: #0ea5e9;"><?= $pulse_sum['hostel_male'] ?? 0 ?></b></span>
                                <span style="font-size: 0.8rem; font-weight: 700; color: #0369a1;">Girls: <b style="font-size:1.2rem; color: #0ea5e9;"><?= $pulse_sum['hostel_female'] ?? 0 ?></b></span>
                            </div>
                        </div>
                    </div>

                    <div id="pulse-overall-view">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px;">
                            <?php
                            $seat_capacity = 60; 
                            // Total Pulse Query
                            $stmtHeat = $pdo->prepare("SELECT CONCAT(degree, ' ', department) as dept_key, COUNT(*) as count FROM admissions $sqlFilter GROUP BY degree, department");
                            $stmtHeat->execute($params);
                            $heat_data = $stmtHeat->fetchAll(PDO::FETCH_KEY_PAIR);
                            
                            $target_depts = [
                                'B.E CSE', 'B.E MECHANICAL', 'B.E ECE', 'B.E CIVIL', 'B.E EEE', 
                                'B.Tech IT', 'B.Tech AI&DS', 'M.E Structural Engineering', 'M.E Manufacturing Engineering'
                            ];
                            
                            foreach($target_depts as $dept):
                                $count = $heat_data[$dept] ?? 0;
                                if ($count == 0) {
                                    foreach($heat_data as $key => $val) {
                                        if (stripos($key, str_replace('B.E ', '', $dept)) !== false) {
                                            $count = $val; break;
                                        }
                                    }
                                }
                                $percent = min(100, round(($count / $seat_capacity) * 100));
                                $color = ($percent > 85) ? '#e11d48' : (($percent > 50) ? '#f59e0b' : '#10b981');
                            ?>
                            <div style="background:#f8fafc; padding:25px; border-radius:24px; border:1px solid #e2e8f0; transition: all 0.3s ease;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
                                    <span style="font-weight: 800; font-size: 0.85rem; color: var(--brand-navy);"><?= $dept ?></span>
                                    <span style="font-size: 0.75rem; font-weight: 800; color: <?= $color ?>; background: white; padding: 4px 10px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                        <?= $count ?>
                                    </span>
                                </div>
                                <div style="height: 8px; background: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 12px;">
                                    <div style="width: <?= $percent ?>%; height: 100%; background: <?= $color ?>; border-radius: 10px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.6rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
                                    <span>TOTAL LOAD</span>
                                    <span><?= $percent ?>%</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div id="pulse-mq-view" style="display: none;">
                        <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 20px; border-radius: 18px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                            <div style="font-size: 1.5rem;">🏢</div>
                            <div>
                                <h3 style="color: #92400e; font-size: 1rem; margin: 0;">Management Quota Pulse</h3>
                                <p style="color: #b45309; font-size: 0.7rem; font-weight: 600; margin: 0;">Real-time tracking of institutional management segment</p>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px;">
                            <?php
                            $sqlMQFilter = $sqlFilter . " AND quota = 'Management'";
                            $stmtMQHeat = $pdo->prepare("SELECT CONCAT(degree, ' ', department) as dept_key, COUNT(*) as count FROM admissions $sqlMQFilter GROUP BY degree, department");
                            $stmtMQHeat->execute($params);
                            $mq_heat_data = $stmtMQHeat->fetchAll(PDO::FETCH_KEY_PAIR);
                            
                            $mq_capacity = 30;
                            foreach($target_depts as $dept):
                                $mq_count = $mq_heat_data[$dept] ?? 0;
                                if ($mq_count == 0) {
                                    foreach($mq_heat_data as $key => $val) {
                                        if (stripos($key, str_replace('B.E ', '', $dept)) !== false) {
                                            $mq_count = $val; break;
                                        }
                                    }
                                }
                                $percent = min(100, round(($mq_count / $mq_capacity) * 100));
                            ?>
                            <div style="background:#fffbeb; padding:25px; border-radius:24px; border:1px solid #fde68a; transition: all 0.3s ease;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
                                    <span style="font-weight: 800; font-size: 0.85rem; color: #92400e;"><?= $dept ?></span>
                                    <span style="font-size: 0.8rem; font-weight: 900; color: #b45309; background: white; padding: 6px 12px; border-radius: 12px; border: 1px solid #fde68a;">
                                        <?= $mq_count ?>
                                    </span>
                                </div>
                                <div style="height: 10px; background: #fef3c7; border-radius: 10px; overflow: hidden; margin-bottom: 12px; border: 1px solid #fde68a;">
                                    <div style="width: <?= $percent ?>%; height: 100%; background: linear-gradient(90deg, #fbbf24, #f59e0b); border-radius: 10px;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.6rem; color: #92400e; font-weight: 800; text-transform: uppercase;">
                                    <span>MQ UTILIZATION</span>
                                    <span><?= $percent ?>%</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>

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
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    function switchPulse(type) {
        document.getElementById('pulse-overall-view').style.display = (type === 'overall' ? 'block' : 'none');
        document.getElementById('pulse-mq-view').style.display = (type === 'mq' ? 'block' : 'none');
        
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active', 'btn-primary-designer'));
        document.getElementById('btn-' + type).classList.add('active', 'btn-primary-designer');
    }
</script>
</body>
</html>
