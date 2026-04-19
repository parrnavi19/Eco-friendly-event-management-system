<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoEvents - Sustainable Event Management</title>
    <meta name="description" content="Discover and manage eco-friendly events aligned with sustainable development goals.">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">
                🌱 EcoEvents
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Browse Events</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php" class="btn btn-secondary">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-secondary">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
