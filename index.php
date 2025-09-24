<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bykea Clone - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #6b48ff, #00ddeb);
            color: #fff;
            overflow-x: hidden;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.85);
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .navbar a {
            color: #fff;
            margin: 0 25px;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .navbar a:hover {
            color: #00ddeb;
            transform: scale(1.1);
        }
        .hero {
            text-align: center;
            padding: 100px 20px;
            animation: fadeIn 2s ease-in-out;
        }
        .hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        }
        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
        }
        .btn {
            background: linear-gradient(45deg, #6b48ff, #00ddeb);
            padding: 15px 30px;
            border-radius: 25px;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn:hover {
            transform: scale(1.15);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 32px; }
            .hero p { font-size: 16px; }
            .navbar a { font-size: 16px; margin: 0 15px; }
            .btn { padding: 12px 20px; font-size: 16px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="#" onclick="redirect('book_ride.php')">Book Ride</a>
        <a href="#" onclick="redirect('book_parcel.php')">Send Parcel</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="#" onclick="redirect('profile.php')">Profile</a>
            <a href="#" onclick="redirect('logout.php')">Logout</a>
        <?php elseif (isset($_SESSION['driver_id'])): ?>
            <a href="#" onclick="redirect('driver_dashboard.php')">Driver Dashboard</a>
            <a href="#" onclick="redirect('logout.php')">Logout</a>
        <?php else: ?>
            <a href="#" onclick="redirect('login.php')">User Login</a>
            <a href="#" onclick="redirect('driver_login.php')">Driver Login</a>
            <a href="#" onclick="redirect('signup.php')">User Signup</a>
            <a href="#" onclick="redirect('driver_signup.php')">Become a Driver</a>
        <?php endif; ?>
    </div>
    <div class="hero">
        <h1>Welcome to Bykea Clone</h1>
        <p>Ride or send parcels with ease, anytime, anywhere!</p>
        <a href="#" onclick="redirect('book_ride.php')" class="btn">Book a Ride</a>
        <a href="#" onclick="redirect('book_parcel.php')" class="btn">Send a Parcel</a>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
