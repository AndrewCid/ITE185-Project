<?php
session_start();
require_once "includes/db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$loggedRole = $_SESSION["user"]["role"];
$loggedId   = $_SESSION["user"]["id"];

$id = $_GET["id"];

// Get user to delete
$conn = getDB();
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

if ($id == $loggedId) {
    echo json_encode(["error" => "Cannot delete your own account"]);
    exit;
}

if ($loggedRole === "admin" && $user["role"] === "superadmin") {
    echo json_encode(["error" => "Admins cannot delete a superadmin"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->execute([$id]);

echo json_encode(["success" => true]);
