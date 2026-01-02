<?php
session_start();
include 'db_config.php';

// Get the reference and amount from the URL
$reference = $_GET['reference'];
$amount = (float)$_GET['amount']; 
$email = $_SESSION['user'];

// 1. (Optional) You should verify the reference with Paystack API here for security

// 2. Add the amount to the user's current balance
$sql = "UPDATE users SET balance = balance + $amount WHERE email = '$email'";

if (mysqli_query($conn, $sql)) {
    // Redirect back to dashboard to see the new balance
    header("Location: index.php?status=success");
} else {
    echo "Error updating balance: " . mysqli_error($conn);
}
?>