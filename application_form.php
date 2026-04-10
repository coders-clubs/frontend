<?php
require 'config.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Receipt Entry</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="brand">
        <img src="assets/logo.png" alt="Logo" class="logo-img">
        <div class="college-name">Nadar Saraswathi College of <br>Engineering and Technology</div>
    </div>
    <div class="user-nav">
        <div class="user-info"><?= htmlspecialchars($_SESSION['faculty_name'] ?? $_SESSION['faculty_email']) ?></div>
        <a href="dashboard.php" class="btn-logout" style="background: rgba(255,255,255,0.2); margin-right: 10px;">Dashboard</a>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</header>

<div class="container fade-in">
    <div class="glass-panel">
        <h2 class="form-title">Application Receipt Entry</h2>

        <form id="applicationForm" action="admission_handler.php" method="POST">
            <input type="hidden" name="action_type" value="save">
            
            <div class="section-group">
                <h3>Basic Information</h3>
                <div class="input-grid">
                    <div class="input-group">
                        <label>Receipt No</label>
                        <input type="text" placeholder="(Auto-generated on Save)" readonly style="background:#f1f5f9;">
                    </div>
                    <div class="input-group">
                        <label>Admission Type</label>
                        <select name="admission_type">
                            <option value="Fresh Entry">Fresh Entry</option>
                            <option value="Lateral Entry">Lateral Entry</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="section-group">
                <h3>Student Personal Details</h3>
                <div class="input-grid">
                    <div class="input-group">
                        <label>Student Name *</label>
                        <input type="text" name="student_name" required>
                    </div>
                    <div class="input-group">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Date of Birth *</label>
                        <input type="date" name="date_of_birth" required>
                    </div>
                </div>
                <div class="input-grid" style="margin-top:20px;">
                    <div class="input-group">
                        <label>Father's Name *</label>
                        <input type="text" name="father_name" required>
                    </div>
                    <div class="input-group">
                        <label>Mother's Name</label>
                        <input type="text" name="mother_name">
                    </div>
                    <div class="input-group">
                        <label>Cell No *</label>
                        <input type="text" name="cell_1" required>
                    </div>
                </div>
                <div class="input-group" style="margin-top:20px;">
                    <label>Address</label>
                    <textarea name="address" rows="1"></textarea>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-v3 btn-v3-success">Save & Generate Receipt</button>
                <button type="button" class="btn-v3 btn-v3-secondary" onclick="window.location.href='dashboard.php'">Close</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
