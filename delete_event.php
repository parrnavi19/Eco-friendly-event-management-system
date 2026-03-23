<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $event_id = (int)$_POST['id'];
    $user_id  = $_SESSION['user_id'];

    // Only delete if the event belongs to this user
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND organizer_id = ?");
    $stmt->execute([$event_id, $user_id]);
}

header("Location: dashboard.php");
exit;
