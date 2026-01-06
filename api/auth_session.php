<?php
// VERCEL SESSION KEEPER
ini_set('display_errors', 1);
ini_set('session.save_path', '/tmp');
session_set_cookie_params(['path' => '/', 'samesite' => 'Lax']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CHECK LOGIN (Session OR Cookie)
$user_id = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['auth_user_id'])) {
    // Restore session from Cookie
    $user_id = $_COOKIE['auth_user_id'];
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $_COOKIE['auth_username'] ?? 'User'; // Optional safety
}

// KICK OUT IF NOT LOGGED IN
if (!$user_id) {
    header("Location: /login");
    exit();
}
?>
