<?php
session_start();
require 'db.php';

if (!isset($_SESSION['driver_id'])) {
    echo "<script>redirect('driver_login.php');</script>";
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Check if vehicle_type column exists
$columns = $pdo->query("SHOW COLUMNS FROM rides LIKE 'vehicle_type'")->fetchAll();
$has_vehicle_type = count($columns) > 0;

// Fetch pending rides
$stmt_rides = $pdo->prepare("SELECT * FROM rides WHERE driver_id IS NULL AND status = 'pending'");
$stmt_rides->execute();
$pending_rides = $stmt_rides->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending parcels
$stmt_parcels = $pdo->prepare("SELECT * FROM parcels WHERE driver_id IS NULL AND status = 'pending'");
$stmt_parcels->execute();
$pending_parcels = $stmt_parcels->fetchAll(PDO::FETCH_ASSOC);

// Fetch accepted rides
$stmt_accepted_rides = $pdo->prepare("SELECT * FROM rides WHERE driver_id = ? AND status = 'accepted'");
$stmt_accepted_rides->execute([$driver_id]);
$accepted_rides = $stmt_accepted_rides->fetchAll(PDO::FETCH_ASSOC);

// Fetch accepted parcels
$stmt_accepted_parcels = $pdo->prepare("SELECT * FROM parcels WHERE driver_id = ? AND status = 'accepted'");
$stmt_accepted_parcels->execute([$driver_id]);
$accepted_parcels = $stmt_accepted_parcels->fetchAll(PDO::FETCH_ASSOC);

// Handle ride acceptance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ride_id'])) {
    try {
        $ride_id = $_POST['ride_id'];
        $stmt = $pdo->prepare("UPDATE rides SET driver_id = ?, status = 'accepted' WHERE id = ? AND status = 'pending'");
        if ($stmt->execute([$driver_id, $ride_id])) {
            echo "<script>alert('Ride accepted successfully!'); redirect('track.php');</script>";
        } else {
            echo "<script>alert('Error accepting ride.');</script>";
        }
    } catch (PDOException $e) {
        error_log("Ride acceptance error: " . $e->getMessage());
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Handle parcel acceptance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['parcel_id'])) {
    try {
        $parcel_id = $_POST['parcel_id'];
        $stmt = $pdo->prepare("UPDATE parcels SET driver_id = ?, status = 'accepted' WHERE id = ? AND status = 'pending'");
        if ($stmt->execute([$driver_id, $parcel_id])) {
            echo "<script>alert('Parcel accepted successfully!'); redirect('track.php');</script>";
        } else {
            echo "<script>alert('Error accepting parcel.');</script>";
        }
    } catch (PDOException $e) {
        error_log("Parcel acceptance error: " . $e->getMessage());
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Bykea Clone</title>
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
        .dashboard {
            max-width: 900px;
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
            color: #6b48ff;
            font-size: 36px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px;
        }
        h3 {
            color: #6b48ff;
            font-size: 24px;
            margin: 20px 0 10px;
        }
        .request-card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .request-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .request-card p {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
        }
        .request-card p strong {
            color: #6b48ff;
        }
        .request-card button {
            background: linear-gradient(45deg, #6b48ff, #00ddeb);
            border: none;
            padding: 10px 20px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .request-card button:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
        .no-requests {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin: 20px 0;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .dashboard { padding: 20px; margin: 20px; }
            h2 { font-size: 28px; }
            h3 { font-size: 20px; }
            .request-card { padding: 15px; }
            .request-card p { font-size: 14px; }
            .request-card button { font-size: 14px; padding: 8px 15px; }
            .navbar a { font-size: 16px; margin: 0 15px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="redirect('index.php')">Home</a>
        <a href="#" onclick="redirect('driver_dashboard.php')">Driver Dashboard</a>
        <a href="#" onclick="redirect('track.php')">Track Rides/Parcels</a>
        <a href="#" onclick="redirect('logout.php')">Logout</a>
    </div>
    <div class="dashboard">
        <h2>Driver Dashboard</h2>
        
        <!-- Ongoing Rides -->
        <h3>Ongoing Rides</h3>
        <?php if (empty($accepted_rides)): ?>
            <p class="no-requests">No ongoing rides.</p>
        <?php else: ?>
            <?php foreach ($accepted_rides as $ride): ?>
                <div class="request-card">
                    <p><strong>Pickup:</strong> <?php echo htmlspecialchars($ride['pickup_location']); ?></p>
                    <p><strong>Drop-off:</strong> <?php echo htmlspecialchars($ride['dropoff_location']); ?></p>
                    <p><strong>Distance:</strong> <?php echo number_format($ride['distance'], 2); ?> km</p>
                    <p><strong>Price:</strong> PKR <?php echo number_format($ride['price'], 2); ?></p>
                    <?php if ($has_vehicle_type): ?>
                        <p><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($ride['vehicle_type']); ?></p>
                    <?php endif; ?>
                    <p><strong>Status:</strong> <?php echo ucfirst($ride['status']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Ongoing Parcels -->
        <h3>Ongoing Parcels</h3>
        <?php if (empty($accepted_parcels)): ?>
            <p class="no-requests">No ongoing parcels.</p>
        <?php else: ?>
            <?php foreach ($accepted_parcels as $parcel): ?>
                <div class="request-card">
                    <p><strong>Pickup:</strong> <?php echo htmlspecialchars($parcel['pickup_location']); ?></p>
                    <p><strong>Drop-off:</strong> <?php echo htmlspecialchars($parcel['dropoff_location']); ?></p>
                    <p><strong>Package Details:</strong> <?php echo htmlspecialchars($parcel['package_details']); ?></p>
                    <p><strong>Distance:</strong> <?php echo number_format($parcel['distance'], 2); ?> km</p>
                    <p><strong>Price:</strong> PKR <?php echo number_format($parcel['price'], 2); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst($parcel['status']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Pending Rides -->
        <h3>Pending Ride Requests</h3>
        <?php if (empty($pending_rides)): ?>
            <p class="no-requests">No pending ride requests available.</p>
        <?php else: ?>
            <?php foreach ($pending_rides as $ride): ?>
                <div class="request-card">
                    <p><strong>Pickup:</strong> <?php echo htmlspecialchars($ride['pickup_location']); ?></p>
                    <p><strong>Drop-off:</strong> <?php echo htmlspecialchars($ride['dropoff_location']); ?></p>
                    <p><strong>Distance:</strong> <?php echo number_format($ride['distance'], 2); ?> km</p>
                    <p><strong>Price:</strong> PKR <?php echo number_format($ride['price'], 2); ?></p>
                    <?php if ($has_vehicle_type): ?>
                        <p><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($ride['vehicle_type']); ?></p>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="ride_id" value="<?php echo $ride['id']; ?>">
                        <button type="submit">Accept Ride</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Pending Parcels -->
        <h3>Pending Parcel Requests</h3>
        <?php if (empty($pending_parcels)): ?>
            <p class="no-requests">No pending parcel requests available.</p>
        <?php else: ?>
            <?php foreach ($pending_parcels as $parcel): ?>
                <div class="request-card">
                    <p><strong>Pickup:</strong> <?php echo htmlspecialchars($parcel['pickup_location']); ?></p>
                    <p><strong>Drop-off:</strong> <?php echo htmlspecialchars($parcel['dropoff_location']); ?></p>
                    <p><strong>Package Details:</strong> <?php echo htmlspecialchars($parcel['package_details']); ?></p>
                    <p><strong>Distance:</strong> <?php echo number_format($parcel['distance'], 2); ?> km</p>
                    <p><strong>Price:</strong> PKR <?php echo number_format($parcel['price'], 2); ?></p>
                    <form method="POST">
                        <input type="hidden" name="parcel_id" value="<?php echo $parcel['id']; ?>">
                        <button type="submit">Accept Parcel</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
