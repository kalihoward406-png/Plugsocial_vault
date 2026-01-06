<?php
// 1. SETTINGS
ini_set('display_errors', 1);
ini_set('session.save_path', '/tmp');
session_set_cookie_params(['path' => '/', 'samesite' => 'Lax']);
session_start();

include 'db_config.php'; // Ensure this file exists!

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
            
            // --- THE FIX: SET A COOKIE + SESSION ---
            // 1. Standard Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // 2. Backup Cookie (Lasts 24 hours) - This saves you on Vercel
            setcookie("auth_user_id", $user['id'], time() + 86400, "/");

            // --- STOP & SHOW SUCCESS (MANUAL CLICK) ---
            // We stop here to PROVE login worked. Click the link to go to dashboard.
            echo '<!DOCTYPE html>
            <html>
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>body{font-family:sans-serif;background:#0f172a;color:white;text-align:center;padding:50px;}</style>
            </head>
            <body>
                <h1 style="color:#22c55e;">Login Successful!</h1>
                <p>Welcome, ' . $user['username'] . '</p>
                <br>
                <a href="/dashboard" style="background:#3b82f6; color:white; padding:15px 30px; text-decoration:none; border-radius:8px; font-size:18px;">Click to Enter Dashboard</a>
            </body>
            </html>';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Vault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #0f172a; 
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #f8fafc;
        }

        .login-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            width: 90%; /* Mobile friendly width */
            max-width: 400px;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .brand-name {
            color: #3b82f6;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 10px;
            display: block;
        }

        h2 { font-size: 1.4rem; margin-bottom: 5px; color: white; }
        
        .subtitle { color: #94a3b8; font-size: 0.85rem; margin-bottom: 25px; }

        .form-group { margin-bottom: 15px; text-align: left; position: relative; }

        input {
            width: 100%;
            padding: 12px;
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            font-size: 1rem;
            color: white;
            box-sizing: border-box;
        }

        input:focus { outline: none; border-color: #3b82f6; }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.85rem;
        }

        .footer-text { margin-top: 20px; font-size: 0.85rem; color: #94a3b8; }
        .footer-text a { color: #3b82f6; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-card">
    <span class="brand-name">Vault</span>
    <h2>Login to your account</h2>
    <p class="subtitle">Enter your details below.</p>

    <?php if($error): ?>
        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <input type="text" name="email" placeholder="Email or Username" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" name="login_btn" class="btn-login">Login</button>
    </form>

    <div class="footer-text">
        Don't have an account? <a href="signup.php">Sign Up</a>
    </div>
</div>

</body>
</html>

