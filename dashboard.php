<?php
require 'connection/config.php';
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
        .dash-container { max-width: 1200px; margin: 60px auto; padding: 0 40px; }
        .hero-section { text-align: center; margin-bottom: 80px; }
        .hero-section h1 { font-size: 4rem; background: linear-gradient(135deg, var(--brand-navy) 0%, #475569 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -2px; }
        
        .elite-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .elite-card { 
            background: #fff; border-radius: 32px; padding: 40px; 
            transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
            text-decoration: none; border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            position: relative; overflow: hidden;
            display: flex; flex-direction: column; align-items: flex-start;
        }
        .elite-card:hover { transform: translateY(-10px); box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.12); border-color: var(--brand-gold); }
        .elite-card i { font-size: 2.5rem; margin-bottom: 25px; }
        .elite-card h3 { font-size: 1.5rem; color: var(--brand-navy); margin-bottom: 15px; }
        .elite-card p { color: var(--text-secondary); font-size: 0.95rem; }
        .card-accent { margin-top: auto; padding-top: 30px; font-weight: 800; font-size: 0.75rem; color: var(--brand-gold-bright); letter-spacing: 2px; }
        
        .stat-banner { margin-top: 60px; background: var(--brand-navy); border-radius: 40px; padding: 40px; display: flex; justify-content: space-around; color: #fff; }
        .stat-item { text-align: center; }
        .stat-val { display: block; font-size: 2.5rem; font-weight: 800; font-family: 'Outfit'; color: var(--brand-gold); }
        .stat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.7; }
    </style>
</head>
<body class="designer-animate">
<header>
    <div class="branding-center" style="flex: 1; flex-direction: row; gap: 30px; justify-content: center;">
        <img src="assets/logo.png" alt="NSCET" style="height: 120px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
        <div style="text-align: left;">
            <div class="college-title" style="font-size: 1.8rem; line-height: 1;">NADAR SARASWATHI COLLEGE</div>
            <div style="font-size: 1.1rem; color: var(--text-secondary); font-weight: 600; margin-top: 5px;">OF ENGINEERING & TECHNOLOGY</div>
        </div>
    </div>
</header>
<div class="dash-container">
    <div class="hero-section stagger-1">
        <h1>Admission center</h1>
        <p style="font-size: 1.2rem; color: var(--text-secondary); max-width: 600px; margin: 20px auto;">Welcome to the high-performance orchestration portal for academic excellence and student registration.</p>
    </div>
    <?php if (!isset($_SESSION['selected_center'])): ?>
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: var(--brand-navy);">Select Your Center to Proceed</h2>
    </div>
    <div class="elite-grid" style="grid-template-columns: repeat(2, 1fr); max-width: 800px; margin: 0 auto;">
    <?php
    $centers = [
        'uravinmurai_office' => 'Uravinmurai Office',
        'tmhnu' => 'TMHNU',
        'main_campus' => 'Main Campus',
        'south_branch' => 'South Branch'
    ];
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
    <div style="text-align: center; margin-bottom: 40px; background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; display: inline-block; margin-left: 50%; transform: translateX(-50%);">
        <span style="color: #64748b; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Active Center</span><br>
        <strong style="color: var(--brand-navy); font-size: 24px;"><?= htmlspecialchars($_SESSION['selected_center_name']) ?></strong>
        <br>
        <a href="select_center.php?clear=1" style="display: inline-block; margin-top: 10px; color: #e11d48; text-decoration: none; font-size: 14px; font-weight: bold; padding: 5px 15px; border-radius: 20px; background: #ffe4e6;">Change Center</a>
    </div>

    <div class="elite-grid">
        <a href="admission_entry.php" class="elite-card stagger-2">
            <i></i>
            <h3>Admission Entry</h3>
            <p>Initialize student profiles with concurrent-safe sequential processing.</p>
            <div class="card-accent">INITIATE FLOW →</div>
        </a>
        <a href="admission_registry.php" class="elite-card stagger-3">
            <i></i>
            <h3>Admision Registry</h3>
            <p>Master command for marks entry, financial records, and institutional verification.</p>
            <div class="card-accent">FINALIZATION →</div>
        </a>
        <a href="application_records.php" class="elite-card stagger-4">
            <i></i>
            <h3>My applicants</h3>
            <p>Full audit trail of all applications with deep-link modification capabilities.</p>
            <div class="card-accent">VIEW ARCHIVE →</div>
        </a>
    </div>
    <?php endif; ?>
    <div class="stat-banner stagger-5">
        <div class="stat-item">
            <span class="stat-val">ACTIVE</span>
            <span class="stat-label">System Status</span>
        </div>
        <div class="stat-item">
            <span class="stat-val"><?= date('Y') ?></span>
            <span class="stat-label">Academic Cycle</span>
        </div>
        <div class="stat-item">
            <div class="nav-actions"><a href="logout.php" class="btn-designer btn-accent-designer">SECURE LOGOUT</a></div>
        </div>
    </div>
</div>
</body>
</html>
