<?php
require_once 'config.php';
require_once 'includes/functions.php';

$products = getAllProducts($pdo);
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COCO - The Signature Collection</title>
  <meta name="description" content="Explore the signature fragrance collection of COCO. Pure elegance, classic scents, and modern luxury.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- CSS Stylesheet (unchanged) -->
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="collection-page-container">
    <!-- Top Navigation -->
    <header class="header collection-header">
      <a href="index.php" class="logo">COCO</a>
      <nav class="nav">
        <a href="collection.php" class="nav-link active">Product</a>
        <a href="index.php#story" class="nav-link">Our Story</a>
        <a href="index.php#event" class="nav-link">New and Event</a>
      </nav>
      <div class="nav-actions">
        <button class="icon-btn" aria-label="Search" id="search-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </button>
        <a href="add-bag.php" class="icon-btn" aria-label="Shopping Bag" id="bag-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        </a>
        <?php if ($user): ?>
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

    <!-- Collection Intro Header -->
    <section class="collection-intro animate-up">
      <h1 class="collection-title">The Signature Collection</h1>
      <p class="collection-subtitle">Discover our highly curated scents, crafted for absolute night-time allure and timeless elegance.</p>
    </section>

    <!-- Luxury Category Filters -->
    <section class="filters-section animate-up delay-1">
      <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">All Fragrances</button>
        <button class="filter-tab" data-filter="classic">Classic</button>
        <button class="filter-tab" data-filter="floral">Floral</button>
        <button class="filter-tab" data-filter="noir">Noir</button>
        <button class="filter-tab" data-filter="fresh">Fresh</button>
        <button class="filter-tab" data-filter="woody">Woody</button>
      </div>
      <div class="collection-count">
        Showing <span id="visible-count"><?= count($products) ?></span> fragrances
      </div>
    </section>

    <!-- Product Grid: rendered from the products table -->
    <main class="collection-grid-container animate-up delay-2">
      <div class="product-grid" id="product-grid-element">
        <?php foreach ($products as $p): ?>
        <div class="product-item-card" data-category="<?= htmlspecialchars($p['category']) ?>">
          <div class="product-card-img-wrapper">
            <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?> perfume bottle" class="product-card-img">
            <?php if (!empty($p['badge'])): ?>
              <span class="product-badge"><?= htmlspecialchars($p['badge']) ?></span>
            <?php endif; ?>
            <button class="add-to-bag-overlay" aria-label="Add to Bag" data-id="<?= $p['id'] ?>">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
              <span>Add to Bag</span>
            </button>
          </div>
          <div class="product-card-info">
            <div class="product-meta">
              <span class="product-cat"><?= htmlspecialchars(ucfirst($p['category'])) ?></span>
              <span class="product-price"><?= fmtInr($p['price']) ?></span>
            </div>
            <h2 class="product-title"><?= htmlspecialchars($p['name']) ?></h2>
            <p class="product-notes"><?= htmlspecialchars($p['notes']) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </main>

    <!-- Signature Footer -->
    <footer class="collection-footer animate-up delay-3">
      <div class="footer-brand">COCO</div>
      <div class="footer-links">
        <a href="#privacy">Privacy Policy</a>
        <a href="#terms">Terms of Service</a>
        <a href="#support">Customer Care</a>
      </div>
      <p class="footer-copy">&copy; 2026 COCO Paris. All rights reserved.</p>
    </footer>
  </div>

  <!-- Javascript Logic -->
  <script src="main.js"></script>
</body>
</html>
