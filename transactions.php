<?php
session_start();
include 'db_config.php';

// Professional Login Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user balance professionally to avoid TypeErrors
$query = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($query);
$display_balance = (float)str_replace(',', '', ($user_data['balance'] ?? 0));

// Fetch all transactions for this user
$sql = "SELECT method, amount, status, reference, created_at 
        FROM transactions 
        WHERE user_id = '$user_id' 
        ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History | Vault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --primary: #2563eb;
            --text-muted: #94a3b8;
            --border: #334155;
            --success: #22c55e;
            --warning: #fbbf24;
            --danger: #ef4444;
        }

        body { background-color: var(--bg-dark); color: white; font-family: 'Inter', sans-serif; margin: 0; display: flex; }
        
        .sidebar { width: 220px; background: var(--bg-card); height: 100vh; position: fixed; padding: 20px; border-right: 1px solid var(--border); }
        .sidebar .brand { color: var(--primary); font-size: 1.8rem; font-weight: 800; margin-bottom: 30px; display: block; text-decoration: none; }
        .nav-link { color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 8px; margin-bottom: 5px; }
        .nav-link.active { background: rgba(37, 99, 235, 0.1); color: var(--primary); }

        .main-content { margin-left: 220px; flex: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: