<?php
require_once __DIR__ . '/header.php';

if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  if (empty($email) || empty($password)) {
    $error = "Both fields are required.";
  } else {
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
      session_regenerate_id(true);
      $_SESSION['user_id']  = $user['id'];
      $_SESSION['username'] = $user['username'];
      header("Location: dashboard.php"); exit;
    } else {
      $error = "Invalid email or password.";
    }
  }
}
?>

<div class="form-page">
  <div class="form-page-left">
    <div class="form-page-left-inner">
      <div class="logo" style="margin-bottom:2rem">
        <div class="logo-icon">🌱</div>
        <span class="logo-text" style="color:white">EcoEvents</span>
      </div>
      <h2>Welcome back to the green side.</h2>
      <p>Log in to manage your eco-friendly events, track your carbon offsets, and inspire your community.</p>
      <div class="form-features">
        <div class="form-feature"><div class="form-feature-dot"></div>Manage your events in one place</div>
        <div class="form-feature"><div class="form-feature-dot"></div>Track cumulative carbon offset</div>
        <div class="form-feature"><div class="form-feature-dot"></div>Connect with eco-conscious attendees</div>
      </div>
    </div>
  </div>
  <div class="form-page-right">
    <div class="form-container">
      <h2>Sign In</h2>
      <p class="form-subtitle">Don't have an account? <a href="register.php" class="form-link">Register free</a></p>

      <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" required
                 placeholder="you@example.com"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;padding:.8rem">Sign In</button>
      </form>

      <div class="form-divider">or</div>
      <p style="text-align:center;font-size:.875rem;color:var(--text-light)">
        New here? <a href="register.php" class="form-link">Create a free account</a>
      </p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
