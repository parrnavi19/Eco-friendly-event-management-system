<?php
require_once __DIR__ . '/header.php';

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }

$event_id = (int)$_GET['id'];

$stmt = $pdo->prepare(
  "SELECT e.*, u.username AS organizer FROM events e
   JOIN users u ON e.organizer_id = u.id WHERE e.id = ?"
);
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
  echo "<div class='alert alert-error'>Event not found.</div>";
  require_once __DIR__ . '/footer.php';
  exit;
}

$pledge_labels = [
  'zero-waste'       => 'Zero Waste',
  'carbon-neutral'   => 'Carbon Neutral',
  'renewable-energy' => 'Renewable Energy',
  'local-sourcing'   => 'Local Sourcing',
  'plant-based'      => 'Plant-Based',
];

// Registration count
$reg_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
$reg_count_stmt->execute([$event_id]);
$reg_count = (int)$reg_count_stmt->fetchColumn();

// Is the current user registered?
$is_registered = false;
if (isset($_SESSION['user_id'])) {
  $check = $pdo->prepare("SELECT 1 FROM registrations WHERE event_id = ? AND user_id = ?");
  $check->execute([$event_id, $_SESSION['user_id']]);
  $is_registered = (bool)$check->fetchColumn();
}

// Flash message handling
$flash = '';
$flash_type = 'success';
if (isset($_GET['msg'])) {
  switch ($_GET['msg']) {
    case 'joined': $flash = '🌱 You\'re registered for this event!'; break;
    case 'left':   $flash = 'You have withdrawn your registration.'; $flash_type = 'error'; break;
    case 'own':    $flash = 'You can\'t register for your own event.'; $flash_type = 'error'; break;
    case 'error':  $flash = 'Something went wrong. Please try again.'; $flash_type = 'error'; break;
  }
}
?>

<a href="index.php" class="back-link">← Back to Events</a>

<?php if ($flash): ?>
  <div class="alert alert-<?php echo $flash_type; ?>" style="margin-bottom:1.25rem"><?php echo htmlspecialchars($flash); ?></div>
<?php endif; ?>

<!-- HERO -->
<div class="event-detail-hero">
  <span class="eco-badge">🌿 <?php echo htmlspecialchars($pledge_labels[$event['eco_impact_pledge']] ?? ucfirst(str_replace('-',' ',$event['eco_impact_pledge']))); ?></span>
  <h1><?php echo htmlspecialchars($event['title']); ?></h1>
  <div class="event-detail-meta-row">
    <div class="event-detail-meta-item">
      <span class="event-detail-meta-label">📅 Date</span>
      <span class="event-detail-meta-value"><?php echo htmlspecialchars($event['event_date']); ?></span>
    </div>
    <div class="event-detail-meta-item">
      <span class="event-detail-meta-label">⏰ Time</span>
      <span class="event-detail-meta-value"><?php echo htmlspecialchars($event['event_time']); ?></span>
    </div>
    <div class="event-detail-meta-item">
      <span class="event-detail-meta-label">📍 Location</span>
      <span class="event-detail-meta-value"><?php echo htmlspecialchars($event['location']); ?></span>
    </div>
    <?php if ($event['carbon_offset_kg'] > 0): ?>
    <div class="event-detail-meta-item">
      <span class="event-detail-meta-label">☁️ CO₂ Offset</span>
      <span class="event-detail-meta-value"><?php echo (int)$event['carbon_offset_kg']; ?> kg</span>
    </div>
    <?php endif; ?>
    <div class="event-detail-meta-item">
      <span class="event-detail-meta-label">👥 Attendees</span>
      <span class="event-detail-meta-value"><?php echo $reg_count; ?> registered</span>
    </div>
  </div>
</div>

<!-- BODY -->
<div class="event-detail-body">
  <div class="event-detail-main">
    <h3>About This Event</h3>
    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
  </div>

  <div class="event-detail-sidebar">
    <!-- Organiser -->
    <div class="sidebar-card">
      <h4>Organiser</h4>
      <div class="organizer-info">
        <div class="organizer-avatar"><?php echo strtoupper(substr($event['organizer'],0,1)); ?></div>
        <div>
          <div class="organizer-name"><?php echo htmlspecialchars($event['organizer']); ?></div>
          <div class="organizer-role">Event Organiser</div>
        </div>
      </div>
    </div>

    <!-- Eco Impact -->
    <div class="sidebar-card">
      <h4>Eco Impact</h4>
      <div style="display:flex;flex-direction:column;gap:.75rem">
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:.85rem;color:var(--text-mid)">Pledge</span>
          <span class="table-eco-badge">🌿 <?php echo htmlspecialchars($pledge_labels[$event['eco_impact_pledge']] ?? $event['eco_impact_pledge']); ?></span>
        </div>
        <?php if ($event['carbon_offset_kg'] > 0): ?>
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:.85rem;color:var(--text-mid)">CO₂ Offset</span>
          <strong style="color:var(--forest)"><?php echo (int)$event['carbon_offset_kg']; ?> kg</strong>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- CTA -->
    <div class="sidebar-card" style="background:var(--forest);border-color:var(--forest)">
      <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $event['organizer_id']): ?>
        <h4 style="color:rgba(255,255,255,.6)">Your Event</h4>
        <p style="font-size:.875rem;color:rgba(255,255,255,.75);margin-bottom:1rem">Make changes to your event details.</p>
        <a href="edit_event.php?id=<?php echo (int)$event['id']; ?>" class="btn btn-hero-primary" style="width:100%;text-align:center">Edit Event</a>
      <?php elseif (isset($_SESSION['user_id'])): ?>
        <?php if ($is_registered): ?>
          <h4 style="color:rgba(255,255,255,.6)">You're Going! 🎉</h4>
          <p style="font-size:.875rem;color:rgba(255,255,255,.75);margin-bottom:1rem">You're registered for this event. We'll see you there!</p>
          <form action="register_event.php" method="POST">
            <input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
            <input type="hidden" name="action" value="leave">
            <button type="submit" class="btn btn-hero-primary" style="width:100%;background:rgba(255,255,255,.15);color:white;border:1.5px solid rgba(255,255,255,.4)"
                    onclick="return confirm('Withdraw your registration?')">Withdraw Registration</button>
          </form>
        <?php else: ?>
          <h4 style="color:rgba(255,255,255,.6)">Ready to Attend?</h4>
          <p style="font-size:.875rem;color:rgba(255,255,255,.75);margin-bottom:1rem">Join <?php echo $reg_count; ?> other<?php echo $reg_count !== 1 ? 's' : ''; ?> committed to a greener future.</p>
          <form action="register_event.php" method="POST">
            <input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
            <input type="hidden" name="action" value="join">
            <button type="submit" class="btn btn-hero-primary" style="width:100%">Register for Event 🌱</button>
          </form>
        <?php endif; ?>
      <?php else: ?>
        <h4 style="color:rgba(255,255,255,.6)">Join This Event</h4>
        <p style="font-size:.875rem;color:rgba(255,255,255,.75);margin-bottom:1rem">Sign in to register your attendance and make a difference.</p>
        <a href="login.php" class="btn btn-hero-primary" style="width:100%;text-align:center">Sign In to Register</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
