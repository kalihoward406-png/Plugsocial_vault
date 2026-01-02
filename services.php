<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit();
}

// Get the service name from the URL (e.g., 'tinder')
$service = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'Service';
$email = $_SESSION['user'];

// Fetch current balance to show the user
$query = mysqli_query($conn, "SELECT balance FROM users WHERE email = '$email'");
$user = mysqli_fetch_assoc($query);
$currentBalance = $user['balance'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Country for <?php echo ucfirst($service); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: white; padding: 40px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #334155; padding-bottom: 20px; }
        .balance-pill { background: #1e293b; padding: 8px 15px; border-radius: 20px; border: 1px solid #334155; color: #22c55e; font-weight: bold; }
        
        .country-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .country-card { background: #1e293b; border: 1px solid #334155; padding: 20px; border-radius: 12px; transition: 0.3s; cursor: pointer; }
        .country-card:hover { border-color: #2563eb; background: #26334d; }
        .country-name { display: block; font-weight: bold; font-size: 1.1rem; margin-bottom: 5px; }
        .country-price { color: #94a3b8; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <a href="all_services.php" style="color:#94a3b8; text-decoration:none; font-size:0.9rem;">← Back</a>
            <h1 style="margin:5px 0;"><?php echo ucfirst($service); ?> Numbers</h1>
        </div>
        <div class="balance-pill">
            Balance: ₦<?php echo number_format($currentBalance, 2); ?>
        </div>
    </div>

   <div class="country-grid">
    <?php
    $countries = [
        "Antigua and Barbuda", "Argentina", "Australia", "Austria", "Bahrain", "Barbados", 
        "Belgium", "Bermuda", "Brazil", "British Virgin Islands", "Bulgaria", "Canada", 
        "Chile", "Costa Rica", "Croatia", "Cyprus", "Czech Republic", "Denmark", 
        "Dominican Republic", "El Salvador", "Estonia", "Finland", "France", "Germany", 
        "Greece", "Guatemala", "Hong Kong", "Hungary", "Malta", "Mexico", "Montserrat", 
        "Netherlands", "New Zealand", "Nigeria", "Norway", "Peru", "Philippines", 
        "Poland", "Portugal", "Puerto Rico", "Singapore", "Slovenia", "South Africa", 
        "South Korea", "Spain", "Sweden", "Switzerland", "Taiwan", "Thailand", 
        "Turks and Caicos", "United Kingdom", "United States", "U.S. Virgin Islands", "Vietnam"
    ];

    // Assuming a flat rate for this example, or you can map specific prices
    $price = 3500; 

    foreach ($countries as $country): ?>
        <div class="country-card" style="display: flex; justify-content: space-between; align-items: center; background: #1e293b; padding: 15px; margin-bottom: 10px; border-radius: 10px; border: 1px solid #334155;">
            <div>
                <span style="font-weight: bold; font-size: 1.1rem;"><?php echo $country; ?></span>
                <div style="color: #22c55e; font-size: 0.9rem;">Price: ₦<?php echo number_format($price); ?></div>
            </div>
            
            <button onclick="handlePurchase('<?php echo $country; ?>', <?php echo $price; ?>)" 
                    style="background: #2563eb; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                Get Number
            </button>
        </div>
    <?php endforeach; ?>
</div>
</div>

<script>
function handlePurchase(country, price) {
    const balance = <?php echo $currentBalance; ?>;
    const serviceName = "<?php echo $service; ?>";

    if (balance >= price) {
        if (confirm(`Confirm purchase of ${country} number for ${serviceName}? ₦${price} will be deducted.`)) {
            // Redirect to the processor that subtracts money from database
            window.location.href = `process_purchase.php?item=${serviceName}_${country}&price=${price}`;
        }
    } else {
        alert("Insufficient Balance! Please top up your vault first.");
        // Optional: redirect to deposit
        if(confirm("Would you like to deposit now?")) {
            window.location.href = "index.php"; // Or wherever your deposit trigger is
        }
    }
}
</script>

</body>
</html>