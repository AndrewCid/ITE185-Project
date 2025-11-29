<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="min-h-screen p-10">
    <nav class="flex justify-between items-center mb-10 card">
        <h1 class="text-xl font-semibold">WDN Optimizer</h1>
        <div class="space-x-6">
            <a href="home.php">Home</a>
            <a href="about.php">About</a>
            <a href="main.php">Mapping</a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
