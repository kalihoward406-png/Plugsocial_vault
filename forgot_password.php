<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $code = rand(1000, 9999); // Generate random 4-digit code
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        
        // Save code to DB
        mysqli_query($conn, "UPDATE users SET reset_code='$code', reset_expires='$expires' WHERE email='$email'");
        
        // IMPORTANT: On Localhost, mail() usually won't send a real email
        // For testing, we will echo the code so you can see it works
        echo "<script>alert('Test Mode: Your 4-digit code is $code'); window.location='verify_code.php?email=$email';</script>";
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body style="background:#0f172a; color:white; font-family:sans-serif; display:flex; justify-content:center; align-items:center; height:100vh;">
    <div style="background:#1e293b; padding:30px; border-radius:10px; width:300px; border:1px solid #334155;">
        <h3>Reset Password</h3>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your registered email" required style="width:100%; padding:10px; margin:10px 0; border-radius:5px; background:#0f172a; color:white; border:1px solid #334155;">
            <button type="submit" style="width:100%; padding:10px; background:#2563eb; color:white; border:none; border-radius:5px; cursor:pointer;">Send Reset Code</button>
        </form>
    </div>
</body>
</html>