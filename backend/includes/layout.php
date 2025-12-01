<?php
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION["user"];
$role = $user["role"];   // "admin" or "staff"
?>
<style>
/* Sidebar Container */
.sidebar {
    width: 260px;
    height: 100vh;
    background: linear-gradient(135deg, #0e1e2f, #1d3557);
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    transform: translateX(0);
    transition: transform .3s ease;
    padding: 20px;
}

.sidebar.hidden {
    transform: translateX(-260px);
}

/* Toggle Button */
.toggle-btn {
    position: fixed;
    top: 15px;
    left: 270px;
    font-size: 24px;
    cursor: pointer;
    color: #1d3557;
    transition: left .3s ease;
}

.sidebar.hidden ~ .toggle-btn {
    left: 20px;
}

/* Links */
.sidebar a {
    display: block;
    margin: 12px 0;
    text-decoration: none;
    color: #f1faee;
    font-size: 17px;
    padding: 8px 0;
}

.sidebar a:hover {
    color: #a8dadc;
}

/* User Info */
.profile-box {
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.profile-box h3 {
    margin: 0;
}

.main-content {
    margin-left: 260px;
    padding: 25px;
    transition: margin-left .3s ease;
}

.sidebar.hidden ~ .main-content {
    margin-left: 20px;
}
</style>

<div class="sidebar" id="sidebar">
    <div class="profile-box">
        <h3><?= htmlspecialchars($user["username"]) ?></h3>
        <small><?= strtoupper($role) ?></small>
    </div>

    <!-- Navigation -->
    <a href="home.php">🏠 Home</a>
    <a href="main.php">🗺️ Water Network</a>
    <a href="about.php">👥 About Authors</a>

    <?php if ($role === "admin"): ?>
        <a href="admin_users.php">🔐 Manage Users</a>
        <a href="admin_projects.php">📁 Project Database</a>
    <?php endif; ?>

    <a href="logout.php" style="color:#ef5350">🚪 Logout</a>
</div>

<div class="toggle-btn" onclick="toggleSidebar()">☰</div>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("hidden");
}
</script>
