<?php
// api/login.php

// 1. SAFE SESSION START (Fixes the Warning)
// We set settings manually here instead of including auth_session.php to avoid the loop.
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', '/tmp');
    session_set_cookie_params([
        'path' => '/',
        'secure' => true, 
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// 2. CHECK IF ALREADY LOGGED IN
// If they are already logged in, send them to dashboard immediately.
if (isset($_SESSION['user_id']) || isset($_COOKIE['auth_user_id'])) {
    header("Location: /dashboard");
    exit();
}

// 3. INCLUDE DB CONFIG
// Use __DIR__ to find the file in the same folder
require_once __DIR__ . '/db_config.php';

$error = "";

if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check User
    $query = "SELECT * FROM users WHERE email = '$email' OR username = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            // FIX: Use $user['id'], not $row['id']
            $_SESSION['user_id'] = $user['id'];

            // SET COOKIE (Global Path '/')
            setcookie("auth_user_id", $user['id'], [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'domain' => '', 
                'secure' => true,
                'httponly' => false,
                'samesite' => 'Lax'
            ]);

            // Redirect using Javascript to force a clean load
            echo "<script>window.location.href='/dashboard';</script>";
            exit();
        } else {
            $error = "Incorrect Password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login | Vault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* RESET & BASICS */
        * { box-sizing: border-box; }
        
        body {
            background-color: #0f172a; 
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px; /* Padding prevents edge touching on mobile */
            color: #f8fafc;
        }

        .login-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            width: 100%; 
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            transition: transform 0.3s ease;
        }

        .brand-name {
            color: #3b82f6;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
            display: block;
            letter-spacing: -1px;
        }

        h2 { 
            font-size: 1.5rem; 
            margin-bottom: 10px; 
            color: white; 
            font-weight: 700;
        }
        
        .subtitle { 
            color: #94a3b8; 
            font-size: 0.95rem; 
            margin-bottom: 30px; 
            line-height: 1.5;
        }

        .form-group { 
            margin-bottom: 20px; 
            text-align: left; 
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            font-size: 1rem;
            color: white;
            transition: border-color 0.2s;
        }

        input:focus { 
            outline: none; 
            border-color: #3b82f6; 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s;
        }

        .btn-login:active { transform: scale(0.98); }
        .btn-login:hover { background-color: #1d4ed8; }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .footer-text { 
            margin-top: 25px; 
            font-size: 0.95rem; 
            color: #94a3b8; 
        }
        .footer-text a { 
            color: #3b82f6; 
            text-decoration: none; 
            font-weight: 600;
        }

        /* MOBILE RESPONSIVE TWEAKS */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
                border: none; /* Cleaner look on very small screens */
                background: transparent;
                box-shadow: none;
            }
            .brand-name { font-size: 2.5rem; }
            h2 { font-size: 1.3rem; }
            input, .btn-login { font-size: 16px; /* Prevents iOS zoom on focus */ }
        }
    </style>
</head>
<body>

<div class="login-card">
    <span class="brand-name">Vault</span>
    <h2>Welcome Back</h2>
    <p class="subtitle">Enter your credentials to access your account.</p>

    <?php if($error): ?>
        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <input type="text" name="email" placeholder="Email or Username" required autocomplete="username">
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
        </div>

        <button type="submit" name="login_btn" class="btn-login">Sign In</button>
    </form>

    <div class="footer-text">
        Don't have an account? <a href="/signup">Sign Up</a>
    </div>
</div>

</body>
</html>
