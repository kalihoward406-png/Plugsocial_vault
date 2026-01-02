<?php
session_start();
include 'db_config.php';

// 1. Security & Validation
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: receive_sms.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$country_code = $_POST['country'] ?? '';
$service_name = $_POST['service'] ?? '';

if (empty($country_code) || empty($service_name)) {
    header("Location: receive_sms.php?error=empty_fields");
    exit();
}

// 2. Define Pricing Source of Truth
// We redefine the array here so the price is trusted and secure.
$services_pricing = [
    'WhatsApp' => 3000, 'Telegram' => 3000, 'OpenAI / ChatGPT' => 3000, 
    'Binance' => 3000, 'Bybit' => 3000, 'KuCoin' => 3000, 'Tinder' => 3000,
    'Claude AI' => 3000, 'WeChat' => 3000, 'Coinbase' => 3000, 'CashApp' => 3000,
    
    'Google / Gmail' => 2000, 'TikTok' => 2000, 'Apple' => 2000, 'PayPal' => 2000,
    'Stripe' => 2000, 'Bumble' => 2000, 'Hinge' => 2000, 'Gemini AI' => 2000,
    'Viber' => 2000, 'Line' => 2000, 'Signal' => 2000, 'Other/Global' => 2000,
    
    'Instagram' => 1500, 'Facebook' => 1500, 'Microsoft' => 1500, 
    'Snapchat' => 1500, 'Uber' => 1500, 'Bolt' => 1500, 'Skrill' => 1500, 'Neteller' => 1500,
    
    'Twitter (X)' => 1000, 'Amazon' => 1000, 'Netflix' => 1000, 'LinkedIn' => 1000,
    'Discord' => 1000, 'Spotify' => 1000, 'Airbnb' => 1000, 'AliExpress' => 1000,
    'eBay' => 1000, 'Alibaba' => 1000, 'Steam' => 1000, 'PlayStation' => 1000,
    'Xbox' => 1000, 'Twitch' => 1000, 'Yahoo' => 1000, 'Outlook' => 1000,
    'Pinterest' => 1000, 'Reddit' => 1000, 'Quora' => 1000, 'Zoho' => 1000, 'Wisdom' => 1000
];

// Get Price (Default to 2000 if not found)
$amount = $services_pricing[$service_name] ?? 2000;

// 3. Database Transaction
mysqli_begin_transaction($conn);

try {
    // Fetch current balance (Locking the row for safety)
    $stmt = mysqli_prepare($conn, "SELECT balance FROM users WHERE id = ? FOR UPDATE");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $current_balance = (float)$user['balance'];

    // Check Funds
    if ($current_balance < $amount) {
        throw new Exception("insufficient_funds");
    }

    // Deduct Balance
    $new_balance = $current_balance - $amount;
    $update_bal = mysqli_prepare($conn, "UPDATE users SET balance = ? WHERE id = ?");
    mysqli_stmt_bind_param($update_bal, "di", $new_balance, $user_id);
    mysqli_stmt_execute($update_bal);

    // Generate Mock Number (Simulating API)
    // In a real app, you would call an external API here like 5sim.net
    $prefix = rand(100, 999);
    $line = rand(1000, 9999);
    $mock_number = "+{$country_code} {$prefix} {$line}";

    // Record the Order
    $insert_order = mysqli_prepare($conn, "INSERT INTO sms_orders (user_id, service, country, amount, phone_number, status) VALUES (?, ?, ?, ?, ?, 'active')");
    mysqli_stmt_bind_param($insert_order, "issds", $user_id, $service_name, $country_code, $amount, $mock_number);
    mysqli_stmt_execute($insert_order);

    // Commit Transaction
    mysqli_commit($conn);
    header("Location: receive_sms.php?status=success");

} catch (Exception $e) {
    mysqli_rollback($conn);
    if ($e->getMessage() == "insufficient_funds") {
        header("Location: receive_sms.php?error=insufficient_funds");
    } else {
        header("Location: receive_sms.php?error=system_error");
    }
// ... inside your SMS purchase logic ...
if ($sms_purchased_successfully) {
    // LOG THE TRANSACTION
    logTransaction($conn, $user_id, "SMS Verification ($country)", $service_cost);
    
    echo "Number assigned!";
}
}
?>