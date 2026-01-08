<?php
session_start();
include 'db_config.php';
include 'auth_session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and Sanitize Inputs
    $username    = mysqli_real_escape_string($conn, $_POST['username']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $password    = $_POST['password']; 
    $referred_by = mysqli_real_escape_string($conn, $_POST['referred_by']);

    // 2. Check if Username or Email already exists
    $check_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' OR email = '$email'");
    
    if (mysqli_num_rows($check_user) > 0) {
        $_SESSION['error'] = "Username or Email already taken!";
        header("Location: signup.php");
        exit();
    }

    // 3. Securely Hash the Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insert into Database (Including the referrer)
    $sql = "INSERT INTO users (username, email, password, referred_by, balance) 
            VALUES ('$username', '$email', '$hashed_password', '$referred_by', 0.00)";

    if (mysqli_query($conn, $sql)) {
        // Log the user in automatically after registration
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['username'] = $username;
        
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: signup.php");
        exit();
    }

}
