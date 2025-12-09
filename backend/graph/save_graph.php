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

$owner_id = $_SESSION['user']['id'];
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON body"]);
    exit;
}

// Required fields
$nodes = $input["nodes"] ?? null;
$edges = $input["edges"] ?? null;

if (!is_array($nodes) || !is_array($edges)) {
    http_response_code(400);
    echo json_encode(["error" => "nodes and edges arrays are required"]);
    exit;
}

// Basic fields
$name = $input["name"] ?? "Untitled Graph";
$is_directed = !empty($input["is_directed"]) ? 1 : 0;

$center_lat = $input["center_lat"] ?? null;
$center_lng = $input["center_lng"] ?? null;
$zoom = $input["zoom"] ?? 12;  // default

$graph_id = $input["graph_id"] ?? null;

// Metadata (optional)
$metadata = isset($input["metadata"]) ? json_encode($input["metadata"]) : null;

try {
    $conn = getDB();
    $conn->beginTransaction();

    // UPDATE existing graph
    if ($graph_id) {
        $stmt = $conn->prepare("SELECT owner_id FROM graphs WHERE id=?");
        $stmt->execute([$graph_id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception("Graph not found");
        }

        if ($row["owner_id"] != $owner_id && $_SESSION["user"]["role"] !== "superadmin") {
            throw new Exception("No permission to edit this graph");
        }

        $update = $conn->prepare("
            UPDATE graphs 
            SET name=?, is_directed=?, center_lat=?, center_lng=?, zoom=?, metadata=?, updated_at=NOW()
            WHERE id=?
        ");

        $update->execute([
            $name,
            $is_directed,
            $center_lat,
            $center_lng,
            $zoom,
            $metadata,
            $graph_id
        ]);

        // wipe nodes/edges
        $conn->prepare("DELETE FROM graph_nodes WHERE graph_id=?")->execute([$graph_id]);
        $conn->prepare("DELETE FROM graph_edges WHERE graph_id=?")->execute([$graph_id]);
    }

    // NEW graph
    else {
        $insert = $conn->prepare("
            INSERT INTO graphs (owner_id, name, is_directed, center_lat, center_lng, zoom, metadata, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $insert->execute([
            $owner_id,
            $name,
            $is_directed,
            $center_lat,
            $center_lng,
            $zoom,
            $metadata
        ]);

        $graph_id = $conn->lastInsertId();
    }

    // === INSERT NODES ===
    $nodeStmt = $conn->prepare("
        INSERT INTO graph_nodes (graph_id, node_key, label, lat, lng, meta)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($nodes as $n) {
        if (!isset($n["key"]) || !isset($n["lat"]) || !isset($n["lng"])) {
            throw new Exception("Node missing required fields (key, lat, lng)");
        }

        $node_id = $n["key"];
        $label = $n["label"] ?? $n["key"];
        $lat = (float)$n["lat"];
        $lng = (float)$n["lng"];
        $meta = isset($n["meta"]) ? json_encode($n["meta"]) : null;

        $nodeStmt->execute([$graph_id, $node_id, $label, $lat, $lng, $meta]);
    }

    // === INSERT EDGES ===
    $edgeStmt = $conn->prepare("
        INSERT INTO graph_edges (graph_id, from_key, to_key, weight, properties)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($edges as $e) {
        if (!isset($e["from"]) || !isset($e["to"])) {
            throw new Exception("Edge missing from/to fields");
        }

        $from = $e["from"];
        $to = $e["to"];
        $weight = isset($e["weight"]) ? (float)$e["weight"] : null;
        $props = isset($e["properties"]) ? json_encode($e["properties"]) : null;

        $edgeStmt->execute([$graph_id, $from, $to, $weight, $props]);
    }

    $conn->commit();

    echo json_encode([
        "success" => true,
        "graph_id" => (int)$graph_id
    ]);

} catch (Exception $ex) {

    if ($conn && $conn->inTransaction()) {
        $conn->rollBack();
    }

    http_response_code(500);
    echo json_encode(["error" => $ex->getMessage()]);
}
?>
