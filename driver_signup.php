<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = trim($_POST['name']);
        $email = trim(strtolower($_POST['email'])); // Normalize email to lowercase
        $password = trim($_POST['password']);
        $phone = trim($_POST['phone']);
        $vehicle_type = $_POST['vehicle_type'];

        // Validate inputs
        if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($vehicle_type)) {
            echo "<script>alert('All fields are required.');</script>";
            exit;
        }

        // Validate vehicle type
        if (!in_array($vehicle_type, ['Bike', 'Car'])) {
            echo "<script>alert('Invalid vehicle type selected.');</script>";
            exit;
        }

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM drivers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            echo "<script>alert('Email already registered. Please log in.'); redirect('driver_login.php');</script>";
            exit;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if (!$hashed_password) {
            echo "<script>alert('Error hashing password.');</script>";
            exit;
        }

        // Insert driver
        $stmt = $pdo->prepare("INSERT INTO drivers (name, email, password, phone, vehicle_type) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $hashed_password, $phone, $vehicle_type])) {
            echo "<script>alert('Driver registration successful! Please log in.'); redirect('driver_login.php');</script>";
        } else {
            echo "<script>alert('Error during registration.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Signup - Bykea Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Montserrat:wght@700&display=swap" rel="stylesheet">
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
        <h2>Driver Sign Up</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <select name="vehicle_type" required>
                <option value="" disabled selected>Select Vehicle Type</option>
                <option value="Bike">Bike</option>
                <option value="Car">Car</option>
            </select>
            <button type="submit">Sign Up</button>
        </form>
        <p class="link">Already a driver? <a href="#" onclick="redirect('driver_login.php')">Login</a></p>
        <p class="link"><a href="#" onclick="redirect('index.php')">Back to Home</a></p>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
