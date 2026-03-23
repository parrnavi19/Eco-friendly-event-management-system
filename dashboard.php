<?php
require_once __DIR__ . '/header.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];

$events = $pdo->prepare("SELECT * FROM events WHERE organizer_id = ? ORDER BY event_date DESC");
$events->execute([$user_id]);
$my_events = $events->fetchAll();

$total_events  = count($my_events);
$total_carbon  = array_sum(array_column($my_events, 'carbon_offset_kg'));
$upcoming      = count(array_filter($my_events, fn($e) => $e['event_date'] >= date('Y-m-d')));

// Events the user has registered for (not their own)
$reg_stmt = $pdo->prepare(
  "SELECT e.*, u.username AS organizer FROM registrations r
   JOIN events e ON r.event_id = e.id
   JOIN users u ON e.organizer_id = u.id
   WHERE r.user_id = ?
   ORDER BY e.event_date DESC"
);
$reg_stmt->execute([$user_id]);
$registered_events = $reg_stmt->fetchAll();

$pledge_labels = [
  'zero-waste'       => 'Zero Waste',
  'carbon-neutral'   => 'Carbon Neutral',
  'renewable-energy' => 'Renewable Energy',
  'local-sourcing'   => 'Local Sourcing',
  'plant-based'      => 'Plant-Based',
];
?>

<div class="page-header">
  <div>
    <h2>My Dashboard</h2>
    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</p>
  </div>
  <a href="create_event.php" class="btn btn-hero-primary btn">+ Create New Event</a>
</div>

<!-- STAT CARDS -->
<div class="dashboard-grid">
  <div class="dash-stat-card">
    <div class="dash-stat-icon">📅</div>
    <div>
      <div class="dash-stat-value"><?php echo $total_events; ?></div>
      <div class="dash-stat-label">Total Events</div>
    </div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon">🚀</div>
    <div>
      <div class="dash-stat-value"><?php echo $upcoming; ?></div>
      <div class="dash-stat-label">Upcoming</div>
    </div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon">☁️</div>
    <div>
      <div class="dash-stat-value"><?php echo number_format($total_carbon); ?></div>
      <div class="dash-stat-label">kg CO₂ Offset</div>
    </div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon">🌿</div>
    <div>
      <div class="dash-stat-value"><?php echo count(array_unique(array_column($my_events, 'eco_impact_pledge'))); ?></div>
      <div class="dash-stat-label">Pledge Types Used</div>
    </div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon">🎟️</div>
    <div>
      <div class="dash-stat-value"><?php echo count($registered_events); ?></div>
      <div class="dash-stat-label">Events Joined</div>
    </div>
  </div>
</div>

<!-- EVENTS TABLE -->
<div class="table-card">
  <div class="table-card-header">
    <h3>Your Events</h3>
    <a href="create_event.php" class="btn btn-primary btn-sm">+ Add Event</a>
  </div>

  <?php if ($total_events > 0): ?>
    <div style="overflow-x:auto">
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>Event</th>
            <th>Date</th>
            <th>Location</th>
            <th>Pledge</th>
            <th>CO₂ Offset</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($my_events as $ev): ?>
            <?php $past = $ev['event_date'] < date('Y-m-d'); ?>
            <tr>
              <td>
                <div class="table-event-title"><?php echo htmlspecialchars($ev['title']); ?></div>
              </td>
              <td><?php echo htmlspecialchars($ev['event_date']); ?> <span style="color:var(--text-light)"><?php echo htmlspecialchars($ev['event_time']); ?></span></td>
              <td><?php echo htmlspecialchars($ev['location']); ?></td>
              <td><span class="table-eco-badge">🌿 <?php echo htmlspecialchars($pledge_labels[$ev['eco_impact_pledge']] ?? $ev['eco_impact_pledge']); ?></span></td>
              <td><?php echo (int)$ev['carbon_offset_kg']; ?> kg</td>
              <td>
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.78rem;font-weight:600;color:<?php echo $past ? 'var(--text-light)' : 'var(--success)'; ?>">
                  <?php echo $past ? '✓ Past' : '● Upcoming'; ?>
                </span>
              </td>
              <td>
                <div class="actions">
                  <a href="event_details.php?id=<?php echo (int)$ev['id']; ?>" class="btn btn-ghost btn-sm">View</a>
                  <a href="edit_event.php?id=<?php echo (int)$ev['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                  <form action="delete_event.php" method="POST" style="display:inline"
                        onsubmit="return confirm('Delete this event? This cannot be undone.')">
                    <input type="hidden" name="id" value="<?php echo (int)$ev['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-state-icon">🌱</div>
      <h3>No events yet</h3>
      <p>Create your first eco-friendly event and start making an impact.</p>
      <a href="create_event.php" class="btn btn-primary">Create Your First Event</a>
    </div>
  <?php endif; ?>
</div>

<!-- REGISTERED EVENTS -->
<div class="table-card" style="margin-top:2rem">
  <div class="table-card-header">
    <h3>🎟️ Events I've Joined</h3>
    <a href="index.php" class="btn btn-ghost btn-sm">Browse More Events</a>
  </div>

  <?php if (count($registered_events) > 0): ?>
    <div style="overflow-x:auto">
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>Event</th>
            <th>Date</th>
            <th>Location</th>
            <th>Pledge</th>
            <th>Organiser</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($registered_events as $ev): ?>
            <?php $past = $ev['event_date'] < date('Y-m-d'); ?>
            <tr>
              <td><div class="table-event-title"><?php echo htmlspecialchars($ev['title']); ?></div></td>
              <td><?php echo htmlspecialchars($ev['event_date']); ?> <span style="color:var(--text-light)"><?php echo htmlspecialchars($ev['event_time']); ?></span></td>
              <td><?php echo htmlspecialchars($ev['location']); ?></td>
              <td><span class="table-eco-badge">🌿 <?php echo htmlspecialchars($pledge_labels[$ev['eco_impact_pledge']] ?? $ev['eco_impact_pledge']); ?></span></td>
              <td><?php echo htmlspecialchars($ev['organizer']); ?></td>
              <td>
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.78rem;font-weight:600;color:<?php echo $past ? 'var(--text-light)' : 'var(--success)'; ?>">
                  <?php echo $past ? '✓ Past' : '● Upcoming'; ?>
                </span>
              </td>
              <td>
                <div class="actions">
                  <a href="event_details.php?id=<?php echo (int)$ev['id']; ?>" class="btn btn-ghost btn-sm">View</a>
                  <form action="register_event.php" method="POST" style="display:inline"
                        onsubmit="return confirm('Withdraw your registration?')">
                    <input type="hidden" name="event_id" value="<?php echo (int)$ev['id']; ?>">
                    <input type="hidden" name="action" value="leave">
                    <button type="submit" class="btn btn-danger btn-sm">Leave</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-state-icon">🗓️</div>
      <h3>No registered events yet</h3>
      <p>Browse eco-friendly events and sign up to make an impact together.</p>
      <a href="index.php" class="btn btn-primary">Browse Events</a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
