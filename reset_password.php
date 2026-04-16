<?php
require 'connection/connection.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Invalid or missing token.");
}

$stmt = $pdo->prepare("SELECT id, email, reset_token_expires FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user || strtotime($user['reset_token_expires']) < time()) {
    die("This password reset token is invalid or has expired.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        if ($stmt->execute([$hashed_password, $user['id']])) {
            $success = "Password has been successfully reset! You can now log in.";
        } else {
            $error = "An error occurred while resetting your password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Admission Entry</title>
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
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card h2 { margin-top: 0; color: #333; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 16px; }
        .btn-primary { background-color: #2a5298; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; transition: background 0.3s; margin-bottom: 15px;}
        .btn-primary:hover { background-color: #1e3c72; }
        .error { color: #d9534f; background: #fdf7f7; border: 1px solid #ebccd1; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
        .success { color: #3c763d; background: #dff0d8; border: 1px solid #d6e9c6; padding: 15px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; line-height: 1.5; }
        .text-link { color: #2a5298; text-decoration: none; font-size: 14px; font-weight: 500;}
    </style>
</head>
<body>
<div class="login-card">
    <h2>Reset Password</h2>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
        <div style="margin-top: 20px;">
            <a href="login.php" class="btn-primary" style="display:inline-block; text-decoration:none; box-sizing: border-box;">Go to Login</a>
        </div>
    <?php else: ?>
        <p style="color: #666; margin-bottom: 20px; font-size: 15px;">Enter your new password for <strong><?= htmlspecialchars($user['email']) ?></strong>.</p>
        <form action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="POST">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
