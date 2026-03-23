<?php
// config.php
// Database connection using PDO for MySQL (XAMPP)

// Start session first, before anything else
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // XAMPP default username
define('DB_PASS', '');           // XAMPP default password (empty)
define('DB_NAME', 'ecoevents');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    die("<h3 style='font-family:sans-serif;color:red;'>Database connection failed: " . $e->getMessage() . "<br><br>Make sure MySQL is running in XAMPP and you have run <a href='setup.php'>setup.php</a> first.</h3>");
}
