<?php
// Force session settings for Vercel file system
ini_set('session.save_path', '/tmp');
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$user_id = null;

// 1. Check if Session is alive
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} 
// 2. If Session is dead (Vercel reset), check the COOKIE
elseif (isset($_COOKIE['auth_user_id'])) {
    $user_id = $_COOKIE['auth_user_id'];
    $_SESSION['user_id'] = $user_id; // Restore the session
}

// 3. Debugging: If you are still stuck, uncomment the line below to see what's happening
// die("Session: " . print_r($_SESSION, true) . " | Cookie: " . print_r($_COOKIE, true));

// 4. If neither exists, Kick them out
if (!$user_id) {
    echo "<script>
        alert('Session Expired. Please Login Again.');
        window.location.href='/login';
    </script>";
    exit();
}
?>
