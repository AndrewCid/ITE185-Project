<?php
require_once __DIR__ . "/../includes/cors.php";

session_start();
header("Content-Type: application/json");

require_once __DIR__ . "/../includes/db.php";

// Require login
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$conn = getDB();
$userId = $_SESSION['user']['id'];

// Get all graphs for user
$stmt = $conn->prepare("
    SELECT 
        id, 
        name, 
        data, 
        is_directed,
        created_at, 
        updated_at
    FROM graphs 
    WHERE owner_id = ?
    ORDER BY updated_at DESC
");
$stmt->execute([$userId]);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert JSON string → PHP array for frontend
foreach ($rows as &$g) {
    if (!empty($g["data"])) {
        $g["data"] = json_decode($g["data"], true);
    }
}

echo json_encode([
    "success" => true,
    "graphs" => $rows
]);
