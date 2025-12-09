<?php
require_once __DIR__ . "/../includes/cors.php";
require_once "../includes/db.php";

$conn = getDB();

$name = $_POST["name"];
$email = $_POST["email"];
$username = $_POST["username"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
$role = $_POST["role"] ?? "staff";

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, username, password, role)
     VALUES (?, ?, ?, ?, ?)"
);

$ok = $stmt->execute([$name, $email, $username, $password, $role]);

echo json_encode(["success" => $ok]);
