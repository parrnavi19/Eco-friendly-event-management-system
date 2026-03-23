<?php
require_once __DIR__ . '/header.php';

$search = trim($_GET['search'] ?? '');
$filter = $_GET['filter'] ?? 'all';

// Build query with optional search — includes attendee count
if ($search !== '') {
  $stmt = $pdo->prepare(
    "SELECT e.*, u.username AS organizer,
            (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS attendee_count
     FROM events e
     JOIN users u ON e.organizer_id = u.id
     WHERE e.title LIKE ? OR e.location LIKE ? OR e.description LIKE ?
     ORDER BY e.event_date DESC"
  );
  $like = "%$search%";
  $stmt->execute([$like, $like, $like]);
} else {
  $stmt = $pdo->query(
    "SELECT e.*, u.username AS organizer,
            (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS attendee_count
     FROM events e
     JOIN users u ON e.organizer_id = u.id
     ORDER BY e.event_date DESC"
  );
}
$events = $stmt->fetchAll();

// Stats
$totalEvents   = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCarbon   = $pdo->query("SELECT COALESCE(SUM(carbon_offset_kg),0) FROM events")->fetchColumn();

$pledge_labels = [
  'zero-waste'       => 'Zero Waste',
  'carbon-neutral'   => 'Carbon Neutral',
  'renewable-energy' => 'Renewable Energy',
  'local-sourcing'   => 'Local Sourcing',
  'plant-based'      => 'Plant-Based',
];
?>

<?php if (!$search): ?>
<!-- HERO -->
<section class="hero">
  <div class="hero-leaf">🍃</div>
  <div class="hero-leaf">🌿</div>
  <div class="hero-badge">🌍 Aligned with SDG 11 &amp; 12</div>
  <h1>Events That<br><em>Heal the Planet</em></h1>
  <p>Discover, join, and organise eco-friendly events that make a real difference to our communities and environment.</p>
  <div class="hero-actions">
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="register.php" class="btn btn-hero-primary">Start Organising</a>
      <a href="#events" class="btn btn-hero-secondary">Browse Events ↓</a>
    <?php else: ?>
      <a href="create_event.php" class="btn btn-hero-primary">+ Create Event</a>
      <a href="#events" class="btn btn-hero-secondary">Browse Events ↓</a>
    <?php endif; ?>
  </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar">
  <div class="stat-item">
    <div class="stat-number"><?php echo $totalEvents; ?></div>
    <div class="stat-label">Eco Events</div>
  </div>
  <div class="stat-item">
    <div class="stat-number"><?php echo $totalUsers; ?></div>
    <div class="stat-label">Organisers</div>
  </div>
  <div class="stat-item">
    <div class="stat-number"><?php echo number_format($totalCarbon); ?><small style="font-size:1rem">kg</small></div>
    <div class="stat-label">CO₂ Offset</div>
  </div>
  <div class="stat-item">
    <div class="stat-number">5</div>
    <div class="stat-label">Eco Pledges</div>
  </div>
</div>
<?php endif; ?>

<!-- EVENTS SECTION -->
<div id="events">
  <div class="section-header">
    <h2><?php echo $search ? 'Search Results' : 'All Events'; ?></h2>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="create_event.php" class="btn btn-primary btn-sm">+ New Event</a>
    <?php endif; ?>
  </div>

  <?php if ($search): ?>
    <div class="search-results-bar">
      <span>Showing results for <strong>"<?php echo htmlspecialchars($search); ?>"</strong> — <?php echo count($events); ?> event<?php echo count($events) !== 1 ? 's' : ''; ?> found</span>
      <a href="index.php" class="btn btn-ghost btn-sm">✕ Clear</a>
    </div>
  <?php else: ?>
    <!-- FILTER BAR -->
    <div class="filter-bar">
      <span class="filter-label">Filter:</span>
      <button class="filter-btn active" data-filter="all" data-group="pledge">All</button>
      <?php foreach ($pledge_labels as $val => $label): ?>
        <button class="filter-btn" data-filter="<?php echo $val; ?>" data-group="pledge"><?php echo $label; ?></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (count($events) > 0): ?>
    <div class="events-grid">
      <?php foreach ($events as $i => $event): ?>
        <div class="event-card" data-pledge="<?php echo htmlspecialchars($event['eco_impact_pledge']); ?>">
          <div class="event-card-header">
            <span class="eco-badge">🌿 <?php echo htmlspecialchars($pledge_labels[$event['eco_impact_pledge']] ?? ucfirst(str_replace('-', ' ', $event['eco_impact_pledge']))); ?></span>
          </div>
          <div class="event-card-body">
            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
            <div class="event-meta-grid">
              <div class="event-meta-item">
                <span class="event-meta-label">📅 Date</span>
                <span class="event-meta-value"><?php echo htmlspecialchars($event['event_date']); ?></span>
              </div>
              <div class="event-meta-item">
                <span class="event-meta-label">⏰ Time</span>
                <span class="event-meta-value"><?php echo htmlspecialchars($event['event_time']); ?></span>
              </div>
              <div class="event-meta-item">
                <span class="event-meta-label">📍 Location</span>
                <span class="event-meta-value"><?php echo htmlspecialchars($event['location']); ?></span>
              </div>
              <div class="event-meta-item">
                <span class="event-meta-label">👤 Organiser</span>
                <span class="event-meta-value"><?php echo htmlspecialchars($event['organizer']); ?></span>
              </div>
            </div>
            <p class="event-description"><?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 110))) . (strlen($event['description']) > 110 ? '…' : ''); ?></p>
            <?php if ($event['carbon_offset_kg'] > 0): ?>
              <div class="carbon-chip">☁️ <?php echo (int)$event['carbon_offset_kg']; ?> kg CO₂ offset</div>
            <?php endif; ?>
            <?php if ($event['attendee_count'] > 0): ?>
              <div class="carbon-chip" style="background:var(--success-bg);border-color:rgba(39,174,96,.25);color:var(--success)">👥 <?php echo (int)$event['attendee_count']; ?> attending</div>
            <?php endif; ?>
            <a href="event_details.php?id=<?php echo (int)$event['id']; ?>" class="btn btn-secondary btn-sm" style="align-self:flex-start">View Details →</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div id="noResultsMsg" class="empty-state" style="display:none">
      <div class="empty-state-icon">🔍</div>
      <h3>No events match this filter</h3>
      <p>Try a different category or clear the filter.</p>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-state-icon">🌱</div>
      <h3><?php echo $search ? 'No events found' : 'No events yet'; ?></h3>
      <p><?php echo $search ? 'Try different keywords.' : 'Be the first to create an eco-friendly event!'; ?></p>
      <?php if (isset($_SESSION['user_id']) && !$search): ?>
        <a href="create_event.php" class="btn btn-primary">Create First Event</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
