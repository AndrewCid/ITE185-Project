<?php
require_once __DIR__ . "/../includes/cors.php";
session_start();
require_once "../includes/db.php";
$conn = getDB();

$currentRole = $_SESSION["user"]["role"] ?? "staff";

$id = $_POST["id"];
$name = $_POST["name"];
$email = $_POST["email"];
$username = $_POST["username"];
$role = $_POST["role"];

// role changing restrictions
if ($currentRole !== "superadmin") {
    $stmtRole = $conn->prepare("SELECT role FROM users WHERE id=?");
    $stmtRole->execute([$id]);
    $existingRole = $stmtRole->fetchColumn();

    if ($existingRole !== $role) {
        echo json_encode(["success" => false, "error" => "Not allowed"]);
        exit;
    }
}

$stmt = $conn->prepare(
    "UPDATE users SET name=?, email=?, username=?, role=? WHERE id=?"
);

$ok = $stmt->execute([$name, $email, $username, $role, $id]);

echo json_encode(["success" => $ok]);
