<?php
require_once __DIR__ . "/../includes/cors.php";

// save_graph.php
session_start();
header("Content-Type: application/json");
require_once "includes/db.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$owner_id = $_SESSION['user']['id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

// Required fields: nodes (array), edges (array). name optional.
$nodes = $data['nodes'] ?? null;
$edges = $data['edges'] ?? null;
$name = $data['name'] ?? null;
$is_directed = isset($data['is_directed']) ? (int)($data['is_directed'] ? 1 : 0) : 0;
$center_lat = $data['center_lat'] ?? null;
$center_lng = $data['center_lng'] ?? null;
$zoom = $data['zoom'] ?? null;
$rotation = $data['rotation'] ?? 0;
$tilt = $data['tilt'] ?? 0;
$graph_id = $data['graph_id'] ?? null; // if provided -> update

if (!is_array($nodes) || !is_array($edges)) {
    http_response_code(400);
    echo json_encode(["error" => "nodes and edges arrays required"]);
    exit;
}

try {
    $conn = getDB();
    $conn->beginTransaction();

    if ($graph_id) {
        // Update graph metadata
        $stmt = $conn->prepare("SELECT owner_id FROM graphs WHERE id=?");
        $stmt->execute([$graph_id]);
        $row = $stmt->fetch();
        if (!$row) throw new Exception("Graph not found");
        if ($row['owner_id'] != $owner_id && $_SESSION['user']['role'] !== 'superadmin') {
            throw new Exception("Not permitted to modify this graph");
        }

        $upd = $conn->prepare("UPDATE graphs SET name=?, is_directed=?, center_lat=?, center_lng=?, zoom=?, rotation=?, tilt=?, metadata=? WHERE id=?");
        $upd->execute([$name, $is_directed, $center_lat, $center_lng, $zoom, $rotation, $tilt, json_encode($data['metadata'] ?? null), $graph_id]);

        // delete old nodes/edges
        $conn->prepare("DELETE FROM graph_nodes WHERE graph_id=?")->execute([$graph_id]);
        $conn->prepare("DELETE FROM graph_edges WHERE graph_id=?")->execute([$graph_id]);

    } else {
        // Insert new graph
        $ins = $conn->prepare("INSERT INTO graphs (owner_id, name, is_directed, center_lat, center_lng, zoom, rotation, tilt, metadata) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->execute([$owner_id, $name, $is_directed, $center_lat, $center_lng, $zoom, $rotation, $tilt, json_encode($data['metadata'] ?? null)]);
        $graph_id = $conn->lastInsertId();
    }

    // Insert nodes
    $nodeStmt = $conn->prepare("INSERT INTO graph_nodes (graph_id, node_key, label, lat, lng, meta) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($nodes as $n) {
        // expect n: { key, label, lat, lng, meta (optional) }
        $key = $n['key'] ?? null;
        if ($key === null) throw new Exception("Node missing key");
        $label = $n['label'] ?? null;
        $lat = $n['lat'];
        $lng = $n['lng'];
        $meta = isset($n['meta']) ? json_encode($n['meta']) : null;
        $nodeStmt->execute([$graph_id, $key, $label, $lat, $lng, $meta]);
    }

    // Insert edges
    $edgeStmt = $conn->prepare("INSERT INTO graph_edges (graph_id, from_key, to_key, weight, properties) VALUES (?, ?, ?, ?, ?)");
    foreach ($edges as $e) {
        // expect e: { from, to, weight, properties (optional) }
        $from = $e['from'];
        $to = $e['to'];
        $weight = isset($e['weight']) ? (float)$e['weight'] : null;
        $props = isset($e['properties']) ? json_encode($e['properties']) : null;
        $edgeStmt->execute([$graph_id, $from, $to, $weight, $props]);
    }

    $conn->commit();
    echo json_encode(["success" => true, "graph_id" => (int)$graph_id]);

} catch (Exception $ex) {
    if ($conn && $conn->inTransaction()) $conn->rollBack();
    http_response_code(500);
    echo json_encode(["error" => $ex->getMessage()]);
}
