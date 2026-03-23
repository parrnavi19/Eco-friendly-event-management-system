</main>

<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo">
          <div class="logo-icon">🌱</div>
          <span class="logo-text">EcoEvents</span>
        </div>
        <p>A platform for eco-conscious event organisers and attendees, promoting sustainability through every gathering.</p>
      </div>
      <div class="footer-col">
        <h5>Platform</h5>
        <ul class="footer-links">
          <li><a href="index.php">Browse Events</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="create_event.php">Create Event</a></li>
            <li><a href="dashboard.php">My Dashboard</a></li>
          <?php else: ?>
            <li><a href="register.php">Get Started</a></li>
            <li><a href="login.php">Sign In</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Goals</h5>
        <ul class="footer-links">
          <li><a href="#">SDG 11 — Sustainable Cities</a></li>
          <li><a href="#">SDG 12 — Responsible Consumption</a></li>
          <li><a href="#">Carbon Offset Tracking</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> EcoEvents. All rights reserved.</p>
      <div class="sdg-badges">
        <span class="sdg-badge">SDG 11</span>
        <span class="sdg-badge">SDG 12</span>
      </div>
    </div>
  </div>
</footer>

<div class="toast-container" id="toastContainer"></div>

<script>
// Toast notification system
function showToast(message, icon = '✅') {
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `<span>${icon}</span> ${message}`;
  container.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(8px)'; toast.style.transition = '0.3s'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// Filter buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const group = btn.dataset.group || 'default';
    document.querySelectorAll(`.filter-btn[data-group="${group}"]`).forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const filter = btn.dataset.filter;
    document.querySelectorAll('.event-card[data-pledge]').forEach(card => {
      if (filter === 'all' || card.dataset.pledge === filter) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });

    // Update no-results message
    const grid = document.querySelector('.events-grid');
    if (grid) {
      const visible = grid.querySelectorAll('.event-card:not([style*="display: none"])');
      const empty = document.getElementById('noResultsMsg');
      if (empty) empty.style.display = visible.length === 0 ? '' : 'none';
    }
  });
});

// Stagger card animations
document.querySelectorAll('.event-card').forEach((card, i) => {
  card.style.animationDelay = (i * 0.06) + 's';
});

// Live search: filter cards instantly as user types in nav search
const navSearchInput = document.querySelector('.nav-search input');
if (navSearchInput) {
  navSearchInput.addEventListener('input', function() {
    const query = this.value.trim().toLowerCase();
    if (!query) return; // let normal card rendering show everything

    const cards = document.querySelectorAll('.event-card');
    let visible = 0;
    cards.forEach(card => {
      const text = card.textContent.toLowerCase();
      const match = text.includes(query);
      card.style.display = match ? '' : 'none';
      if (match) visible++;
    });

    const empty = document.getElementById('noResultsMsg');
    if (empty) empty.style.display = visible === 0 ? '' : 'none';
  });
}
</script>
</body>
</html>
