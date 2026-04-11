<?php
require 'connection/config.php';

$message = '';
$error = '';
$reset_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
            $stmt->execute([$token, $expires, $email]);
            
            // For development, we just show the link.
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            $message = "Password reset initiated.";
        } else {
            // Generic message to prevent email enumeration
            $message = "If that email is registered, a reset link has been generated.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Admission Entry</title>
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
        .text-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="login-card">
    <h2>Forgot Password</h2>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($message && !$reset_link): ?><div class="success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    
    <?php if ($reset_link): ?>
        <div class="success" style="text-align: left;">
            <strong>Password Reset Link Generated:</strong><br><br>
            <a href="<?= htmlspecialchars($reset_link) ?>" style="color: #2a5298; word-break: break-all; text-decoration: underline; font-weight:bold;"><?= htmlspecialchars($reset_link) ?></a>
        </div>
    <?php else: ?>
        <p style="color: #666; margin-bottom: 20px; font-size: 15px;">Enter your email address to get a password reset link.</p>
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="admin@college.edu">
            </div>
            <button type="submit" class="btn-primary">Request Reset Link</button>
        </form>
    <?php endif; ?>
    
    <div style="margin-top: 20px;">
        <a href="login.php" class="text-link">Back to Login</a>
    </div>
</div>
</body>
</html>
