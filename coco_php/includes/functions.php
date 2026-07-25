<?php
/**
 * Shared helper functions - included after config.php on every page
 */

/* ---------- Auth helpers ---------- */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
    ];
}

/* ---------- Product helpers ---------- */
function getAllProducts(PDO $pdo) {
    return $pdo->query("SELECT * FROM products ORDER BY id ASC")->fetchAll();
}

function getProduct(PDO $pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getRandomProducts(PDO $pdo, $limit = 4) {
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY RAND() LIMIT " . intval($limit));
    $stmt->execute();
    return $stmt->fetchAll();
}

/* ---------- Cart helpers (cart rows are tied to the PHP session id) ---------- */
function getCartItems(PDO $pdo) {
    $stmt = $pdo->prepare(
        "SELECT c.id AS cart_id, c.quantity, p.*
         FROM cart c
         JOIN products p ON p.id = c.product_id
         WHERE c.session_id = ?
         ORDER BY c.id ASC"
    );
    $stmt->execute([session_id()]);
    return $stmt->fetchAll();
}

function getCartCount(PDO $pdo) {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) AS total FROM cart WHERE session_id = ?");
    $stmt->execute([session_id()]);
    return (int) $stmt->fetch()['total'];
}

function addToCart(PDO $pdo, $productId, $qty = 1) {
    $userId = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->execute([session_id(), $productId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $upd = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        $upd->execute([$qty, $existing['id']]);
    } else {
        $ins = $pdo->prepare("INSERT INTO cart (session_id, user_id, product_id, quantity) VALUES (?, ?, ?, ?)");
        $ins->execute([session_id(), $userId, $productId, $qty]);
    }
}

function updateCartQuantity(PDO $pdo, $cartId, $qty) {
    if ($qty < 1) $qty = 1;
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?");
    $stmt->execute([$qty, $cartId, session_id()]);
}

function removeCartItem(PDO $pdo, $cartId) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
    $stmt->execute([$cartId, session_id()]);
}

function clearCart(PDO $pdo) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
    $stmt->execute([session_id()]);
}

function cartTotals(PDO $pdo, $discountPercent = 0) {
    $items = getCartItems($pdo);
    $subtotal = 0;
    foreach ($items as $i) {
        $subtotal += $i['price'] * $i['quantity'];
    }
    $discount   = $discountPercent > 0 ? round($subtotal * $discountPercent / 100) : 0;
    $afterDisc  = $subtotal - $discount;
    $gst        = round($afterDisc * 0.18);
    $shipping   = $subtotal >= 2000 ? 0 : 199;
    $grandTotal = $afterDisc + $gst + $shipping;

    return [
        'subtotal'    => $subtotal,
        'discount'    => $discount,
        'gst'         => $gst,
        'shipping'    => $shipping,
        'grand_total' => $grandTotal,
    ];
}

function fmtInr($n) {
    return '₹' . number_format($n);
}
