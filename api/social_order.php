<?php
session_start();
include 'auth_session.php'; // <--- THIS ONE LINE FIXES THE LOGIN ISSUE
include 'db_config.php';
include 'header.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to place an order.");
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// 2. Fetch User Data (Balance)
$user_query = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$user_balance = $user_data['balance'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Define the variables from your form
    $service_id = mysqli_real_escape_string($conn, $_POST['service']);
    $quantity = (int)$_POST['quantity'];
    
    // Example: Fetching price from a services table (adjust table name if needed)
    $price_per_1k = 500; // You should fetch this from your database
    $total_cost = ($price_per_1k / 1000) * $quantity;

    // 4. Check if user has enough money
    if ($user_balance >= $total_cost) {
        // 5. Deduct balance and process order
        $new_balance = $user_balance - $total_cost;
        
        $update_sql = "UPDATE users SET balance = '$new_balance' WHERE id = '$user_id'";
        
        if (mysqli_query($conn, $update_sql)) {
            $success = "Order placed successfully! New balance: ₦" . number_format($new_balance, 2);
            // Re-fetch balance for the display
            $user_balance = $new_balance;
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Insufficient balance! You need ₦" . number_format($total_cost, 2);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Boost Social Media | Vault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --primary: #2563eb;
            --text-muted: #94a3b8;
            --border: #334155;
        }

        body {
            background-color: var(--bg-dark);
            color: white;
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
        }

        .sidebar { width: 220px; background: var(--bg-card); height: 100vh; position: fixed; padding: 20px; border-right: 1px solid var(--border); }
        .sidebar .brand { color: var(--primary); font-size: 1.8rem; font-weight: 800; margin-bottom: 30px; display: block; }
        .nav-link { color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 8px; margin-bottom: 5px; }
        .nav-link.active { background: rgba(37, 99, 235, 0.1); color: var(--primary); }

        .main-content { margin-left: 220px; flex: 1; padding: 40px; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .balance-badge { background: #152033; padding: 8px 15px; border-radius: 20px; border: 1px solid var(--border); color: #22c55e; font-weight: 700; }

        .tab-container { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; }
        .tab { padding: 10px 30px; border-radius: 8px; cursor: pointer; font-weight: 600; border: 1px solid var(--border); color: var(--text-muted); transition: 0.3s; }
        .tab.active { background: var(--primary); color: white; border-color: var(--primary); }

        .order-card {
            max-width: 550px;
            margin: 0 auto;
            background: var(--bg-card);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid var(--border);
        }

        .order-card h3 { display: flex; align-items: center; gap: 10px; margin-top: 0; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; }
        
        select, input {
            width: 100%;
            padding: 14px;
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .cost-box {
            background: rgba(37, 99, 235, 0.05);
            border: 1px dashed var(--primary);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }
        .cost-label { color: var(--text-muted); font-size: 0.9rem; }
        .cost-value { display: block; font-size: 2rem; font-weight: 800; color: var(--primary); }

        .btn-confirm {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <span class="brand">Vault</span>
    <a href="/dashboard" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="/social_order" class="nav-link"><i class="fas fa-rocket"></i> Boost Account</a>
    <a href="/receive_sms" class="nav-link"><i class="fas fa-envelope"></i> Receive SMS</a>
    <a href="/fund_wallet" class="nav-link"><i class="fas fa-wallet"></i> Fund Wallet</a>
    <a href="/settings" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
</div>

<div class="main-content">
    <div class="header">
        <h2>Boost Social Media</h2>
        <div class="balance-badge">Balance: ₦<?= $balance ?></div>
    </div>

    <div class="tab-container">
        <div class="tab active">Social Media</div>
        <div class="tab" onclick="window.location.href='receive_sms.php'">SMS Verification</div>
    </div>

    <div class="order-card">
        <h3>🚀 Boost Social Media</h3>
        
        <form action="process_order.php" method="POST">
            <div class="form-group">
                <label>Service Type</label>
                <select name="platform" id="platform" required onchange="calculateCost()">
                    <option value="">-- Select Platform --</option>
                    
                    <optgroup label="Popular Socials">
                        <option value="facebook">Facebook (Likes/Followers/Views)</option>
                        <option value="instagram">Instagram (Followers/Likes/Reels)</option>
                        <option value="tiktok">TikTok (Followers/Likes/Views)</option>
                        <option value="twitter_x">Twitter / X (Followers/Retweets)</option>
                        <option value="threads">Threads (Followers/Likes)</option>
                        <option value="snapchat">Snapchat (Friends/Score)</option>
                    </optgroup>

                    <optgroup label="Video & Streaming">
                        <option value="youtube">YouTube (Subs/Views/Watch Time)</option>
                        <option value="twitch">Twitch (Followers/Live Viewers)</option>
                        <option value="kick">Kick (Followers/Views)</option>
                        <option value="vimeo">Vimeo (Plays/Likes)</option>
                    </optgroup>

                    <optgroup label="Music Platforms">
                        <option value="spotify">Spotify (Plays/Followers)</option>
                        <option value="audiomack">Audiomack (Plays/Followers)</option>
                        <option value="soundcloud">SoundCloud (Plays/Likes)</option>
                        <option value="apple_music">Apple Music (Plays)</option>
                        <option value="deezer">Deezer (Fans/Plays)</option>
                        <option value="tidal">Tidal (Plays)</option>
                    </optgroup>

                    <optgroup label="Professional & Business">
                        <option value="linkedin">LinkedIn (Connections/Followers)</option>
                        <option value="google_business">Google Business (Reviews/Ratings)</option>
                        <option value="trustpilot">Trustpilot (Reviews)</option>
                    </optgroup>

                    <optgroup label="Communities & Niche">
                        <option value="telegram">Telegram (Members/Post Views)</option>
                        <option value="discord">Discord (Server Members)</option>
                        <option value="reddit">Reddit (Upvotes/Karma)</option>
                        <option value="quora">Quora (Followers/Upvotes)</option>
                        <option value="pinterest">Pinterest (Followers/Repins)</option>
                        <option value="tumblr">Tumblr (Followers/Likes)</option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label>Target Link</label>
                <input type="text" name="link" placeholder="Enter profile or post link..." required>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" id="quantity" placeholder="Enter quantity..." required oninput="calculateCost()">
            </div>

            <div class="cost-box">
                <span class="cost-label">Total Cost</span>
                <span class="cost-value" id="total_cost">₦0.00</span>
            </div>

            <button type="submit" class="btn-confirm">Confirm Order</button>
        </form>
    </div>
</div>

<script>
const servicePrices = <?= json_encode($prices) ?>;

function calculateCost() {
    const platform = document.getElementById('platform').value;
    const quantity = document.getElementById('quantity').value;
    const costDisplay = document.getElementById('total_cost');

    if (platform && quantity > 0) {
        const pricePerUnit = servicePrices[platform] / 1000;
        const total = (pricePerUnit * quantity).toFixed(2);
        costDisplay.innerText = "₦" + new Intl.NumberFormat().format(total);
    } else {
        costDisplay.innerText = "₦0.00";
    }
}
</script>

</body>

</html>

