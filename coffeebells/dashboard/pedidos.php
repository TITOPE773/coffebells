<?php
// ============================================================
// DASHBOARD/PEDIDOS.PHP — Gestión completa de pedidos
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
requireAdmin();

$pdo    = getDB();
$view   = (int)($_GET['view'] ?? 0);
$status = htmlspecialchars($_GET['status'] ?? '', ENT_QUOTES, 'UTF-8');

// ── ACTUALIZAR ESTADO ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id   = (int)$_POST['order_id'];
    $new_status = htmlspecialchars($_POST['new_status'], ENT_QUOTES, 'UTF-8');
    $pdo->prepare("UPDATE orders SET status=:s, updated_at=NOW() WHERE id=:id")
        ->execute([':s' => $new_status, ':id' => $order_id]);
    setFlash('success', 'Estado del pedido actualizado.');
    redirect("pedidos.php" . ($view ? "?view={$order_id}" : ''));
}

// ── VER DETALLE ───────────────────────────────────────────
if ($view > 0) {
    $order = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
    $order->execute([':id' => $view]);
    $order = $order->fetch();

    $order_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :id");
    $order_items->execute([':id' => $view]);
    $order_items = $order_items->fetchAll();
}

// ── LISTADO ───────────────────────────────────────────────
$where  = $status ? "WHERE status = :status" : "";
$params = $status ? [':status' => $status] : [];

$page_num = max(1, (int)($_GET['page'] ?? 1));
$per_page = 15;
$offset   = ($page_num - 1) * $per_page;

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM orders $where");
$total_stmt->execute($params);
$total_count = (int)$total_stmt->fetchColumn();
$total_pages = ceil($total_count / $per_page);

$stmt = $pdo->prepare("SELECT * FROM orders $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();

$page_title = 'Pedidos';
require_once 'includes/dash_header.php';
?>

<?php if ($view > 0 && $order): ?>
<!-- ── DETALLE DE PEDIDO ──────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="dash-page-title mb-1">Pedido: <code><?= e($order['folio']) ?></code></h4>
        <p class="text-muted small mb-0"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
    </div>
    <a href="pedidos.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Items -->
        <div class="dash-card mb-4">
            <div class="dash-card-header"><h6>Productos del pedido</h6></div>
            <div class="table-responsive">
                <table class="table dash-table mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td style="font-size:.88rem;"><?= e($item['product_name']) ?></td>
                            <td class="text-center"><?= $item['qty'] ?></td>
                            <td class="text-end"><?= formatPrice($item['price']) ?></td>
                            <td class="text-end" style="font-weight:700;color:var(--terracota);"><?= formatPrice($item['subtotal']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end fw-semibold">Subtotal</td><td class="text-end"><?= formatPrice($order['subtotal']) ?></td></tr>
                        <tr><td colspan="3" class="text-end fw-semibold">Envío</td><td class="text-end"><?= $order['shipping'] == 0 ? 'Gratis' : formatPrice($order['shipping']) ?></td></tr>
                        <tr style="background:var(--beige);">
                            <td colspan="3" class="text-end fw-bold" style="color:var(--coffee);">Total</td>
                            <td class="text-end fw-bold" style="color:var(--terracota);font-size:1.1rem;"><?= formatPrice($order['total']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Cliente -->
        <div class="dash-card">
            <div class="dash-card-header"><h6>Datos del cliente</h6></div>
            <div class="dash-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Nombre</label>
                        <div><?= e($order['customer_name']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Teléfono</label>
                        <div>
                            <?= e($order['customer_phone']) ?>
                            <a href="https://wa.me/52<?= preg_replace('/\D/','',$order['customer_phone']) ?>"
                               target="_blank" class="ms-2" style="color:#25D366;">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Email</label>
                        <div><?= e($order['customer_email'] ?: '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Método de pago</label>
                        <div><?= e(ucfirst($order['payment_method'])) ?></div>
                    </div>
                    <div class="col-12">
                        <label class="small fw-semibold text-muted">Dirección</label>
                        <div><?= e($order['customer_address'] ?: '—') ?>, <?= e($order['customer_city'] ?: '') ?></div>
                    </div>
                    <?php if ($order['notes']): ?>
                    <div class="col-12">
                        <label class="small fw-semibold text-muted">Notas</label>
                        <div><?= e($order['notes']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado y acciones -->
    <div class="col-lg-4">
        <div class="dash-card mb-4">
            <div class="dash-card-header"><h6>Estado del pedido</h6></div>
            <div class="dash-card-body">
                <div class="mb-3 text-center">
                    <?= orderStatusBadge($order['status'], true) ?>
                </div>
                <form method="POST">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="new_status" class="form-select mb-3">
                        <?php
                        $statuses = ['pending'=>'⏳ Pendiente','confirmed'=>'✅ Confirmado','processing'=>'🔧 En proceso','shipped'=>'🚚 Enviado','delivered'=>'📦 Entregado','cancelled'=>'❌ Cancelado'];
                        foreach ($statuses as $key => $label):
                        ?>
                        <option value="<?= $key ?>" <?= $order['status']==$key?'selected':'' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-primary-custom w-100 justify-content-center btn-sm">
                        <i class="bi bi-arrow-repeat me-1"></i> Actualizar estado
                    </button>
                </form>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="dash-card">
            <div class="dash-card-header"><h6>Acciones rápidas</h6></div>
            <div class="dash-card-body d-grid gap-2">
                <a href="https://wa.me/52<?= preg_replace('/\D/','',$order['customer_phone']) ?>?text=Hola+<?= urlencode($order['customer_name']) ?>%2C+tu+pedido+<?= urlencode($order['folio']) ?>+ya+fue+procesado."
                   class="btn-wa-green justify-content-center btn-sm" target="_blank">
                    <i class="bi bi-whatsapp"></i> Notificar por WhatsApp
                </a>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Imprimir pedido
                </button>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- ── LISTADO PEDIDOS ────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="dash-page-title mb-0">Pedidos</h4>
    <small class="text-muted"><?= $total_count ?> pedidos</small>
</div>

<!-- Filtros por estado -->
<div class="d-flex gap-2 mb-3 flex-wrap">
    <?php
    $status_tabs = [''=>'Todos','pending'=>'⏳ Pendientes','confirmed'=>'✅ Confirmados','processing'=>'🔧 En proceso','shipped'=>'🚚 Enviados','delivered'=>'📦 Entregados','cancelled'=>'❌ Cancelados'];
    foreach ($status_tabs as $key => $label):
    ?>
    <a href="pedidos.php?status=<?= $key ?>" class="btn btn-sm <?= $status===$key?'btn-coffee':'btn-outline-secondary' ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="dash-card">
    <div class="table-responsive">
        <table class="table table-hover dash-table mb-0">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Total</th>
                    <th>Pago</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><code style="font-size:.78rem;"><?= e($order['folio']) ?></code></td>
                        <td style="font-size:.88rem;font-weight:600;"><?= e($order['customer_name']) ?></td>
                        <td>
                            <a href="https://wa.me/52<?= preg_replace('/\D/','',$order['customer_phone']) ?>"
                               style="color:#25D366;" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i><?= e($order['customer_phone']) ?>
                            </a>
                        </td>
                        <td style="font-weight:700;color:var(--terracota);"><?= formatPrice($order['total']) ?></td>
                        <td style="font-size:.82rem;"><?= e(ucfirst($order['payment_method'])) ?></td>
                        <td><?= orderStatusBadge($order['status']) ?></td>
                        <td style="font-size:.78rem;color:var(--gray-500);">
                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                        </td>
                        <td>
                            <a href="pedidos.php?view=<?= $order['id'] ?>" class="btn btn-xs"
                               style="background:var(--beige);color:var(--coffee);border-radius:6px;padding:.3rem .6rem;">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin pedidos <?= $status ? "con estado \"$status\"" : '' ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/dash_footer.php'; ?>