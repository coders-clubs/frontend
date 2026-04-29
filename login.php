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

        #splash-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .splash-square {
            width: 250px;
            height: 250px;
            background: #fff;
            border-radius: 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .splash-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
            fill: var(--brand-gold);
            animation: floatLogo 3s ease-in-out infinite;
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .splash-title {
            color: var(--brand-navy);
            font-size: 2.2rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -1px;
            opacity: 0;
            animation: fadeUp 0.6s ease forwards 0.3s;
        }

        .splash-tagline {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 5px;
            opacity: 0;
            animation: fadeUp 0.6s ease forwards 0.5s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-dots {
            display: inline-flex;
            gap: 3px;
            margin-left: 6px;
        }

        .loading-dots span {
            width: 5px;
            height: 5px;
            background: var(--brand-gold);
            border-radius: 50%;
            animation: bounceDots 1.4s infinite ease-in-out both;
        }

        .loading-dots span:nth-child(1) { animation-delay: -0.32s; }
        .loading-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounceDots {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .splash-credit {
            position: absolute;
            bottom: 40px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
            opacity: 0;
            animation: fadeUp 0.6s ease forwards 0.8s;
        }
    </style>
</head>
<body>
    <div id="splash-overlay">
        <div class="splash-square">
            <svg class="splash-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <!-- Academic shield/cap concept -->
                <path d="M50 5 L10 25 L10 55 C10 75 30 90 50 95 C70 90 90 75 90 55 L90 25 Z" fill="var(--brand-gold)"/>
                <path d="M50 20 L25 35 L50 50 L75 35 Z" fill="#fff"/>
                <path d="M30 45 L30 65 L50 75 L70 65 L70 45 L50 55 Z" fill="#fff" opacity="0.8"/>
            </svg>
            <h1 class="splash-title">AdmitPro</h1>
            <div class="splash-tagline">
                Initializing 
                <div class="loading-dots"><span></span><span></span><span></span></div>
            </div>
        </div>
        <div class="splash-credit">Proudly Developed by an NSCET Student</div>
    </div>

    <div class="premium-container">
        <div class="brand-panel">
            <img src="assets/logo.png" alt="NSCET Logo">
            <div class="college-name" style="font-size: 1.6rem;">NADAR SARASWATHI COLLEGE OF ENGINEERING AND TECHNOLOGY</div>
            <div class="college-tagline">Theni Melapettai Hindu Nadargal Uravinmurai</div>
            <p style="opacity: 0.6; font-size: 0.8rem; max-width: 300px; margin-top: 20px;">Optimized Admission Management System</p>
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
                    <a href="register.php" style="color: var(--brand-gold-bright)">Create Account</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const splash = document.getElementById('splash-overlay');
                if (splash) {
                    splash.style.opacity = '0';
                    setTimeout(() => {
                        splash.style.visibility = 'hidden';
                    }, 500); // wait for fade out transition
                }
            }, 2500); // Show splash for 2.5 seconds to see animations
        });
    </script>
</body>
</html>
