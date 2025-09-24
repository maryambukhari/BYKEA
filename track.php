<?php
session_start();
require 'db.php';

if (!isset($_SESSION['driver_id']) && !isset($_SESSION['user_id'])) {
    echo "<script>redirect('login.php');</script>";
    exit;
}

$driver_id = isset($_SESSION['driver_id']) ? $_SESSION['driver_id'] : null;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch accepted rides
$rides = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM rides WHERE user_id = ? AND status = 'accepted' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($driver_id) {
    $stmt = $pdo->prepare("SELECT * FROM rides WHERE driver_id = ? AND status = 'accepted' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$driver_id]);
    $rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch accepted parcels
$parcels = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM parcels WHERE user_id = ? AND status = 'accepted' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($driver_id) {
    $stmt = $pdo->prepare("SELECT * FROM parcels WHERE driver_id = ? AND status = 'accepted' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$driver_id]);
    $parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Ride or Parcel - Bykea Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
        .tracking-container {
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
        #map {
            height: 400px;
            width: 100%;
            border-radius: 10px;
            margin-top: 20px;
            border: 2px solid #6b48ff;
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
            .tracking-container { padding: 20px; margin: 20px; }
            h2 { font-size: 28px; }
            h3 { font-size: 20px; }
            .request-card p { font-size: 14px; }
            #map { height: 300px; }
            .navbar a { font-size: 16px; margin: 0 15px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="redirect('index.php')">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="#" onclick="redirect('profile.php')">Profile</a>
            <a href="#" onclick="redirect('book_ride.php')">Book Ride</a>
            <a href="#" onclick="redirect('book_parcel.php')">Send Parcel</a>
        <?php elseif (isset($_SESSION['driver_id'])): ?>
            <a href="#" onclick="redirect('driver_dashboard.php')">Driver Dashboard</a>
        <?php endif; ?>
        <a href="#" onclick="redirect('logout.php')">Logout</a>
    </div>
    <div class="tracking-container">
        <h2>Track Your Ride or Parcel</h2>
        
        <!-- Accepted Rides -->
        <h3>Active Rides</h3>
        <?php if (empty($rides)): ?>
            <p class="no-requests">No active rides to track.</p>
        <?php else: ?>
            <?php foreach ($rides as $ride): ?>
                <div class="request-card">
                    <p><strong>Pickup:</strong> <?php echo htmlspecialchars($ride['pickup_location']); ?></p>
                    <p><strong>Drop-off:</strong> <?php echo htmlspecialchars($ride['dropoff_location']); ?></p>
                    <p><strong>Distance:</strong> <?php echo number_format($ride['distance'], 2); ?> km</p>
                    <p><strong>Price:</strong> PKR <?php echo number_format($ride['price'], 2); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst($ride['status']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Accepted Parcels -->
        <h3>Active Parcels</h3>
        <?php if (empty($parcels)): ?>
            <p class="no-requests">No active parcels to track.</p>
        <?php else: ?>
            <?php foreach ($parcels as $parcel): ?>
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
        
        <!-- Map -->
        <?php if (!empty($rides) || !empty($parcels)): ?>
            <div id="map"></div>
        <?php endif; ?>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function redirect(page) {
            window.location.href = page;
        }

        // Simulated map integration (using Leaflet.js)
        function initMap() {
            var map = L.map('map').setView([51.505, -0.09], 13); // Sample coordinates
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            L.marker([51.5, -0.09]).addTo(map).bindPopup('Current Location').openPopup();
        }

        <?php if (!empty($rides) || !empty($parcels)): ?>
            initMap();
        <?php endif; ?>
    </script>
</body>
</html>
