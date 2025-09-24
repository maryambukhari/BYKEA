<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $email = trim(strtolower($_POST['email'])); // Normalize email to lowercase
        $password = trim($_POST['password']);

        // Validate inputs
        if (empty($email) || empty($password)) {
            echo "<script>alert('Email and password are required.');</script>";
            exit;
        }

        // Fetch driver
        $stmt = $pdo->prepare("SELECT * FROM drivers WHERE email = ?");
        $stmt->execute([$email]);
        $driver = $stmt->fetch();

        if ($driver) {
            // Debug: Check stored password
            error_log("Stored password hash: " . $driver['password']);
            error_log("Entered password: " . $password);

            if (password_verify($password, $driver['password'])) {
                $_SESSION['driver_id'] = $driver['id'];
                echo "<script>redirect('driver_dashboard.php');</script>";
            } else {
                echo "<script>alert('Invalid password.');</script>";
            }
        } else {
            echo "<script>alert('Email not found.');</script>";
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login - Bykea Clone</title>
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
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input:focus {
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
            input, button { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Driver Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p class="link">Not a driver? <a href="#" onclick="redirect('driver_signup.php')">Sign Up</a></p>
        <p class="link"><a href="#" onclick="redirect('index.php')">Back to Home</a></p>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
