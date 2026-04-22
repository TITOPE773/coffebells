<?php
// ============================================================
// UPDATE_CART.PHP — Actualizar o eliminar item del carrito
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$action     = $_POST['action']     ?? 'update'; // update | remove | clear
$product_id = (int)($_POST['product_id'] ?? 0);
$qty        = max(0, (int)($_POST['qty'] ?? 1));
$key        = 'p_' . $product_id;

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true, 'cart_count' => 0, 'subtotal' => '$0.00 MXN']);
    exit;
}

if ($action === 'remove') {
    unset($_SESSION['cart'][$key]);
    echo json_encode([
        'success'    => true,
        'cart_count' => getCartCount(),
        'subtotal'   => formatPrice(array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $_SESSION['cart'] ?? [])))
    ]);
    exit;
}

if ($action === 'update' && isset($_SESSION['cart'][$key])) {
    if ($qty <= 0) {
        unset($_SESSION['cart'][$key]);
    } else {
        $_SESSION['cart'][$key]['qty'] = $qty;
    }
    $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $_SESSION['cart'] ?? []));
    echo json_encode([
        'success'    => true,
        'cart_count' => getCartCount(),
        'subtotal'   => formatPrice($subtotal),
        'item_total' => isset($_SESSION['cart'][$key]) ? formatPrice($_SESSION['cart'][$key]['price'] * $qty) : '$0.00 MXN',
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Operación no válida.']);