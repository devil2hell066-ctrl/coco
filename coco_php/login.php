<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userRow = $stmt->fetch();

        if ($userRow && password_verify($password, $userRow['password'])) {
            $_SESSION['user_id']    = $userRow['id'];
            $_SESSION['user_name']  = $userRow['name'];
            $_SESSION['user_email'] = $userRow['email'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COCO - Sign In</title>
  <meta name="description" content="Sign in to your COCO account. Experience night-time allure and timeless elegance.">

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
          <h1 class="form-title">Welcome back</h1>
          <p class="form-subtitle">Enter your credentials to access your account</p>
        </div>

        <?php if ($error): ?>
          <p class="error-message" style="display:block;text-align:center;margin-bottom:1rem;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form class="login-form animate-up delay-1" id="login-form-element" method="post" action="login.php" novalidate>
          <!-- Email Input Group -->
          <div class="input-group">
            <input type="email" id="email" name="email" required placeholder=" " autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label for="email">Email Address</label>
            <span class="error-message" id="email-error"></span>
          </div>

          <!-- Password Input Group with show/hide toggle -->
          <div class="input-group">
            <input type="password" id="password" name="password" required placeholder=" " autocomplete="current-password">
            <label for="password">Password</label>
            <button type="button" class="password-toggle" id="password-toggle-btn" aria-label="Show password">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </button>
            <span class="error-message" id="password-error"></span>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="form-options">
            <label class="checkbox-container">
              <input type="checkbox" id="remember-me">
              <span class="checkmark"></span>
              <span class="checkbox-label">Remember me</span>
            </label>
            <a href="#forgot" class="forgot-link">Forgot password?</a>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="submit-btn">
            <span>Sign In</span>
          </button>
        </form>

        <div class="social-divider animate-up delay-2">
          <span>or sign in with</span>
        </div>

        <div class="social-actions animate-up delay-2">
          <button class="social-btn google-btn" type="button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.24 10.285V13.4h6.887c-.275 1.565-1.88 4.604-6.887 4.604-4.33 0-7.859-3.578-7.859-8s3.53-8 7.859-8c2.46 0 4.105 1.025 5.047 1.926l2.427-2.334C17.955 2.192 15.34 1 12.24 1 6.033 1 1 6.033 1 12.24s5.033 11.24 11.24 11.24c6.478 0 10.793-4.537 10.793-10.985 0-.737-.08-1.303-.177-1.858H12.24z"/></svg>
            <span>Google</span>
          </button>
          <button class="social-btn apple-btn" type="button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.17c.66-.81 1.11-1.93.99-3.06-1 .04-2.2.67-2.92 1.49-.62.71-1.16 1.85-1.01 2.96 1.12.09 2.26-.57 2.94-1.39z"/></svg>
            <span>Apple</span>
          </button>
        </div>

        <div class="form-footer animate-up delay-3">
          <span>Don't have an account?</span>
          <a href="register.php" class="register-link">Create one now</a>
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
