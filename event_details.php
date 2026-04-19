<?php
require_once 'header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT e.*, u.username as organizer FROM events e JOIN users u ON e.organizer_id = u.id WHERE e.id = ?");
$stmt->execute([$_GET['id']]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "<div class='alert alert-error'>Event not found.</div>";
    require_once 'footer.php';
    exit;
}
?>

<div style="background: var(--surface-color); padding: 3rem; border-radius: var(--border-radius); box-shadow: var(--shadow-lg); max-width: 800px; margin: 0 auto; animation: fadeIn 0.5s ease-out;">
    <div class="eco-badge" style="font-size: 1rem; margin-bottom: 1.5rem;">🌿 <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $event['eco_impact_pledge']))); ?></div>
    
    <h1 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($event['title']); ?></h1>
    
    <div style="display: flex; flex-wrap: wrap; gap: 2rem; margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 2rem;">
        <div>
            <h4 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Date & Time</h4>
            <p style="font-size: 1.1rem; font-weight: 600;">📅 <?php echo htmlspecialchars($event['event_date']); ?> at <?php echo htmlspecialchars($event['event_time']); ?></p>
        </div>
        <div>
            <h4 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Location</h4>
            <p style="font-size: 1.1rem; font-weight: 600;">📍 <?php echo htmlspecialchars($event['location']); ?></p>
        </div>
        <div>
            <h4 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Organizer</h4>
            <p style="font-size: 1.1rem; font-weight: 600;">👤 <?php echo htmlspecialchars($event['organizer']); ?></p>
        </div>
        <div>
            <h4 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Carbon Offset</h4>
            <p style="font-size: 1.1rem; font-weight: 600; color: var(--primary-color);">☁️ <?php echo htmlspecialchars($event['carbon_offset_kg']); ?> kg CO2e</p>
        </div>
    </div>
    
    <div style="margin-bottom: 3rem;">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem;">About This Event</h3>
        <p style="font-size: 1.1rem; line-height: 1.8; color: #444;">
            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
        </p>
    </div>
    
    <div style="text-align: center;">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $event['organizer_id']): ?>
            <!-- Placeholder for registration functionality -->
            <button class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 3rem;">Pledge to Attend</button>
        <?php elseif (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $event['organizer_id']): ?>
            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-secondary">Edit This Event</a>
        <?php else: ?>
            <p><a href="login.php" style="color: var(--primary-color); font-weight: 600;">Login</a> to register for this event and track your eco-impact.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
