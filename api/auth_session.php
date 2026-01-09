<?php
// api/auth_session.php

// 1. Only set ini settings if the session hasn't started yet
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

// 2. Check session or restore from cookie
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} 
elseif (isset($_COOKIE['auth_user_id'])) {
    $user_id = $_COOKIE['auth_user_id'];
    $_SESSION['user_id'] = $user_id; 
}

// 3. Final Verdict
if (!$user_id) {
    echo "<script>window.location.href='/login';</script>";
    exit();
}
?>
