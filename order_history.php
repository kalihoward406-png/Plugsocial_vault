<?php
session_start();
include 'db_config.php'; // Ensure path is correct

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for this specific user
$orders_query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Vault</title>
    <style>
        body { background: #0f172a; color: white; font-family: sans-serif; padding: 20px; }
        .container { max-width: 900px; margin: auto; }
        table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 10px; overflow: hidden; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #334155; color: #94a3b8; text-transform: uppercase; font-size: 0.8rem; }
        .status { padding: 5px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: bold; }
        .pending { background: #92400e; color: #fef3c7; }
        .processing { background: #1e40af; color: #dbeafe; }
        .completed { background: #065f46; color: #d1fae5; }
        .back-link { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Order History</h2>
        <a href="index.php" class="back-link">← Back to Dashboard</a>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Service</th>
                    <th>Quantity</th>
                    <th>Cost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($orders_query)): ?>
                <tr>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td><?php echo $order['service_name']; ?></td>
                    <td><?php echo number_format($order['quantity']); ?></td>
                    <td>₦<?php echo number_format($order['cost'], 2); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($order['status']); ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($orders_query) == 0): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">You haven't placed any orders yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>