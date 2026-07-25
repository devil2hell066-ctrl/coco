<?php
require_once 'config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');

$cartId = intval($_POST['cart_id'] ?? 0);
if (!$cartId) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

removeCartItem($pdo, $cartId);

echo json_encode([
    'success' => true,
    'totals' => cartTotals($pdo),
    'cart_count' => getCartCount($pdo),
]);
