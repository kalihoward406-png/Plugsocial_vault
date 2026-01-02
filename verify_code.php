<?php
include 'db_config.php';
$email = $_GET['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_code = $_POST['code'];
    $new_pass = $_POST['new_password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND reset_code='$user_code' AND reset_expires > NOW()");
    
    if (mysqli_num_rows($res) > 0) {
        // Success: Update password and clear code
        mysqli_query($conn, "UPDATE users SET password='$new_pass', reset_code=NULL, reset_expires=NULL WHERE email='$email'");
        header("Location: login.php?msg=Reset Successful");
    } else {
        $error = "Invalid or expired code!";
    }
}
?>
<!DOCTYPE html>
<html>
<body style="background:#0f172a; color:white; font-family:sans-serif; display:flex; justify-content:center; align-items:center; height:100vh;">
    <form method="POST" style="background:#1e293b; padding:30px; border-radius:10px; width:300px; border:1px solid #334155;">
        <h3>Verify Code</h3>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <input type="text" name="code" placeholder="4-Digit Code" required maxlength="4" style="width:100%; padding:10px; margin:5px 0; border-radius:5px; background:#0f172a; color:white; border:1px solid #334155;">
        <input type="password" name="new_password" placeholder="New Password" required style="width:100%; padding:10px; margin:5px 0; border-radius:5px; background:#0f172a; color:white; border:1px solid #334155;">
        <button type="submit" style="width:100%; padding:10px; background:#2563eb; color:white; border:none; border-radius:5px; cursor:pointer; margin-top:10px;">Change Password</button>
    </form>
</body>
</html>