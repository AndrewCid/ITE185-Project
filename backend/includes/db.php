<?php
// DATABASE CONFIGURATION
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = ""; // XAMPP default is empty
$DB_NAME = "wdn_db";

// Create and return PDO Database connection
function getDB() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;

    try {
        $pdo = new PDO(
            "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
            $DB_USER,
            $DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "error" => "Database connection failed",
            "details" => $e->getMessage()
        ]);
        exit;
    }
}
?>
