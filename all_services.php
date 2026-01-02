<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Services - PlugSocial Vault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
.get-btn {
    display: block;
    background: var(--primary);
    color: white;
    text-decoration: none;
    padding: 10px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 0.9rem;
    transition: background 0.2s;
    margin-top: auto; /* Pushes button to bottom of card */
}

.get-btn:hover {
    background: #1d4ed8;
    color: white;
}

.service-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    /* ... keep your existing card styles ... */
}
        :root { --bg: #0f172a; --card: #1e293b; --primary: #2563eb; --text: #f8fafc; --dim: #94a3b8; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { text-align: center; padding: 40px 0; }
        .header h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .header p { color: var(--dim); }
        
        .service-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; margin-top: 30px; }
        .service-card { 
            background: var(--card); border: 1px solid #334155; padding: 25px; 
            border-radius: 16px; text-align: center; text-decoration: none; 
            color: white; transition: all 0.3s ease; display: block;
        }
        .service-card:hover { border-color: var(--primary); transform: translateY(-5px); background: #26334d; }
        .service-card i { font-size: 2.5rem; margin-bottom: 15px; color: var(--primary); }
        .service-card h3 { margin: 10px 0 5px 0; font-size: 1.1rem; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: var(--dim); text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    
    <div class="header">
        <h1>All Services</h1>
        <p>Select a platform to generate your verification number</p>
    </div>

 <div class="service-grid">
    <?php
    // Array of services for easy management
    $all_services = [
        ['id' => 'facebook', 'name' => 'Facebook', 'icon' => 'fa-brands fa-facebook'],
        ['id' => 'instagram', 'name' => 'Instagram', 'icon' => 'fa-brands fa-instagram'],
        ['id' => 'x', 'name' => 'X (Twitter)', 'icon' => 'fa-brands fa-x-twitter'],
        ['id' => 'tiktok', 'name' => 'TikTok', 'icon' => 'fa-brands fa-tiktok'],
        ['id' => 'snapchat', 'name' => 'Snapchat', 'icon' => 'fa-brands fa-snapchat'],
        ['id' => 'discord', 'name' => 'Discord', 'icon' => 'fa-brands fa-discord'],
        ['id' => 'signal', 'name' => 'Signal', 'icon' => 'fa-solid fa-comment-sms'],
        ['id' => 'outlook', 'name' => 'Microsoft', 'icon' => 'fa-solid fa-envelope'],
        ['id' => 'yahoo', 'name' => 'Yahoo', 'icon' => 'fa-brands fa-yahoo'],
        ['id' => 'protonmail', 'name' => 'ProtonMail', 'icon' => 'fa-solid fa-shield-halved'],
        ['id' => 'amazon', 'name' => 'Amazon', 'icon' => 'fa-brands fa-amazon'],
        ['id' => 'ebay', 'name' => 'eBay', 'icon' => 'fa-brands fa-ebay'],
        ['id' => 'airbnb', 'name' => 'Airbnb', 'icon' => 'fa-brands fa-airbnb'],
        ['id' => 'uber', 'name' => 'Uber / Eats', 'icon' => 'fa-brands fa-uber'],
        ['id' => 'tinder', 'name' => 'Tinder', 'icon' => 'fa-solid fa-fire'],
        ['id' => 'idme', 'name' => 'ID.me', 'icon' => 'fa-solid fa-id-card'],
        // Add any other services here...
    ];

    foreach ($all_services as $s): ?>
        <div class="service-card">
            <i class="<?php echo $s['icon']; ?>"></i>
            <h3><?php echo $s['name']; ?></h3>
            <p style="color: var(--dim); font-size: 0.8rem; margin-bottom: 15px;">Global Verifications</p>
            
            <a href="services.php?type=<?php echo $s['id']; ?>" class="get-btn">
                Get Number
            </a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>