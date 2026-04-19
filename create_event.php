
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $eco_pledge = $_POST['eco_pledge'];
    $carbon_offset = (int)$_POST['carbon_offset'];

    if (empty($title) || empty($description) || empty($event_date) || empty($event_time) || empty($location)) {
        $error = "All required fields must be filled.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (organizer_id, title, description, event_date, event_time, location, eco_impact_pledge, carbon_offset_kg) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $title, $description, $event_date, $event_time, $location, $eco_pledge, $carbon_offset])) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Failed to create event.";
        }
    }
}
?>

<div class="form-container" style="max-width: 700px;">
    <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Create Eco-Friendly Event</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="create_event.php" method="POST">
        <div class="form-group">
            <label for="title">Event Title *</label>
            <input type="text" id="title" name="title" class="form-control" required placeholder="E.g., Community Beach Clean-up">
        </div>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="event_date">Date *</label>
                <input type="date" id="event_date" name="event_date" class="form-control" required>
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="event_time">Time *</label>
                <input type="time" id="event_time" name="event_time" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" class="form-control" required placeholder="E.g., Central Park">
        </div>

        <div class="form-group">
            <label for="eco_pledge">Primary Eco Pledge *</label>
            <select id="eco_pledge" name="eco_pledge" class="form-control" required>
                <option value="zero-waste">Zero Waste Event</option>
                <option value="carbon-neutral">Carbon Neutral</option>
                <option value="renewable-energy">100% Renewable Energy</option>
                <option value="local-sourcing">Local Sourcing Only</option>
                <option value="plant-based">100% Plant-Based Catering</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="carbon_offset">Estimated Carbon Offset (kg)</label>
            <input type="number" id="carbon_offset" name="carbon_offset" class="form-control" value="0" min="0">
            <small style="color: var(--text-secondary);">Estimated emissions prevented.</small>
        </div>

        <div class="form-group">
            <label for="description">Event Description *</label>
            <textarea id="description" name="description" class="form-control" rows="5" required placeholder="Describe your event and its sustainability goals..."></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Create Event</button>
            <a href="dashboard.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
