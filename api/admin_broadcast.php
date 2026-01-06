<?php
session_start();
include 'db_config.php';

// 1. Check if user_id exists in session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch the latest role directly from the database
$result = mysqli_query($conn, "SELECT role FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($result);

// 3. If the role in DB is NOT admin, send them to user dashboard ONCE
if (!$user || $user['role'] !== 'admin') {
    header("Location: dashboard.php"); // Removed the ?error= part to prevent loops
    exit();
}


$msg = "";

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Clear old broadcasts and insert the new one
    mysqli_query($conn, "DELETE FROM broadcasts"); 
    $insert = mysqli_query($conn, "INSERT INTO broadcasts (message) VALUES ('$message')");
    
    if ($insert) {
        $msg = "<p style='color: #22c55e; margin-bottom: 20px;'>✔ Broadcast sent successfully!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Send Broadcast</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-w: 260px; --bg: #0f172a; --card: #1e293b; --accent: #3b82f6; --text: #f8fafc; --border: #334155; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }

        .sidebar { width: var(--sidebar-w); background: var(--card); border-right: 1px solid var(--border); height: 100vh; position: fixed; padding: 25px; }
        .sidebar h2 { color: var(--accent); margin-bottom: 30px; text-align: center; }
        .nav-item { display: flex; align-items: center; padding: 12px 15px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 8px; }
        .nav-item:hover, .nav-item.active { background: rgba(59, 130, 246, 0.1); color: var(--accent); }
        .nav-item i { margin-right: 12px; width: 20px; }

        .main-content { margin-left: var(--sidebar-w); padding: 40px; width: calc(100% - var(--sidebar-w)); }

        .broadcast-card { background: var(--card); border-radius: 16px; padding: 30px; border: 1px solid var(--border); max-width: 600px; }
        textarea { width: 100%; height: 150px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; color: white; padding: 15px; margin-bottom: 20px; resize: none; }
        button { background: var(--accent); color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; transition: 0.3s; }
        button:hover { background: #2563eb; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Vault Admin</h2>
    <a href="admin_dashboard.php" class="nav-item"><i class="fas fa-th-large"></i> Overview</a>
    <a href="admin_users.php" class="nav-item"><i class="fas fa-users"></i> Users</a>
    <a href="admin_transactions.php" class="nav-item"><i class="fas fa-list-ul"></i> Master Logs</a>
    <a href="admin_broadcast.php" class="nav-item active"><i class="fas fa-bullhorn"></i> Broadcast</a>
</div>

<div class="main-content">
    <h1>Global Broadcast</h1>
    <p style="color: #94a3b8; margin-bottom: 30px;">This message will appear at the top of every user's dashboard.</p>

    <?php echo $msg; ?>

    <div class="broadcast-card">
        <form method="POST">
            <label style="display: block; margin-bottom: 10px; font-weight: bold;">Notification Message</label>
            <textarea name="message" placeholder="Type your announcement here... (e.g. 5Sim Nigeria is currently offline, please use Ghana operators)"></textarea>
            <button type="submit">Send to All Users</button>
        </form>
    </div>
</div>

</body>
</html>
