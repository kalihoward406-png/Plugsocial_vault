<?php
session_start();
include 'db_config.php';

// Security: Ensure only admins can access this page
$user_id = $_SESSION['user_id'];
$u_check = mysqli_query($conn, "SELECT role FROM users WHERE id = '$user_id'");
$admin_data = mysqli_fetch_assoc($u_check);

if (!$admin_data || $admin_data['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Search/Filter Logic (Optional)
$where_clause = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause = "WHERE users.username LIKE '%$search%' OR transactions.type LIKE '%$search%'";
}

// Fetch Transactions with Usernames
$query = "SELECT transactions.*, users.username 
          FROM transactions 
          JOIN users ON transactions.user_id = users.id 
          $where_clause
          ORDER BY transactions.id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Master Transaction Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-w: 260px; --bg: #0f172a; --card: #1e293b; --accent: #3b82f6; --text: #f8fafc; --border: #334155; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }

        /* Sidebar Styles */
        .sidebar { width: var(--sidebar-w); background: var(--card); border-right: 1px solid var(--border); height: 100vh; position: fixed; padding: 25px; }
        .sidebar h2 { color: var(--accent); margin-bottom: 30px; text-align: center; }
        .nav-item { display: flex; align-items: center; padding: 12px 15px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 8px; }
        .nav-item:hover, .nav-item.active { background: rgba(59, 130, 246, 0.1); color: var(--accent); }
        .nav-item i { margin-right: 12px; width: 20px; }

        /* Main Content */
        .main-content { margin-left: var(--sidebar-w); padding: 40px; width: calc(100% - var(--sidebar-w)); }

        /* Table & Cards */
        .log-container { background: var(--card); border-radius: 16px; padding: 25px; border: 1px solid var(--border); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #94a3b8; border-bottom: 1px solid var(--border); font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.95rem; }

        /* Status Badges */
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-success { background: rgba(34, 197, 94, 0.2); color: #22c55e; border: 1px solid #22c55e; }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444; }
        .badge-warning { background: rgba(234, 179, 8, 0.2); color: #eab308; border: 1px solid #eab308; }
        
        .search-bar { margin-bottom: 20px; width: 100%; max-width: 400px; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Vault Admin</h2>
    <a href="admin_dashboard.php" class="nav-item"><i class="fas fa-th-large"></i> Overview</a>
    <a href="admin_users.php" class="nav-item"><i class="fas fa-users"></i> Users</a>
    <a href="admin_transactions.php" class="nav-item active"><i class="fas fa-list-ul"></i> Master Logs</a>
    <a href="admin_broadcast.php" class="nav-item"><i class="fas fa-bullhorn"></i> Broadcast</a>
</div>

<div class="main-content">
    <h1 style="margin-bottom: 20px;">Master Transaction Logs</h1>
    
    <form action="" method="GET">
        <input type="text" name="search" class="search-bar" placeholder="Search by username or type..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    </form>

    <div class="log-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Description/Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $status = $row['status'];
                        $badge = 'badge-warning';
                        if ($status == 'Completed' || $status == 'Success') $badge = 'badge-success';
                        if ($status == 'Failed' || $status == 'Declined') $badge = 'badge-danger';

                        echo "<tr>
                            <td style='color: #94a3b8;'>#{$row['id']}</td>
                            <td style='font-weight: 600;'>".htmlspecialchars($row['username'])."</td>
                            <td>".htmlspecialchars($row['type'])."</td>
                            <td style='color: #22c55e; font-weight: bold;'>₦".number_format($row['amount'], 2)."</td>
                            <td><span class='badge $badge'>$status</span></td>
                            <td style='color: #94a3b8;'>".date('M d, Y H:i', strtotime($row['created_at']))."</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align: center; padding: 40px;'>No transactions found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>