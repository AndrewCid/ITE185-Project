<?php
require_once __DIR__ . "/../includes/cors.php";
session_start();
require_once "../includes/db.php";

$conn = getDB();

$id = $_POST["id"];

// cannot delete superadmins
$stmt = $conn->prepare("SELECT role FROM users WHERE id=?");
$stmt->execute([$id]);
$role = $stmt->fetchColumn();

if ($role === "superadmin") {
    echo json_encode(["success" => false, "error" => "Cannot delete superadmin"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$ok = $stmt->execute([$id]);

echo json_encode(["success" => $ok]);
