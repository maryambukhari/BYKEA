<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>redirect('login.php');</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pickup = $_POST['pickup'];
    $dropoff = $_POST['dropoff'];
    $details = $_POST['details'];
    $distance = rand(1, 50); // Simulated distance in km
    $price = $distance * 30; // PKR 30 per km for parcels

    $stmt = $pdo->prepare("INSERT INTO parcels (user_id, pickup_location, dropoff_location, package_details, distance, price) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$_SESSION['user_id'], $pickup, $dropoff, $details, $distance, $price])) {
        echo "<script>alert('Parcel booked successfully!'); redirect('track.php');</script>";
    } else {
        echo "<script>alert('Error booking parcel.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Parcel - Bykea Clone</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #4ecdc4, #ff6b6b);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            animation: slideIn 1s ease-in-out;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #ff6b6b;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #e55a5a;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @media (max-width: 768px) {
            .form-container { padding: 20px; }
            input, textarea, button { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Send a Parcel</h2>
        <form method="POST">
            <input type="text" name="pickup" placeholder="Pickup Location" required>
            <input type="text" name="dropoff" placeholder="Drop-off Location" required>
            <textarea name="details" placeholder="Package Details" required></textarea>
            <button type="submit">Book Parcel</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            <a href="#" onclick="redirect('index.php')">Back to Home</a>
        </p>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
