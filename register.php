<?php
require 'connection/config.php';

if (isset($_SESSION['faculty_email'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email address is already configured. Try logging in.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Registration - Admission Entry</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 800px;
            display: flex;
            overflow: hidden;
        }
        .login-left {
            background: #f4f6f9;
            padding: 40px;
            width: 40%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #e0e0e0;
        }
        .login-left img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.1));
        }
        .login-right {
            padding: 40px;
            width: 60%;
            text-align: center;
        }
        .login-right h2 { margin-top: 0; color: #333; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 16px; }
        .btn-primary { background-color: #2a5298; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; transition: background 0.3s; margin-bottom: 15px;}
        .btn-primary:hover { background-color: #1e3c72; }
        .error { color: #d9534f; background: #fdf7f7; border: 1px solid #ebccd1; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
        .success { color: #3c763d; background: #dff0d8; border: 1px solid #d6e9c6; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
        .text-link { color: #2a5298; text-decoration: none; font-size: 14px; font-weight: 500;}
        .text-link:hover { text-decoration: underline; }
    </style>
</head>
<div class="login-card">
    <div class="login-left">
        <img src="assets/logo.png" alt="College Logo">
        <h3 style="color: #2a5298; margin: 0; text-align: center;">NSCET</h3>
        <p style="color: #666; text-align: center; font-size: 14px; margin-top: 10px;">Academic Excellence Portal</p>
    </div>
    <div class="login-right">
        <h2>Faculty Registration</h2>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="admin@college.edu">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-primary">Register</button>
            <div>
                <span style="font-size: 14px; color: #555;">Already have an account?</span> <a href="login.php" class="text-link">Sign in</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
