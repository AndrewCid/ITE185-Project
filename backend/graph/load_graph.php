<?php
require_once __DIR__ . "/../includes/cors.php";

session_start();
header("Content-Type: application/json");
require_once "includes/db.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { echo json_encode(["error"=>"Missing id"]); exit; }

$conn = getDB();
$stmt = $conn->prepare("SELECT * FROM graphs WHERE id=?");
$stmt->execute([$id]);
$graph = $stmt->fetch();

if (!$graph) { http_response_code(404); echo json_encode(["error"=>"Graph not found"]); exit; }

// permission check: owner or superadmin
if ($graph['owner_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'superadmin') {
    http_response_code(403);
    echo json_encode(["error"=>"Forbidden"]);
    exit;
}

// fetch nodes and edges
$nodes = $conn->prepare("SELECT node_key AS `key`, label, lat, lng, meta FROM graph_nodes WHERE graph_id=?");
$nodes->execute([$id]);
$nodes = $nodes->fetchAll();

$edges = $conn->prepare("SELECT from_key AS `from`, to_key AS `to`, weight, properties FROM graph_edges WHERE graph_id=?");
$edges->execute([$id]);
$edges = $edges->fetchAll();

echo json_encode([
    "graph" => $graph,
    "nodes" => $nodes,
    "edges" => $edges
]);
