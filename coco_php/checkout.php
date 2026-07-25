<?php
require_once 'config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');

$COUPONS = ['WELCOME10' => 10, 'LUXURY20' => 20, 'COCO50' => 50];

$items = getCartItems($pdo);
if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Your bag is empty']);
    exit;
}

// Required customer fields
$required = ['name', 'email', 'mobile', 'country', 'state', 'city', 'address', 'pin'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit;
    }
}

$paymentMethod = $_POST['payment_method'] ?? 'cod';

// Discount percent comes from the server-side session coupon, never trust client input
$discountPercent = 0;
if (!empty($_SESSION['coupon']) && isset($COUPONS[$_SESSION['coupon']])) {
    $discountPercent = $COUPONS[$_SESSION['coupon']];
}
$totals = cartTotals($pdo, $discountPercent);

$orderNumber = 'COCO-' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "INSERT INTO orders
         (order_number, user_id, full_name, email, mobile, country, state, city, address, pin_code, payment_method, subtotal, discount, gst, shipping, grand_total)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $orderNumber,
        $_SESSION['user_id'] ?? null,
        $_POST['name'], $_POST['email'], $_POST['mobile'], $_POST['country'],
        $_POST['state'], $_POST['city'], $_POST['address'], $_POST['pin'],
        $paymentMethod,
        $totals['subtotal'], $totals['discount'], $totals['gst'], $totals['shipping'], $totals['grand_total'],
    ]);
    $orderId = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        "INSERT INTO order_items (order_id, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)"
    );
    foreach ($items as $item) {
        $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['quantity']]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Order could not be placed. Please try again.']);
    exit;
}

// Clear the cart and the applied coupon now that the order is placed
clearCart($pdo);
unset($_SESSION['coupon']);

echo json_encode([
    'success'      => true,
    'order_number' => $orderNumber,
    'grand_total'  => $totals['grand_total'],
]);
