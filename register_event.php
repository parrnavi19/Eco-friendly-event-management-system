<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['event_id'])) {
    header("Location: index.php");
    exit;
}

$event_id = (int)$_POST['event_id'];
$user_id  = (int)$_SESSION['user_id'];
$action   = $_POST['action'] ?? 'join'; // 'join' or 'leave'

// Verify event exists
$stmt = $pdo->prepare("SELECT id, organizer_id FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header("Location: index.php");
    exit;
}

// Organiser cannot register for their own event
if ($event['organizer_id'] == $user_id) {
    header("Location: event_details.php?id=$event_id&msg=own");
    exit;
}

if ($action === 'leave') {
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    header("Location: event_details.php?id=$event_id&msg=left");
    exit;
}

// Join: insert, ignore duplicate
try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO registrations (event_id, user_id) VALUES (?, ?)");
    $stmt->execute([$event_id, $user_id]);
    header("Location: event_details.php?id=$event_id&msg=joined");
} catch (\PDOException $e) {
    header("Location: event_details.php?id=$event_id&msg=error");
}
exit;
