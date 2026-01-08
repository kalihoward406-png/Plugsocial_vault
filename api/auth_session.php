<?php
// api/auth_session.php

// 1. Force Session Settings for Vercel
ini_set('session.save_path', '/tmp');
if (session_status() === PHP_SESSION_NONE) { 
    // Set cookie params BEFORE starting session
    session_set_cookie_params([
        'path' => '/',
        'secure' => true, 
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start(); 
}

$user_id = null;

// 2. PRIMARY CHECK: Is the session active?
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} 
// 3. BACKUP CHECK: Session died? Restore it from the Cookie!
elseif (isset($_COOKIE['auth_user_id'])) {
    $user_id = $_COOKIE['auth_user_id'];
    $_SESSION['user_id'] = $user_id; // Resurrect the session
}

// 4. FINAL VERDICT: If both failed, kick them out.
if (!$user_id) {
    // We use JS redirect because header() often fails on Vercel if HTML is already loading
    echo "<script>window.location.href='/login';</script>";
    exit();
}
?>
