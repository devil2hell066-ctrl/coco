<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Pull the two hero products from the database instead of hardcoding them
$heroNoir = getProduct($pdo, 1); // COCO Noir
$heroGold = getProduct($pdo, 2); // COCO Gold
$cartCount = getCartCount($pdo);
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COCO - Wrap Yourself in Noir Elegance</title>
  <meta name="description" content="A seductive blend of grapefruit, rose, oud, and amber designed to evoke night-time allure and timeless elegance.">

  <!-- Preconnect to Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Google Fonts: Cinzel (luxurious serif) and Inter (sleek sans-serif) -->
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- CSS Stylesheet (unchanged) -->
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="app-container">
    <!-- Left Panel: Content, Navigation, and UI Controls -->
    <main class="left-panel">
      <!-- Top Navigation -->
      <header class="header">
        <a href="index.php" class="logo">COCO</a>
        <nav class="nav">
          <a href="collection.php" class="nav-link">Product</a>
          <a href="#story" class="nav-link">Our Story</a>
          <a href="#event" class="nav-link">New and Event</a>
          <a href="add-bag.php" class="nav-link">Add Cart</a>
        </nav>
        <div class="nav-actions">
          <button class="icon-btn" aria-label="Search" id="search-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </button>
          <a href="add-bag.php" class="icon-btn" aria-label="Shopping Bag" id="bag-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
          </a>
          <?php if ($user): ?>
            <span class="login-btn" style="cursor:default;">Hi, <?= htmlspecialchars($user['name']) ?></span>
            <a href="logout.php" class="login-btn" id="login-btn">Logout</a>
          <?php else: ?>
            <a href="login.php" class="login-btn" id="login-btn">Login</a>
          <?php endif; ?>
          <!-- Mobile Menu Toggle Button -->
          <button class="menu-toggle" aria-label="Toggle Menu" id="menu-toggle-btn">
            <span></span>
            <span></span>
          </button>
        </div>
      </header>

      <!-- Main Hero Content -->
      <section class="hero-content">
        <h1 class="hero-title animate-up">Wrap Yourself <br>in Noir Elegance</h1>
        <p class="hero-subtitle animate-up delay-1">
          A seductive blend of grapefruit, rose, oud, and amber—designed to evoke night-time allure and timeless elegance.
        </p>

        <div class="hero-actions animate-up delay-2">
          <a href="collection.php" class="collections-btn">
            <span>View Our Collections</span>
          </a>
          <a href="collection.php" class="arrow-btn" aria-label="Explore collections">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
          </a>
        </div>
      </section>

      <!-- Bottom Product Card Overlay & Slider Nav -->
      <section class="bottom-section">
        <!-- Floating Product Previews (Interactive Slider Cards) -->
        <div class="product-cards-container animate-up delay-3">
          <!-- Card 1 (Active) - pulled from products table -->
          <div class="product-card active" id="card-coco-noir" data-index="1">
            <div class="card-details">
              <span class="card-tag"><?= htmlspecialchars(strtoupper($heroNoir['name'])) ?></span>
              <p class="card-desc">Explore the <?= htmlspecialchars($heroNoir['name']) ?> perfume</p>
              <div class="card-price">
                <span class="current-price"><?= fmtInr($heroNoir['original_price']) ?></span>
                <span class="original-price"><?= fmtInr($heroNoir['price']) ?></span>
                <button class="add-to-cart-btn" data-id="<?= $heroNoir['id'] ?>" data-name="<?= htmlspecialchars($heroNoir['name']) ?>" data-price="<?= $heroNoir['price'] ?>" data-image="<?= htmlspecialchars($heroNoir['image']) ?>">Add to Cart</button>
              </div>
            </div>
            <div class="card-img-wrapper">
              <img src="<?= htmlspecialchars($heroNoir['image']) ?>" alt="COCO Noir perfume bottle" class="card-img">
            </div>
          </div>

          <!-- Card 2 (Background stacked) -->
          <div class="product-card" id="card-coco-mademoiselle" data-index="2">
            <div class="card-details">
              <span class="card-tag"><?= htmlspecialchars(strtoupper($heroGold['name'])) ?></span>
              <p class="card-desc">Warm amber elegance</p>
              <div class="card-price">
                <span class="current-price"><?= fmtInr($heroGold['price']) ?></span>
                <span class="original-price"><?= fmtInr($heroGold['original_price']) ?></span>
                <button class="add-to-cart-btn" data-id="<?= $heroGold['id'] ?>" data-name="<?= htmlspecialchars($heroGold['name']) ?>" data-price="<?= $heroGold['price'] ?>" data-image="<?= htmlspecialchars($heroGold['image']) ?>">Add to Cart</button>
              </div>
            </div>
            <div class="card-img-wrapper">
              <img src="<?= htmlspecialchars($heroGold['image']) ?>" alt="COCO Gold perfume bottle" class="card-img">
            </div>
          </div>
        </div>

        <!-- Slider Progress Indicator and Controls -->
        <div class="slider-controls animate-up delay-4">
          <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progress-fill"></div>
          </div>
          <div class="slider-arrows">
            <button class="slider-arrow prev" aria-label="Previous product" id="prev-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            </button>
            <button class="slider-arrow next" aria-label="Next product" id="next-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
          </div>
        </div>
      </section>
    </main>

    <!-- Right Panel: Full-height visual layout containing the generated hero perfume photography -->
    <aside class="right-panel">
      <div class="hero-image-wrapper">
        <img src="assets/hero.jpg" alt="Hands holding a bottle of COCO Noir" class="hero-image" id="hero-bg-img">
        <div class="hero-image-overlay"></div>
      </div>
      <!-- Right Side pagination details -->
      <div class="right-pagination">
        <span class="active-num" id="current-slide-num">01</span>
        <span class="page-line"></span>
        <span class="total-num">02</span>
      </div>
    </aside>
  </div>

  <!-- Javascript Logic -->
  <script src="main.js"></script>
</body>
</html>
