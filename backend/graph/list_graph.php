<?php
require_once __DIR__ . "/../includes/cors.php";

session_start();
header("Content-Type: application/json");
require_once "includes/db.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401); echo json_encode(["error"=>"Unauthorized"]); exit;
}

$conn = getDB();
$userId = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT id, name, created_at, updated_at FROM graphs WHERE owner_id=? ORDER BY updated_at DESC");
$stmt->execute([$userId]);
$rows = $stmt->fetchAll();

echo json_encode(["graphs" => $rows]);
