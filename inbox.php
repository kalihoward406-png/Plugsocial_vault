<?php
session_start();
include 'db_config.php';
$email = $_SESSION['user'];

$messages = mysqli_query($conn, "SELECT * FROM incoming_sms WHERE user_email = '$email' ORDER BY received_at DESC");
?>

<div class="inbox-container" style="background: #1e293b; padding: 20px; border-radius: 15px; color: white;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>📥 Your Verification Codes</h2>
        <span id="refresh-status" style="font-size: 0.8rem; color: #94a3b8;">Checking for codes...</span>
    </div>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="border-bottom: 1px solid #334155; color: #94a3b8; text-align: left;">
                <th style="padding: 10px;">From</th>
                <th style="padding: 10px;">Message/Code</th>
                <th style="padding: 10px;">Time</th>
            </tr>
        </thead>
        <tbody id="sms-table-body">
            </tbody>
    </table>
</div>

<script>
function refreshInbox() {
    const statusBox = document.getElementById('refresh-status');
    const tableBody = document.getElementById('sms-table-body');

    // Create AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_messages.php', true);

    xhr.onload = function() {
        if (this.status === 200) {
            tableBody.innerHTML = this.responseText;
            statusBox.innerText = "Last updated: " + new Date().toLocaleTimeString();
        }
    };

    xhr.send();
}

// 1. Refresh immediately on load
refreshInbox();

// 2. Refresh every 5 seconds (5000 milliseconds)
setInterval(refreshInbox, 5000);
</script>