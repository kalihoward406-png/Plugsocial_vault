<?php
include 'db_config.php';

$to_number = $_POST['To'] ?? '';
$body = $_POST['Body'] ?? '';

if ($to_number && $body) {
    $stmt = $conn->prepare("UPDATE vault_sessions SET sms_content = ?, status = 'received' WHERE phone_number = ? AND status = 'waiting' ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("ss", $body, $to_number);
    $stmt->execute();
}

header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?><Response></Response>";
?>