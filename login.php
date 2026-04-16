<?php
require 'connection/connection.php';
if (isset($_SESSION['faculty_email'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Portal | NSCET</title>
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
            width: 140px;
            margin-bottom: 30px;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
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
            font-size: 2.2rem;
            color: var(--brand-navy);
            margin: 0 0 10px 0;
            font-weight: 800;
        }

        .login-header p {
            color: var(--text-secondary);
            margin-bottom: 40px;
            font-size: 0.95rem;
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
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.3);
            margin-top: 10px;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.4);
            background: #1e293b;
        }

        .footer-links {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .footer-links a:hover { color: var(--brand-gold-bright); }

        .error-toast {
            background: #ffe4e6;
            color: #e11d48;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            border-left: 5px solid #e11d48;
        }
    </style>
</head>
<body>
    <div class="premium-container">
        <div class="brand-panel">
            <img src="assets/logo.png" alt="NSCET Logo">
            <div class="college-name">NADAR SARASWATHI COLLEGE</div>
            <div class="college-tagline">Engineering & Technology</div>
            <p style="opacity: 0.6; font-size: 0.9rem; max-width: 300px;">Optimized Admission Management System</p>
        </div>
        
        <div class="login-panel">
            <div class="login-header">
                <h2>Welcome back</h2>
                <p>Sign in to manage academic admissions</p>
            </div>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="error-toast"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form action="authenticate.php" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Enter email">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>

                <button type="submit" class="btn-submit">Login →</button>
                
                <div class="footer-links">
                    <a href="forgot_password.php">Forget Password</a>
                    <a href="register.php" style="color: var(--brand-gold-bright)">Create Account</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
