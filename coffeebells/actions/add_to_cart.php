<?php
// ============================================================
// ADD_TO_CART.PHP — Agrega productos al carrito (sesión)
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$qty        = max(1, (int)($_POST['qty'] ?? 1));

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Producto no válido.']);
    exit;
}

$pdo  = getDB();
$stmt = $pdo->prepare("SELECT id, name, price, stock, image FROM products WHERE id = :id AND active = 1");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
    exit;
}

// Verificar stock
if ($product['stock'] !== null && $product['stock'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Producto sin stock disponible.']);
    exit;
}

// Inicializar carrito en sesión
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$key = 'p_' . $product_id;

if (isset($_SESSION['cart'][$key])) {
    // Si ya existe, incrementar cantidad
    $new_qty = $_SESSION['cart'][$key]['qty'] + $qty;
    // Respetar stock máximo
    if ($product['stock'] !== null) {
        $new_qty = min($new_qty, $product['stock']);
    }
    $_SESSION['cart'][$key]['qty'] = $new_qty;
} else {
    $_SESSION['cart'][$key] = [
        'id'    => $product['id'],
        'name'  => $product['name'],
        'price' => $product['price'],
        'image' => $product['image'],
        'qty'   => $qty,
    ];
}

$cart_count = getCartCount();
$subtotal   = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $_SESSION['cart']));

echo json_encode([
    'success'    => true,
    'message'    => 'Producto agregado al carrito.',
    'cart_count' => $cart_count,
    'subtotal'   => formatPrice($subtotal),
    'product'    => $product['name'],
]);