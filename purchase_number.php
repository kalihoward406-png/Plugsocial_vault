<?php
session_start();
include 'db_config.php'; // Contains your DB connection and logTransaction function

// 1. API Configuration
$apiKey = "eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3OTg2Mjk5NTksImlhdCI6MTc2NzA5Mzk1OSwicmF5IjoiOTJkYzA2MmUzNjJjYWQ5ODIxNDE5N2U3MTM2Nzc0YjgiLCJzdWIiOjM2OTgwMjN9.XgY4bVI2WCm_NBnQ-fk03POW9ZWksnydXBE1fnDCwGrN3qx-x-Van6bM1kcUnUdM3DH1KsGziEEy7JRtLhSLP21jC5lnN5jGMXpUq4PW8Pqx9Oq4-dEFROSon_metVVFvRrfD3pL_cU6p9miEMrnQ3uiBMXkmjGoJ70An2wGM90RsHzkVx1Uvlztpl7QjGrIFL4-vfeQTQ3OR_rhO7OOKzKpii7vZDDa9n979FCBlG27g36Yq9UbpPcswSAqaB8ndWGC-6Yqxxu8piOZw9Pbnq8hUa9U54RdE8At5815XfAhLEsIah80l9xe1GSVP0Fj2znGVKsajxRNqhZvhkKrGA"; // Replace with your actual key
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service = mysqli_real_escape_string($conn, $_POST['service']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $internal_price = mysqli_real_escape_string($conn, $_POST['price']); // The price you charge your users

    // 2. Check User Balance
    $u_query = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id'");
    $user = mysqli_fetch_assoc($u_query);

    if ($user['balance'] < $internal_price) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient balance.']);
        exit();
    }

    // 3. Call 5sim API to Buy Number
    // Endpoint format: /user/buy/activation/country/operator/service
    $url = "https://5sim.net/v1/user/buy/activation/$country/any/$service";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Accept: application/json"
    ]);

    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    // 4. Handle API Response
    if (isset($data['id'])) {
        $api_order_id = $data['id'];
        $phone_number = $data['phone'];

        // A. Deduct balance from user
        mysqli_query($conn, "UPDATE users SET balance = balance - $internal_price WHERE id = '$user_id'");

        // B. Log the transaction for your "Recent Transactions" table
        logTransaction($conn, $user_id, "SMS Purchase: " . ucfirst($service) . " ($country)", $internal_price);

        // C. Save the active order so you can check for the OTP code later
        mysqli_query($conn, "INSERT INTO active_sms (user_id, api_order_id, phone_number, service, status) 
                             VALUES ('$user_id', '$api_order_id', '$phone_number', '$service', 'pending')");

        echo json_encode([
            'status' => 'success',
            'number' => $phone_number,
            'order_id' => $api_order_id
        ]);
    } else {
        // API error (e.g., "no free phones")
        echo json_encode(['status' => 'error', 'message' => 'Service currently unavailable on 5sim.']);
    }
}
?>