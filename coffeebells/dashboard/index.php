<?php
// ============================================================
// DASHBOARD/INDEX.PHP — Panel principal de administración
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

$pdo = getDB();

// ── MÉTRICAS PRINCIPALES ──────────────────────────────────
$total_orders    = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$orders_today    = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$revenue_total   = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$revenue_month   = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) AND status != 'cancelled'")->fetchColumn();
$total_contacts  = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$new_contacts    = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'")->fetchColumn();
$total_products  = $pdo->query("SELECT COUNT(*) FROM products WHERE active = 1")->fetchColumn();
$low_stock       = $pdo->query("SELECT COUNT(*) FROM products WHERE stock IS NOT NULL AND stock <= 3 AND active = 1")->fetchColumn();
$total_users     = $pdo->query("SELECT COUNT(*) FROM users WHERE active = 1")->fetchColumn();
$new_users_month = $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn();

// ── ÚLTIMOS PEDIDOS ───────────────────────────────────────
$recent_orders = $pdo->query("
    SELECT * FROM orders ORDER BY created_at DESC LIMIT 8
")->fetchAll();

// ── ÚLTIMOS CONTACTOS ─────────────────────────────────────
$recent_contacts = $pdo->query("
    SELECT * FROM contacts ORDER BY created_at DESC LIMIT 6
")->fetchAll();

// ── GRÁFICO VENTAS ÚLTIMOS 7 DÍAS ────────────────────────
$sales_chart = $pdo->query("
    SELECT DATE(created_at) AS day, COALESCE(SUM(total),0) AS total
    FROM orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    AND status != 'cancelled'
    GROUP BY DATE(created_at)
    ORDER BY day ASC
")->fetchAll();

// ── PRODUCTOS MÁS VENDIDOS ────────────────────────────────
$top_products = $pdo->query("
    SELECT p.name, SUM(oi.qty) AS total_sold, SUM(oi.subtotal) AS revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 5
")->fetchAll();

$page_title = 'Dashboard';
require_once 'includes/dash_header.php';
?>

<!-- ── BIENVENIDA ────────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="dash-page-title mb-1">¡Buen día, <?= e($_SESSION['admin_name'] ?? 'Admin') ?>! 👋</h4>
        <p class="text-muted mb-0 small"><?= date('l, d \d\e F \d\e Y') ?> — Resumen del negocio</p>
    </div>
    <div class="d-flex gap-2">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary" target="_blank">
            <i class="bi bi-eye me-1"></i> Ver sitio
        </a>
        <a href="pedidos.php?status=new" class="btn btn-sm btn-success position-relative">
            <i class="bi bi-bell me-1"></i> Alertas
            <?php if ($new_contacts > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?= $new_contacts ?>
            </span>
            <?php endif; ?>
        </a>
    </div>
</div>

<!-- ── TARJETAS KPI ──────────────────────────────────────── -->
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:rgba(107,143,113,.15);color:var(--primary);">
                <i class="bi bi-bag-check-fill"></i>
            </div>
            <div class="kpi-body">
                <div class="kpi-value"><?= number_format($total_orders) ?></div>
                <div class="kpi-label">Pedidos totales</div>
                <div class="kpi-trend trend-up">
                    <i class="bi bi-arrow-up-short"></i> +<?= $orders_today ?> hoy
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:rgba(193,103,75,.12);color:var(--terracota);">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="kpi-body">
                <div class="kpi-value"><?= formatPrice($revenue_total) ?></div>
                <div class="kpi-label">Ingresos totales</div>
                <div class="kpi-trend trend-up">
                    <i class="bi bi-arrow-up-short"></i> <?= formatPrice($revenue_month) ?> este mes
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:rgba(232,168,56,.15);color:var(--warm-yellow);">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div class="kpi-body">
                <div class="kpi-value"><?= number_format($total_contacts) ?></div>
                <div class="kpi-label">Contactos / Leads</div>
                <div class="kpi-trend <?= $new_contacts > 0 ? 'trend-warn' : 'trend-up' ?>">
                    <i class="bi bi-circle-fill" style="font-size:.5rem;"></i>
                    <?= $new_contacts ?> sin atender
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:rgba(62,39,35,.1);color:var(--coffee);">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="kpi-body">
                <div class="kpi-value"><?= number_format($total_products) ?></div>
                <div class="kpi-label">Productos activos</div>
                <div class="kpi-trend <?= $low_stock > 0 ? 'trend-danger' : 'trend-up' ?>">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?= $low_stock ?> con stock bajo
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── GRÁFICO + TOP PRODUCTOS ──────────────────────────── -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="dash-card">
            <div class="dash-card-header">
                <h6><i class="bi bi-bar-chart-line-fill me-2"></i>Ventas últimos 7 días</h6>
                <a href="pedidos.php" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            <div class="dash-card-body">
                <canvas id="salesChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <h6><i class="bi bi-trophy-fill me-2" style="color:var(--warm-yellow);"></i>Top Productos</h6>
            </div>
            <div class="dash-card-body p-0">
                <?php if (!empty($top_products)): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($top_products as $i => $tp): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill" style="background:var(--beige);color:var(--coffee);width:24px;height:24px;line-height:24px;text-align:center;">
                                <?= $i+1 ?>
                            </span>
                            <span style="font-size:.85rem;color:var(--coffee);"><?= e(mb_substr($tp['name'],0,22)) ?>...</span>
                        </div>
                        <div class="text-end">
                            <div style="font-size:.85rem;font-weight:700;color:var(--terracota);"><?= formatPrice($tp['revenue']) ?></div>
                            <small class="text-muted"><?= $tp['total_sold'] ?> vendidos</small>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
                    Sin datos de ventas aún
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── ÚLTIMOS PEDIDOS ───────────────────────────────────── -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="dash-card">
            <div class="dash-card-header">
                <h6><i class="bi bi-cart-check-fill me-2"></i>Últimos pedidos</h6>
                <a href="pedidos.php" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            <div class="dash-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover dash-table mb-0">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_orders)): ?>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><code style="font-size:.78rem;"><?= e($order['folio']) ?></code></td>
                                    <td style="font-size:.85rem;"><?= e(mb_substr($order['customer_name'],0,15)) ?></td>
                                    <td style="font-weight:700;color:var(--terracota);"><?= formatPrice($order['total']) ?></td>
                                    <td><?= orderStatusBadge($order['status']) ?></td>
                                    <td style="font-size:.78rem;color:var(--gray-500);">
                                        <?= date('d/m H:i', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td>
                                        <a href="pedidos.php?view=<?= $order['id'] ?>" class="btn btn-xs" style="background:var(--beige);color:var(--coffee);border-radius:6px;padding:.2rem .5rem;font-size:.75rem;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Sin pedidos aún</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ÚLTIMOS CONTACTOS -->
    <div class="col-lg-5">
        <div class="dash-card">
            <div class="dash-card-header">
                <h6><i class="bi bi-envelope-fill me-2"></i>Leads recientes</h6>
                <a href="contactos.php" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            <div class="dash-card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($recent_contacts)): ?>
                        <?php foreach ($recent_contacts as $c): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                            <div>
                                <div style="font-weight:600;font-size:.85rem;color:var(--coffee);">
                                    <?= e($c['name']) ?>
                                    <?php if ($c['status'] === 'new'): ?>
                                    <span class="badge ms-1" style="background:var(--terracota);color:#fff;font-size:.65rem;">Nuevo</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted" style="font-size:.78rem;"><?= e($c['service'] ?: $c['subject']) ?></div>
                            </div>
                            <div class="d-flex gap-1">
                                <?php if ($c['phone']): ?>
                                <a href="https://wa.me/52<?= preg_replace('/\D/','',$c['phone']) ?>"
                                   class="btn btn-xs" style="background:rgba(37,211,102,.1);color:#128C7E;border-radius:6px;padding:.2rem .5rem;font-size:.75rem;"
                                   target="_blank" title="WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                                <?php endif; ?>
                                <a href="contactos.php?view=<?= $c['id'] ?>" class="btn btn-xs" style="background:var(--beige);color:var(--coffee);border-radius:6px;padding:.2rem .5rem;font-size:.75rem;">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <li class="list-group-item text-center text-muted py-4">Sin contactos aún</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Accesos rápidos -->
<div class="row g-3">
    <?php
    $quick_actions = [
        ['href'=>'productos.php?action=new','icon'=>'plus-circle-fill','label'=>'Nuevo producto','color'=>'var(--primary)','bg'=>'rgba(107,143,113,.12)'],
        ['href'=>'pedidos.php','icon'=>'cart-check-fill','label'=>'Ver pedidos','color'=>'var(--terracota)','bg'=>'rgba(193,103,75,.1)'],
        ['href'=>'contactos.php?status=new','icon'=>'envelope-fill','label'=>'Leads sin atender','color'=>'var(--warm-yellow)','bg'=>'rgba(232,168,56,.1)'],
        ['href'=>'productos.php?filter=low_stock','icon'=>'exclamation-triangle-fill','label'=>'Stock bajo','color'=>'#dc3545','bg'=>'rgba(220,53,69,.08)'],
        ['href'=>'blog.php?action=new','icon'=>'file-earmark-plus-fill','label'=>'Nuevo artículo','color'=>'var(--coffee)','bg'=>'rgba(62,39,35,.08)'],
        ['href'=>'config.php','icon'=>'gear-fill','label'=>'Configuración','color'=>'var(--gray-600)','bg'=>'rgba(0,0,0,.05)'],
    ];
    foreach ($quick_actions as $qa):
    ?>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= $qa['href'] ?>" class="d-flex flex-column align-items-center gap-2 p-3 rounded text-center text-decoration-none"
           style="background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);transition:all .2s;"
           onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
            <div style="width:44px;height:44px;border-radius:12px;background:<?= $qa['bg'] ?>;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-<?= $qa['icon'] ?>" style="font-size:1.3rem;color:<?= $qa['color'] ?>;"></i>
            </div>
            <small style="color:var(--coffee);font-weight:600;font-size:.78rem;"><?= $qa['label'] ?></small>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Gráfico de ventas
const salesLabels = <?= json_encode(array_map(fn($r) => date('d/m', strtotime($r['day'])), $sales_chart)) ?>;
const salesData   = <?= json_encode(array_map(fn($r) => (float)$r['total'], $sales_chart)) ?>;

// Demo si vacío
const labels = salesLabels.length ? salesLabels : ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
const data   = salesData.length   ? salesData   : [1200,2400,900,3100,1800,4200,2100];

new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Ventas MXN',
            data: data,
            backgroundColor: 'rgba(107,143,113,.7)',
            borderColor: 'rgba(107,143,113,1)',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: v => '$' + v.toLocaleString('es-MX') }
            }
        }
    }
});
</script>

<?php require_once 'includes/dash_footer.php'; ?>