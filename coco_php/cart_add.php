<?php
require_once 'config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');

$productId = intval($_POST['product_id'] ?? 0);
$qty       = max(1, intval($_POST['quantity'] ?? 1));

if (!$productId || !getProduct($pdo, $productId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

addToCart($pdo, $productId, $qty);

echo json_encode([
    'success' => true,
    'cart_count' => getCartCount($pdo),
]);
