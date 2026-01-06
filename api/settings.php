<?php
session_start();
include 'auth_session.php'; // <--- THIS ONE LINE FIXES THE LOGIN ISSUE
include 'db_config.php';
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit();
}

$email = $_SESSION['user'];
$message = "";

// Handle Password Change
if (isset($_POST['change_password'])) {
    $old_pass = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_password']);
    
    // Check if old password is correct
    $query = mysqli_query($conn, "SELECT password FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($query);

    if ($old_pass === $user['password']) { // Note: If using password_hash, use password_verify()
        $update = mysqli_query($conn, "UPDATE users SET password = '$new_pass' WHERE email = '$email'");
        if ($update) {
            $message = "<p style='color: #22c55e;'>Password updated successfully!</p>";
        }
    } else {
        $message = "<p style='color: #ef4444;'>Current password is incorrect.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vault Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: white; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .settings-card { background: #1e293b; padding: 40px; border-radius: 20px; border: 1px solid #334155; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h2 { font-family: 'Lexend'; margin-bottom: 10px; }
        .input-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.8rem; color: #94a3b8; margin-bottom: 8px; }
        input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: white; box-sizing: border-box; }
        .btn-save { width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-logout { display: block; text-align: center; margin-top: 20px; color: #ef4444; text-decoration: none; font-size: 0.9rem; font-weight: bold; }
        .back-link { display: block; text-align: center; margin-top: 10px; color: #94a3b8; text-decoration: none; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="settings-card">
    <h2><i class="fa-solid fa-gear" style="color:#2563eb"></i> Settings</h2>
    <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 25px;">Logged in as: <b><?php echo $email; ?></b></p>

    <?php echo $message; ?>

    <form method="POST">
        <div class="input-group">
            <label>Current Password</label>
            <input type="password" name="old_password" required>
        </div>
        <div class="input-group">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <button type="submit" name="change_password" class="btn-save">Update Password</button>
    </form>

   <a href="logout.php" style="color: red; font-weight: bold;">
    <i class="fa-solid fa-sign-out-alt"></i> Logout of Vault
</a>
    <a href="index.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>

