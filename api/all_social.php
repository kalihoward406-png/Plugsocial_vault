<?php
// Start session and include your database if needed
session_start();
include 'auth_session.php'; // <--- THIS ONE LINE FIXES THE LOGIN ISSUE
include 'db_config.php';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Social Services | PlugSocial Vault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; font-family: 'Inter', sans-serif; color: white; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 50px; }
        .services-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .service-card { background: #1e293b; padding: 25px; border-radius: 15px; text-align: center; border: 1px solid #334155; transition: 0.3s; }
        .service-card:hover { transform: translateY(-5px); border-color: #2563eb; }
        .service-card i { font-size: 3rem; margin-bottom: 15px; }
        .boost-btn { display: block; background: #2563eb; color: white; text-decoration: none; padding: 12px; border-radius: 8px; margin-top: 15px; font-weight: bold; }
        .back-btn { color: #94a3b8; text-decoration: none; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    
    <div class="header">
        <h1>All Boosting Services</h1>
        <p>Select a platform to increase your engagement</p>
    </div>

    <section class="services-grid">
        <div class="service-card">
            <i class="fa-brands fa-linkedin" style="color: #0077b5;"></i>
            <h3>LinkedIn</h3>
            <a href="social_order.php?p=linkedin" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-snapchat" style="color: #fffc00;"></i>
            <h3>Snapchat</h3>
            <a href="social_order.php?p=snapchat" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-reddit" style="color: #ff4500;"></i>
            <h3>Reddit</h3>
            <a href="social_order.php?p=reddit" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-pinterest" style="color: #e60023;"></i>
            <h3>Pinterest</h3>
            <a href="social_order.php?p=pinterest" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-discord" style="color: #5865F2;"></i>
            <h3>Discord</h3>
            <a href="social_order.php?p=discord" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-twitch" style="color: #9146ff;"></i>
            <h3>Twitch</h3>
            <a href="social_order.php?p=twitch" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-quora" style="color: #a82400;"></i>
            <h3>Quora</h3>
            <a href="social_order.php?p=quora" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-threads" style="color: #ffffff;"></i>
            <h3>Threads</h3>
            <a href="social_order.php?p=threads" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-spotify" style="color: #1DB954;"></i>
            <h3>Spotify</h3>
            <a href="social_order.php?p=spotify" class="boost-btn">Boost Now</a>
        </div>

        <div class="service-card">
            <i class="fa-brands fa-soundcloud" style="color: #ff3300;"></i>
            <h3>SoundCloud</h3>
            <a href="social_order.php?p=soundcloud" class="boost-btn">Boost Now</a>
        </div>
    </section>
</div>

</body>
</html>


