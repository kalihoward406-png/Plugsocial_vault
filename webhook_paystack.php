<?php
include 'db_config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

// Retrieve the request's body and parse it as JSON
$input = file_get_contents("php://input");
define('PAYSTACK_SECRET_KEY', 'sk_test_77f208a3ddb9e03759d2ad1014f0fe08b792559f'); // REPLACE WITH YOUR SECRET KEY

// Validate the request is actually from Paystack
if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY)) exit;

$event = json_decode($input);

// Handle the charge.success event
if ($event->event === 'charge.success') {
    $amount = $event->data->amount / 100; // Convert kobo to Naira
    $email = $event->data->customer->email;
    $reference = $event->data->reference;

    // Check if this transaction was already processed by verify_transaction.php
    $check = mysqli_query($conn, "SELECT status FROM transactions WHERE reference = '$reference'");
    $status = mysqli_fetch_assoc($check);

    if ($status && $status['status'] === 'pending') {
        // Update user balance
        mysqli_query($conn, "UPDATE users SET balance = balance + $amount WHERE email = '$email'");
        
        // Update transaction record
        mysqli_query($conn, "UPDATE transactions SET status = 'success' WHERE reference = '$reference'");
    }
}

http_response_code(200); // Tell Paystack you received it
?>