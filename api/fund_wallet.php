<?php
session_start();
include 'db_config.php';

// Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'] ?? 'user@example.com'; // Ensure you have email in session or fetch it

// Ensure the column 'type' exists in the DB before running this
$f_query = mysqli_query($conn, "SELECT * FROM transactions WHERE user_id = '$user_id' AND type LIKE 'Deposit%' ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fund Wallet | Vault</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
   <style>
    :root {
        --bg-dark: #0f172a;
        --bg-card: #1e293b;
        --primary: #2563eb;
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --border: #334155;
        --success: #22c55e;
    }

    body {
        background-color: var(--bg-dark);
        color: var(--text-main);
        font-family: 'Inter', sans-serif;
        margin: 0;
        display: flex;
        min-height: 100vh;
    }

    /* --- SIDEBAR (Reduced Width) --- */
    .sidebar {
        width: 200px; /* Reduced from 260px */
        background-color: var(--bg-card);
        border-right: 1px solid var(--border);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        padding: 20px;
        display: flex;
        flex-direction: column;
        z-index: 100; /* Ensures it stays on top if needed */
    }

    .brand { 
        color: var(--primary); 
        font-size: 1.6rem; /* Slightly smaller to fit */
        font-weight: 800; 
        margin-bottom: 40px; 
        display: block;
    }

    .nav-link { 
        color: var(--text-muted); 
        text-decoration: none; 
        padding: 12px; 
        display: flex; /* Aligns icon and text better */
        align-items: center;
        gap: 10px;
        margin-bottom: 5px; 
        border-radius: 8px; 
        font-size: 0.95rem;
    }
    
    .nav-link:hover, .nav-link.active { 
        background: rgba(37,99,235,0.1); 
        color: var(--primary); 
    }

    /* --- MAIN CONTENT (Adjusted Margin) --- */
    .main-content {
        margin-left: 220px; /* MATCHES SIDEBAR WIDTH EXACTLY */
        width: calc(100% - 220px); /* Prevents overflow */
        padding: 40px;
        box-sizing: border-box; /* Keeps padding inside width */
    }

    h2 { margin-top: 0; font-size: 1.8rem; }
    .subtitle { color: var(--text-muted); font-size: 0.95rem; margin-bottom: 30px; display: block; }

    /* Payment Method Card */
    .payment-method-card {
        background-color: var(--bg-card);
        border: 2px solid var(--success);
        border-radius: 12px;
        padding: 15px 25px;
        display: inline-flex;
        align-items: center;
        gap: 15px;
        position: relative;
        cursor: pointer;
        min-width: 180px;
    }

    .badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.badge-warning {
    background: rgba(234, 179, 8, 0.2);
    color: #eab308;
    border: 1px solid rgba(234, 179, 8, 0.3);
}

.badge-danger {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

    .paystack-logo { font-weight: 700; font-size: 1.1rem; color: white; letter-spacing: -0.5px; }

    /* Input & Button */
    .input-group { margin-top: 30px; max-width: 450px; } /* Constrained width */
    .label-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; }
    
    input[type="number"] {
        width: 100%;
        padding: 16px;
        background-color: var(--bg-dark);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: white;
        font-size: 1.1rem;
        box-sizing: border-box;
    }
    input:focus { outline: none; border-color: var(--primary); }

    .btn-pay {
        width: 100%;
        margin-top: 20px;
        padding: 16px;
        background-color: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-pay:hover { background-color: #1d4ed8; }

    /* History Table */
    .history-section { margin-top: 50px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { text-align: left; color: var(--text-muted); padding: 10px; font-size: 0.85rem; border-bottom: 1px solid var(--border); }
    td { padding: 15px 10px; font-size: 0.9rem; border-bottom: 1px solid #33415555; }

    /* Mobile Responsiveness */
    @media(max-width: 768px) { 
        .sidebar { display: none; } 
        .main-content { margin-left: 0; width: 100%; padding: 20px; } 
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
    <h2>Fund wallet</h2>
    <span class="subtitle">Choose a payment method to fund your wallet</span>

    <div class="payment-method-card">
        <div class="checkmark-badge"><i class="fas fa-check"></i></div>
        <i class="fas fa-layer-group" style="color: #0BA4DB; font-size: 1.5rem;"></i> <span class="paystack-logo">paystack</span>
    </div>

    <form id="paymentForm" class="input-group">
        <div class="label-row">
            <span style="color:var(--text-muted)">Enter amount</span>
            <span style="color:var(--text-muted)">Min is ₦500</span>
        </div>
        <input type="number" id="amount" placeholder="1000" min="500" required>
        
        <button type="submit" class="btn-pay" onclick="payWithPaystack(event)">Pay Now</button>
    </form>

    <div class="recent-funding">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="color: var(--text-muted); border-bottom: 1px solid var(--border);">
                <th style="padding: 12px; text-align: left;">Method</th>
                <th style="padding: 12px; text-align: left;">Amount</th>
                <th style="padding: 12px; text-align: left;">Status</th>
                <th style="padding: 12px; text-align: left;">Date</th>
            </tr>
        </thead>
     <tbody>
    <?php
    $user_id = $_SESSION['user_id'];
    $f_query = mysqli_query($conn, "SELECT * FROM transactions WHERE user_id = '$user_id' AND type LIKE 'Deposit%' ORDER BY id DESC LIMIT 5");

    if (mysqli_num_rows($f_query) > 0) {
        while ($row = mysqli_fetch_assoc($f_query)) {
            // Determine the badge class
            $status = $row['status'];
            $badge_class = 'badge-warning'; // Default (Pending)
            
            if ($status == 'Completed' || $status == 'Success') {
                $badge_class = 'badge-success';
            } elseif ($status == 'Failed' || $status == 'Declined') {
                $badge_class = 'badge-danger';
            }

            echo "<tr style='border-bottom: 1px solid rgba(255,255,255,0.05);'>
                    <td style='padding: 15px;'>".htmlspecialchars($row['type'])."</td>
                    <td style='padding: 15px; font-weight: bold;'>₦".number_format($row['amount'], 2)."</td>
                    <td style='padding: 15px;'>
                        <span class='badge $badge_class'>$status</span>
                    </td>
                    <td style='padding: 15px; color: #94a3b8; font-size: 0.85rem;'>".date('M d, Y', strtotime($row['created_at']))."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4' style='padding: 40px; text-align: center; color: #64748b;'>No recent funding attempts found.</td></tr>";
    }
    ?>
</tbody>
    </table>
</div>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    function payWithPaystack(e) {
        e.preventDefault();
        
        const amount = document.getElementById("amount").value;
        if(amount < 500) {
            alert("Minimum deposit is ₦500");
            return;
        }

        const handler = PaystackPop.setup({
            key: 'pk_test_f5381a2da5d97877200c11252d1a79ac026a254f', // <--- REPLACE THIS WITH YOUR PAYSTACK PUBLIC KEY
            email: '<?php echo $user_email; ?>',
            amount: amount * 100, // Paystack expects amount in kobo
            currency: 'NGN',
            callback: function(response) {
    // This sends the user to the verification script we just made
    window.location.href = "verify_payment.php?reference=" + response.reference;
},
            onClose: function(){
                alert('Transaction was not completed, window closed.');
            }
        });
        handler.openIframe();
    }
</script>

</body>

</html>
