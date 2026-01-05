<?php
// 1. SESSION CONFIGURATION (MUST BE FIRST)
ini_set('display_errors', 1);
ini_set('session.save_path', '/tmp');
session_set_cookie_params([
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// 2. START SESSION (ONLY ONCE)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 3. CHECK LOGIN (BEFORE ANY HTML IS SHOWN)
if (!isset($_SESSION['user_id'])) {
    // If not logged in, force redirect using JavaScript
    echo "<script>window.location.href='/login';</script>";
    exit();
}

// 4. INCLUDE DB & FETCH DATA
require 'db_config.php';
$user_id = $_SESSION['user_id'];

// Fetch latest data from DB to ensure balance is accurate
$stmt = $conn->prepare("SELECT username, email, balance, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fallback values
$username = $user['username'] ?? 'User';
$clean_balance = (float)str_replace(',', '', $user['balance'] ?? 0);
$user_role = $user['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Vault</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #0b0f19;
            --bg-card: #1e293b;
            --primary: #3b82f6;
            --accent: #22c55e;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --sidebar-width: 250px;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* --- LAYOUT WRAPPER --- */
        .dashboard-wrapper { display: flex; min-height: 100vh; }

        /* --- SIDEBAR --- */
        #sidebar {
            width: var(--sidebar-width);
            background-color: #111827;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: fixed;
            height: 100vh;
            transition: 0.3s ease;
            z-index: 1000;
        }

        #sidebar.hidden { margin-left: calc(-1 * var(--sidebar-width)); }

        .brand {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 40px;
            text-align: center;
        }

        .nav-link {
            display: flex;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            font-weight: 500;
            transition: 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary);
        }

        .nav-link i { width: 25px; margin-right: 10px; }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 30px;
            transition: 0.3s ease;
            width: 100%;
        }

        .main-content.expanded { margin-left: 0; }

        /* --- HEADER / TOP BAR --- */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .bar-left { display: flex; align-items: center; gap: 20px; }

        .welcome-text {
            color: var(--accent);
            font-weight: 800;
            font-size: 1.6rem;
            margin: 0;
        }

        .bar-right { display: flex; align-items: center; gap: 15px; }

        /* Buttons & Icons */
        .icon-btn {
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: white;
            padding: 10px;
            border-radius: 8px;
            position: relative;
            text-decoration: none;
            cursor: pointer;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 50%;
        }

        .btn-logout {
            background: #ef4444;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        /* --- GRID SYSTEM --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 25px;
        }

        .card-title { color: var(--text-muted); font-size: 0.9rem; display: block; margin-bottom: 10px; }
        .card-value { font-size: 2.2rem; font-weight: 700; display: block; margin-bottom: 20px; }
        
        .btn-card {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            background: var(--primary);
            color: white;
            transition: 0.2s;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* WhatsApp Button */
        .btn-whatsapp {
            display: inline-block;
            margin-top: 10px;
            background: #22c55e;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-whatsapp:hover { background: #16a34a; }

        /* Referral Card */
        .referral-card {
            text-align: center;
            cursor: pointer;
            border: 1px dashed #3b82f6;
            transition: transform 0.2s;
        }
        .referral-card:hover {
            transform: translateY(-5px);
            background: rgba(59, 130, 246, 0.05);
        }
        .copy-badge { margin-top: 10px; font-size: 0.75rem; color: #3b82f6; font-weight: 600; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; }
            #sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            #sidebar.active { margin-left: 0; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <aside id="sidebar">
        <div class="brand">Vault</div>
        <nav>
            <a href="/dashboard" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="social_order.php" class="nav-link"><i class="fas fa-rocket"></i> Boost Account</a>
            <a href="receive_sms.php" class="nav-link"><i class="fas fa-envelope"></i> Receive SMS</a>
            <a href="fund_wallet.php" class="nav-link"><i class="fas fa-wallet"></i> Fund Wallet</a>
            <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
        </nav>
    </aside>

    <main class="main-content" id="main-content">
        <header class="top-bar">
            <div class="bar-left">
                <button class="icon-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <h2 class="welcome-text">Welcome, <?php echo htmlspecialchars($username); ?></h2>
            </div>
            
            <div class="bar-right">
                <a href="messages.php" class="icon-btn">
                    <i class="fas fa-envelope"></i>
                    <?php
                    // Check messages safely
                    if(isset($conn)){
                        $m_q = mysqli_query($conn, "SELECT COUNT(*) as t FROM broadcasts");
                        if($m_q){
                            $m_d = mysqli_fetch_assoc($m_q);
                            if ($m_d['t'] > 0) echo '<span class="badge">'.$m_d['t'].'</span>';
                        }
                    }
                    ?>
                </a>
                <a href="logout.php" class="btn-logout">Logout</a>
                <?php if ($user_role === 'admin'): ?>
                    <a href="admin_dashboard.php" class="icon-btn" style="background:#3b82f6;">Admin</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="stats-grid">
            <div class="card">
                <span class="card-title">Current Balance</span>
                <span class="card-value">₦<?php echo number_format($clean_balance, 2); ?></span>
                <a href="fund_wallet.php" class="btn-card">+ Fund Wallet</a>
            </div>
            <div class="card">
                <span class="card-title">Purchased Numbers</span>
                <span class="card-value">0</span>
                <a href="receive_sms.php" class="btn-card">Receive SMS →</a>
            </div>
            <div class="card">
                <span class="card-title">Rented Numbers</span>
                <span class="card-value">0</span>
                <a href="#" class="btn-card" style="background:transparent; border:1px solid var(--border);">Rent Number →</a>
            </div>
        </div>

        <div class="info-grid">
            <div class="card referral-card" onclick="copyReferralLink()">
                <i class="fas fa-link" style="font-size: 2rem; color: #3b82f6; margin-bottom: 15px;"></i>
                <h3>Referral Link</h3>
                <p style="color: #94a3b8; font-size: 0.9rem;">Earn 5% on every deposit your referrals make.</p>
                <input type="text" value="https://yourwebsite.com/signup.php?ref=<?php echo $username; ?>" id="refLink" style="display:none;">
                <div class="copy-badge">Click to copy</div>
            </div>

            <div class="card whatsapp-card">
                <i class="fab fa-whatsapp" style="font-size: 2.5rem; color: #22c55e; margin-bottom: 15px;"></i>
                <h3>Join WhatsApp Group</h3>
                <p style="color: #94a3b8; font-size: 0.9rem;">Join our community for the latest updates and support.</p>
                <a href="https://chat.whatsapp.com/li41dwblAKC7yfaa93Jcm7?mode=wwt" target="_blank" class="btn-whatsapp">Join Group &rarr;</a>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top:0;">Recent Transactions</h3>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:left;">
                    <tr style="color:var(--text-muted); border-bottom:1px solid var(--border);">
                        <th style="padding:15px 10px;">Type</th>
                        <th style="padding:15px 10px;">Amount</th>
                        <th style="padding:15px 10px;">Status</th>
                        <th style="padding:15px 10px;">Date</th>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding:40px; text-align:center; color:var(--text-muted);">
                            <i class="fas fa-folder-open" style="display:block; font-size:2rem; margin-bottom:10px;"></i>
                            No transactions found.
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
// Fixed Sidebar Toggle Function
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    sidebar.classList.toggle('hidden');
    mainContent.classList.toggle('expanded');
    // For mobile
    sidebar.classList.toggle('active');
}

function copyReferralLink() {
    var copyText = document.getElementById("refLink");
    
    // Create a temporary element to perform the copy
    var tempInput = document.createElement("input");
    tempInput.value = copyText.value;
    document.body.appendChild(tempInput);
    
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // Mobile support
    document.execCommand("copy");
    
    document.body.removeChild(tempInput);
    
    alert("Referral link copied to clipboard!");
}
</script>

</body>
</html>
