<?php
session_start();
require_once "includes/db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$loggedRole = $_SESSION["user"]["role"];

$conn = getDB();

$id       = $_POST["id"];
$name     = $_POST["name"];
$email    = $_POST["email"];
$username = $_POST["username"];
$password = $_POST["password"];

// Get existing user
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

// Admin cannot edit superadmin
if ($loggedRole === "admin" && $user["role"] === "superadmin") {
    echo json_encode(["error" => "Admins cannot edit a superadmin"]);
    exit;
}

$role = $user["role"]; // default

if ($loggedRole === "superadmin") {
    $role = $_POST["role"];
}

if (!empty($password)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET name=?, email=?, username=?, password=?, role=? WHERE id=?";
    $params = [$name, $email, $username, $hash, $role, $id];
} else {
    $sql = "UPDATE users SET name=?, email=?, username=?, role=? WHERE id=?";
    $params = [$name, $email, $username, $role, $id];
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);

echo json_encode(["success" => true]);
