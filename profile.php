<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>redirect('login.php');</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    echo "<script>alert('User not found.'); redirect('logout.php');</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Bykea Clone</title>
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
        .profile-container {
            max-width: 700px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            color: #333;
            animation: slideIn 1.2s ease-in-out;
            border: 2px solid #6b48ff;
        }
        h2 {
            font-family: 'Montserrat', sans-serif;
            text-align: center;
            font-size: 36px;
            color: #6b48ff;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .profile-card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .profile-card p {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }
        .profile-card p strong {
            color: #6b48ff;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(45deg, #6b48ff, #00ddeb);
            padding: 15px 30px;
            border-radius: 25px;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            margin: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn:hover {
            transform: scale(1.15);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .profile-container { padding: 20px; margin: 20px; }
            h2 { font-size: 28px; }
            .profile-card p { font-size: 16px; }
            .navbar a { font-size: 16px; margin: 0 15px; }
            .btn { padding: 12px 20px; font-size: 16px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="redirect('index.php')">Home</a>
        <a href="#" onclick="redirect('book_ride.php')">Book Ride</a>
        <a href="#" onclick="redirect('book_parcel.php')">Send Parcel</a>
        <a href="#" onclick="redirect('logout.php')">Logout</a>
    </div>
    <div class="profile-container">
        <h2>Your Profile</h2>
        <div class="profile-card">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><strong>Wallet Balance:</strong> PKR <?php echo number_format($user['wallet_balance'], 2); ?></p>
        </div>
        <div style="text-align: center;">
            <a href="#" onclick="redirect('book_ride.php')" class="btn">Book a Ride</a>
            <a href="#" onclick="redirect('book_parcel.php')" class="btn">Send a Parcel</a>
        </div>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
