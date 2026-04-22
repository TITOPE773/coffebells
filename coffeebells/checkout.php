<?php
// ============================================================
// CHECKOUT.PHP — Página de checkout y confirmación
// ============================================================
require_once 'includes/db.php';
require_once 'includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Si el carrito está vacío y no es página de éxito, redirigir
$success = isset($_GET['success']) && $_GET['success'] == '1';
$folio   = htmlspecialchars($_GET['folio'] ?? '', ENT_QUOTES, 'UTF-8');

if (!$success && empty($_SESSION['cart'])) {
    setFlash('error', 'Tu carrito está vacío.');
    redirect('carrito.php');
}

$cart     = $_SESSION['cart'] ?? [];
$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
$shipping = $subtotal >= 800 ? 0 : 99;
$total    = $subtotal + $shipping;

$meta_title = 'Checkout — CoffeeBells & Home';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="carrito.php">Carrito</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-pad bg-gray">
    <div class="container">

        <?php if ($success): ?>
        <!-- ── CONFIRMACIÓN DE PEDIDO ───────────────────── -->
        <?php $last = $_SESSION['last_order'] ?? []; ?>
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="quick-form-card py-5">
                    <div style="font-size:4rem;margin-bottom:1rem;">🎉</div>
                    <h3 style="color:var(--primary);">¡Pedido confirmado!</h3>
                    <p class="text-muted">Gracias <?= e($last['name'] ?? 'Cliente') ?>, recibimos tu pedido correctamente.</p>
                    <div class="p-3 rounded my-3" style="background:var(--beige);">
                        <strong style="font-family:var(--font-title);font-size:1.3rem;color:var(--coffee);">
                            Folio: <?= e($last['folio'] ?? $folio) ?>
                        </strong><br>
                        <span class="text-muted small">Guarda este número como referencia.</span>
                    </div>
                    <p class="text-muted small mb-4">
                        Nos pondremos en contacto contigo muy pronto para confirmar tu pedido y coordinar la entrega.
                    </p>
                    <?php if (!empty($_SESSION['whatsapp_order_url'])): ?>
                    <a href="<?= e($_SESSION['whatsapp_order_url']) ?>"
                       class="btn-wa-green mb-3" target="_blank">
                        <i class="bi bi-whatsapp"></i> Confirmar por WhatsApp
                    </a>
                    <?php unset($_SESSION['whatsapp_order_url']); ?>
                    <?php endif; ?>
                    <br>
                    <a href="tienda.php" class="btn-primary-custom mt-2">
                        <i class="bi bi-shop"></i> Seguir comprando
                    </a>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- ── FORMULARIO DE CHECKOUT ───────────────────── -->
        <?= showFlash() ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="quick-form-card">
                    <h5 style="font-family:var(--font-title);color:var(--coffee);margin-bottom:1.5rem;">
                        <i class="bi bi-person-fill me-2"></i>Datos de entrega
                    </h5>
                    <form action="actions/checkout_action.php" method="POST" id="checkoutForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-custom"
                                       placeholder="Tu nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control form-control-custom"
                                       placeholder="+52 464 000 0000" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Correo electrónico</label>
                                <input type="email" name="email" class="form-control form-control-custom"
                                       placeholder="tu@correo.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Dirección de entrega <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control form-control-custom"
                                       placeholder="Calle, número, colonia" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ciudad</label>
                                <input type="text" name="city" class="form-control form-control-custom"
                                       placeholder="Salamanca" value="Salamanca">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notas del pedido</label>
                                <textarea name="notes" class="form-control form-control-custom" rows="2"
                                          placeholder="Instrucciones especiales, referencias de entrega..."></textarea>
                            </div>

                            <!-- Método de pago -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Método de pago</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="d-flex align-items-center gap-2 p-3 rounded border cursor-pointer payment-option"
                                               style="cursor:pointer;transition:all .2s;">
                                            <input type="radio" name="payment" value="whatsapp" checked>
                                            <i class="bi bi-whatsapp text-success fs-5"></i>
                                            <span class="small fw-semibold">Confirmar por WhatsApp</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="d-flex align-items-center gap-2 p-3 rounded border cursor-pointer payment-option"
                                               style="cursor:pointer;transition:all .2s;">
                                            <input type="radio" name="payment" value="transferencia">
                                            <i class="bi bi-bank text-primary fs-5"></i>
                                            <span class="small fw-semibold">Transferencia bancaria</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="d-flex align-items-center gap-2 p-3 rounded border cursor-pointer payment-option"
                                               style="cursor:pointer;transition:all .2s;">
                                            <input type="radio" name="payment" value="efectivo">
                                            <i class="bi bi-cash-stack text-success fs-5"></i>
                                            <span class="small fw-semibold">Efectivo en entrega</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="d-flex align-items-center gap-2 p-3 rounded border cursor-pointer payment-option"
                                               style="cursor:pointer;transition:all .2s;">
                                            <input type="radio" name="payment" value="tarjeta_pronto">
                                            <i class="bi bi-credit-card text-info fs-5"></i>
                                            <span class="small fw-semibold">Tarjeta (próximamente)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn-primary-custom w-100 justify-content-center" style="font-size:1.05rem;padding:.85rem;">
                                    <i class="bi bi-bag-check-fill"></i> Confirmar pedido — <?= formatPrice($total) ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resumen checkout -->
            <div class="col-lg-5">
                <div class="order-summary-card">
                    <h5>Tu pedido</h5>
                    <?php foreach ($cart as $item): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div class="d-flex gap-2 align-items-center">
                            <img src="<?= e($item['image'] ?: 'assets/img/product-default.jpg') ?>"
                                 style="width:48px;height:48px;object-fit:cover;border-radius:8px;" alt="">
                            <div>
                                <div class="small fw-semibold" style="color:var(--coffee);"><?= e($item['name']) ?></div>
                                <div class="text-muted" style="font-size:.78rem;">x<?= $item['qty'] ?></div>
                            </div>
                        </div>
                        <strong style="color:var(--terracota);"><?= formatPrice($item['price'] * $item['qty']) ?></strong>
                    </div>
                    <?php endforeach; ?>
                    <div class="summary-row mt-2"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
                    <div class="summary-row">
                        <span>Envío</span>
                        <span><?= $shipping === 0 ? '<span style="color:var(--primary);font-weight:700;">Gratis</span>' : formatPrice($shipping) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span style="color:var(--terracota);"><?= formatPrice($total) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>