<?php
require 'connection/connection.php';

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
    $role = $_POST['role'] ?? 'faculty';

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
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password, $role])) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration | NSCET</title>
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
            min-height: 100vh;
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        .premium-container {
            width: 1100px;
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
            flex: 0.8;
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
            width: 120px;
            margin-bottom: 30px;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
        }

        .college-name {
            font-size: 1.8rem;
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
            font-size: 0.8rem;
            margin-bottom: 40px;
        }

        .login-panel {
            flex: 1.2;
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group { margin-bottom: 20px; }
        .form-group.full { grid-column: span 2; }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            border-radius: 12px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--brand-gold);
            background: #fff;
            box-shadow: 0 0 0 5px rgba(194, 157, 89, 0.1);
        }

        .btn-submit {
            grid-column: span 2;
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
            text-transform: uppercase;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.4);
            background: #1e293b;
        }

        .footer-links {
            grid-column: span 2;
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .footer-links a {
            color: var(--brand-gold-bright);
            text-decoration: none;
            font-weight: 800;
        }

        .status-msg {
            grid-column: span 2;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .error { background: #ffe4e6; color: #e11d48; border-left: 5px solid #e11d48; }
        .success { background: #ecfdf5; color: #059669; border-left: 5px solid #059669; }
    </style>
</head>
<body>
    <div class="premium-container">
        <div class="brand-panel">
            <img src="assets/logo.png" alt="NSCET Logo">
            <div class="college-name" style="font-size: 1.6rem;">NADAR SARASWATHI COLLEGE OF ENGINEERING AND TECHNOLOGY</div>
            <div class="college-tagline">Theni Melapettai Hindu Nadargal Uravinmurai</div>
            <p style="opacity: 0.6; font-size: 0.8rem; max-width: 250px; margin-top: 20px;">Join the academic orchestration team today.</p>
        </div>
        
        <div class="login-panel">
            <div class="login-header">
                <h2>Create Account</h2>
                <p>Register as Faculty or Administrator</p>
            </div>

            <form action="register.php" method="POST" class="form-grid">
                <?php if ($error): ?><div class="status-msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="status-msg success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

                <div class="form-group full">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="Enter name">
                </div>
                
                <div class="form-group full">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter email">
                </div>
                
                <div class="form-group">
                    <label>Enter Password</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••">
                </div>

                <div class="form-group full">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="faculty">Faculty</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">REGISTER ACCOUNT →</button>
                
                <div class="footer-links">
                    Already have an account? <a href="login.php">SIGN IN HERE</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
