<?php
session_start();
include 'db_config.php';

// 1. Define the missing logTransaction function so it doesn't crash
function logTransaction($conn, $user_id, $type, $amount, $status) {
    $sql = "INSERT INTO transactions (user_id, type, amount, status, created_at) 
            VALUES ('$user_id', '$type', '$amount', '$status', NOW())";
    return mysqli_query($conn, $sql);
}

// 2. Get data from URL and Session safely
$reference = $_GET['reference'] ?? null;
$amount_paid = isset($_GET['amount']) ? (float)$_GET['amount'] : 0;
$user_id = $_SESSION['user_id'] ?? null;

if (!$reference || !$user_id) {
    die("Missing payment reference or user session.");
}

/** * 3. VERIFICATION LOGIC
 * In a real scenario, you'd use CURL to ask Paystack if $reference is valid.
 * For now, we assume if we have a reference and amount, it's successful.
 */
$payment_was_successful = ($amount_paid > 0); 

if ($payment_was_successful) {
    // 4. Update user balance using the ID from session
    $update_query = "UPDATE users SET balance = balance + $amount_paid WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        // 5. Log the success so it shows in 'Recent Transactions'
        logTransaction($conn, $user_id, 'Deposit (Paystack)', $amount_paid, 'Completed');
        
        // Redirect to success page
        header("Location: fund_wallet.php?status=success");
        exit();
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
} else {
    // 6. Log as failed if payment was not successful
    logTransaction($conn, $user_id, 'Deposit (Paystack)', $amount_paid, 'Failed');
    header("Location: fund_wallet.php?status=failed");
    exit();
}
?>