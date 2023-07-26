<?php

$servername = getenv("MYSQL_HOST") ?: "mysql_db";
$username = getenv("MYSQL_USER") ?: "root";
$password = getenv("MYSQL_PASSWORD") ?: "root";
$database = getenv("MYSQL_DATABASE") ?: "mobile-store"; 


try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>