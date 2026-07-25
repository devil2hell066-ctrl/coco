<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $ins->execute([$name, $email, $hash]);
            $success = 'Account created! You can now sign in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COCO - Create Account</title>
  <meta name="description" content="Create your COCO account to enjoy timeless elegance and exclusive fragrances.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- CSS Stylesheet (unchanged) -->
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="app-container">
    <main class="left-panel form-panel">
      <header class="header">
        <a href="index.php" class="logo">COCO</a>
        <a href="index.php" class="back-link">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
          <span>Back to Home</span>
        </a>
      </header>

      <section class="form-container">
        <div class="form-header animate-up">
          <h1 class="form-title">Create your account</h1>
          <p class="form-subtitle">Join COCO for a world of timeless elegance</p>
        </div>

        <?php if ($error): ?>
          <p class="error-message" style="display:block;text-align:center;margin-bottom:1rem;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
          <p class="form-subtitle" style="display:block;text-align:center;margin-bottom:1rem;color:#2e7d32;"><?= htmlspecialchars($success) ?> <a href="login.php" class="register-link">Sign in</a></p>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form class="login-form animate-up delay-1" method="post" action="register.php" novalidate>
          <div class="input-group">
            <input type="text" id="name" name="name" required placeholder=" " value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            <label for="name">Full Name</label>
          </div>

          <div class="input-group">
            <input type="email" id="email" name="email" required placeholder=" " value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label for="email">Email Address</label>
          </div>

          <div class="input-group">
            <input type="password" id="password" name="password" required placeholder=" ">
            <label for="password">Password</label>
          </div>

          <div class="input-group">
            <input type="password" id="confirm_password" name="confirm_password" required placeholder=" ">
            <label for="confirm_password">Confirm Password</label>
          </div>

          <button type="submit" class="submit-btn">
            <span>Create Account</span>
          </button>
        </form>
        <?php endif; ?>

        <div class="form-footer animate-up delay-3">
          <span>Already have an account?</span>
          <a href="login.php" class="register-link">Sign in</a>
        </div>
      </section>

      <footer class="login-footer animate-up delay-4">
        <span>&copy; 2026 COCO Paris. All rights reserved.</span>
      </footer>
    </main>

    <aside class="right-panel">
      <div class="hero-image-wrapper">
        <img src="assets/hero.jpg" alt="COCO Noir luxury aesthetic" class="hero-image">
        <div class="hero-image-overlay"></div>
      </div>
    </aside>
  </div>

  <script src="main.js"></script>
</body>
</html>
