<?php
// config.php
// Database connection using PDO for MySQL

$host = 'localhost';
$dbname = 'ecoevents';      // your database name in phpMyAdmin
$username = 'root';          // XAMPP default
$password = '';              // XAMPP default is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Session start if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>