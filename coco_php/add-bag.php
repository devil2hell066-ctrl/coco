<?php
require_once 'config.php';
require_once 'includes/functions.php';

$cartItems = getCartItems($pdo);
$cartCount = getCartCount($pdo);
$recommended = getRandomProducts($pdo, 4);
$allProducts = getAllProducts($pdo); // used by add-bag.js for the "Recently Viewed" section

$discountPercent = 0;
$appliedCoupon = $_SESSION['coupon'] ?? '';
$COUPONS = ['WELCOME10' => 10, 'LUXURY20' => 20, 'COCO50' => 50];
if ($appliedCoupon && isset($COUPONS[$appliedCoupon])) {
    $discountPercent = $COUPONS[$appliedCoupon];
}
$totals = cartTotals($pdo, $discountPercent);
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COCO - Add Cart | Premium Shopping Cart & Checkout</title>
  <meta name="description" content="Review your selected COCO luxury fragrances, manage your shopping bag, and complete a secure checkout.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <!-- CSS unchanged -->
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="add-bag.css">
</head>
<body class="add-bag-page">

  <!-- Loading Spinner -->
  <div class="loading-overlay" id="loading-overlay">
    <div class="spinner"></div>
  </div>

  <!-- Header -->
  <header class="ab-header">
    <div class="ab-header-inner">
      <a href="index.php" class="logo">COCO</a>
      <nav class="ab-nav" id="ab-nav">
        <a href="index.php" class="ab-nav-link">Home</a>
        <a href="collection.php" class="ab-nav-link">Collection</a>
        <a href="collection.php" class="ab-nav-link">Best Sellers</a>
        <a href="#" class="ab-nav-link">About</a>
        <a href="#" class="ab-nav-link">Contact</a>
        <a href="add-bag.php" class="ab-nav-link active">Add Cart</a>
        <?php if ($user): ?>
          <a href="logout.php" class="ab-nav-link">Logout</a>
        <?php else: ?>
          <a href="login.php" class="ab-nav-link">Login</a>
        <?php endif; ?>
      </nav>
      <div class="ab-header-actions">
        <a href="add-bag.php" class="ab-cart-icon" id="header-cart-icon" aria-label="Shopping Bag">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
          <span class="cart-count" id="cart-count"><?= $cartCount ?></span>
        </a>
        <button class="ab-menu-toggle" id="ab-menu-toggle" aria-label="Toggle Menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="ab-main">

    <!-- Cart Section -->
    <section class="ab-cart-section" id="cart-section">
      <div class="ab-container">
        <div class="ab-section-header">
          <h1 class="ab-title">Add Cart</h1>
          <p class="ab-subtitle">Review your selected luxury fragrances before checkout.</p>
        </div>

        <div class="ab-cart-layout">
          <!-- Cart Items Column -->
          <div class="ab-cart-items-col">
            <div class="ab-cart-items" id="cart-items-container">
              <?php foreach ($cartItems as $item):
                  $stars = str_repeat('★', floor($item['rating'])) . (fmod($item['rating'], 1) >= 0.5 ? '½' : '');
              ?>
              <div class="ab-cart-item" data-cart-id="<?= $item['cart_id'] ?>">
                <img class="ab-item-img" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <div class="ab-item-info">
                  <span class="ab-item-brand"><?= htmlspecialchars($item['brand']) ?></span>
                  <span class="ab-item-name"><?= htmlspecialchars($item['name']) ?></span>
                  <div class="ab-item-meta">
                    <span class="ab-item-size"><?= htmlspecialchars($item['size']) ?></span>
                    <span><?= htmlspecialchars(ucfirst($item['category'])) ?></span>
                    <span class="ab-item-rating"><?= $stars ?> <?= $item['rating'] ?></span>
                  </div>
                  <div class="ab-item-price-row">
                    <span class="ab-item-price"><?= fmtInr($item['price']) ?></span>
                    <span class="ab-item-original-price"><?= fmtInr($item['original_price']) ?></span>
                    <span class="ab-item-discount"><?= $item['discount'] ?>% OFF</span>
                  </div>
                  <span class="ab-item-stock">Only <?= $item['stock'] ?> left</span>
                  <span class="ab-item-delivery"><?= htmlspecialchars($item['delivery_estimate']) ?></span>
                </div>
                <div class="ab-item-actions">
                  <div class="ab-qty-controls">
                    <button class="ab-qty-btn ab-qty-minus" data-cart-id="<?= $item['cart_id'] ?>">−</button>
                    <span class="ab-qty-value"><?= $item['quantity'] ?></span>
                    <button class="ab-qty-btn ab-qty-plus" data-cart-id="<?= $item['cart_id'] ?>">+</button>
                  </div>
                  <span class="ab-item-total"><?= fmtInr($item['price'] * $item['quantity']) ?></span>
                  <div class="ab-item-btn-row">
                    <button class="ab-remove-btn" data-cart-id="<?= $item['cart_id'] ?>">Remove</button>
                    <button class="ab-wishlist-btn">♡ Wishlist</button>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="ab-empty-cart" id="empty-cart" style="display:<?= empty($cartItems) ? 'block' : 'none' ?>;">
              <div class="ab-empty-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
              </div>
              <h2>Your bag is empty</h2>
              <p>Explore our luxurious fragrances and add your favourites.</p>
              <a href="collection.php" class="ab-btn-primary">Continue Shopping</a>
            </div>
          </div>

          <!-- Order Summary Column -->
          <div class="ab-summary-col" id="summary-col" style="display:<?= empty($cartItems) ? 'none' : 'flex' ?>;">
            <!-- Coupon -->
            <div class="ab-glass-card ab-coupon-card">
              <h3>Have a Coupon?</h3>
              <div class="ab-coupon-input-row">
                <input type="text" id="coupon-input" placeholder="Enter code" class="ab-coupon-input" value="<?= htmlspecialchars($appliedCoupon) ?>">
                <button class="ab-btn-sm" id="apply-coupon-btn">Apply</button>
              </div>
              <p class="ab-coupon-msg" id="coupon-msg"><?= $appliedCoupon ? 'Coupon "' . htmlspecialchars($appliedCoupon) . '" applied! ' . $discountPercent . '% off' : '' ?></p>
              <div class="ab-coupon-tags">
                <span class="ab-coupon-tag" data-code="WELCOME10">WELCOME10</span>
                <span class="ab-coupon-tag" data-code="LUXURY20">LUXURY20</span>
                <span class="ab-coupon-tag" data-code="COCO50">COCO50</span>
              </div>
            </div>
            <!-- Summary -->
            <div class="ab-glass-card ab-order-summary">
              <h3>Order Summary</h3>
              <div class="ab-summary-row"><span>Subtotal</span><span id="summary-subtotal"><?= fmtInr($totals['subtotal']) ?></span></div>
              <div class="ab-summary-row ab-discount-row" style="display:<?= $totals['discount'] > 0 ? 'flex' : 'none' ?>;"><span>Discount</span><span id="summary-discount">-<?= fmtInr($totals['discount']) ?></span></div>
              <div class="ab-summary-row"><span>GST (18%)</span><span id="summary-gst"><?= fmtInr($totals['gst']) ?></span></div>
              <div class="ab-summary-row"><span>Shipping</span><span id="summary-shipping"><?= $totals['shipping'] === 0 ? 'FREE' : fmtInr($totals['shipping']) ?></span></div>
              <div class="ab-summary-row"><span>Est. Delivery</span><span id="summary-delivery">3-5 Business Days</span></div>
              <div class="ab-summary-divider"></div>
              <div class="ab-summary-row ab-grand-total"><span>Grand Total</span><span id="summary-grand-total"><?= fmtInr($totals['grand_total']) ?></span></div>
              <button class="ab-btn-primary ab-checkout-trigger" id="checkout-trigger-btn">Proceed to Checkout</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Checkout Section (hidden by default) -->
    <section class="ab-checkout-section" id="checkout-section" style="display:none;">
      <div class="ab-container">
        <div class="ab-section-header">
          <h2 class="ab-title">Secure Checkout</h2>
          <p class="ab-subtitle">Complete your order with confidence.</p>
        </div>
        <div class="ab-checkout-layout">
          <!-- Customer Info -->
          <div class="ab-checkout-form-col">
            <div class="ab-glass-card">
              <h3>Customer Information</h3>
              <form id="checkout-form" class="ab-form" novalidate>
                <div class="ab-form-row">
                  <div class="ab-form-group"><label for="ch-name">Full Name</label><input type="text" id="ch-name" required value="<?= $user ? htmlspecialchars($user['name']) : '' ?>"></div>
                  <div class="ab-form-group"><label for="ch-email">Email Address</label><input type="email" id="ch-email" required value="<?= $user ? htmlspecialchars($user['email']) : '' ?>"></div>
                </div>
                <div class="ab-form-row">
                  <div class="ab-form-group"><label for="ch-mobile">Mobile Number</label><input type="tel" id="ch-mobile" required></div>
                  <div class="ab-form-group"><label for="ch-country">Country</label><input type="text" id="ch-country" value="India" required></div>
                </div>
                <div class="ab-form-row">
                  <div class="ab-form-group"><label for="ch-state">State</label><input type="text" id="ch-state" required></div>
                  <div class="ab-form-group"><label for="ch-city">City</label><input type="text" id="ch-city" required></div>
                </div>
                <div class="ab-form-group full-width"><label for="ch-address">Address</label><input type="text" id="ch-address" required></div>
                <div class="ab-form-group"><label for="ch-pin">PIN Code</label><input type="text" id="ch-pin" required maxlength="6"></div>

                <h3 class="ab-payment-title">Payment Method</h3>
                <div class="ab-payment-options" id="payment-options">
                  <label class="ab-payment-option selected"><input type="radio" name="payment" value="credit-card" checked><span class="ab-pay-icon">&#128179;</span> Credit Card</label>
                  <label class="ab-payment-option"><input type="radio" name="payment" value="debit-card"><span class="ab-pay-icon">&#128179;</span> Debit Card</label>
                  <label class="ab-payment-option"><input type="radio" name="payment" value="upi"><span class="ab-pay-icon">&#128241;</span> UPI</label>
                  <label class="ab-payment-option"><input type="radio" name="payment" value="gpay"><span class="ab-pay-icon">&#127758;</span> Google Pay</label>
                  <label class="ab-payment-option"><input type="radio" name="payment" value="apple-pay"><span class="ab-pay-icon">&#63743;</span> Apple Pay</label>
                  <label class="ab-payment-option"><input type="radio" name="payment" value="netbanking"><span class="ab-pay-icon">&#127974;</span> Net Banking</label>
                  <label class="ab-payment-option"><input type="radio" name="payment" value="cod"><span class="ab-pay-icon">&#128176;</span> Cash on Delivery</label>
                </div>

                <!-- Credit Card Form -->
                <div class="ab-card-form" id="card-form">
                  <div class="ab-form-group full-width"><label for="card-holder">Card Holder Name</label><input type="text" id="card-holder" required></div>
                  <div class="ab-form-group full-width"><label for="card-number">Card Number</label><input type="text" id="card-number" placeholder="XXXX XXXX XXXX XXXX" required maxlength="19"></div>
                  <div class="ab-form-row">
                    <div class="ab-form-group"><label for="card-expiry">Expiry Date</label><input type="text" id="card-expiry" placeholder="MM/YY" required maxlength="5"></div>
                    <div class="ab-form-group"><label for="card-cvv">CVV</label><input type="text" id="card-cvv" placeholder="***" required maxlength="4"></div>
                  </div>
                  <label class="ab-save-card"><input type="checkbox" id="save-card"> Save this card for future purchases</label>
                </div>

                <button type="submit" class="ab-btn-primary ab-complete-order-btn" id="complete-order-btn">Complete Order</button>
                <button type="button" class="ab-btn-secondary ab-back-to-cart-btn" id="back-to-cart-btn">Back to Cart</button>
              </form>
            </div>
          </div>

          <!-- Checkout Summary -->
          <div class="ab-checkout-summary-col">
            <div class="ab-glass-card ab-order-summary">
              <h3>Your Order</h3>
              <div class="ab-checkout-items" id="checkout-items-list"></div>
              <div class="ab-summary-divider"></div>
              <div class="ab-summary-row ab-grand-total"><span>Grand Total</span><span id="checkout-grand-total"><?= fmtInr($totals['grand_total']) ?></span></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Recommended Products -->
    <section class="ab-recommended-section">
      <div class="ab-container">
        <h2 class="ab-section-title">You May Also Like</h2>
        <div class="ab-recommended-grid" id="recommended-grid">
          <?php foreach ($recommended as $p): ?>
            <div class="ab-product-card">
              <img class="ab-product-card-img" src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
              <div class="ab-product-card-body">
                <div class="ab-product-card-name"><?= htmlspecialchars($p['name']) ?></div>
                <div class="ab-product-card-price"><?= fmtInr($p['price']) ?></div>
                <div class="ab-product-card-rating"><?= str_repeat('★', floor($p['rating'])) ?> <?= $p['rating'] ?></div>
                <div class="ab-product-card-actions">
                  <button class="ab-btn-primary ab-add-to-bag" data-id="<?= $p['id'] ?>">Add Bag</button>
                  <button class="ab-wishlist-btn">♡</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Recently Viewed -->
    <section class="ab-recently-viewed-section">
      <div class="ab-container">
        <h2 class="ab-section-title">Recently Viewed</h2>
        <div class="ab-recently-grid" id="recently-viewed-grid">
          <!-- Injected by JS from localStorage + PRODUCTS data below -->
        </div>
      </div>
    </section>

    <!-- Trust Section -->
    <section class="ab-trust-section">
      <div class="ab-container">
        <div class="ab-trust-grid">
          <div class="ab-trust-item"><div class="ab-trust-icon">&#10003;</div><span>100% Original Products</span></div>
          <div class="ab-trust-item"><div class="ab-trust-icon">&#128274;</div><span>Secure Payment</span></div>
          <div class="ab-trust-item"><div class="ab-trust-icon">&#8634;</div><span>Easy Returns</span></div>
          <div class="ab-trust-item"><div class="ab-trust-icon">&#128666;</div><span>Fast Delivery</span></div>
          <div class="ab-trust-item"><div class="ab-trust-icon">&#127873;</div><span>Luxury Packaging</span></div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="ab-footer">
    <div class="ab-container">
      <div class="ab-footer-grid">
        <div class="ab-footer-col">
          <a href="index.php" class="ab-footer-logo">COCO</a>
          <p class="ab-footer-tagline">Wrap yourself in luxury. Timeless fragrances crafted for the bold and elegant.</p>
        </div>
        <div class="ab-footer-col">
          <h4>Quick Links</h4>
          <a href="index.php">Home</a>
          <a href="collection.php">Collection</a>
          <a href="#">About Us</a>
          <a href="#">Contact</a>
        </div>
        <div class="ab-footer-col">
          <h4>Customer Care</h4>
          <a href="#">Shipping Info</a>
          <a href="#">Returns</a>
          <a href="#">FAQs</a>
          <a href="#">Privacy Policy</a>
        </div>
        <div class="ab-footer-col">
          <h4>Connect</h4>
          <p>support@coco.com</p>
          <p>+91 98765 43210</p>
        </div>
      </div>
      <div class="ab-footer-bottom">
        <p>&copy; 2026 COCO. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Order Success Modal -->
  <div class="ab-success-modal" id="success-modal" style="display:none;">
    <div class="ab-success-content">
      <div class="ab-success-animation">
        <svg class="ab-checkmark" viewBox="0 0 52 52"><circle class="ab-checkmark-circle" cx="26" cy="26" r="25" fill="none"/><path class="ab-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
      </div>
      <h2>Order Confirmed!</h2>
      <p class="ab-success-subtitle">Thank You for Shopping with COCO</p>
      <p class="ab-success-desc">Your luxury fragrance is on its way.</p>
      <div class="ab-success-details">
        <p><strong>Order Number:</strong> <span id="order-number">COCO-000000</span></p>
        <p><strong>Estimated Delivery:</strong> <span id="order-delivery">3-5 Business Days</span></p>
      </div>
      <a href="collection.php" class="ab-btn-primary">Continue Shopping</a>
    </div>
  </div>

  <!-- Product data from the database, used by add-bag.js for "Recently Viewed" -->
  <script>
    window.COCO_PRODUCTS = <?= json_encode(array_map(function ($p) {
        return [
            'id' => (int) $p['id'], 'brand' => $p['brand'], 'name' => $p['name'],
            'category' => ucfirst($p['category']) . ' ' . ($p['category'] === 'noir' || $p['category'] === 'woody' ? 'Eau De Parfum' : 'Eau De Toilette'),
            'size' => $p['size'], 'price' => (int) $p['price'], 'originalPrice' => (int) $p['original_price'],
            'discount' => (int) $p['discount'], 'stock' => (int) $p['stock'], 'rating' => (float) $p['rating'],
            'image' => $p['image'], 'delivery' => $p['delivery_estimate'],
        ];
    }, $allProducts)); ?>;
  </script>

  <script src="main.js"></script>
  <script src="add-bag.js"></script>
</body>
</html>
