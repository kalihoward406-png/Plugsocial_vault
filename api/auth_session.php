<?php
// api/auth_session.php
ini_set('session.save_path', '/tmp');
session_start();

$user_id = null;

// 1. Check Session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} 
// 2. Check Cookie
elseif (isset($_COOKIE['auth_user_id'])) {
    $user_id = $_COOKIE['auth_user_id'];
    $_SESSION['user_id'] = $user_id; // Restore session from cookie
}

// 3. FINAL VALIDATION
if (!$user_id) {
    // Instead of a silent header redirect, use JS to see what's happening
    echo "<script>
        console.log('Auth Failed: No Session or Cookie found.');
        window.location.href='/login';
    </script>";
    exit();
}
?>
