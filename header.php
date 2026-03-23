<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoEvents &mdash; Sustainable Event Management</title>
  <meta name="description" content="Discover and organise eco-friendly events aligned with SDG 11 & 12.">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <nav class="navbar">
    <a href="index.php" class="logo">
      <div class="logo-icon">🌱</div>
      <span class="logo-text">EcoEvents</span>
    </a>

    <form class="nav-search" action="index.php" method="GET">
      <span class="nav-search-icon">🔍</span>
      <input type="text" name="search"
             placeholder="Search events, locations…"
             value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
    </form>

    <ul class="nav-links">
      <li><a href="index.php">Browse</a></li>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="create_event.php" class="btn btn-primary btn-sm">+ New Event</a></li>
        <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
      <?php else: ?>
        <li><a href="login.php" class="btn btn-ghost btn-sm">Login</a></li>
        <li><a href="register.php" class="btn btn-primary btn-sm">Register</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<main>
