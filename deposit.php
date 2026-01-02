<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fund Wallet | PlugSocial Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Lexend:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --accent: #3b82f6;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --success: #22c55e;
            --moniepoint: #003399; /* Moniepoint Corporate Blue */
            --moniepoint-gold: #ffcc00;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #1e293b 0%, #0f172a 100%);
            color: var(--text-main);
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .deposit-card {
            width: 100%;
            max-width: 450px;
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .bank-badge {
            background: var(--moniepoint);
            color: var(--moniepoint-gold);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            display: inline-block;
            margin-bottom: 20px;
            border: 1px solid var(--moniepoint-gold);
        }

        .bank-info-group {
            background: #0f172a;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .info-item { margin-bottom: 15px; }
        .info-item:last-child { margin-bottom: 0; }

        .label { color: var(--text-dim); font-size: 0.75rem; text-transform: uppercase; display: block; margin-bottom: 5px; }
        .value-wrapper { display: flex; justify-content: space-between; align-items: center; }
        .value { font-family: 'Lexend'; font-size: 1.1rem; font-weight: 700; color: #fff; }

        .copy-trigger {
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent);
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s;
        }
        .copy-trigger:hover { background: var(--accent); color: white; }

        .instruction-alert {
            background: rgba(255, 204, 0, 0.1);
            border: 1px solid var(--moniepoint-gold);
            color: #fff;
            padding: 15px;
            border-radius: 12px;
            font-size: 0.85rem;
            line-height: 1.4;
            margin-bottom: 25px;
        }

        .btn-done {
            width: 100%;
            padding: 16px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 15px;
        }

        .btn-whatsapp {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px;
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="deposit-card">
        <div style="text-align:center;">
            <div class="bank-badge">MONIEPOINT MICROFINANCE BANK</div>
            <h2 style="font-family: 'Lexend'; margin-bottom: 10px;">Fund Wallet</h2>
            <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 30px;">Transfer the exact amount to get credited.</p>
        </div>

        <div class="bank-info-group">
            <div class="info-item">
                <span class="label">Bank Name</span>
                <div class="value-wrapper">
                    <span class="value">Moniepoint MFB</span>
                </div>
            </div>
            <hr style="opacity: 0.05; margin: 15px 0;">
            <div class="info-item">
                <span class="label">Account Number</span>
                <div class="value-wrapper">
                    <span class="value" id="accNum">5128599550</span>
                    <div class="copy-trigger" onclick="copyToClipboard('5128599550', 'Account Number')">
                        <i class="fa-regular fa-copy"></i>
                    </div>
                </div>
            </div>
            <hr style="opacity: 0.05; margin: 15px 0;">
            <div class="info-item">
                <span class="label">Account Name</span>
                <div class="value-wrapper">
                    <span class="value">Cyriacus Charles Chima</span>
                    <div class="copy-trigger" onclick="copyToClipboard('Cyriacus Charles Chima', 'Account Name')">
                        <i class="fa-regular fa-copy"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="instruction-alert">
            <i class="fa-solid fa-circle-info" style="color: var(--moniepoint-gold);"></i>
            <b>Narration:</b> Please use your <b>Username</b> as the transfer description for faster approval.
        </div>

        <button class="btn-done" onclick="alert('Transaction submitted! Please send proof of payment to admin via WhatsApp.')">
            I Have Sent The Money
        </button>

        <a href="https://whatsapp.com/channel/0029VbBbGjcF1YIO0jTgDt3b" class="btn-whatsapp" target="_blank">
            <i class="fa-brands fa-whatsapp" style="color: #25D366;"></i> Send Proof to Admin
        </a>
    </div>

    <script>
        function copyToClipboard(text, type) {
            navigator.clipboard.writeText(text).then(() => {
                alert(type + ' copied to clipboard!');
            });
        }
    </script>

</body>
</html>