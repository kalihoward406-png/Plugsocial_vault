<?php
session_start();
require 'db_config.php';
// 1. Fetch Total Users count
$user_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$user_row = mysqli_fetch_assoc($user_res);
$total_users = $user_row['total'];

// 2. Fetch Total Balance (Liability)
$bal_res = mysqli_query($conn, "SELECT SUM(balance) as total_bal FROM users");
$bal_row = mysqli_fetch_assoc($bal_res);
$total_balance = $bal_row['total_bal'] ?? 0; // Use 0 if no users exist

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check role directly from database to avoid session corruption
$uid = $_SESSION['user_id'];
$check = mysqli_query($conn, "SELECT role FROM users WHERE id = '$uid'");
$userData = mysqli_fetch_assoc($check);

if ($userData['role'] !== 'admin') {
    header("Location: dashboard.php?error=not_admin");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vault Admin | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --accent: #3b82f6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }

        body { background-color: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

        /* SIDEBAR FIX */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--card-bg);
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 { color: var(--accent); margin-bottom: 30px; text-align: center; font-size: 1.5rem; }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: 0.3s;
        }

        .nav-link:hover, .nav-link.active { background: rgba(59, 130, 246, 0.1); color: var(--accent); }
        .nav-link i { margin-right: 12px; width: 20px; text-align: center; }

        /* MAIN CONTENT FIX */
        .main-content {
            margin-left: var(--sidebar-width); /* Push content away from sidebar */
            padding: 40px;
            width: 100%;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .stat-card h3 { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .stat-card .value { font-size: 2rem; font-weight: bold; margin-top: 10px; display: block; }

        /* TABLE FIX */
        .content-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid var(--border);
            overflow-x: auto;
        }

        .content-card h2 { margin-bottom: 20px; font-size: 1.2rem; }

        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { text-align: left; padding: 15px; color: var(--text-muted); border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.95rem; }

        .btn-user-view {
            margin-top: auto;
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid #22c55e;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Vault Admin</h2>
    <a href="admin_dashboard.php" class="nav-link active"><i class="fas fa-chart-line"></i> Overview</a>
    <a href="admin_users.php" class="nav-link"><i class="fas fa-users"></i> User Management</a>
    <a href="admin_transactions.php" class="nav-link"><i class="fas fa-history"></i> Transactions</a>
    <a href="admin_pricing.php" class="nav-link"><i class="fas fa-tag"></i> Pricing</a>
    <a href="admin_broadcast.php" class="nav-link"><i class="fas fa-bullhorn"></i> Broadcast</a>
    
    <a href="dashboard.php" class="btn-user-view"><i class="fas fa-user"></i> User View</a>
</div>

<div class="main-content">
    <h1 style="margin-bottom: 30px;">Dashboard Overview</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <span class="value"><?php echo $total_users; ?></span>
        </div>
        <div class="stat-card">
            <h3>Users Liability</h3>
            <span class="value" style="color: #22c55e;">₦<?php echo number_format($total_balance, 2); ?></span>
        </div>
        <div class="stat-card">
            <h3>5Sim API Balance</h3>
            <span class="value" style="color: #eab308;">0 RUB</span>