<?php
require 'connection/connection.php';

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
            $message = "If that email is registered, a reset link has been generated.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password | NSCET</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-navy: #0f172a;
            --brand-gold: #c29d59;
            --brand-gold-bright: #d4af37;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        body {
            height: 100vh;
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .premium-container {
            width: 1000px;
            height: 600px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border-radius: 40px;
            display: flex;
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            position: relative;
        }

        .brand-panel {
            flex: 1.2;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.8) 100%);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .brand-panel img {
            width: 130px;
            margin-bottom: 30px;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
        }

        .college-name {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.1;
            margin-bottom: 10px;
        }

        .college-tagline {
            color: var(--brand-gold);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            margin-bottom: 40px;
        }

        .login-panel {
            flex: 1;
            background: #fff;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header h2 {
            font-size: 2rem;
            color: var(--brand-navy);
            margin: 0 0 10px 0;
            font-weight: 800;
        }

        .login-header p {
            color: var(--text-secondary);
            margin-bottom: 35px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .form-group { margin-bottom: 25px; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            border-radius: 16px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--brand-gold);
            background: #fff;
            box-shadow: 0 0 0 5px rgba(194, 157, 89, 0.1);
        }

        .btn-submit {
            background: var(--brand-navy);
            color: #fff;
            border: none;
            padding: 18px;
            border-radius: 16px;
            width: 100%;
            font-weight: 800;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.3);
            margin-top: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.4);
            background: #1e293b;
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 800;
            transition: color 0.3s;
        }

        .footer-links a:hover { color: var(--brand-navy); }

        .status-msg {
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 30px;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .error { background: #ffe4e6; color: #e11d48; border-left: 5px solid #e11d48; }
        .success { background: #ecfdf5; color: #059669; border-left: 5px solid #059669; }

        .reset-link-box {
            background: #f8fafc;
            border: 2px dashed var(--brand-gold);
            padding: 15px;
            border-radius: 12px;
            margin-top: 15px;
            word-break: break-all;
            font-family: monospace;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="premium-container">
        <div class="brand-panel">
            <img src="assets/logo.png" alt="NSCET Logo">
            <div class="college-name">NADAR SARASWATHI</div>
            <div class="college-tagline">College of Engineering</div>
            <p style="opacity: 0.6; font-size: 0.9rem; max-width: 300px;">Access recovery and account restoration portal.</p>
        </div>
        
        <div class="login-panel">
            <div class="login-header">
                <h2>Forget Password</h2>
                <?php if (!$reset_link): ?>
                    <p>Enter your primary account email to verify your identity and generate a reset link.</p>
                <?php endif; ?>
            </div>

            <?php if ($error): ?><div class="status-msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($message && !$reset_link): ?><div class="status-msg success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

            <?php if ($reset_link): ?>
                <div class="status-msg success">
                    <strong>Recovery link generated successfully!</strong><br>
                    Please use the link below to set your new password.
                    <div class="reset-link-box">
                        <a href="<?= htmlspecialchars($reset_link) ?>" style="color: var(--brand-navy); font-weight: bold;"><?= htmlspecialchars($reset_link) ?></a>
                    </div>
                </div>
            <?php else: ?>
                <form action="forgot_password.php" method="POST">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="Enter your email">
                    </div>

                    <button type="submit" class="btn-submit">Generate Link →</button>
                </form>
            <?php endif; ?>

            <div class="footer-links">
                <a href="login.php">← RETURN TO LOGIN</a>
            </div>
        </div>
    </div>
</body>
</html>
