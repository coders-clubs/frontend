<?php
require 'connection/config.php';
require_login();

$faculty_email = $_SESSION['faculty_email'];
$stmt = $pdo->prepare("SELECT * FROM admissions WHERE faculty_email = ? ORDER BY created_at DESC");
$stmt->execute([$faculty_email]);
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Record | NSCET</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .record-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 20px;
        }
        .record-table th {
            text-align: left;
            padding: 15px 20px;
            color: var(--text-secondary);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .record-row {
            background: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .record-row:hover {
            transform: scale(1.01);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            background: var(--input-bg);
        }
        .record-row td {
            padding: 20px;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
            color: var(--text-primary);
        }
        .record-row td:first-child { border-left: 1px solid #f1f5f9; border-radius: 16px 0 0 16px; font-weight: 700; color: var(--brand-navy); }
        .record-row td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 16px 16px 0; text-align: right; }
        
        .badge-dept {
            background: var(--brand-gold-soft);
            color: var(--brand-navy);
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="designer-animate">

<header class="no-print">
    <div class="nav-actions"><a href="dashboard.php" class="nav-btn btn-ghost">← DASHBOARD</a></div>
    <div class="branding-center">
        <img src="assets/logo.png" alt="NSCET" class="logo-main" style="height: 110px;">
        <div class="college-title" style="margin-top: 10px;">NADAR SARASWATHI COLLEGE OF ENGINEERING & TECHNOLOGY</div>
    </div>
    <div class="nav-actions">
        <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;"><?= htmlspecialchars($_SESSION['faculty_name'] ?? $_SESSION['faculty_email']) ?></span>
        <a href="logout.php" class="nav-btn btn-danger-soft">LOGOUT</a>
    </div>
</header>

<div class="page-hero">
    <h1>Application Registry</h1>
    <p>Complete historical log of submitted admission forms.</p>
</div>

<div class="form-container">
    <div class="designer-card">
        <div class="section-label">
            <div class="section-number">📊</div>
            <div class="section-title">Verified Student Records</div>
        </div>

        <?php if(empty($records)): ?>
            <div style="text-align:center; padding: 60px; color: #64748b;">
                <div style="font-size: 4rem; margin-bottom:20px;">📂</div>
                <h3>No records found yet.</h3>
                <p>Start your first application from the dashboard!</p>
            </div>
        <?php else: ?>
            <table class="record-table">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student Name</th>
                        <th>Program / Dept</th>
                        <th>Date of Joining</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $r): ?>
                        <tr class="record-row" id="row-<?= $r['id'] ?>">
                            <td><?= htmlspecialchars((string)($r['receipt_no'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($r['student_name'] ?? '')) ?></td>
                            <td><span class="badge-dept"><?= htmlspecialchars((string)($r['department'] ?? '')) ?></span></td>
                            <td style="color: #64748b;"><?= htmlspecialchars((string)($r['date_of_joining'] ?? '')) ?></td>
                            <td>
                                <a href="admission_registry.php?fetch=<?= urlencode((string)($r['receipt_no'] ?? '')) ?>" class="btn-designer btn-primary-designer" style="padding: 8px 15px; font-size: 0.7rem;">VIEW / MODIFY</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<footer style="text-align: center; margin-top: 60px; padding-bottom: 40px; color: #94a3b8; font-size: 0.8rem;">
    &copy; <?= date('Y') ?> Nadar Saraswathi College of Engineering and Technology
</footer>

</body>
</html>
