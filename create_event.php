<?php
require_once __DIR__ . '/header.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$error = '';
$pledge_options = [
  'zero-waste'       => ['label' => 'Zero Waste Event',           'icon' => '♻️'],
  'carbon-neutral'   => ['label' => 'Carbon Neutral',             'icon' => '🌍'],
  'renewable-energy' => ['label' => '100% Renewable Energy',      'icon' => '☀️'],
  'local-sourcing'   => ['label' => 'Local Sourcing Only',         'icon' => '🥦'],
  'plant-based'      => ['label' => '100% Plant-Based Catering',  'icon' => '🌱'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title         = trim($_POST['title'] ?? '');
  $description   = trim($_POST['description'] ?? '');
  $event_date    = $_POST['event_date'] ?? '';
  $event_time    = $_POST['event_time'] ?? '';
  $location      = trim($_POST['location'] ?? '');
  $eco_pledge    = $_POST['eco_pledge'] ?? 'zero-waste';
  $carbon_offset = (int)($_POST['carbon_offset'] ?? 0);

  if (empty($title) || empty($description) || empty($event_date) || empty($event_time) || empty($location)) {
    $error = "Please fill in all required fields.";
  } else {
    $stmt = $pdo->prepare(
      "INSERT INTO events (organizer_id, title, description, event_date, event_time, location, eco_impact_pledge, carbon_offset_kg)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if ($stmt->execute([$_SESSION['user_id'], $title, $description, $event_date, $event_time, $location, $eco_pledge, $carbon_offset])) {
      header("Location: dashboard.php"); exit;
    } else {
      $error = "Failed to create event. Please try again.";
    }
  }
}
?>

<a href="dashboard.php" class="back-link">← Back to Dashboard</a>

<div class="wide-form-container">
  <div class="wide-form-header">
    <h2>Create Eco-Friendly Event</h2>
    <p>Fill in the details below. Fields marked * are required.</p>
  </div>
  <div class="wide-form-body">
    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="create_event.php" method="POST">
      <div class="form-group">
        <label for="title">Event Title *</label>
        <input type="text" id="title" name="title" class="form-control" required
               placeholder="E.g., Community Beach Clean-up"
               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="event_date">Date *</label>
          <input type="date" id="event_date" name="event_date" class="form-control" required
                 value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="event_time">Time *</label>
          <input type="time" id="event_time" name="event_time" class="form-control" required
                 value="<?php echo htmlspecialchars($_POST['event_time'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="location">Location *</label>
        <input type="text" id="location" name="location" class="form-control" required
               placeholder="E.g., Juhu Beach, Mumbai"
               value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="eco_pledge">Eco Pledge *</label>
          <select id="eco_pledge" name="eco_pledge" class="form-control" required>
            <?php foreach ($pledge_options as $val => $opt): ?>
              <option value="<?php echo $val; ?>" <?php echo (($_POST['eco_pledge'] ?? '') === $val) ? 'selected' : ''; ?>>
                <?php echo $opt['icon'] . ' ' . $opt['label']; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="carbon_offset">Estimated Carbon Offset (kg)</label>
          <input type="number" id="carbon_offset" name="carbon_offset" class="form-control"
                 value="<?php echo (int)($_POST['carbon_offset'] ?? 0); ?>" min="0">
          <p class="form-hint">Estimated CO₂ emissions prevented by this event.</p>
        </div>
      </div>

      <div class="form-group">
        <label for="description">Event Description *</label>
        <textarea id="description" name="description" class="form-control" rows="5" required
                  placeholder="Describe your event, its sustainability goals, what attendees can expect…"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary" style="flex:1">Create Event</button>
        <a href="dashboard.php" class="btn btn-ghost" style="flex:1;text-align:center">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
