<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages from Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
<body style="background: #0f172a; color: white; padding: 40px;">

    <h2><i class="fas fa-bullhorn"></i> Admin Broadcasts</h2>
    <hr style="border: 0.5px solid #334155; margin: 20px 0;">

    <?php
    $result = mysqli_query($conn, "SELECT * FROM broadcasts ORDER BY created_at DESC");
    
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo "<div style='background:#1e293b; padding:20px; border-radius:10px; margin-bottom:15px; border-left:4px solid #3b82f6;'>";
            echo "<small style='color:#94a3b8;'>" . $row['created_at'] . "</small>";
            echo "<p style='margin-top:10px; font-size:1.1rem;'>" . $row['message'] . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No messages at this time.</p>";
    }
    ?>
    
    <a href="dashboard.php" style="color:#3b82f6; text-decoration:none;">← Back to Dashboard</a>
</body>
</html>