<?php
require_once 'config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');

$cartId = intval($_POST['cart_id'] ?? 0);
$action = $_POST['action'] ?? ''; // 'increase' | 'decrease'
$qty    = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;

if (!$cartId) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

// Look up current quantity for this cart row (scoped to this session)
$stmt = $pdo->prepare("SELECT quantity FROM cart WHERE id = ? AND session_id = ?");
$stmt->execute([$cartId, session_id()]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

if ($qty !== null) {
    $newQty = max(1, $qty);
} else {
    $newQty = $action === 'decrease' ? max(1, $row['quantity'] - 1) : $row['quantity'] + 1;
}

updateCartQuantity($pdo, $cartId, $newQty);

echo json_encode([
    'success' => true,
    'quantity' => $newQty,
    'totals' => cartTotals($pdo),
    'cart_count' => getCartCount($pdo),
]);
