<?php
// ============================================================
// PRODUCTO.PHP — Página individual de producto
// ============================================================
require_once 'includes/db.php';
require_once 'includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$pdo = getDB();
$id  = (int)($_GET['id'] ?? 0);

// Intentar obtener de BD
$product = null;
if ($id > 0) {
    $stmt = $pdo->prepare("
        SELECT p.*, pc.name AS category_name, pc.slug AS category_slug
        FROM products p
        LEFT JOIN product_categories pc ON p.category_id = pc.id
        WHERE p.id = :id AND p.active = 1
    ");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
}

// Demo si no existe en BD
if (!$product) {
    $product = [
        'id' => $id ?: 1, 'name' => 'Café Blend Especial 250g',
        'category_name' => 'Café', 'category_slug' => 'cafe',
        'price' => 189, 'price_old' => 220, 'badge' => 'hot',
        'stock' => 15, 'featured' => 1,
        'description' => 'Nuestro Blend Especial es una mezcla cuidadosamente seleccionada de los mejores granos de Chiapas y Oaxaca. Tostado medio, con notas a chocolate amargo, frutos rojos y un final cítrico que lo hace único. Ideal para espresso, americano y filtrado.',
        'short_description' => 'Blend premium con granos de origen Chiapas-Oaxaca, tostado medio.',
        'image' => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=700&q=80',
        'sku' => 'CB-CAF-001',
    ];
}

// Imágenes del producto
$images = [];
if ($id > 0) {
    $img_stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = :id ORDER BY sort_order ASC");
    $img_stmt->execute([':id' => $id]);
    $images = $img_stmt->fetchAll();
}
if (empty($images)) {
    $images = [
        ['image' => $product['image']],
        ['image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=700&q=80'],
        ['image' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=700&q=80'],
    ];
}

// Productos relacionados
$related = [];
if ($id > 0 && !empty($product['category_id'])) {
    $rel_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = :cat AND id != :id AND active = 1 LIMIT 4");
    $rel_stmt->execute([':cat' => $product['category_id'], ':id' => $id]);
    $related = $rel_stmt->fetchAll();
}

$meta_title = e($product['name']) . ' — CoffeeBells & Home';
$meta_description = e($product['short_description'] ?? mb_substr($product['description'], 0, 160));

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
                <?php if (!empty($product['category_slug'])): ?>
                <li class="breadcrumb-item">
                    <a href="tienda.php?cat=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a>
                </li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-pad bg-gray">
    <div class="container">
        <div class="row g-5">

            <!-- Galería -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="product-gallery">
                    <!-- Imagen principal -->
                    <div class="product-main-img mb-3 position-relative"
                         style="border-radius:var(--radius-lg);overflow:hidden;height:460px;">
                        <a href="<?= e($images[0]['image']) ?>" class="glightbox" data-gallery="product">
                            <img src="<?= e($images[0]['image']) ?>" alt="<?= e($product['name']) ?>"
                                 id="mainProductImg"
                                 style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
                        </a>
                        <?php if (!empty($product['badge'])): ?>
                        <span class="product-badge badge-<?= e($product['badge']) ?>" style="font-size:.8rem;">
                            <?= $product['badge']==='new'?'Nuevo':($product['badge']==='sale'?'Oferta':'🔥 Hot') ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <!-- Miniaturas -->
                    <?php if (count($images) > 1): ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php foreach ($images as $i => $img): ?>
                        <a href="<?= e($img['image']) ?>" class="glightbox product-thumb <?= $i===0?'thumb-active':'' ?>"
                           data-gallery="product"
                           style="width:80px;height:80px;border-radius:10px;overflow:hidden;
                                  border:2px solid <?= $i===0?'var(--primary)':'var(--gray-200)' ?>;
                                  transition:all .2s;">
                            <img src="<?= e($img['image']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info producto -->
            <div class="col-lg-6" data-aos="fade-left">
                <span class="section-tag"><?= e($product['category_name'] ?? '') ?></span>
                <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);color:var(--coffee);margin:.5rem 0;">
                    <?= e($product['name']) ?>
                </h1>

                <!-- Estrellas -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="color:var(--warm-yellow);">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <small class="text-muted">4.5 (18 reseñas)</small>
                </div>

                <!-- Precio -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span style="font-size:2rem;font-weight:800;color:var(--terracota);font-family:var(--font-title);">
                        <?= formatPrice($product['price']) ?>
                    </span>
                    <?php if (!empty($product['price_old']) && $product['price_old'] > 0): ?>
                    <span style="font-size:1.1rem;text-decoration:line-through;color:var(--gray-500);">
                        <?= formatPrice($product['price_old']) ?>
                    </span>
                    <span class="badge" style="background:var(--terracota);color:#fff;font-size:.85rem;">
                        -<?= round((1 - $product['price']/$product['price_old'])*100) ?>% OFF
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Stock -->
                <?php if (!is_null($product['stock'])): ?>
                <div class="mb-3">
                    <?php if ($product['stock'] > 5): ?>
                    <span style="color:var(--primary);font-weight:600;font-size:.88rem;">
                        <i class="bi bi-check-circle-fill me-1"></i> En stock (<?= $product['stock'] ?> disponibles)
                    </span>
                    <?php elseif ($product['stock'] > 0): ?>
                    <div class="alert-stock-low">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        ¡Solo quedan <?= $product['stock'] ?> unidades!
                    </div>
                    <?php else: ?>
                    <span style="color:var(--terracota);font-weight:600;font-size:.88rem;">
                        <i class="bi bi-x-circle-fill me-1"></i> Agotado temporalmente
                    </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <p style="color:var(--gray-700);line-height:1.8;margin-bottom:1.5rem;">
                    <?= nl2br(e($product['description'])) ?>
                </p>

                <!-- SKU -->
                <?php if (!empty($product['sku'])): ?>
                <p class="text-muted small mb-3">SKU: <strong><?= e($product['sku']) ?></strong></p>
                <?php endif; ?>

                <!-- Cantidad + Carrito -->
                <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                    <div class="qty-control">
                        <button class="qty-btn" id="qtyMinus"><i class="bi bi-dash"></i></button>
                        <input type="number" class="qty-input" id="productQty" value="1" min="1"
                               max="<?= $product['stock'] ?? 99 ?>">
                        <button class="qty-btn" id="qtyPlus"><i class="bi bi-plus"></i></button>
                    </div>
                    <button class="btn-primary-custom flex-grow-1 justify-content-center btn-add-cart"
                            data-id="<?= $product['id'] ?>" id="addToCartBtn"
                            style="padding:.75rem 1.5rem;">
                        <i class="bi bi-cart-plus"></i> Agregar al carrito
                    </button>
                </div>

                <!-- WhatsApp + Favoritos -->
                <div class="d-flex gap-2 mb-4 flex-wrap">
                    <a href="https://wa.me/524641234567?text=Hola%2C+quiero+comprar:+<?= urlencode($product['name']) ?>+%28%24<?= $product['price'] ?>+MXN%29"
                       class="btn-wa-green flex-grow-1 justify-content-center" target="_blank" style="padding:.7rem;">
                        <i class="bi bi-whatsapp"></i> Pedir por WhatsApp
                    </a>
                    <button class="btn-outline-custom justify-content-center" id="wishlistBtn" style="padding:.7rem 1.2rem;">
                        <i class="bi bi-heart me-1"></i> Favoritos
                    </button>
                </div>

                <!-- Beneficios rápidos -->
                <div class="p-3 rounded" style="background:var(--beige);border:1px solid var(--beige-dark);">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <i class="bi bi-truck" style="color:var(--primary);font-size:1.3rem;"></i>
                            <p class="mb-0" style="font-size:.72rem;color:var(--gray-700);">Envío gratis<br>+$800</p>
                        </div>
                        <div class="col-4">
                            <i class="bi bi-shield-check" style="color:var(--primary);font-size:1.3rem;"></i>
                            <p class="mb-0" style="font-size:.72rem;color:var(--gray-700);">Compra<br>segura</p>
                        </div>
                        <div class="col-4">
                            <i class="bi bi-arrow-counterclockwise" style="color:var(--primary);font-size:1.3rem;"></i>
                            <p class="mb-0" style="font-size:.72rem;color:var(--gray-700);">Devolución<br>15 días</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs descripción / info adicional / reseñas -->
        <div class="mt-5" data-aos="fade-up">
            <ul class="nav nav-tabs" id="productTabs">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-desc">
                        Descripción
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-info">
                        Información adicional
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reviews">
                        Reseñas (18)
                    </button>
                </li>
            </ul>
            <div class="tab-content p-4" style="background:#fff;border:1px solid #dee2e6;border-top:none;border-radius:0 0 var(--radius-md) var(--radius-md);">
                <div class="tab-pane fade show active" id="tab-desc">
                    <p style="color:var(--gray-700);line-height:1.9;"><?= nl2br(e($product['description'])) ?></p>
                </div>
                <div class="tab-pane fade" id="tab-info">
                    <table class="table table-sm">
                        <tbody>
                            <tr><th style="width:30%;color:var(--coffee);">SKU</th><td><?= e($product['sku'] ?? 'N/A') ?></td></tr>
                            <tr><th style="color:var(--coffee);">Categoría</th><td><?= e($product['category_name'] ?? 'N/A') ?></td></tr>
                            <tr><th style="color:var(--coffee);">Stock</th><td><?= $product['stock'] ?? 'Disponible' ?> unidades</td></tr>
                            <tr><th style="color:var(--coffee);">Envío</th><td>2-5 días hábiles. Gratis en pedidos +$800 MXN.</td></tr>
                            <tr><th style="color:var(--coffee);">Garantía</th><td>15 días para devoluciones. Ver política.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="tab-reviews">
                    <!-- Reseñas demo -->
                    <?php
                    $reviews_demo = [
                        ['name'=>'Laura M.','stars'=>5,'text'=>'Excelente producto, llegó perfectamente empacado y el aroma es increíble. Definitivamente volvería a comprar.','date'=>'12 Abr 2026'],
                        ['name'=>'Carlos R.','stars'=>4,'text'=>'Muy buen café, el sabor es exactamente como lo describen. El envío llegó rápido y bien cuidado.','date'=>'8 Abr 2026'],
                        ['name'=>'Ana P.','stars'=>5,'text'=>'Me encantó, ya es parte de mi rutina matutina. El blend especial tiene un balance perfecto.','date'=>'1 Abr 2026'],
                    ];
                    foreach ($reviews_demo as $r):
                    ?>
                    <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                        <div class="testimonial-avatar flex-shrink-0"><i class="bi bi-person-fill"></i></div>
                        <div>
                            <strong style="color:var(--coffee);"><?= $r['name'] ?></strong>
                            <div style="color:var(--warm-yellow);font-size:.8rem;">
                                <?php for($s=0;$s<$r['stars'];$s++) echo '<i class="bi bi-star-fill"></i>'; ?>
                            </div>
                            <p style="color:var(--gray-700);font-size:.9rem;margin:.4rem 0 0;"><?= $r['text'] ?></p>
                            <small class="text-muted"><?= $r['date'] ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <button class="btn-outline-custom btn-sm" data-bs-toggle="collapse" data-bs-target="#reviewForm">
                        <i class="bi bi-pencil me-1"></i> Escribir una reseña
                    </button>
                    <div class="collapse mt-3" id="reviewForm">
                        <div class="p-3 rounded" style="background:var(--beige);">
                            <div class="row g-2">
                                <div class="col-md-6"><input type="text" class="form-control form-control-custom" placeholder="Tu nombre"></div>
                                <div class="col-md-6"><input type="email" class="form-control form-control-custom" placeholder="Tu correo"></div>
                                <div class="col-12"><textarea class="form-control form-control-custom" rows="3" placeholder="Tu experiencia..."></textarea></div>
                                <div class="col-12"><button class="btn-primary-custom btn-sm">Enviar reseña</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos relacionados -->
        <div class="mt-5" data-aos="fade-up">
            <h3 style="font-family:var(--font-title);color:var(--coffee);margin-bottom:1.5rem;">
                También te puede interesar
            </h3>
            <div class="row g-4">
                <?php
                $related_demo = [
                    ['id'=>2,'name'=>'Café Ethiopia Single Origin','cat'=>'Café','price'=>245,'img'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&q=80','badge'=>'new'],
                    ['id'=>3,'name'=>'Prensa Francesa Premium','cat'=>'Accesorios','price'=>580,'img'=>'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&q=80','badge'=>'sale'],
                    ['id'=>9,'name'=>'Molinillo Café Manual','cat'=>'Accesorios','price'=>460,'img'=>'https://images.unsplash.com/photo-1516743619420-154b70a65fea?w=400&q=80','badge'=>''],
                    ['id'=>12,'name'=>'Vela Aromática Premium','cat'=>'Decoración','price'=>145,'img'=>'https://images.unsplash.com/photo-1603905527016-8b56f8a3a34e?w=400&q=80','badge'=>''],
                ];
                $show_related = !empty($related) ? $related : $related_demo;
                foreach (array_slice($show_related, 0, 4) as $i => $rel):
                ?>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?= $i*80 ?>">
                    <div class="product-card h-100">
                        <div class="product-card-img">
                            <a href="producto.php?id=<?= $rel['id'] ?>">
                                <img src="<?= e($rel['image'] ?? $rel['img']) ?>" alt="<?= e($rel['name']) ?>" loading="lazy">
                            </a>
                            <?php if (!empty($rel['badge'])): ?>
                            <span class="product-badge badge-<?= e($rel['badge']) ?>">
                                <?= $rel['badge']==='new'?'Nuevo':($rel['badge']==='sale'?'Oferta':'🔥') ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-body">
                            <div class="product-category"><?= e($rel['category_name'] ?? $rel['cat']) ?></div>
                            <a href="producto.php?id=<?= $rel['id'] ?>">
                                <div class="product-name"><?= e($rel['name']) ?></div>
                            </a>
                            <span class="product-price"><?= formatPrice($rel['price']) ?></span>
                            <div class="product-actions mt-2">
                                <button class="btn-add-cart" data-id="<?= $rel['id'] ?>">
                                    <i class="bi bi-cart-plus me-1"></i> Agregar
                                </button>
                                <a href="https://wa.me/524641234567?text=Hola%2C+quiero:+<?= urlencode($rel['name']) ?>"
                                   class="btn-wa-product" target="_blank">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Qty producto
    const qtyInput = document.getElementById('productQty');
    document.getElementById('qtyMinus')?.addEventListener('click', () => {
        if (parseInt(qtyInput.value) > 1) qtyInput.value--;
    });
    document.getElementById('qtyPlus')?.addEventListener('click', () => {
        qtyInput.value++;
    });

    // Agregar al carrito con qty personalizada
    document.getElementById('addToCartBtn')?.addEventListener('click', function() {
        const qty = parseInt(qtyInput?.value) || 1;
        fetch('actions/add_to_cart.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `product_id=<?= $product['id'] ?>&qty=${qty}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(`✅ ${data.product} × ${qty} agregado al carrito`, 'success');
                const badge = document.getElementById('cartCountBadge');
                if (badge) badge.textContent = data.cart_count;
            } else {
                showToast('❌ ' + data.message, 'danger');
            }
        });
    });

    // Wishlist
    document.getElementById('wishlistBtn')?.addEventListener('click', function() {
        this.classList.toggle('active');
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-heart');
        icon.classList.toggle('bi-heart-fill');
        showToast(icon.classList.contains('bi-heart-fill') ? '❤️ Guardado en favoritos' : '💔 Eliminado', 'success');
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>