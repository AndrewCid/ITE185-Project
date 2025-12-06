<!-- Authentication -->
<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main Dashboard</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            overflow: hidden; /* Prevent scroll when panel is open */
        }

        /* ==== SIDEBAR (Slide-in Dashboard) ==== */
        #sidebar {
            position: fixed;
            top: 0;
            left: -260px; /* Hidden default */
            width: 260px;
            height: 100vh;
            background: #1e1e1e;
            color: white;
            padding: 20px;
            transition: 0.3s ease;
            z-index: 2000;
        }

        #sidebar.open {
            left: 0; /* Slide visible */
        }

        #sidebar h2 {
            margin-top: 0;
        }

        /* ==== TOGGLE BUTTON ==== */
        #toggleBtn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #333;
            color: white;
            padding: 10px 15px;
            cursor: pointer;
            z-index: 2100;
            border-radius: 4px;
        }

        /* ==== MAP CONTAINER ==== */
        #map {
            width: 100%;
            height: 100vh;
            z-index: 1;
        }
    </style>
</head>
<body>

<!-- Toggle Button -->
<div id="toggleBtn">☰ Menu</div>

<!-- Slide-in Dashboard -->
<div id="sidebar">
    <h2>Dashboard</h2>
    <p>This is your slide-in dashboard.</p>
    <p>You can put:</p>
    <ul>
        <li>Recent Projects</li>
        <li>User Profile</li>
        <li>Announcements</li>
        <li>Links</li>
        <li>etc.</li>
    </ul>
</div>

<!-- Main Map -->
<div id="map"></div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // ========== TOGGLE SIDEBAR ========== //
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleBtn");

    toggleBtn.onclick = () => {
        sidebar.classList.toggle("open");
    };

    // ========== WORLD MAP INITIALIZATION ========== //
    const map = L.map("map").setView([20, 0], 2.3);

    // Add OpenStreetMap Tiles
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap contributors"
    }).addTo(map);
</script>

</body>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");

    sidebar.classList.toggle("closed");
    content.classList.toggle("expanded");
}
</script>

</html>

<?php
// include 'includes/footer.php';
?>
