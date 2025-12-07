<?php
require_once __DIR__ . "/../includes/cors.php";

session_start();
session_unset();
session_destroy();

header("Location: /../backend/pages/home.php");
exit;
