<?php
// setup.php
// Run this script ONCE to create the MySQL database and tables.
// Visit: http://localhost/EcoEvents/setup.php
// Make sure MySQL is running in XAMPP before visiting this page.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Step 1: Connect WITHOUT selecting a database
try {
    $pdo_init = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("<h3 style='font-family:sans-serif;color:red;'>Could not connect to MySQL: " . $e->getMessage() . "<br>Make sure MySQL is started in XAMPP Control Panel.</h3>");
}

// Step 2: Create the database explicitly first
try {
    $pdo_init->exec("CREATE DATABASE IF NOT EXISTS ecoevents CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (\PDOException $e) {
    die("<h3 style='font-family:sans-serif;color:red;'>Could not create database: " . $e->getMessage() . "</h3>");
}

// Step 3: Reconnect with the database selected
try {
    $pdo_init = new PDO("mysql:host=localhost;dbname=ecoevents;charset=utf8mb4", 'root', '');
    $pdo_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("<h3 style='font-family:sans-serif;color:red;'>Could not select database: " . $e->getMessage() . "</h3>");
}

// Step 4: Run table creation statements, skipping CREATE DATABASE and USE lines
$sql_file = __DIR__ . '/database.sql';
if (!file_exists($sql_file)) {
    die("<h3 style='font-family:sans-serif;color:red;'>ERROR: database.sql not found.</h3>");
}

$sql = file_get_contents($sql_file);
$statements = array_filter(array_map('trim', explode(';', $sql)));
$errors = [];

foreach ($statements as $statement) {
    if (empty($statement)) continue;
    if (stripos($statement, 'CREATE DATABASE') !== false) continue;
    if (stripos($statement, 'USE ') === 0) continue;

    try {
        $pdo_init->exec($statement);
    } catch (\PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            $errors[] = htmlspecialchars($e->getMessage());
        }
    }
}

if (empty($errors)) {
    echo "<h2 style='font-family:sans-serif;color:green;'>Database and tables created successfully!</h2>";
    echo "<p style='font-family:sans-serif;'><a href='index.php'>Go to EcoEvents Home</a></p>";
} else {
    echo "<h2 style='font-family:sans-serif;color:orange;'>Setup finished with errors:</h2><ul style='font-family:sans-serif;'>";
    foreach ($errors as $err) {
        echo "<li>$err</li>";
    }
    echo "</ul><p style='font-family:sans-serif;'><a href='index.php'>Try going home anyway</a></p>";
}
