<?php
require 'config.php';
require_login();

$faculty_email = $_SESSION['faculty_email'];

// Fetch strictly logged-in faculty applications
$stmt = $pdo->prepare("SELECT * FROM admissions WHERE faculty_email = ? ORDER BY id DESC");
$stmt->execute([$faculty_email]);
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        function previewApplication(appNo) {
            // Very simple redirect to form with the application_no so they can fetch/modify
            // Actually, we can show a nice alert summary, or redirect to a read-only form
            // The requirement says: Provide Preview option.
            alert("Preview Application: " + appNo + "\nUse Modify on Admission Form to edit.");
            window.location.href = "admission_form.php";
        }
    </script>
</head>
<body>

<header>
    <div class="logo">College Admissions</div>
    <div class="user-info">
        <?= htmlspecialchars($_SESSION['faculty_name'] ?? $_SESSION['faculty_email']) ?>
        <a href="dashboard.php" class="logout-btn" style="background-color: #5a6268;">Dashboard</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<div class="container">
    <h2 style="text-align: center; color: #2a5298;">My Applications</h2>

    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <?php if (count($applications) > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Application No</th>
                        <th>Student Name</th>
                        <th>Department</th>
                        <th>Date of Joining</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['receipt_no']) ?></td>
                            <td><?= htmlspecialchars($app['application_no']) ?></td>
                            <td><?= htmlspecialchars($app['student_name']) ?></td>
                            <td><?= htmlspecialchars($app['department']) ?></td>
                            <td><?= htmlspecialchars($app['date_of_joining']) ?></td>
                            <td>
                                <!-- Real preview or link to fetch into the form -->
                                <button class="btn btn-primary" onclick="previewApplication('<?= htmlspecialchars($app['application_no']) ?>')">Preview</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #666; font-size: 18px;">No applications found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
