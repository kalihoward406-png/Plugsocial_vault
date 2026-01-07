<?php
// Force session to save in a place Vercel can see
ini_set('session.save_path', '/tmp');
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Check for Session OR the Backup Cookie we made
$user_id = $_SESSION['user_id'] ?? $_COOKIE['auth_user_id'] ?? null;

if (!$user_id) {
    // If no user is found, we use JAVASCRIPT to redirect. 
    // This is 100% more reliable on mobile and Vercel.
    echo "<script>window.location.href='/login';</script>";
    exit();
}
?>
