<?php
session_start();
include 'db_config.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Fetch User Balance
$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($query);

// Ensure balance is a float for number_format() to avoid TypeError
$balance = (float)($user_data['balance'] ?? 0);

// 3. Define Unique Countries
$countries = [
    "Popular" => [
        ['name' => 'United States', 'code' => 'US', 'flag' => '🇺🇸'],
        ['name' => 'United Kingdom', 'code' => 'GB', 'flag' => '🇬🇧'],
        ['name' => 'Nigeria', 'code' => 'NG', 'flag' => '🇳🇬'],
        ['name' => 'Canada', 'code' => 'CA', 'flag' => '🇨🇦'],
        ['name' => 'Germany', 'code' => 'DE', 'flag' => '🇩🇪'],
        ['name' => 'Russia', 'code' => 'RU', 'flag' => '🇷🇺'],
    ],
    "Africa" => [
        ['name' => 'South Africa', 'code' => 'ZA', 'flag' => '🇿🇦'],
        ['name' => 'Egypt', 'code' => 'EG', 'flag' => '🇪🇬'],
        ['name' => 'Kenya', 'code' => 'KE', 'flag' => '🇰🇪'],
        ['name' => 'Ghana', 'code' => 'GH', 'flag' => '🇬🇭'],
        ['name' => 'Morocco', 'code' => 'MA', 'flag' => '🇲🇦'],
        ['name' => 'Ethiopia', 'code' => 'ET', 'flag' => '🇪🇹'],
        ['name' => 'Algeria', 'code' => 'DZ', 'flag' => '🇩🇿'],
        ['name' => 'Uganda', 'code' => 'UG', 'flag' => '🇺🇬'],
        ['name' => 'Senegal', 'code' => 'SN', 'flag' => '🇸🇳'],
        ['name' => 'Tunisia', 'code' => 'TN', 'flag' => '🇹🇳'],
    ],
    "Europe" => [
        ['name' => 'France', 'code' => 'FR', 'flag' => '🇫🇷'],
        ['name' => 'Netherlands', 'code' => 'NL', 'flag' => '🇳🇱'],
        ['name' => 'Poland', 'code' => 'PL', 'flag' => '🇵🇱'],
        ['name' => 'Italy', 'code' => 'IT', 'flag' => '🇮🇹'],
        ['name' => 'Spain', 'code' => 'ES', 'flag' => '🇪🇸'],
        ['name' => 'Sweden', 'code' => 'SE', 'flag' => '🇸🇪'],
        ['name' => 'Ukraine', 'code' => 'UA', 'flag' => '🇺🇦'],
        ['name' => 'Romania', 'code' => 'RO', 'flag' => '🇷🇴'],
        ['name' => 'Portugal', 'code' => 'PT', 'flag' => '🇵🇹'],
        ['name' => 'Greece', 'code' => 'GR', 'flag' => '🇬🇷'],
        ['name' => 'Belgium', 'code' => 'BE', 'flag' => '🇧🇪'],
        ['name' => 'Switzerland', 'code' => 'CH', 'flag' => '🇨🇭'],
    ],
    "Asia & Middle East" => [
        ['name' => 'China', 'code' => 'CN', 'flag' => '🇨🇳'],
        ['name' => 'India', 'code' => 'IN', 'flag' => '🇮🇳'],
        ['name' => 'Japan', 'code' => 'JP', 'flag' => '🇯🇵'],
        ['name' => 'South Korea', 'code' => 'KR', 'flag' => '🇰🇷'],
        ['name' => 'Indonesia', 'code' => 'ID', 'flag' => '🇮🇩'],
        ['name' => 'Vietnam', 'code' => 'VN', 'flag' => '🇻🇳'],
        ['name' => 'Thailand', 'code' => 'TH', 'flag' => '🇹🇭'],
        ['name' => 'Turkey', 'code' => 'TR', 'flag' => '🇹🇷'],
        ['name' => 'Saudi Arabia', 'code' => 'SA', 'flag' => '🇸🇦'],
        ['name' => 'UAE', 'code' => 'AE', 'flag' => '🇦🇪'],
        ['name' => 'Israel', 'code' => 'IL', 'flag' => '🇮🇱'],
        ['name' => 'Pakistan', 'code' => 'PK', 'flag' => '🇵🇰'],
        ['name' => 'Philippines', 'code' => 'PH', 'flag' => '🇵🇭'],
        ['name' => 'Malaysia', 'code' => 'MY', 'flag' => '🇲🇾'],
    ],
    "Americas" => [
        ['name' => 'Brazil', 'code' => 'BR', 'flag' => '🇧🇷'],
        ['name' => 'Mexico', 'code' => 'MX', 'flag' => '🇲🇽'],
        ['name' => 'Argentina', 'code' => 'AR', 'flag' => '🇦🇷'],
        ['name' => 'Colombia', 'code' => 'CO', 'flag' => '🇨🇴'],
        ['name' => 'Chile', 'code' => 'CL', 'flag' => '🇨🇱'],
        ['name' => 'Peru', 'code' => 'PE', 'flag' => '🇵🇪'],
    ]
];

// 4. Define Unique Services
$services = [
    ['name' => 'WhatsApp', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'Telegram', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'OpenAI / ChatGPT', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'Google / Gmail', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Instagram', 'price' => 1500, 'demand' => 'Low'],
    ['name' => 'Facebook', 'price' => 1500, 'demand' => 'Low'],
    ['name' => 'TikTok', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Twitter (X)', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Amazon', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Netflix', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Apple', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Microsoft', 'price' => 1500, 'demand' => 'Low'],
    ['name' => 'Snapchat', 'price' => 1500, 'demand' => 'Low'],
    ['name' => 'LinkedIn', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Discord', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Spotify', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Uber', 'price' => 1500, 'demand' => 'Low'],
    ['name' => 'Bolt', 'price' => 1500, 'demand' => 'Low'],
    ['name' => 'Airbnb', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Binance', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'Bybit', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'KuCoin', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'PayPal', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Stripe', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Tinder', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'Bumble', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Hinge', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Claude AI', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'Gemini AI', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'AliExpress', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'eBay', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Alibaba', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Steam', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'PlayStation', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Xbox', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Twitch', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Yahoo', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Outlook', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Pinterest', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Reddit', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Quora', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Viber', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'WeChat', 'price' => 3000, 'demand' => 'High'],
    ['name' => 'Line', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Signal', 'price' => 2000, 'demand' => 'Medium'],
    ['name' => 'Zoho', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Skrill', 'price' => 1500, 'demand' => 'Low'],
    ['name' => 'Neteller', 'price' => 1500, 'demand' => 'Low'],
    ['name' => 'Wisdom', 'price' => 1000, 'demand' => 'Low'],
    ['name' => 'Other/Global', 'price' => 2000, 'demand' => 'Medium'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receive SMS | Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-dark: #0f172a; --bg-card: #1e293b; --primary: #2563eb; --border: #334155; --text-muted: #94a3b8; }
        body { background: var(--bg-dark); color: white; font-family: 'Inter', sans-serif; margin: 0; display: flex; }
        .sidebar { width: 220px; background: var(--bg-card); height: 100vh; position: fixed; padding: 20px; border-right: 1px solid var(--border); }
        .brand { color: var(--primary); font-size: 1.8rem; font-weight: 800; display: block; margin-bottom: 30px; text-decoration: none; }
        .nav-link { color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 8px; margin-bottom: 5px; }
        .nav-link.active { background: rgba(37, 99, 235, 0.1); color: var(--primary); }
        .main-content { margin-left: 260px; flex: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .balance-badge { background: #152033; padding: 10px 20px; border-radius: 30px; border: 1px solid var(--border); color: #22c55e; font-weight: 700; }
        .order-card { background: var(--bg-card); border-radius: 12px; padding: 30px; border: 1px solid var(--border); max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 8px; font-weight: 600; }
        .price-display { width: 95%; padding: 15px; background: #0f172a; border: 1px solid var(--border); border-radius: 8px; color: #22c55e; font-weight: 700; font-size: 1.2rem; }
        .btn-buy { width: 100%; padding: 16px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; }
        
        /* Select2 Custom Dark Styles */
        .select2-container--default .select2-selection--single { background: #0f172a; border: 1px solid var(--border); height: 45px; color: white; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { color: white; line-height: 45px; padding-left: 15px; }
        .select2-dropdown { background: #1e293b; color: white; border: 1px solid var(--border); }
        .select2-results__option--highlighted { background-color: var(--primary) !important; }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="dashboard.php" class="brand">Vault</a>
    <a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
    <a href="receive_sms.php" class="nav-link active"><i class="fas fa-envelope"></i> Receive SMS</a>
    <a href="fund_wallet.php" class="nav-link"><i class="fas fa-wallet"></i> Fund Wallet</a>
</div>

<div class="main-content">
    <div class="header">
        <h2>Receive SMS</h2>
        <div class="balance-badge">Balance: ₦<?= number_format($balance, 2) ?></div>
    </div>

    <div class="order-card">
        <form action="process_sms.php" method="POST">
            <div class="form-group">
                <label>Country</label>
                <select name="country" class="searchable-select" style="width: 100%;" required>
                    <option value="" disabled selected>Select country...</option>
                    <?php foreach($countries as $region => $list): ?>
                        <optgroup label="<?= $region ?>">
                            <?php foreach($list as $c): ?>
                                <option value="<?= $c['code'] ?>"><?= $c['flag'] ?> <?= $c['name'] ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Service</label>
                <select name="service" id="serviceSelect" class="searchable-select" style="width: 100%;" required>
                    <option value="" disabled selected>Select service...</option>
                    <?php foreach($services as $s): ?>
                        <option value="<?= $s['name'] ?>" data-price="<?= $s['price'] ?>"><?= $s['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Price</label>
                <div class="price-display">₦ <span id="priceValue">0.00</span></div>
            </div>

            <button type="submit" class="btn-buy">Purchase Number</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.searchable-select').select2();
        
        $('#serviceSelect').on('change', function() {
            let price = $(this).find(':selected').data('price');
            $('#priceValue').text(parseFloat(price || 0).toLocaleString());
        });
    });
</script>

</body>
</html>