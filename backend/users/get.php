<?php
require_once __DIR__ . "/../includes/cors.php";
require_once "../includes/db.php";
$conn = getDB();

$sort = $_GET["sort"] ?? "created_at";
$order = $_GET["order"] ?? "DESC";
$search = $_GET["search"] ?? "";
$date = $_GET["date"] ?? "";

$allowedSort = ["name","email","username","role","created_at","id"];
if (!in_array($sort, $allowedSort)) $sort = "created_at";

$sql = "SELECT * FROM users WHERE 1 ";

$params = [];

if ($search !== "") {
    $sql .= "AND (name LIKE ? OR username LIKE ? OR email LIKE ? OR id = ?) ";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "$search";
}

if ($date !== "") {
    $sql .= "AND DATE(created_at) = ? ";
    $params[] = $date;
}

$sql .= "ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll());
