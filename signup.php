<?php
session_start();
include 'db_config.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // 1. Check if email or username already exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' OR username = '$username'");
    
    if(mysqli_num_rows($check) > 0) {
        $error = "Email or Username already registered!";
    } else {
        // 2. Handle Referrer (Optional)
        $ref = isset($_GET['ref']) ? mysqli_real_escape_string($conn, $_GET['ref']) : '';

        // 3. Insert User - Note: used 'password' and added 'role' and 'balance'
        // Ensure your table has these columns!
        $sql = "INSERT INTO users (username, email, password, role, balance) 
                VALUES ('$username', '$email', '$password_hash', 'user', '0.00')";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account | Vault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #0f172a; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; color: #f8fafc; padding: 20px 0; }
        .signup-card { background-color: #1e293b; border: 1px solid #334155; width: 100%; max-width: 480px; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        
        .brand-name { color: #3b82f6; font-size: 1.8rem; font-weight: 800; text-align: center; display: block; margin-bottom: 10px; }
        h2 { font-size: 1.5rem; text-align: center; margin: 0; color: white; }
        .subtitle { color: #94a3b8; font-size: 0.9rem; text-align: center; margin-bottom: 30px; }

        .form-group { margin-bottom: 18px; text-align: left; position: relative; }
        input, select { width: 100%; padding: 14px; background-color: #0f172a; border: 1px solid #334155; color: white; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; }
        input:focus { outline: none; border-color: #3b82f6; }

        /* Phone Input Layout */
        .phone-row { display: flex; gap: 10px; }
        .phone-row select { flex: 1.5; }
        .phone-row input { flex: 2; }

        /* Password Strength UI */
        .strength-meter { height: 4px; width: 100%; background: #334155; margin-top: 10px; border-radius: 2px; overflow: hidden; }
        .strength-bar { height: 100%; width: 0%; transition: 0.3s; }
        .strength-text { font-size: 0.8rem; margin-top: 5px; color: #94a3b8; }

        /* Checklist */
        .checklist { margin: 15px 0; padding: 0; list-style: none; }
        .checklist li { font-size: 0.8rem; color: #64748b; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }
        .checklist li.valid { color: #34d399; }
        .checklist li.valid i { color: #34d399; }

        .btn-signup { width: 100%; padding: 14px; background-color: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 20px; }
        .btn-signup:hover { background-color: #1d4ed8; }
        .footer-text { text-align: center; margin-top: 25px; font-size: 0.9rem; color: #94a3b8; }
        .footer-text a { color: #3b82f6; text-decoration: none; font-weight: 600; }

        .toggle-password { position: absolute; right: 15px; top: 45px; color: #64748b; cursor: pointer; z-index: 10; }
    </style>
</head>
<body>

<div class="signup-card">
    <span class="brand-name">Vault</span>
    <h2>Create an account</h2>
    <p class="subtitle">Enter your details below to get started</p>
<?php if($success): ?>
    <div style="background: rgba(52, 211, 153, 0.1); color: #34d399; padding: 10px; border-radius: 8px; border: 1px solid #34d399; margin-bottom: 20px; text-align: center;">
        <i class="fas fa-check-circle"></i> <?= $success; ?>
    </div>
<?php endif; ?>

    <form method="POST" id="signupForm">
        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="form-group">
            <input type="email" name="email" placeholder="Email address" required>
        </div>

        <div class="form-group">
            <div class="phone-row">
                <select name="country_code">
                    <option value="+234">+234 (Nigeria)</option>
                    <option value="+1">+1 (USA)</option>
                    <option value="+44">+44 (UK)</option>
                </select>
                <input type="text" name="phone" placeholder="Phone number" required>
            </div>
        </div>

        <div class="form-group">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
            
            <div class="strength-meter"><div id="meter-bar" class="strength-bar"></div></div>
            <div class="strength-text">Strength: <span id="strength-label">None</span></div>

            <ul class="checklist">
                <li id="char-limit"><i class="fas fa-check-circle"></i> At least 8 characters</li>
                <li id="upper-check"><i class="fas fa-check-circle"></i> One uppercase letter</li>
                <li id="number-check"><i class="fas fa-check-circle"></i> One number (0-9)</li>
            </ul>
        </div>

        <div class="form-group">
            <input type="text" name="ref" placeholder="Referred By (Optional)">
        </div>

        <form action="process_register.php" method="POST">
    <input type="hidden" name="referrer" value="<?= htmlspecialchars($referrer) ?>">
    
    <button type="submit">Register</button>
</form>
    </form>

    <div class="footer-text">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

<script>
const passwordInput = document.getElementById('password');
const meterBar = document.getElementById('meter-bar');
const strengthLabel = document.getElementById('strength-label');

passwordInput.addEventListener('input', function() {
    const val = passwordInput.value;
    let strength = 0;

    // Validation Logic
    const rules = {
        char: val.length >= 8,
        upper: /[A-Z]/.test(val),
        num: /[0-9]/.test(val)
    };

    document.getElementById('char-limit').className = rules.char ? 'valid' : '';
    document.getElementById('upper-check').className = rules.upper ? 'valid' : '';
    document.getElementById('number-check').className = rules.num ? 'valid' : '';

    if(rules.char) strength += 33;
    if(rules.upper) strength += 33;
    if(rules.num) strength += 34;

    meterBar.style.width = strength + "%";
    
    if(strength < 40) { meterBar.style.backgroundColor = "#ef4444"; strengthLabel.innerText = "Weak"; }
    else if(strength < 80) { meterBar.style.backgroundColor = "#f59e0b"; strengthLabel.innerText = "Medium"; }
    else { meterBar.style.backgroundColor = "#34d399"; strengthLabel.innerText = "Strong"; }
});

function togglePassword() {
    const icon = document.querySelector('.toggle-password');
    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye-slash');
}
</script>

</body>
</html>