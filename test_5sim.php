<?php
include 'db_config.php'; // Ensure your DB and API functions are included

$apiKey = "eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3OTg2Mjk5NTksImlhdCI6MTc2NzA5Mzk1OSwicmF5IjoiOTJkYzA2MmUzNjJjYWQ5ODIxNDE5N2U3MTM2Nzc0YjgiLCJzdWIiOjM2OTgwMjN9.XgY4bVI2WCm_NBnQ-fk03POW9ZWksnydXBE1fnDCwGrN3qx-x-Van6bM1kcUnUdM3DH1KsGziEEy7JRtLhSLP21jC5lnN5jGMXpUq4PW8Pqx9Oq4-dEFROSon_metVVFvRrfD3pL_cU6p9miEMrnQ3uiBMXkmjGoJ70An2wGM90RsHzkVx1Uvlztpl7QjGrIFL4-vfeQTQ3OR_rhO7OOKzKpii7vZDDa9n979FCBlG27g36Yq9UbpPcswSAqaB8ndWGC-6Yqxxu8piOZw9Pbnq8hUa9U54RdE8At5815XfAhLEsIah80l9xe1GSVP0Fj2znGVKsajxRNqhZvhkKrGA"; // Put your actual key here

// Test Endpoint: Profile details (shows balance and email)
$url = "https://5sim.net/v1/user/profile";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $apiKey,
    "Accept: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

echo "<h2>5Sim Connectivity Test</h2>";

if ($httpCode == 200) {
    echo "<p style='color: green;'>✔ Connection Successful!</p>";
    echo "<b>Your 5Sim Balance:</b> " . $data['balance'] . " RUB<br>";
    echo "<b>Account Email:</b> " . $data['email'];
} else {
    echo "<p style='color: red;'>✘ Connection Failed!</p>";
    echo "<b>Error Code:</b> " . $httpCode . "<br>";
    echo "<b>Response:</b> " . $response;
}
?>