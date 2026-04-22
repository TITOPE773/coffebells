<?php
// ============================================================
// DASHBOARD/PRODUCTOS.PHP — CRUD completo de productos
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
requireAdmin();

$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$filter = $_GET['filter'] ?? '';

// ── PROCESAR POST ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['post_action'] ?? '';

    // GUARDAR / ACTUALIZAR PRODUCTO
    if ($post_action === 'save_product') {
        $data = [
            ':name'        => trim($_POST['name'] ?? ''),
            ':slug'        => slugify($_POST['name'] ?? ''),
            ':category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
            ':price'       => (float)($_POST['price'] ?? 0),
            ':price_old'   => (float)($_POST['price_old'] ?? 0) ?: null,
            ':stock'       => ($_POST['stock'] !== '') ? (int)$_POST['stock'] : null,
            ':description' => trim($_POST['description'] ?? ''),
            ':short_desc'  => trim($_POST['short_description'] ?? ''),
            ':sku'         => trim($_POST['sku'] ?? ''),
            ':badge'       => trim($_POST['badge'] ?? ''),
            ':featured'    => isset($_POST['featured']) ? 1 : 0,
            ':active'      => isset($_POST['active']) ? 1 : 0,
        ];

        // Subir imagen si viene
        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $image = uploadImage($_FILES['image'], '../assets/img/products/');
        } elseif (!empty($_POST['image_current'])) {
            $image = $_POST['image_current'];
        }
        $data[':image'] = $image;

        $edit_id = (int)($_POST['edit_id'] ?? 0);
        if ($edit_id > 0) {
            $data[':id'] = $edit_id;
            $sql = "UPDATE products SET name=:name, slug=:slug, category_id=:category_id,
                    price=:price, price_old=:price_old, stock=:stock, description=:description,
                    short_description=:short_desc, sku=:sku, badge=:badge, featured=:featured,
                    active=:active, image=:image, updated_at=NOW()
                    WHERE id=:id";
        } else {
            $sql = "INSERT INTO products (name,slug,category_id,price,price_old,stock,description,
                    short_description,sku,badge,featured,active,image,created_at,updated_at)
                    VALUES (:name,:slug,:category_id,:price,:price_old,:stock,:description,
                    :short_desc,:sku,:badge,:featured,:active,:image,NOW(),NOW())";
        }
        $pdo->prepare($sql)->execute($data);
        setFlash('success', $edit_id > 0 ? 'Producto actualizado.' : 'Producto creado correctamente.');
        redirect('productos.php');
    }

    // ELIMINAR PRODUCTO
    if ($post_action === 'delete_product') {
        $del_id = (int)($_POST['del_id'] ?? 0);
        $pdo->prepare("UPDATE products SET active = 0 WHERE id = :id")->execute([':id' => $del_id]);
        setFlash('success', 'Producto eliminado.');
        redirect('productos.php');
    }

    // TOGGLE ACTIVO/INACTIVO
    if ($post_action === 'toggle_active') {
        $tog_id  = (int)($_POST['tog_id'] ?? 0);
        $current = (int)($_POST['current'] ?? 1);
        $pdo->prepare("UPDATE products SET active = :val WHERE id = :id")
            ->execute([':val' => $current ? 0 : 1, ':id' => $tog_id]);
        echo json_encode(['success' => true]);
        exit;
    }
}

// ── CARGAR PRODUCTO PARA EDITAR ───────────────────────────
$edit_product = null;
if ($action === 'edit' && $id > 0) {
    $edit_product = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $edit_product->execute([':id' => $id]);
    $edit_product = $edit_product->fetch();
}

// ── CATEGORÍAS ────────────────────────────────────────────
$cats = $pdo->query("SELECT * FROM product_categories ORDER BY name")->fetchAll();

// ── QUERY LISTADO ─────────────────────────────────────────
$where  = [];
$params = [];
if ($filter === 'low_stock')  { $where[] = 'stock IS NOT NULL AND stock <= 3 AND active = 1'; }
if ($filter === 'inactive')   { $where[] = 'active = 0'; }
if ($filter === 'featured')   { $where[] = 'featured = 1 AND active = 1'; }
if (!$filter)                 { $where[] = 'active = 1'; }
if (!empty($_GET['q'])) {
    $where[] = 'name LIKE :q';
    $params[':q'] = '%'.trim($_GET['q']).'%';
}
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$page_num = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset   = ($page_num - 1) * $per_page;

$total_count  = $pdo->prepare("SELECT COUNT(*) FROM products $where_sql");
$total_count->execute($params);
$total_count  = (int)$total_count->fetchColumn();
$total_pages  = ceil($total_count / $per_page);

$stmt = $pdo->prepare("
    SELECT p.*, pc.name AS category_name
    FROM products p
    LEFT JOIN product_categories pc ON p.category_id = pc.id
    $where_sql
    ORDER BY p.created_at DESC
    LIMIT :limit OFFSET :offset
");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$page_title = $action === 'new' || $action === 'edit' ? 'Productos — ' . ($action === 'new' ? 'Nuevo' : 'Editar') : 'Productos';
require_once 'includes/dash_header.php';
?>

<?php if ($action === 'list'): ?>
<!-- ── LISTADO DE PRODUCTOS ──────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="dash-page-title mb-1">Productos</h4>
        <small class="text-muted"><?= $total_count ?> productos <?= $filter ?: '' ?></small>
    </div>
    <a href="productos.php?action=new" class="btn-primary-custom btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Nuevo producto
    </a>
</div>

<!-- Filtros rápidos -->
<div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="productos.php" class="btn btn-sm <?= !$filter?'btn-coffee':'btn-outline-secondary' ?>">Todos</a>
    <a href="productos.php?filter=low_stock" class="btn btn-sm <?= $filter==='low_stock'?'btn-danger':'btn-outline-danger' ?>">
        <i class="bi bi-exclamation-triangle me-1"></i>Stock bajo
    </a>
    <a href="productos.php?filter=featured" class="btn btn-sm <?= $filter==='featured'?'btn-warning':'btn-outline-warning' ?>">
        <i class="bi bi-star me-1"></i>Destacados
    </a>
    <a href="productos.php?filter=inactive" class="btn btn-sm <?= $filter==='inactive'?'btn-secondary':'btn-outline-secondary' ?>">
        <i class="bi bi-eye-slash me-1"></i>Inactivos
    </a>
    <!-- Buscador -->
    <form class="d-flex gap-1 ms-auto" method="GET">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Buscar producto..."
               value="<?= e($_GET['q'] ?? '') ?>" style="width:200px;">
        <button class="btn btn-sm btn-coffee"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="dash-card">
    <div class="table-responsive">
        <table class="table table-hover dash-table mb-0">
            <thead>
                <tr>
                    <th style="width:60px;">Imagen</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $prod): ?>
                    <tr>
                        <td>
                            <img src="<?= e($prod['image'] ?: '../assets/img/product-default.jpg') ?>"
                                 style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:.88rem;color:var(--coffee);"><?= e($prod['name']) ?></div>
                            <div class="text-muted" style="font-size:.75rem;">SKU: <?= e($prod['sku'] ?: '—') ?></div>
                            <?php if ($prod['featured']): ?>
                            <span class="badge" style="background:var(--warm-yellow);color:var(--coffee);font-size:.65rem;">⭐ Destacado</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.85rem;"><?= e($prod['category_name'] ?? '—') ?></td>
                        <td>
                            <div style="font-weight:700;color:var(--terracota);"><?= formatPrice($prod['price']) ?></div>
                            <?php if ($prod['price_old']): ?>
                            <small class="text-muted text-decoration-line-through"><?= formatPrice($prod['price_old']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (is_null($prod['stock'])): ?>
                            <span class="badge bg-secondary">∞</span>
                            <?php elseif ($prod['stock'] <= 0): ?>
                            <span class="badge bg-danger">Agotado</span>
                            <?php elseif ($prod['stock'] <= 3): ?>
                            <span class="badge bg-warning text-dark"><?= $prod['stock'] ?></span>
                            <?php else: ?>
                            <span class="badge bg-success"><?= $prod['stock'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-active" type="checkbox"
                                       <?= $prod['active'] ? 'checked' : '' ?>
                                       data-id="<?= $prod['id'] ?>" data-current="<?= $prod['active'] ?>">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="productos.php?action=edit&id=<?= $prod['id'] ?>"
                                   class="btn btn-xs" style="background:var(--beige);color:var(--coffee);border-radius:6px;padding:.3rem .6rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" onsubmit="return confirm('¿Eliminar este producto?');" style="display:inline;">
                                    <input type="hidden" name="post_action" value="delete_product">
                                    <input type="hidden" name="del_id" value="<?= $prod['id'] ?>">
                                    <button type="submit" class="btn btn-xs"
                                            style="background:rgba(220,53,69,.1);color:#dc3545;border-radius:6px;padding:.3rem .6rem;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                                <a href="../producto.php?id=<?= $prod['id'] ?>" target="_blank"
                                   class="btn btn-xs" style="background:rgba(107,143,113,.1);color:var(--primary);border-radius:6px;padding:.3rem .6rem;"
                                   title="Ver en sitio">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted py-4">
                    Sin productos <?= $filter ? 'en esta categoría' : '' ?>
                    <a href="productos.php?action=new" class="d-block mt-2 btn-primary-custom btn-sm" style="width:fit-content;margin:0 auto;">
                        + Agregar primer producto
                    </a>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Paginación -->
<?php if ($total_pages > 1): ?>
<nav class="mt-3 d-flex justify-content-center">
    <ul class="pagination pagination-sm">
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <li class="page-item <?= $p === $page_num ? 'active' : '' ?>">
            <a class="page-link" href="productos.php?page=<?= $p ?>&filter=<?= e($filter) ?>"><?= $p ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php else: ?>
<!-- ── FORMULARIO CREAR / EDITAR ─────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="dash-page-title mb-0">
        <?= $action === 'new' ? '<i class="bi bi-plus-circle me-2"></i>Nuevo producto' : '<i class="bi bi-pencil me-2"></i>Editar: '.e($edit_product['name'] ?? '') ?>
    </h4>
    <a href="productos.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<form action="productos.php" method="POST" enctype="multipart/form-data" id="productForm">
    <input type="hidden" name="post_action" value="save_product">
    <input type="hidden" name="edit_id" value="<?= $edit_product['id'] ?? 0 ?>">
    <input type="hidden" name="image_current" value="<?= e($edit_product['image'] ?? '') ?>">

    <div class="row g-4">
        <!-- Col principal -->
        <div class="col-lg-8">
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>Información básica</h6></div>
                <div class="dash-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del producto <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-custom"
                               placeholder="Ej: Café Blend Especial 250g" required
                               value="<?= e($edit_product['name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción corta</label>
                        <input type="text" name="short_description" class="form-control form-control-custom"
                               placeholder="Descripción breve para listados (máx 160 chars)"
                               maxlength="160"
                               value="<?= e($edit_product['short_description'] ?? '') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Descripción completa</label>
                        <textarea name="description" class="form-control form-control-custom" rows="6"
                                  placeholder="Descripción detallada del producto..."><?= e($edit_product['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Imagen -->
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>Imagen del producto</h6></div>
                <div class="dash-card-body">
                    <div class="d-flex gap-3 align-items-start flex-wrap">
                        <?php if (!empty($edit_product['image'])): ?>
                        <div>
                            <img src="<?= e('../' . $edit_product['image']) ?>" id="imgPreview"
                                 style="width:120px;height:120px;object-fit:cover;border-radius:12px;border:2px solid var(--gray-200);">
                        </div>
                        <?php else: ?>
                        <div id="imgPreview" style="width:120px;height:120px;border-radius:12px;border:2px dashed var(--gray-300);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-image text-muted fs-2"></i>
                        </div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <label class="form-label fw-semibold">Subir imagen</label>
                            <input type="file" name="image" class="form-control form-control-custom"
                                   accept="image/*" id="imageInput">
                            <div class="form-text">JPG, PNG, WebP — máx 2MB. Recomendado: 800×800px.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Col lateral -->
        <div class="col-lg-4">
            <!-- Precio y stock -->
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>Precio y stock</h6></div>
                <div class="dash-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Precio (MXN) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="price" class="form-control" step="0.01" min="0"
                                   placeholder="0.00" required
                                   value="<?= e($edit_product['price'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Precio anterior (tachado)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="price_old" class="form-control" step="0.01" min="0"
                                   placeholder="0.00"
                                   value="<?= e($edit_product['price_old'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stock (dejar vacío = ilimitado)</label>
                        <input type="number" name="stock" class="form-control" min="0"
                               placeholder="Ilimitado"
                               value="<?= e($edit_product['stock'] ?? '') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">SKU</label>
                        <input type="text" name="sku" class="form-control"
                               placeholder="CB-CAT-001"
                               value="<?= e($edit_product['sku'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Organización -->
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>Organización</h6></div>
                <div class="dash-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoría</label>
                        <select name="category_id" class="form-select">
                            <option value="">Sin categoría</option>
                            <?php foreach ($cats as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                    <?= ($edit_product['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Etiqueta / Badge</label>
                        <select name="badge" class="form-select">
                            <option value="">Sin etiqueta</option>
                            <option value="new"  <?= ($edit_product['badge'] ?? '')==='new'  ?'selected':'' ?>>🆕 Nuevo</option>
                            <option value="sale" <?= ($edit_product['badge'] ?? '')==='sale' ?'selected':'' ?>>💸 Oferta</option>
                            <option value="hot"  <?= ($edit_product['badge'] ?? '')==='hot'  ?'selected':'' ?>>🔥 Hot</option>
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="featured" id="chk_featured"
                               <?= !empty($edit_product['featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="chk_featured">⭐ Producto destacado</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="active" id="chk_active"
                               <?= ($action === 'new' || !empty($edit_product['active'])) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="chk_active">Visible en tienda</label>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn-primary-custom justify-content-center">
                    <i class="bi bi-floppy-fill"></i>
                    <?= $action === 'new' ? 'Crear producto' : 'Guardar cambios' ?>
                </button>
                <a href="productos.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i> Cancelar
                </a>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>

<script>
// Toggle activo/inactivo con AJAX
document.querySelectorAll('.toggle-active').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const formData = new FormData();
        formData.append('post_action', 'toggle_active');
        formData.append('tog_id', this.dataset.id);
        formData.append('current', this.dataset.current);
        fetch('productos.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                this.dataset.current = this.dataset.current == '1' ? '0' : '1';
                showToast(this.checked ? '✅ Producto activado' : '⛔ Producto ocultado', 'success');
            }
        });
    });
});
// Preview de imagen
document.getElementById('imageInput')?.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const prev = document.getElementById('imgPreview');
        if (prev.tagName === 'IMG') prev.src = e.target.result;
        else {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.id  = 'imgPreview';
            img.style = 'width:120px;height:120px;object-fit:cover;border-radius:12px;border:2px solid var(--gray-200);';
            prev.replaceWith(img);
        }
    };
    reader.readAsDataURL(file);
});
</script>

<?php require_once 'includes/dash_footer.php'; ?>