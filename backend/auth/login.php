<?php
require_once __DIR__ . "/../includes/cors.php";

session_start();
require_once __DIR__ . "/../includes/db.php";
$conn = getDB();

header("Content-Type: application/json");

// 🔹 1. CHECK SESSION (GET REQUEST)
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    if (!isset($_SESSION["user"])) {
        echo json_encode(null);
        exit;
    }

    echo json_encode($_SESSION["user"]);
    exit;
}

// 🔹 2. LOGIN (POST REQUEST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user["password"])) {
        echo json_encode(["success" => false, "message" => "Invalid username or password."]);
        exit;
    }

    $_SESSION["user"] = [
        "id" => $user["id"],
        "username" => $user["username"],
        "role" => $user["role"]
    ];

    echo json_encode(["success" => true]);
    exit;
}

// 🔹 Invalid method
echo json_encode(["success" => false, "message" => "Invalid request"]);
exit;
?>
