
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="color: var(--primary-color);">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <a href="create_event.php" class="btn btn-primary">+ Create New Event</a>
</div>

<div style="background: var(--surface-color); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm);">
    <h3 style="margin-bottom: 1.5rem;">Your Managed Events</h3>
    
    <?php if (count($my_events) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Eco Pledge</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo htmlspecialchars($event['event_date']) . ' ' . htmlspecialchars($event['event_time']); ?></td>
                            <td><?php echo htmlspecialchars($event['location']); ?></td>
                            <td><span class="eco-badge" style="margin: 0; padding: 0.1rem 0.4rem; font-size: 0.7rem;"><?php echo htmlspecialchars($event['eco_impact_pledge']); ?></span></td>
                            <td class="actions">
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-secondary" style="padding: 0.2rem 0.6rem;">Edit</a>
                                <form action="delete_event.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                    <button type="submit" class="btn btn-primary" style="background-color: var(--error-color); padding: 0.2rem 0.6rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>You haven't created any events yet. <a href="create_event.php" style="color: var(--primary-color);">Create one now!</a></p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
