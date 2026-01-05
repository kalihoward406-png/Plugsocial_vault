<?php
// 1. ENABLE ERROR REPORTING FOR DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. CONFIGURE SESSION FOR VERCEL
// This stores sessions in a temp folder so Vercel doesn't lose them immediately
ini_set('session.save_path', '/tmp');
session_set_cookie_params([
    'path' => '/',
    'secure' => true,     // Required for HTTPS (Vercel)
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// 3. CONNECT TO DATABASE
require 'db_config.php'; // Ensure this file exists in api/ folder

// 4. HANDLE LOGIN REQUEST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; 
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify Password
        // Note: If you aren't using password_hash() in signup yet, 
        // compare directly: if ($password === $user['password']) { ... }
        if (password_verify($password, $user['password'])) {
            
            // SAVE SESSION DATA
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // FORCE REDIRECT USING JAVASCRIPT
            // This bypasses PHP header issues
            echo '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="refresh" content="0;url=/dashboard">
                <script type="text/javascript">
                    window.location.href = "/dashboard";
                </script>
            </head>
            <body>
                <p>Login successful. Redirecting to dashboard...</p>
            </body>
            </html>';
            exit();
        } else {
            echo "<h3 style='color:red; text-align:center;'>Incorrect Password</h3>";
        }
    } else {
        echo "<h3 style='color:red; text-align:center;'>User not found</h3>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vault</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-box { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 90%; max-width: 400px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #0070f3; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0051a2; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align:center;">Login</h2>
        <form method="POST" action="/login"> 
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
        <p style="text-align:center; margin-top:15px;">
            Don't have an account? <a href="/signup">Sign Up</a>
        </p>
    </div>
</body>
</html>
