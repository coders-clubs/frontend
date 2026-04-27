<?php
require 'connection/connection.php';
require_login();

$faculty_email = $_SESSION['faculty_email'];
$faculties = [];

// Filtering parameters
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
// Security: Faculty cannot override center via GET
$selCenter = (is_admin() && isset($_GET['center'])) ? $_GET['center'] : ($_SESSION['selected_center'] ?? 'all');
$selFaculty = $_GET['faculty'] ?? (is_admin() ? 'all' : $faculty_email);

// Base queries
$query = "SELECT a.*, u.name as staff_name 
          FROM admissions a 
          LEFT JOIN users u ON a.faculty_email = u.email 
          WHERE 1=1";
$params = [];

if (is_admin()) {
    $fStmt = $pdo->query("SELECT DISTINCT name, email FROM users WHERE role = 'faculty'");
    $faculties = $fStmt->fetchAll();
    
    if ($selFaculty !== 'all') {
        $query .= " AND a.faculty_email = ?";
        $params[] = $selFaculty;
    }
} else {
    // Faculty only sees their own
    $query .= " AND a.faculty_email = ?";
    $params[] = $faculty_email;
}

if (!empty($startDate)) { $query .= " AND DATE(a.created_at) >= ?"; $params[] = $startDate; }
if (!empty($endDate)) { $query .= " AND DATE(a.created_at) <= ?"; $params[] = $endDate; }
if ($selCenter !== 'all') { $query .= " AND a.center = ?"; $params[] = $selCenter; }

$query .= " ORDER BY a.created_at ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
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
            border-radius: 16px;
        }
        .record-row:hover {
            transform: scale(1.01);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            background: #f8fafc;
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
        .filter-panel {
            background: #fff; 
            padding: 30px; 
            border-radius: 28px; 
            border: 1px solid #e2e8f0; 
            box-shadow: var(--shadow-premium); 
            margin-bottom: 40px;
        }
    </style>
</head>
<body class="designer-animate">
<div class="app-container">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <div class="dash-container">
            <?php include 'branding.php'; ?>
            
            <div class="hero-section stagger-1" style="text-align: left; margin-bottom: 40px;">
                <h1 style="font-size: 2.5rem;"><?= is_admin() ? 'Master Registry' : 'My Applicants' ?></h1>
                <p style="color: var(--text-secondary); font-weight: 600;">Audit trail and application archives &middot; Filter enabled</p>
            </div>

            <div class="filter-panel">
                <div class="section-label" style="margin-bottom: 25px;">
                    <div class="section-number">🎯</div>
                    <div class="section-title">Query & Intelligence Filters</div>
                </div>
                <form id="filterForm" method="GET" action="application_records.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 20px; align-items: flex-end;">
                    <div class="field-box">
                        <label>From Date</label>
                        <input type="date" name="startDate" value="<?= htmlspecialchars($startDate) ?>" onchange="this.form.submit()">
                    </div>
                    <div class="field-box">
                        <label>To Date</label>
                        <input type="date" name="endDate" value="<?= htmlspecialchars($endDate) ?>" onchange="this.form.submit()">
                    </div>
                    <?php if (is_admin()): ?>
                    <div class="field-box">
                        <label>Admission Center</label>
                        <select name="center" onchange="this.form.submit()">
                            <option value="all">All Centers</option>
                            <?php foreach($centers_list as $cid => $cname): ?>
                                <option value="<?= $cid ?>" <?= $selCenter == $cid ? 'selected' : '' ?>><?= htmlspecialchars($cname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (is_admin()): ?>
                    <div class="field-box">
                        <label>Faculty / Staff</label>
                        <select name="faculty" onchange="this.form.submit()">
                            <option value="all">All Staff</option>
                            <?php foreach($faculties as $f): ?>
                                <option value="<?= htmlspecialchars($f['email']) ?>" <?= $selFaculty == $f['email'] ? 'selected' : '' ?>><?= htmlspecialchars($f['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="executeExport()" class="btn-designer btn-primary-designer" style="flex: 1; height: 48px; font-size: 0.7rem;">📥 EXPORT CSV</button>
                        <a href="application_records.php" class="btn-designer btn-ghost" style="flex: 0.5; height: 48px; font-size: 0.7rem; border: 1px solid #e2e8f0;">RESET</a>
                    </div>
                </form>
            </div>

            <script>
                function executeExport() {
                    const form = document.getElementById('filterForm');
                    const params = new URLSearchParams(new FormData(form)).toString();
                    window.location.href = 'core/export_data.php?' + params;
                }
            </script>

            <div class="designer-card">
                <div class="section-label">
                    <div class="section-number">📊</div>
                    <div class="section-title">Verified Student Records (<?= count($records) ?> Results)</div>
                </div>

                <?php if(empty($records)): ?>
                    <div style="text-align:center; padding: 60px; color: #64748b;">
                        <div style="font-size: 4rem; margin-bottom:20px;">📂</div>
                        <h3>No records match your filters.</h3>
                        <p>Try adjusting your date range or center selection.</p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="record-table">
                            <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Student Name</th>
                                    <th>Program / Dept</th>
                                    <th>Admission Center</th>
                                    <th>Registered By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($records as $r): ?>
                                    <tr class="record-row" id="row-<?= $r['id'] ?>">
                                        <td><?= htmlspecialchars((string)($r['receipt_no'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string)($r['student_name'] ?? '')) ?></td>
                                        <td><span class="badge-dept"><?= htmlspecialchars((string)($r['department'] ?? '')) ?></span></td>
                                        <td style="font-weight: 600; font-size: 0.8rem; color: var(--brand-navy);">
                                            <?= htmlspecialchars($centers_list[$r['center']] ?? $r['center'] ?? 'N/A') ?>
                                        </td>
                                        <td style="font-size: 0.8rem; font-weight: 700; color: #64748b;">
                                            <?= htmlspecialchars((string)($r['staff_name'] ?? $r['faculty_email'] ?? 'System')) ?>
                                        </td>
                                        <td style="color: #64748b; font-size: 0.85rem;"><?= htmlspecialchars((string)($r['date_of_joining'] ?? '')) ?></td>
                                        <td>
                                            <a href="admission_registry.php?fetch=<?= urlencode((string)($r['receipt_no'] ?? '')) ?>" class="btn-designer btn-primary-designer" style="padding: 8px 15px; font-size: 0.7rem;">VIEW / MODIFY</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <footer style="text-align: center; margin-top: 60px; padding-bottom: 40px; color: #94a3b8; font-size: 0.8rem;">
                &copy; <?= date('Y') ?> Nadar Saraswathi College of Engineering and Technology
            </footer>
        </div>
    </main>
</div>
</body>
</html>
