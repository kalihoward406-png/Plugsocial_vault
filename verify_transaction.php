<?php
session_start();
include 'db_config.php';

// 1. Get the reference from the URL
$reference = $_GET['reference'] ?? '';

if (empty($reference)) {
    die("No reference provided.");
}

// 2. Secret Key from Paystack Dashboard
$secret_key = "sk_test_77f208a3ddb9e03759d2ad1014f0fe08b792559f"; // <--- REPLACE THIS

// 3. Verify with Paystack API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "authorization: Bearer " . $secret_key,
        "cache-control: no-cache"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($response) {
    $result = json_decode($response);
    if ($result->data->status == 'success') {
        
        $amount_paid = $result->data->amount / 100; // Convert kobo to Naira
        $user_id = $_SESSION['user_id'];

        // 4. Update the User Balance
        $update_balance = "UPDATE users SET balance = balance + $amount_paid WHERE id = '$user_id'";
        
        // 5. Update the Transaction Table status
        $update_trans = "UPDATE transactions SET status = 'success' WHERE reference = '$reference'";

        if (mysqli_query($conn, $update_balance) && mysqli_query($conn, $update_trans)) {
            // Success! Redirect to dashboard
            header("Location: dashboard.php?status=success");
            exit();
        }
    } else {
        header("Location: fund_wallet.php?status=failed");
    }
}
?>