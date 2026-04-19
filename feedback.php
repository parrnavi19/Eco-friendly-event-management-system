<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $type    = trim($_POST['type']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($type) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO feedback (name, email, type, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $type, $message]);
    }
}

// Redirect back to the previous page with success flag
$ref = $_SERVER['HTTP_REFERER'] ?? 'index.php';
// Append success param
$sep = strpos($ref, '?') !== false ? '&' : '?';
header("Location: " . $ref . $sep . "feedback=success");
exit;
?>
