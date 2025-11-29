<?php
$host = "localhost";
$user = "root";      // default XAMPP user
$pass = "";          // default: empty
$dbname = "wdn_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
