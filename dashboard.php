<?php
require 'config.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo">College Admissions</div>
    <div class="user-info">
        Welcome, <?= htmlspecialchars($_SESSION['faculty_name'] ?? $_SESSION['faculty_email']) ?>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<div class="container">
    <h2 style="text-align: center; color: #2a5298;">Faculty Dashboard</h2>
    
    <div class="dashboard-cards">
        <a href="admission_form.php" class="card">
            <h3>Admission Entry Form</h3>
            <p>Create a new admission record</p>
        </a>
        <a href="my_applications.php" class="card">
            <h3>My Applications</h3>
            <p>View and manage your submitted applications</p>
        </a>
    </div>
</div>

</body>
</html>
