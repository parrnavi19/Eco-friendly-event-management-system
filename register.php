<?php
require_once __DIR__ . '/header.php';

if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($username) || empty($email) || empty($password)) {
    $error = "All fields are required.";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters.";
  } else {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
      $error = "That username or email is already taken.";
    } else {
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
      if ($stmt->execute([$username, $email, $hash])) {
        session_regenerate_id(true);
        $_SESSION['user_id']  = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        header("Location: dashboard.php"); exit;
      } else {
        $error = "Registration failed. Please try again.";
      }
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
      <h2>Start your eco journey today.</h2>
      <p>Join thousands of organisers creating sustainable events that reduce waste, offset carbon, and build greener communities.</p>
      <div class="form-features">
        <div class="form-feature"><div class="form-feature-dot"></div>Free to join &amp; create events</div>
        <div class="form-feature"><div class="form-feature-dot"></div>Choose from 5 eco-pledge categories</div>
        <div class="form-feature"><div class="form-feature-dot"></div>Track your total carbon offset impact</div>
      </div>
    </div>
  </div>
  <div class="form-page-right">
    <div class="form-container">
      <h2>Create Account</h2>
      <p class="form-subtitle">Already have an account? <a href="login.php" class="form-link">Sign in</a></p>

      <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form action="register.php" method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" required
                 placeholder="greenorganiser42"
                 value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" required
                 placeholder="you@example.com"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="password">Password <span style="color:var(--text-light);font-weight:400">(min. 6 chars)</span></label>
          <input type="password" id="password" name="password" class="form-control" required minlength="6" placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;padding:.8rem">Create Free Account</button>
      </form>

      <p style="text-align:center;font-size:.78rem;color:var(--text-light);margin-top:1.25rem">
        By registering you agree to use the platform responsibly and support sustainable event practices.
      </p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
