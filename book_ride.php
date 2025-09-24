<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>redirect('login.php');</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pickup_location = trim($_POST['pickup_location']);
        $dropoff_location = trim($_POST['dropoff_location']);
        $vehicle_type = $_POST['vehicle_type'];

        // Validate inputs
        if (empty($pickup_location) || empty($dropoff_location) || empty($vehicle_type)) {
            echo "<script>alert('All fields are required.');</script>";
            exit;
        }

        // Validate vehicle type
        if (!in_array($vehicle_type, ['Bike', 'Car'])) {
            echo "<script>alert('Invalid vehicle type selected.');</script>";
            exit;
        }

        // Calculate distance and price (simplified logic)
        $distance = rand(1, 50); // Simulated distance in km
        $price = $vehicle_type === 'Bike' ? $distance * 10 : $distance * 20; // Simplified pricing

        // Check if vehicle_type column exists
        $columns = $pdo->query("SHOW COLUMNS FROM rides LIKE 'vehicle_type'")->fetchAll();
        if (count($columns) > 0) {
            // vehicle_type column exists
            $stmt = $pdo->prepare("INSERT INTO rides (user_id, pickup_location, dropoff_location, distance, price, vehicle_type, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $params = [$user_id, $pickup_location, $dropoff_location, $distance, $price, $vehicle_type];
        } else {
            // Fallback: exclude vehicle_type
            $stmt = $pdo->prepare("INSERT INTO rides (user_id, pickup_location, dropoff_location, distance, price, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $params = [$user_id, $pickup_location, $dropoff_location, $distance, $price];
        }

        if ($stmt->execute($params)) {
            echo "<script>alert('Ride booked successfully!'); redirect('track.php');</script>";
        } else {
            echo "<script>alert('Error booking ride.');</script>";
        }
    } catch (PDOException $e) {
        error_log("Ride booking error: " . $e->getMessage());
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Ride - Bykea Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Montserrat:wght=700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #6b48ff, #00ddeb);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow-x: hidden;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 400px;
            animation: slideIn 1.2s ease-in-out;
            border: 2px solid #6b48ff;
        }
        h2 {
            font-family: 'Montserrat', sans-serif;
            text-align: center;
            color: #6b48ff;
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #6b48ff;
            box-shadow: 0 0 8px rgba(107, 72, 255, 0.3);
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, #6b48ff, #00ddeb);
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        button:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .link a {
            color: #6b48ff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .link a:hover {
            color: #00ddeb;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @media (max-width: 768px) {
            .form-container { padding: 20px; max-width: 90%; }
            h2 { font-size: 24px; }
            input, select, button { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Book a Ride</h2>
        <form method="POST">
            <input type="text" name="pickup_location" placeholder="Pickup Location" required>
            <input type="text" name="dropoff_location" placeholder="Dropoff Location" required>
            <select name="vehicle_type" required>
                <option value="" disabled selected>Select Vehicle Type</option>
                <option value="Bike">Bike</option>
                <option value="Car">Car</option>
            </select>
            <button type="submit">Book Ride</button>
        </form>
        <p class="link"><a href="#" onclick="redirect('index.php')">Back to Home</a></p>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
