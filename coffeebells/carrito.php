<?php
// ============================================================
// CARRITO.PHP — Vista del carrito de compras
// ============================================================
require_once 'includes/db.php';
require_once 'includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$meta_title = 'Mi Carrito — CoffeeBells & Home';
$cart       = $_SESSION['cart'] ?? [];
$subtotal   = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
$shipping   = $subtotal > 0 ? ($subtotal >= 800 ? 0 : 99) : 0;
$total      = $subtotal + $shipping;

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="tienda.php">Tienda</a></li>
                <li class="breadcrumb-item active">Mi carrito</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-pad bg-gray">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Tu selección</span>
            <h2 class="section-title">Mi Carrito</h2>
        </div>

        <?php if (empty($cart)): ?>
        <!-- Carrito vacío -->
        <div class="text-center py-5">
            <div style="font-size:5rem;margin-bottom:1rem;">🛒</div>
            <h4 style="color:var(--coffee);">Tu carrito está vacío</h4>
            <p class="text-muted mb-4">Aún no has agregado productos. ¡Explora nuestra tienda!</p>
            <a href="tienda.php" class="btn-primary-custom me-3">
                <i class="bi bi-shop"></i> Ir a la tienda
            </a>
            <a href="coffee-bells.php" class="btn-coffee">
                <i class="bi bi-cup-hot"></i> Ver café
            </a>
        </div>

        <?php else: ?>
        <div class="row g-4">
            <!-- Items del carrito -->
            <div class="col-lg-8">
                <div id="cart-items-container">
                    <?php foreach ($cart as $key => $item): ?>
                    <div class="cart-item-card" id="cart-item-<?= e($key) ?>">
                        <div class="cart-item-img">
                            <img src="<?= e($item['image'] ?: 'assets/img/product-default.jpg') ?>"
                                 alt="<?= e($item['name']) ?>">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="cart-item-name"><?= e($item['name']) ?></div>
                                    <div class="cart-item-price"><?= formatPrice($item['price']) ?></div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger rounded-circle border-0 remove-item"
                                        data-id="<?= e($item['id']) ?>" data-key="<?= e($key) ?>" title="Eliminar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div class="qty-control">
                                    <button class="qty-btn qty-minus" data-key="<?= e($key) ?>" data-id="<?= $item['id'] ?>">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="qty-input" value="<?= $item['qty'] ?>"
                                           min="1" max="99" data-key="<?= e($key) ?>" data-id="<?= $item['id'] ?>">
                                    <button class="qty-btn qty-plus" data-key="<?= e($key) ?>" data-id="<?= $item['id'] ?>">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <strong class="cart-item-price" id="item-total-<?= e($key) ?>">
                                    <?= formatPrice($item['price'] * $item['qty']) ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex gap-3 mt-3 flex-wrap">
                    <a href="tienda.php" class="btn-outline-custom">
                        <i class="bi bi-arrow-left"></i> Seguir comprando
                    </a>
                    <button class="btn btn-outline-danger" id="clearCart">
                        <i class="bi bi-trash3 me-1"></i> Vaciar carrito
                    </button>
                </div>

                <!-- Comprar por WhatsApp -->
                <?php
                $wa_items = implode('%0A', array_map(
                    fn($i) => '• ' . $i['qty'] . 'x ' . $i['name'] . ' — $' . number_format($i['price'] * $i['qty'], 2),
                    $cart
                ));
                $wa_msg = urlencode("Hola CoffeeBells! 🛒 Quiero hacer un pedido:\n") . $wa_items . urlencode("\nTotal: $" . number_format($total, 2) . " MXN");
                ?>
                <div class="mt-3 p-3 rounded" style="background:rgba(37,211,102,.08);border:1px solid rgba(37,211,102,.3);">
                    <p class="mb-2 small" style="color:#128C7E;font-weight:600;">
                        <i class="bi bi-whatsapp me-1"></i> ¿Prefieres hacer tu pedido por WhatsApp?
                    </p>
                    <a href="https://wa.me/524641234567?text=<?= $wa_msg ?>"
                       class="btn-wa-green" target="_blank">
                        <i class="bi bi-whatsapp"></i> Pedir por WhatsApp
                    </a>
                </div>
            </div>

            <!-- Resumen del pedido -->
            <div class="col-lg-4">
                <div class="order-summary-card">
                    <h5>Resumen del pedido</h5>
                    <div class="summary-row">
                        <span>Subtotal (<span id="item-count"><?= getCartCount() ?></span> artículos)</span>
                        <span id="cart-subtotal"><?= formatPrice($subtotal) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Envío</span>
                        <span id="cart-shipping">
                            <?= $shipping === 0 ? '<span style="color:var(--primary);font-weight:600;">Gratis</span>' : formatPrice($shipping) ?>
                        </span>
                    </div>
                    <?php if ($subtotal < 800 && $subtotal > 0): ?>
                    <div class="alert-stock-low mb-2">
                        <i class="bi bi-info-circle-fill"></i>
                        Agrega <?= formatPrice(800 - $subtotal) ?> más para envío gratis.
                    </div>
                    <?php endif; ?>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="cart-total" style="color:var(--terracota);"><?= formatPrice($total) ?></span>
                    </div>

                    <a href="checkout.php" class="btn-primary-custom w-100 justify-content-center mt-3" style="font-size:1rem;">
                        <i class="bi bi-credit-card-fill"></i> Proceder al checkout
                    </a>
                    <a href="https://wa.me/524641234567?text=<?= $wa_msg ?>"
                       class="btn-wa-green w-100 justify-content-center mt-2" target="_blank">
                        <i class="bi bi-whatsapp"></i> Pedir por WhatsApp
                    </a>

                    <!-- Código de descuento -->
                    <div class="mt-3">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" placeholder="Código de descuento" id="couponInput">
                            <button class="btn btn-outline-secondary" id="applyCoupon">Aplicar</button>
                        </div>
                    </div>

                    <!-- Seguridad -->
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="bi bi-shield-lock-fill me-1 text-success"></i>
                            Compra 100% segura y protegida
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// ── LÓGICA CARRITO EN PÁGINA ──────────────────────────────
document.addEventListener('DOMContentLoaded', function() {

    // Cambiar cantidad con botones +/-
    document.querySelectorAll('.qty-plus, .qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const key   = this.dataset.key;
            const id    = this.dataset.id;
            const input = document.querySelector(`.qty-input[data-key="${key}"]`);
            let qty     = parseInt(input.value);
            if (this.classList.contains('qty-plus')) qty++;
            else qty = Math.max(1, qty - 1);
            input.value = qty;
            updateCartItem(id, key, qty);
        });
    });

    // Cambiar cantidad manualmente
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const qty = Math.max(1, parseInt(this.value) || 1);
            this.value = qty;
            updateCartItem(this.dataset.id, this.dataset.key, qty);
        });
    });

    // Eliminar item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const id  = this.dataset.id;
            const key = this.dataset.key;
            fetch('actions/update_cart.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: `action=remove&product_id=${id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-item-' + key)?.remove();
                    updateSummary(data);
                    if (data.cart_count === 0) location.reload();
                }
            });
        });
    });

    // Vaciar carrito
    document.getElementById('clearCart')?.addEventListener('click', function() {
        if (!confirm('¿Vaciar el carrito?')) return;
        fetch('actions/update_cart.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'action=clear'
        }).then(() => location.reload());
    });

    function updateCartItem(id, key, qty) {
        fetch('actions/update_cart.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `action=update&product_id=${id}&qty=${qty}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const itemTotal = document.getElementById('item-total-' + key);
                if (itemTotal && data.item_total) itemTotal.textContent = data.item_total;
                updateSummary(data);
            }
        });
    }

    function updateSummary(data) {
        const countEl = document.getElementById('item-count');
        const subEl   = document.getElementById('cart-subtotal');
        const totalEl = document.getElementById('cart-total');
        const badge   = document.getElementById('cartCountBadge');
        if (countEl) countEl.textContent = data.cart_count;
        if (subEl && data.subtotal) subEl.textContent = data.subtotal;
        if (totalEl && data.subtotal) totalEl.textContent = data.subtotal;
        if (badge) badge.textContent = data.cart_count;
    }

    // Cupón demo
    document.getElementById('applyCoupon')?.addEventListener('click', function() {
        const code = document.getElementById('couponInput').value.trim().toUpperCase();
        if (code === 'COFFEE10') {
            showToast('✅ Cupón COFFEE10 aplicado: 10% de descuento', 'success');
        } else {
            showToast('❌ Cupón no válido', 'danger');
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>