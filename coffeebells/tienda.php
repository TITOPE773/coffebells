<?php
// ============================================================
// TIENDA.PHP — Catálogo de productos con filtros
// ============================================================
require_once 'includes/db.php';
require_once 'includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$pdo = getDB();

// ── FILTROS GET ───────────────────────────────────────────
$cat_slug = htmlspecialchars(trim($_GET['cat']    ?? ''), ENT_QUOTES, 'UTF-8');
$search   = htmlspecialchars(trim($_GET['q']      ?? ''), ENT_QUOTES, 'UTF-8');
$sort     = htmlspecialchars(trim($_GET['sort']   ?? 'newest'), ENT_QUOTES, 'UTF-8');
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;
$offset   = ($page - 1) * $per_page;

// ── CATEGORÍAS ────────────────────────────────────────────
$cats = $pdo->query("SELECT * FROM product_categories WHERE active = 1 ORDER BY name ASC")->fetchAll();

// ── CONSTRUCCIÓN DE QUERY DINÁMICA ────────────────────────
$where  = ['p.active = 1'];
$params = [];

if ($cat_slug) {
    $where[]             = 'pc.slug = :cat_slug';
    $params[':cat_slug'] = $cat_slug;
}
if ($search) {
    $where[]           = '(p.name LIKE :search OR p.description LIKE :search2)';
    $params[':search']  = "%{$search}%";
    $params[':search2'] = "%{$search}%";
}

$order_map = [
    'newest'     => 'p.created_at DESC',
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'popular'    => 'p.sales_count DESC',
    'name'       => 'p.name ASC',
];
$order_sql = $order_map[$sort] ?? 'p.created_at DESC';
$where_sql = implode(' AND ', $where);

// Contar total para paginación
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) FROM products p
    LEFT JOIN product_categories pc ON p.category_id = pc.id
    WHERE {$where_sql}
");
$count_stmt->execute($params);
$total_products = (int)$count_stmt->fetchColumn();
$total_pages    = ceil($total_products / $per_page);

// Obtener productos
$stmt = $pdo->prepare("
    SELECT p.*, pc.name AS category_name, pc.slug AS category_slug
    FROM products p
    LEFT JOIN product_categories pc ON p.category_id = pc.id
    WHERE {$where_sql}
    ORDER BY {$order_sql}
    LIMIT :limit OFFSET :offset
");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Nombre categoría actual
$current_cat_name = '';
if ($cat_slug) {
    foreach ($cats as $c) {
        if ($c['slug'] === $cat_slug) { $current_cat_name = $c['name']; break; }
    }
}

$meta_title       = ($search ? "Búsqueda: {$search}" : ($current_cat_name ?: 'Tienda')) . ' — CoffeeBells & Home';
$meta_description = 'Compra online productos de jardinería, decoración, iluminación y café de especialidad. Envíos a toda México.';

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <span class="page-hero-tag"><i class="bi bi-shop me-1"></i> Tienda Online</span>
        <h1><?= $search ? "Resultados: \"".e($search)."\"" : ($current_cat_name ?: 'Todos los productos') ?></h1>
        <p>Productos de calidad premium para tu hogar, jardín y mesa de café.</p>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item <?= !$cat_slug && !$search ? 'active' : '' ?>">
                    <a href="tienda.php">Tienda</a>
                </li>
                <?php if ($current_cat_name): ?>
                <li class="breadcrumb-item active"><?= e($current_cat_name) ?></li>
                <?php elseif ($search): ?>
                <li class="breadcrumb-item active">Búsqueda: "<?= e($search) ?>"</li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<section class="section-pad bg-gray">
    <div class="container">
        <div class="row g-4">

            <!-- ── SIDEBAR FILTROS ─────────────────────── -->
            <div class="col-lg-3">
                <div class="shop-sidebar">

                    <!-- Buscador -->
                    <div class="filter-card">
                        <h6><i class="bi bi-search me-2"></i>Buscar</h6>
                        <form action="tienda.php" method="GET">
                            <div class="input-group input-group-sm">
                                <input type="text" name="q" class="form-control"
                                       placeholder="Buscar productos..." value="<?= e($search) ?>">
                                <button class="btn btn-sm" style="background:var(--coffee);color:#fff;" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Categorías -->
                    <div class="filter-card">
                        <h6><i class="bi bi-grid me-2"></i>Categorías</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-1">
                                <a href="tienda.php" class="d-flex justify-content-between align-items-center text-decoration-none
                                   <?= !$cat_slug ? 'fw-bold' : '' ?>" style="color:var(--coffee);font-size:.88rem;padding:.3rem 0;">
                                    <span><i class="bi bi-grid-fill me-2" style="color:var(--primary);"></i>Todos</span>
                                    <span class="badge rounded-pill" style="background:var(--gray-200);color:var(--gray-700);">
                                        <?= $total_products ?>
                                    </span>
                                </a>
                            </li>
                            <?php foreach ($cats as $cat): ?>
                            <?php
                                $cat_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN product_categories pc ON p.category_id = pc.id WHERE pc.id = :id AND p.active = 1");
                                $cat_count_stmt->execute([':id' => $cat['id']]);
                                $cat_count = $cat_count_stmt->fetchColumn();
                            ?>
                            <li class="mb-1">
                                <a href="tienda.php?cat=<?= e($cat['slug']) ?>"
                                   class="d-flex justify-content-between align-items-center text-decoration-none
                                   <?= $cat_slug === $cat['slug'] ? 'fw-bold' : '' ?>"
                                   style="color:var(--coffee);font-size:.88rem;padding:.3rem 0;">
                                    <span>
                                        <i class="bi bi-<?= e($cat['icon'] ?? 'tag') ?> me-2" style="color:var(--primary);"></i>
                                        <?= e($cat['name']) ?>
                                    </span>
                                    <span class="badge rounded-pill" style="background:var(--gray-200);color:var(--gray-700);"><?= $cat_count ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>

                            <!-- Categorías estáticas si BD vacía -->
                            <?php if (empty($cats)): ?>
                            <?php
                            $static_cats = [
                                ['slug'=>'cafe','name'=>'Café','icon'=>'cup-hot'],
                                ['slug'=>'jardineria','name'=>'Jardinería','icon'=>'tree'],
                                ['slug'=>'decoracion','name'=>'Decoración','icon'=>'house-heart'],
                                ['slug'=>'iluminacion','name'=>'Iluminación','icon'=>'lightbulb'],
                                ['slug'=>'accesorios','name'=>'Accesorios','icon'=>'bag'],
                            ];
                            foreach ($static_cats as $sc):
                            ?>
                            <li class="mb-1">
                                <a href="tienda.php?cat=<?= $sc['slug'] ?>"
                                   class="d-flex justify-content-between text-decoration-none
                                   <?= $cat_slug === $sc['slug'] ? 'fw-bold' : '' ?>"
                                   style="color:var(--coffee);font-size:.88rem;padding:.3rem 0;">
                                    <span><i class="bi bi-<?= $sc['icon'] ?> me-2" style="color:var(--primary);"></i><?= $sc['name'] ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Filtro por precio (rango) -->
                    <div class="filter-card">
                        <h6><i class="bi bi-currency-dollar me-2"></i>Precio</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">$0</small>
                            <small class="text-muted" id="priceVal">$5,000</small>
                        </div>
                        <input type="range" class="form-range price-range" min="0" max="5000" step="50"
                               value="5000" id="priceRange">
                        <div class="d-flex gap-2 mt-2">
                            <input type="number" class="form-control form-control-sm" id="priceMin" placeholder="Min" value="0">
                            <input type="number" class="form-control form-control-sm" id="priceMax" placeholder="Max" value="5000">
                        </div>
                        <button class="btn btn-sm w-100 mt-2" style="background:var(--coffee);color:#fff;border-radius:50px;"
                                id="applyPriceFilter">Aplicar</button>
                    </div>

                    <!-- Filtro por etiqueta -->
                    <div class="filter-card">
                        <h6><i class="bi bi-tags me-2"></i>Etiquetas</h6>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="f_new" value="new">
                            <label class="form-check-label" for="f_new">Nuevos</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="f_sale" value="sale">
                            <label class="form-check-label" for="f_sale">En oferta</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="f_hot" value="hot">
                            <label class="form-check-label" for="f_hot">Más vendidos</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="f_feat" value="featured">
                            <label class="form-check-label" for="f_feat">Destacados</label>
                        </div>
                    </div>

                    <!-- CTA Sidebar -->
                    <div class="filter-card" style="background:linear-gradient(135deg,var(--coffee),var(--coffee-mid));color:#fff;">
                        <i class="bi bi-whatsapp fs-3" style="color:#25D366;"></i>
                        <h6 class="mt-2" style="color:#fff;">¿No encuentras lo que buscas?</h6>
                        <p style="font-size:.82rem;color:rgba(255,255,255,.75);">Escríbenos, tenemos más productos disponibles.</p>
                        <a href="https://wa.me/524641234567?text=Hola%2C+busco+un+producto+específico"
                           class="btn-wa-green btn-sm w-100 justify-content-center d-flex" target="_blank" style="font-size:.82rem;">
                            <i class="bi bi-whatsapp me-1"></i> Preguntar ahora
                        </a>
                    </div>
                </div>
            </div>

            <!-- ── PRODUCTOS ────────────────────────────── -->
            <div class="col-lg-9">

                <!-- Toolbar -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                    <p class="text-muted mb-0 small">
                        Mostrando <strong><?= count($products) ?></strong> de <strong><?= $total_products ?></strong> productos
                        <?= $search ? " para \"".e($search)."\"" : '' ?>
                    </p>
                    <div class="d-flex gap-2 align-items-center">
                        <select class="form-select form-select-sm" id="sortSelect" style="width:auto;"
                                onchange="window.location='tienda.php?sort='+this.value+'<?= $cat_slug ? '&cat='.$cat_slug : '' ?><?= $search ? '&q='.$search : '' ?>'">
                            <option value="newest"     <?= $sort==='newest'     ?'selected':'' ?>>Más recientes</option>
                            <option value="price_asc"  <?= $sort==='price_asc'  ?'selected':'' ?>>Precio: menor a mayor</option>
                            <option value="price_desc" <?= $sort==='price_desc' ?'selected':'' ?>>Precio: mayor a menor</option>
                            <option value="popular"    <?= $sort==='popular'    ?'selected':'' ?>>Más vendidos</option>
                            <option value="name"       <?= $sort==='name'       ?'selected':'' ?>>Nombre A-Z</option>
                        </select>
                        <!-- Vista grid/lista -->
                        <button class="btn btn-sm btn-outline-secondary" id="viewGrid" title="Vista grilla">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="viewList" title="Vista lista">
                            <i class="bi bi-list-ul"></i>
                        </button>
                    </div>
                </div>

                <?php if (!empty($products)): ?>
                <div class="row g-4" id="productsGrid">
                    <?php foreach ($products as $i => $prod): ?>
                    <div class="col-6 col-md-4 col-lg-4 product-col" data-price="<?= $prod['price'] ?>"
                         data-badge="<?= e($prod['badge'] ?? '') ?>"
                         data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 80 ?>">
                        <div class="product-card h-100">
                            <div class="product-card-img">
                                <a href="producto.php?id=<?= $prod['id'] ?>">
                                    <img src="<?= e($prod['image'] ?: 'assets/img/product-default.jpg') ?>"
                                         alt="<?= e($prod['name']) ?>" loading="lazy">
                                </a>
                                <?php if (!empty($prod['badge'])): ?>
                                <span class="product-badge badge-<?= e($prod['badge']) ?>">
                                    <?= $prod['badge']==='new' ? 'Nuevo' : ($prod['badge']==='sale' ? 'Oferta' : '🔥 Hot') ?>
                                </span>
                                <?php endif; ?>
                                <?php if (!is_null($prod['stock']) && $prod['stock'] <= 3 && $prod['stock'] > 0): ?>
                                <span class="product-badge" style="top:auto;bottom:.75rem;background:var(--warm-yellow);color:var(--coffee);">
                                    ¡Solo <?= $prod['stock'] ?> restantes!
                                </span>
                                <?php endif; ?>
                                <button class="product-wishlist" data-id="<?= $prod['id'] ?>" title="Guardar en favoritos">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                            <div class="product-card-body">
                                <div class="product-category"><?= e($prod['category_name'] ?? '') ?></div>
                                <a href="producto.php?id=<?= $prod['id'] ?>">
                                    <div class="product-name"><?= e($prod['name']) ?></div>
                                </a>
                                <?php if (!empty($prod['short_description'])): ?>
                                <p class="text-muted" style="font-size:.8rem;margin:.3rem 0;"><?= e(mb_substr($prod['short_description'],0,70)) ?>...</p>
                                <?php endif; ?>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="product-price"><?= formatPrice($prod['price']) ?></span>
                                    <?php if (!empty($prod['price_old'])): ?>
                                    <span class="product-price-old"><?= formatPrice($prod['price_old']) ?></span>
                                    <?php if ($prod['price_old'] > 0): ?>
                                    <span class="badge" style="background:var(--terracota);color:#fff;font-size:.68rem;">
                                        -<?= round((1 - $prod['price']/$prod['price_old'])*100) ?>%
                                    </span>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <!-- Estrellas demo -->
                                <div style="color:var(--warm-yellow);font-size:.75rem;margin-top:.3rem;">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                    <small class="text-muted">(<?= rand(4,28) ?>)</small>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart" data-id="<?= $prod['id'] ?>">
                                        <i class="bi bi-cart-plus me-1"></i> Agregar
                                    </button>
                                    <a href="https://wa.me/524641234567?text=Hola%2C+quiero+comprar:+<?= urlencode($prod['name']) ?>+(%24<?= $prod['price'] ?>+MXN)"
                                       class="btn-wa-product" target="_blank" title="Pedir por WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- DEMO si BD vacía -->
                <?php else: ?>
                <div class="row g-4" id="productsGrid">
                <?php
                $demo_prods = [
                    ['id'=>1,'name'=>'Café Blend Especial 250g','cat'=>'Café','price'=>189,'price_old'=>220,'badge'=>'hot','img'=>'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=400&q=80'],
                    ['id'=>2,'name'=>'Café Ethiopia Single Origin','cat'=>'Café','price'=>245,'price_old'=>0,'badge'=>'new','img'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&q=80'],
                    ['id'=>3,'name'=>'Prensa Francesa Premium','cat'=>'Accesorios','price'=>580,'price_old'=>750,'badge'=>'sale','img'=>'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&q=80'],
                    ['id'=>4,'name'=>'Maceta Terracota Grande','cat'=>'Jardinería','price'=>349,'price_old'=>0,'badge'=>'new','img'=>'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400&q=80'],
                    ['id'=>5,'name'=>'Kit Huerto Urbano Completo','cat'=>'Jardinería','price'=>750,'price_old'=>900,'badge'=>'hot','img'=>'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&q=80'],
                    ['id'=>6,'name'=>'Lámpara Colgante Diseño','cat'=>'Iluminación','price'=>1290,'price_old'=>1500,'badge'=>'sale','img'=>'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?w=400&q=80'],
                    ['id'=>7,'name'=>'Cactus Ornamental','cat'=>'Jardinería','price'=>220,'price_old'=>0,'badge'=>'new','img'=>'https://images.unsplash.com/photo-1463936575829-25148e1db1b8?w=400&q=80'],
                    ['id'=>8,'name'=>'Cuadro Abstracto 60x80','cat'=>'Decoración','price'=>890,'price_old'=>1100,'badge'=>'','img'=>'https://images.unsplash.com/photo-1549887552-cb1071d3e5ca?w=400&q=80'],
                    ['id'=>9,'name'=>'Molinillo Café Manual','cat'=>'Accesorios','price'=>460,'price_old'=>0,'badge'=>'','img'=>'https://images.unsplash.com/photo-1516743619420-154b70a65fea?w=400&q=80'],
                    ['id'=>10,'name'=>'Suculentas Mix x3','cat'=>'Jardinería','price'=>180,'price_old'=>0,'badge'=>'new','img'=>'https://images.unsplash.com/photo-1459156212016-c812468e2115?w=400&q=80'],
                    ['id'=>11,'name'=>'Tira LED Ambiental 5m','cat'=>'Iluminación','price'=>395,'price_old'=>480,'badge'=>'sale','img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80'],
                    ['id'=>12,'name'=>'Vela Aromática Premium','cat'=>'Decoración','price'=>145,'price_old'=>0,'badge'=>'','img'=>'https://images.unsplash.com/photo-1603905527016-8b56f8a3a34e?w=400&q=80'],
                ];
                foreach ($demo_prods as $i => $p):
                ?>
                <div class="col-6 col-md-4 product-col" data-price="<?= $p['price'] ?>"
                     data-badge="<?= $p['badge'] ?>"
                     data-aos="fade-up" data-aos-delay="<?= ($i%3)*80 ?>">
                    <div class="product-card h-100">
                        <div class="product-card-img">
                            <a href="producto.php?id=<?= $p['id'] ?>">
                                <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>" loading="lazy">
                            </a>
                            <?php if ($p['badge']): ?>
                            <span class="product-badge badge-<?= $p['badge'] ?>">
                                <?= $p['badge']==='new'?'Nuevo':($p['badge']==='sale'?'Oferta':'🔥 Hot') ?>
                            </span>
                            <?php endif; ?>
                            <button class="product-wishlist" title="Favoritos"><i class="bi bi-heart"></i></button>
                        </div>
                        <div class="product-card-body">
                            <div class="product-category"><?= $p['cat'] ?></div>
                            <a href="producto.php?id=<?= $p['id'] ?>">
                                <div class="product-name"><?= $p['name'] ?></div>
                            </a>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="product-price">$<?= number_format($p['price'],2) ?> MXN</span>
                                <?php if ($p['price_old']): ?>
                                <span class="product-price-old">$<?= number_format($p['price_old'],2) ?></span>
                                <span class="badge" style="background:var(--terracota);color:#fff;font-size:.68rem;">
                                    -<?= round((1-$p['price']/$p['price_old'])*100) ?>%
                                </span>
                                <?php endif; ?>
                            </div>
                            <div style="color:var(--warm-yellow);font-size:.75rem;margin-top:.3rem;">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                                <small class="text-muted">(<?= rand(4,28) ?>)</small>
                            </div>
                            <div class="product-actions">
                                <button class="btn-add-cart" data-id="<?= $p['id'] ?>">
                                    <i class="bi bi-cart-plus me-1"></i> Agregar
                                </button>
                                <a href="https://wa.me/524641234567?text=Hola%2C+quiero:+<?= urlencode($p['name']) ?>"
                                   class="btn-wa-product" target="_blank">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- PAGINACIÓN -->
                <?php if ($total_pages > 1): ?>
                <nav class="mt-5 d-flex justify-content-center">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="tienda.php?page=<?= $page-1 ?>&cat=<?= e($cat_slug) ?>&sort=<?= e($sort) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="tienda.php?page=<?= $p ?>&cat=<?= e($cat_slug) ?>&sort=<?= e($sort) ?>"><?= $p ?></a>
                        </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="tienda.php?page=<?= $page+1 ?>&cat=<?= e($cat_slug) ?>&sort=<?= e($sort) ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rango de precio
    const priceRange = document.getElementById('priceRange');
    const priceVal   = document.getElementById('priceVal');
    const priceMax   = document.getElementById('priceMax');
    if (priceRange) {
        priceRange.addEventListener('input', function() {
            priceVal.textContent = '$' + parseInt(this.value).toLocaleString('es-MX');
            priceMax.value = this.value;
        });
    }

    // Aplicar filtro de precio en cliente
    document.getElementById('applyPriceFilter')?.addEventListener('click', function() {
        const min = parseInt(document.getElementById('priceMin').value) || 0;
        const max = parseInt(document.getElementById('priceMax').value) || 999999;
        document.querySelectorAll('.product-col').forEach(col => {
            const price = parseFloat(col.dataset.price);
            col.style.display = (price >= min && price <= max) ? '' : 'none';
        });
    });

    // Filtro por badge en cliente
    ['f_new','f_sale','f_hot','f_feat'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', applyBadgeFilter);
    });
    function applyBadgeFilter() {
        const selected = [...document.querySelectorAll('#f_new,#f_sale,#f_hot,#f_feat')]
            .filter(cb => cb.checked).map(cb => cb.value);
        document.querySelectorAll('.product-col').forEach(col => {
            if (!selected.length) { col.style.display = ''; return; }
            col.style.display = selected.some(s => col.dataset.badge === s || (s === 'featured' && col.dataset.featured)) ? '' : 'none';
        });
    }

    // Vista grid/lista
    document.getElementById('viewList')?.addEventListener('click', function() {
        document.querySelectorAll('.product-col').forEach(c => {
            c.className = c.className.replace('col-6 col-md-4 col-lg-4','col-12');
        });
    });
    document.getElementById('viewGrid')?.addEventListener('click', function() {
        document.querySelectorAll('.product-col').forEach(c => {
            c.className = c.className.replace('col-12','col-6 col-md-4 col-lg-4');
        });
    });

    // Wishlist toggle
    document.querySelectorAll('.product-wishlist').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-heart');
            icon.classList.toggle('bi-heart-fill');
            showToast(this.classList.contains('active') ? '❤️ Guardado en favoritos' : '💔 Eliminado de favoritos', 'success');
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>