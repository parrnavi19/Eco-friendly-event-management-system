<?php
require_once 'header.php';

// Fetch all events
$stmt = $pdo->query("SELECT e.*, u.username as organizer FROM events e JOIN users u ON e.organizer_id = u.id ORDER BY e.event_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
    <h1>Welcome to EcoEvents</h1>
    <p>Join us in promoting <strong>Sustainable Cities and Communities (SDG 11)</strong> by participating in and organizing eco-friendly events. Discover events that pledge zero waste, local sourcing, and carbon neutrality.</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn btn-primary">Start Organizing Events</a>
    <?php else: ?>
        <a href="create_event.php" class="btn btn-primary">Create an Event</a>
    <?php endif; ?>
</section>

<h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Upcoming Eco-Friendly Events</h2>

<?php if (count($events) > 0): ?>
    <div class="events-grid">
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <div class="eco-badge">🌿 <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $event['eco_impact_pledge']))); ?></div>
                <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                
                <div class="event-meta">
                    <strong>📅 Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?> at <?php echo htmlspecialchars($event['event_time']); ?>
                </div>
                <div class="event-meta">
                    <strong>📍 Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
                </div>
                <div class="event-meta">
                    <strong>👤 Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?>
                </div>
                <div class="event-meta">
                    <strong>☁️ Carbon Offset:</strong> <?php echo htmlspecialchars($event['carbon_offset_kg']); ?> kg
                </div>
                
                <p style="margin-top: 1rem; margin-bottom: 1.5rem; color: var(--text-secondary);">
                    <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 100))) . (strlen($event['description']) > 100 ? '...' : ''); ?>
                </p>
                
                <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-secondary">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-success">No events found. Be the first to create an eco-friendly event!</div>
<?php endif; ?>

<?php
require_once 'footer.php';
?>
