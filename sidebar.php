<div class="sidebar no-print">
    <div class="sidebar-logo">
        <img src="assets/logo.png" alt="NSCET">
        <div class="brand-text">NSCET ADMISSION PORTAL</div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <span>🏠</span> <span>Dashboard</span>
        </a>
        <a href="admission_entry.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'admission_entry.php' ? 'active' : '' ?>">
            <span>📝</span> <span>Admission Entry</span>
        </a>
        <a href="enquiry_entry.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'enquiry_entry.php' ? 'active' : '' ?>">
            <span>🔍</span> <span>Enquiry Form</span>
        </a>
        <a href="admission_registry.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'admission_registry.php' ? 'active' : '' ?>">
            <span>📋</span> <span>Registry Master</span>
        </a>
        <a href="application_records.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'application_records.php' ? 'active' : '' ?>">
            <span>📁</span> <span><?= (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'Master Registry' : 'My Applicants' ?></span>
        </a>
        <a href="reports.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
            <span>📊</span> <span>Reports (Export)</span>
        </a>
        <?php if(is_admin()): ?>
        <a href="progress_matrix.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'progress_matrix.php' ? 'active' : '' ?>">
            <span>🧬</span> <span>Progress Matrix</span>
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= substr($_SESSION['faculty_name'] ?? 'U', 0, 1) ?></div>
            <div class="user-details">
                <div class="user-name"><?= htmlspecialchars($_SESSION['faculty_name'] ?? 'User') ?></div>
                <div class="user-role"><?= strtoupper(htmlspecialchars($_SESSION['role'] ?? 'Faculty')) ?></div>
            </div>
        </div>
        <a href="logout.php" class="nav-item" style="color: #f87171;">
            <span>🚪</span> <span>Logout</span>
        </a>
    </div>
</div>
