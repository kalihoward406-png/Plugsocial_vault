<?php
session_start();
include 'db_config.php';

$email = $_SESSION['user'];
$price = (float)$_GET['price'];
$item = $_GET['item'];

// 1. Fetch the user's current balance to check if they can afford it
$user_query = mysqli_query($conn, "SELECT balance FROM users WHERE email = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$current_balance = $user_data['balance'];

if ($current_balance >= $price) {
    // 2. Subtract the price from the balance
    $new_balance = $current_balance - $price;
    $update_sql = "UPDATE users SET balance = $new_balance WHERE email = '$email'";
    
    if (mysqli_query($conn, $update_sql)) {
        // Success! Redirect to a page that shows their new number
        echo "<script>alert('Purchase Successful! ₦$price deducted.'); window.location.href='index.php';</script>";
    }
} else {
    // Not enough money
    echo "<script>alert('Insufficient Balance. Please top up your vault.'); window.location.href='index.php';</script>";
}
?>