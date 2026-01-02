<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user'])) {
    exit();
}

$email = $_SESSION['user'];
$query = "SELECT sender_number, message_body, received_at FROM incoming_sms WHERE user_email = '$email' ORDER BY received_at DESC LIMIT 10";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['sender_number']) . "</td>";
        echo "<td style='color: #22c55e; font-weight: bold;'>" . htmlspecialchars($row['message_body']) . "</td>";
        echo "<td>" . date('H:i:s', strtotime($row['received_at'])) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3' style='text-align:center; padding: 20px; color: #94a3b8;'>Waiting for SMS... <i class='fa-solid fa-spinner fa-spin'></i></td></tr>";
}
?>