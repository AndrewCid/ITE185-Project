<!-- Authentication -->
<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'includes/layout.php'; ?>

<h2 class="text-xl font-semibold mb-4">Dashboard</h2>

<div class="grid grid-cols-3 gap-6">
    <div class="h-36 bg-[#141414] border border-gray-800 rounded-xl shadow"></div>
    <div class="h-36 bg-[#141414] border border-gray-800 rounded-xl shadow"></div>
    <div class="h-36 bg-[#141414] border border-gray-800 rounded-xl shadow"></div>
</div>

<div class="mt-6 h-96 bg-[#141414] border border-gray-800 rounded-xl shadow"></div>

