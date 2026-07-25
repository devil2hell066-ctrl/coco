<?php
require_once 'config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');

$COUPONS = ['WELCOME10' => 10, 'LUXURY20' => 20, 'COCO50' => 50];

$code = strtoupper(trim($_POST['code'] ?? ''));

if (!isset($COUPONS[$code])) {
    unset($_SESSION['coupon']);
    echo json_encode(['success' => false, 'message' => 'Invalid coupon code']);
    exit;
}

$_SESSION['coupon'] = $code;

echo json_encode([
    'success'  => true,
    'code'     => $code,
    'percent'  => $COUPONS[$code],
    'totals'   => cartTotals($pdo, $COUPONS[$code]),
]);
