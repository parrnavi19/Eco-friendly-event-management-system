<?php
require_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$event = null;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND organizer_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        header("Location: dashboard.php");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $eco_pledge = $_POST['eco_pledge'];
    $carbon_offset = (int)$_POST['carbon_offset'];

    if (empty($title) || empty($description) || empty($event_date) || empty($event_time) || empty($location)) {
        $error = "All required fields must be filled.";
    } else {
        $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, event_date=?, event_time=?, location=?, eco_impact_pledge=?, carbon_offset_kg=? WHERE id=? AND organizer_id=?");
        if ($stmt->execute([$title, $description, $event_date, $event_time, $location, $eco_pledge, $carbon_offset, $id, $_SESSION['user_id']])) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Failed to update event.";
        }
    }
    
    // Refresh event data
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND organizer_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="form-container" style="max-width: 700px;">
    <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Edit Event</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="edit_event.php?id=<?php echo $event['id']; ?>" method="POST">
        <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
        
        <div class="form-group">
            <label for="title">Event Title *</label>
            <input type="text" id="title" name="title" class="form-control" required value="<?php echo htmlspecialchars($event['title']); ?>">
        </div>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="event_date">Date *</label>
                <input type="date" id="event_date" name="event_date" class="form-control" required value="<?php echo htmlspecialchars($event['event_date']); ?>">
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="event_time">Time *</label>
                <input type="time" id="event_time" name="event_time" class="form-control" required value="<?php echo htmlspecialchars($event['event_time']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" class="form-control" required value="<?php echo htmlspecialchars($event['location']); ?>">
        </div>

        <div class="form-group">
            <label for="eco_pledge">Primary Eco Pledge *</label>
            <select id="eco_pledge" name="eco_pledge" class="form-control" required>
                <option value="zero-waste" <?php echo $event['eco_impact_pledge'] == 'zero-waste' ? 'selected' : ''; ?>>Zero Waste Event</option>
                <option value="carbon-neutral" <?php echo $event['eco_impact_pledge'] == 'carbon-neutral' ? 'selected' : ''; ?>>Carbon Neutral</option>
                <option value="renewable-energy" <?php echo $event['eco_impact_pledge'] == 'renewable-energy' ? 'selected' : ''; ?>>100% Renewable Energy</option>
                <option value="local-sourcing" <?php echo $event['eco_impact_pledge'] == 'local-sourcing' ? 'selected' : ''; ?>>Local Sourcing Only</option>
                <option value="plant-based" <?php echo $event['eco_impact_pledge'] == 'plant-based' ? 'selected' : ''; ?>>100% Plant-Based Catering</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="carbon_offset">Estimated Carbon Offset (kg)</label>
            <input type="number" id="carbon_offset" name="carbon_offset" class="form-control" min="0" value="<?php echo htmlspecialchars($event['carbon_offset_kg']); ?>">
        </div>

        <div class="form-group">
            <label for="description">Event Description *</label>
            <textarea id="description" name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
            <a href="dashboard.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
