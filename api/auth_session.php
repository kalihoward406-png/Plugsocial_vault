<?php
// api/auth_session.php

// 1. Only configure if no session exists yet
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

$user_id = null;

// 2. Check standard session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} 
// 3. Backup: Restore from Cookie if Vercel dropped the session
elseif (isset($_COOKIE['auth_user_id'])) {
    $user_id = $_COOKIE['auth_user_id'];
    $_SESSION['user_id'] = $user_id; 
}

// 4. If still no user, redirect to login
if (!$user_id) {
    echo "<script>window.location.href='/login';</script>";
    exit();
}
?>
