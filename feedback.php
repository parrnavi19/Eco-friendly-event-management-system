
// Redirect back to the previous page with success flag
$ref = $_SERVER['HTTP_REFERER'] ?? 'index.php';
// Append success param
$sep = strpos($ref, '?') !== false ? '&' : '?';
header("Location: " . $ref . $sep . "feedback=success");
exit;
?>
