<?php
require_once __DIR__ . "/../includes/cors.php";

session_start();
header("Content-Type: application/json");
require_once "includes/db.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error"=>"Unauthorized"]); exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$algo = $input['algorithm'] ?? null;

if (!$algo) { http_response_code(400); echo json_encode(["error"=>"algorithm required"]); exit; }

$conn = getDB();

$nodes = $input['nodes'] ?? null;
$edges = $input['edges'] ?? null;

if (isset($input['graph_id'])) {
    $gid = (int)$input['graph_id'];
    // permission check
    $gstmt = $conn->prepare("SELECT * FROM graphs WHERE id=?");
    $gstmt->execute([$gid]);
    $g = $gstmt->fetch();
    if (!$g) { http_response_code(404); echo json_encode(["error"=>"Graph not found"]); exit; }
    if ($g['owner_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'superadmin') {
        http_response_code(403); echo json_encode(["error"=>"Forbidden"]); exit;
    }

    $nstmt = $conn->prepare("SELECT node_key AS `key` FROM graph_nodes WHERE graph_id=?");
    $nstmt->execute([$gid]); $nodes = array_column($nstmt->fetchAll(), 'key');

    $estmt = $conn->prepare("SELECT from_key AS `from`, to_key AS `to`, weight FROM graph_edges WHERE graph_id=?");
    $estmt->execute([$gid]); $edges = $estmt->fetchAll();
}

if (!is_array($nodes) || !is_array($edges)) {
    http_response_code(400); echo json_encode(["error"=>"Provide graph payload or graph_id"]); exit;
}

if ($algo === "mst") {
    // Kruskal's algorithm — build list of edges (weight default = 1 if null)
    // Convert edges to [u,v,w]
    $elist = [];
    foreach ($edges as $e) {
        $u = $e['from']; $v = $e['to']; $w = isset($e['weight']) && $e['weight'] !== null ? (float)$e['weight'] : 1.0;
        $elist[] = [$u, $v, $w];
        // if undirected and only one direction stored, ensure both? Graph's is_directed may matter;
        // We'll assume stored edges reflect direction; MST makes sense for undirected graphs.
    }

    // Union-Find
    $parent = []; $rank = [];
    foreach ($nodes as $n) { $parent[$n] = $n; $rank[$n] = 0; }
    function findp(&$parent, $x) {
        if ($parent[$x] !== $x) $parent[$x] = findp($parent, $parent[$x]);
        return $parent[$x];
    }
    function unionp(&$parent, &$rank, $x, $y) {
        $rx = findp($parent, $x);
        $ry = findp($parent, $y);
        if ($rx === $ry) return false;
        if ($rank[$rx] < $rank[$ry]) $parent[$rx] = $ry;
        else if ($rank[$rx] > $rank[$ry]) $parent[$ry] = $rx;
        else { $parent[$ry] = $rx; $rank[$rx]++; }
        return true;
    }

    // sort edges by weight
    usort($elist, function($a,$b){ return $a[2] <=> $b[2]; });

    $mst = []; $total = 0.0;
    foreach ($elist as $e) {
        $u=$e[0]; $v=$e[1]; $w=$e[2];
        if (!isset($parent[$u]) || !isset($parent[$v])) continue; // ignore unknown nodes
        if (findp($parent,$u) !== findp($parent,$v)) {
            unionp($parent,$rank,$u,$v);
            $mst[] = ["from"=>$u,"to"=>$v,"weight"=>$w];
            $total += $w;
        }
    }

    echo json_encode(["algorithm"=>"mst","total_weight"=>$total,"edges"=>$mst]);
    exit;
}

if ($algo === "dijkstra") {
    $start = $input['start'] ?? null;
    if (!$start) { http_response_code(400); echo json_encode(["error"=>"start node required for dijkstra"]); exit; }

    // Build adjacency list
    $adj = [];
    foreach ($nodes as $n) $adj[$n]=[];

    foreach ($edges as $e) {
        $u = $e['from']; $v = $e['to']; $w = isset($e['weight']) && $e['weight'] !== null ? (float)$e['weight'] : 1.0;
        if (!isset($adj[$u])) $adj[$u]=[];
        $adj[$u][] = ['to'=>$v,'w'=>$w];
        // If graph is undirected, add reverse edge as well — determine by graph metadata if provided
        // If input included "is_directed" flag:
    }
    // If input specified "is_directed" = false, add reverse edges for missing ones
    $is_directed = $input['is_directed'] ?? false;
    if (!$is_directed) {
        // ensure reverse edges exist
        foreach ($edges as $e) {
            $u=$e['from']; $v=$e['to']; $w=isset($e['weight']) && $e['weight']!==null ? (float)$e['weight'] : 1.0;
            if (!isset($adj[$v])) $adj[$v]=[];
            $adj[$v][] = ['to'=>$u,'w'=>$w];
        }
    }

    // Dijkstra
    // initialize
    $dist = []; $prev = [];
    foreach ($adj as $node => $_) { $dist[$node]=INF; $prev[$node]=null; }
    if (!isset($dist[$start])) { http_response_code(400); echo json_encode(["error"=>"start node not found"]); exit; }
    $dist[$start]=0;

    // priority queue min-heap using SplPriorityQueue (note: its priorities are max-first; we'll invert)
    $pq = new SplPriorityQueue();
    $pq->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
    $pq->insert($start, -0);

    while (!$pq->isEmpty()) {
        $item = $pq->extract(); // ['data'=>..., 'priority'=>...]
        $u = $item['data'];
        $d = -$item['priority'];
        if ($d > $dist[$u]) continue;
        foreach ($adj[$u] as $edge) {
            $v = $edge['to']; $w = $edge['w'];
            $nd = $dist[$u] + $w;
            if ($nd < $dist[$v]) {
                $dist[$v] = $nd;
                $prev[$v] = $u;
                $pq->insert($v, -$nd);
            }
        }
    }

    // Build results (distances + paths)
    $paths = [];
    foreach ($dist as $node=>$d) {
        if (is_infinite($d)) { $paths[$node] = null; continue; }
        // reconstruct path
        $path = [];
        $cur = $node;
        while ($cur !== null) { array_unshift($path, $cur); $cur = $prev[$cur]; }
        $paths[$node] = ["distance"=>$d,"path"=>$path];
    }

    echo json_encode(["algorithm"=>"dijkstra","start"=>$start,"results"=>$paths]);
    exit;
}

http_response_code(400);
echo json_encode(["error"=>"Unknown algorithm"]);
