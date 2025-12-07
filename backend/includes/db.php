<?php
/**
 * DATABASE CONFIGURATION
 * This file creates ONE PDO connection and shares it across the entire backend.
 */

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", ""); // XAMPP default
define("DB_NAME", "wdn_db");

// Create PDO connection (only once)
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Return the PDO instance.
 * This replaces creating new PDO objects everywhere.
 */
function getDB() {
    global $pdo;
    return $pdo;
}
?>
