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

$graph_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$graph_id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing graph id"]);
    exit;
}

$conn = getDB();

// Find graph owner
$stmt = $conn->prepare("SELECT owner_id FROM graphs WHERE id=?");
$stmt->execute([$graph_id]);
$row = $stmt->fetch();

if (!$row) {
    http_response_code(404);
    echo json_encode(["error" => "Graph not found"]);
    exit;
}

$user = $_SESSION['user'];

// Permission check
if ($row["owner_id"] != $user["id"] && $user["role"] !== "superadmin") {
    http_response_code(403);
    echo json_encode(["error" => "Forbidden"]);
    exit;
}

try {
    $conn->beginTransaction();

    // Delete nodes and edges first
    $conn->prepare("DELETE FROM graph_nodes WHERE graph_id=?")->execute([$graph_id]);
    $conn->prepare("DELETE FROM graph_edges WHERE graph_id=?")->execute([$graph_id]);

    // Delete graph entry
    $conn->prepare("DELETE FROM graphs WHERE id=?")->execute([$graph_id]);

    $conn->commit();

    echo json_encode(["success" => true]);

} catch (Exception $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
