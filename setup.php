<?php
// setup.php
// Run this script once to initialize the SQLite database tables

require_once 'config.php';

$sql_file = __DIR__ . '/database.sql';

if (file_exists($sql_file)) {
    $sql = file_get_contents($sql_file);
    try {
        $pdo->exec($sql);
        echo "Database tables created successfully.";
    } catch (\PDOException $e) {
        die("Error creating tables: " . $e->getMessage());
    }
} else {
    die("database.sql file not found.");
}
?>
