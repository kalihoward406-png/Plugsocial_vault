<?php
// If on Vercel, use Environment Variables. If Local, use Localhost.
$host = getenv('DB_HOST') ?: "mysql-2e959cd2-kalihoward406-ce8b.l.aivencloud.com";
$user = getenv('DB_USER') ?: "avnadmin";
$pass = getenv('DB_PASS') ?: "AVNS_QI34Uepcxm_36SNtZTe";
$db   = getenv('DB_NAME') ?: "defaultdb";
$port = getenv('DB_PORT') ?: "18904";

$conn = mysqli_init();

// Aiven Cloud requires SSL. Localhost does not.
if ($host !== "localhost") {
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
}

$success = mysqli_real_connect($conn, $host, $user, $pass, $db, (int)$port);

if (!$success) {
    die("Connection failed: " . mysqli_connect_error());
}
?>