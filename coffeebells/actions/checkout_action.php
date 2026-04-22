<?php
// ============================================================
// CHECKOUT_ACTION.PHP — Procesa y guarda el pedido
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../checkout.php');
}

if (empty($_SESSION['cart'])) {
    setFlash('error', 'Tu carrito está vacío.');
    redirect('../carrito.php');
}

// Datos del cliente
$name    = htmlspecialchars(trim($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8');
$phone   = htmlspecialchars(trim($_POST['phone']   ?? ''), ENT_QUOTES, 'UTF-8');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$address = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
$city    = htmlspecialchars(trim($_POST['city']    ?? ''), ENT_QUOTES, 'UTF-8');
$notes   = htmlspecialchars(trim($_POST['notes']   ?? ''), ENT_QUOTES, 'UTF-8');
$payment = htmlspecialchars(trim($_POST['payment'] ?? 'whatsapp'), ENT_QUOTES, 'UTF-8');

if (!$name || !$phone) {
    setFlash('error', 'Nombre y teléfono son obligatorios.');
    redirect('../checkout.php');
}

$pdo = getDB();

try {
    $pdo->beginTransaction();

    // Calcular total
    $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $_SESSION['cart']));
    $shipping = $subtotal >= 800 ? 0 : 99;
    $total    = $subtotal + $shipping;

    // Generar folio único
    $folio = 'CB-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

    // Crear orden
    $stmt = $pdo->prepare("
        INSERT INTO orders
            (folio, customer_name, customer_phone, customer_email, customer_address,
             customer_city, notes, payment_method, subtotal, shipping, total, status, created_at)
        VALUES
            (:folio, :name, :phone, :email, :address,
             :city, :notes, :payment, :subtotal, :shipping, :total, 'pending', NOW())
    ");
    $stmt->execute([
        ':folio'    => $folio,
        ':name'     => $name,
        ':phone'    => $phone,
        ':email'    => $email ?: null,
        ':address'  => $address,
        ':city'     => $city,
        ':notes'    => $notes,
        ':payment'  => $payment,
        ':subtotal' => $subtotal,
        ':shipping' => $shipping,
        ':total'    => $total,
    ]);
    $order_id = $pdo->lastInsertId();

    // Guardar items de la orden
    $stmt2 = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, price, qty, subtotal)
        VALUES (:order_id, :product_id, :product_name, :price, :qty, :subtotal)
    ");
    foreach ($_SESSION['cart'] as $item) {
        $stmt2->execute([
            ':order_id'     => $order_id,
            ':product_id'   => $item['id'],
            ':product_name' => $item['name'],
            ':price'        => $item['price'],
            ':qty'          => $item['qty'],
            ':subtotal'     => $item['price'] * $item['qty'],
        ]);
        // Descontar stock
        $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id AND stock IS NOT NULL")
            ->execute([':qty' => $item['qty'], ':id' => $item['id']]);
    }

    $pdo->commit();

    // Limpiar carrito
    $_SESSION['cart']      = [];
    $_SESSION['last_order'] = [
        'folio'   => $folio,
        'total'   => $total,
        'name'    => $name,
        'payment' => $payment,
    ];

    // Construir mensaje WhatsApp si aplica
    if ($payment === 'whatsapp') {
        $wa_items = '';
        foreach ($_SESSION['last_order'] ?? [] as $k => $v) {
            // construir resumen
        }
        $wa_msg = urlencode("Hola CoffeeBells! Pedido #{$folio}\nNombre: {$name}\nTotal: $" . number_format($total, 2) . " MXN\nMétodo: {$payment}");
        $_SESSION['whatsapp_order_url'] = "https://wa.me/524641234567?text={$wa_msg}";
    }

    setFlash('success', "¡Pedido recibido! Tu folio es {$folio}.");
    redirect('../checkout.php?success=1&folio=' . urlencode($folio));

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log($e->getMessage());
    setFlash('error', 'Ocurrió un error al procesar tu pedido. Por favor intenta de nuevo.');
    redirect('../checkout.php');
}