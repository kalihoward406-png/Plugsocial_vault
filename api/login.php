<?php
session_start();
include 'db_config.php';
include 'header.php';

$error = "";

if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Query supports Email OR Username
    $query = "SELECT * FROM users WHERE email = '$email' OR username = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            // SET SESSION VARIABLES
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
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
    <title>Login | Vault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Dark Theme Background */
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
            background-color: #1e293b; /* Card Color */
            border: 1px solid #334155;
            width: 100%;
            max-width: 420px;
            padding: 40px 30px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        /* Brand & Headers */
        .brand-name {
            color: #3b82f6; /* Bright Blue */
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 10px;
            display: block;
            letter-spacing: -1px;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: white;
        }
        
        .subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        /* Inputs */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
            position: relative;
        }

        input[type="email"], input[type="text"], input[type="password"] {
            width: 100%;
            padding: 14px;
            background-color: #0f172a; /* Dark Input Background */
            border: 1px solid #334155;
            border-radius: 8px;
            font-size: 1rem;
            color: white;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6; /* Blue Focus Border */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        /* Eye Icon */
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            cursor: pointer;
        }
        .toggle-password:hover { color: #94a3b8; }

        /* Remember Me & Forgot Password */
        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 25px;
        }

        .remember-label {
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .forgot-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: #2563eb; /* Blue Button */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-login:hover { background-color: #1d4ed8; }

        /* Footer */
        .footer-text {
            margin-top: 25px;
            font-size: 0.9rem;
            color: #94a3b8;
        }
        .footer-text a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }

        /* Error Message */
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <span class="brand-name">Vault</span>
    
    <h2>Login to your account</h2>
    <p class="subtitle">Welcome back! Please enter your details.</p>

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
            <div style="position:relative;">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
            </div>
        </div>

        <div class="options-row">
            <label class="remember-label">
                <input type="checkbox" name="remember"> Remember Me
            </label>
            <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
        </div>

       <button type="submit" name="login_btn" class="btn-login">Login</button>
    </form>

    <div class="footer-text">
        Don't have an account? <a href="/signup">Sign Up</a>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.querySelector('.toggle-password');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>





