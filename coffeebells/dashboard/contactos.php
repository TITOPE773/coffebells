<?php
// ============================================================
// DASHBOARD/CONTACTOS.PHP — Gestión de leads y contactos
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
requireAdmin();

$pdo    = getDB();
$view   = (int)($_GET['view'] ?? 0);
$status = htmlspecialchars($_GET['status'] ?? '', ENT_QUOTES, 'UTF-8');

// Actualizar estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_id = (int)($_POST['contact_id'] ?? 0);
    $new_status = htmlspecialchars($_POST['new_status'] ?? '', ENT_QUOTES, 'UTF-8');
    if ($contact_id && $new_status) {
        $pdo->prepare("UPDATE contacts SET status=:s WHERE id=:id")
            ->execute([':s' => $new_status, ':id' => $contact_id]);
        setFlash('success', 'Estado actualizado.');
    }
    // Eliminar
    if (!empty($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM contacts WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
        setFlash('success', 'Contacto eliminado.');
    }
    redirect('contactos.php');
}

// Ver detalle
if ($view > 0) {
    $contact = $pdo->prepare("SELECT * FROM contacts WHERE id = :id");
    $contact->execute([':id' => $view]);
    $contact = $contact->fetch();
    // Marcar como leído automáticamente
    if ($contact && $contact['status'] === 'new') {
        $pdo->prepare("UPDATE contacts SET status='read' WHERE id=:id")->execute([':id' => $view]);
        $contact['status'] = 'read';
    }
}

// Listado
$where  = $status ? "WHERE status = :status" : "";
$params = $status ? [':status' => $status] : [];

$page_num = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset   = ($page_num - 1) * $per_page;

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts $where");
$total_stmt->execute($params);
$total_count = (int)$total_stmt->fetchColumn();
$total_pages = ceil($total_count / $per_page);

$stmt = $pdo->prepare("SELECT * FROM contacts $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$contacts = $stmt->fetchAll();

$page_title = 'Contactos / Leads';
require_once 'includes/dash_header.php';
?>

<?php if ($view > 0 && $contact): ?>
<!-- Detalle contacto -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="dash-page-title mb-0">Lead: <?= e($contact['name']) ?></h4>
    <a href="contactos.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="dash-card">
            <div class="dash-card-header"><h6>Información del contacto</h6></div>
            <div class="dash-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Nombre</label>
                        <div style="font-weight:600;"><?= e($contact['name']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Teléfono</label>
                        <div>
                            <?= e($contact['phone'] ?: '—') ?>
                            <?php if ($contact['phone']): ?>
                            <a href="https://wa.me/52<?= preg_replace('/\D/','',$contact['phone']) ?>"
                               target="_blank" class="ms-2" style="color:#25D366;">
                                <i class="bi bi-whatsapp fs-5"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Email</label>
                        <div><?= e($contact['email'] ?: '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Servicio de interés</label>
                        <div><?= e($contact['service'] ?: '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Presupuesto</label>
                        <div><?= e($contact['budget'] ?: '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Prefiere contacto por</label>
                        <div><?= e($contact['contact_pref'] ?: '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Origen</label>
                        <div><?= e(ucfirst($contact['source'] ?: 'web_form')) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-semibold text-muted">Fecha</label>
                        <div><?= date('d/m/Y H:i', strtotime($contact['created_at'])) ?></div>
                    </div>
                    <?php if ($contact['message']): ?>
                    <div class="col-12">
                        <label class="small fw-semibold text-muted">Mensaje</label>
                        <div class="p-3 rounded" style="background:var(--beige);line-height:1.7;">
                            <?= nl2br(e($contact['message'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dash-card mb-3">
            <div class="dash-card-header"><h6>Cambiar estado</h6></div>
            <div class="dash-card-body">
                <form method="POST">
                    <input type="hidden" name="contact_id" value="<?= $contact['id'] ?>">
                    <select name="new_status" class="form-select mb-2">
                        <option value="new"      <?= $contact['status']==='new'       ?'selected':'' ?>>🔵 Nuevo</option>
                        <option value="read"     <?= $contact['status']==='read'      ?'selected':'' ?>>👁 Leído</option>
                        <option value="contacted"<?= $contact['status']==='contacted' ?'selected':'' ?>>📞 Contactado</option>
                        <option value="quoted"   <?= $contact['status']==='quoted'    ?'selected':'' ?>>📋 Cotizado</option>
                        <option value="closed"   <?= $contact['status']==='closed'    ?'selected':'' ?>>✅ Cerrado/Ganado</option>
                        <option value="lost"     <?= $contact['status']==='lost'      ?'selected':'' ?>>❌ Perdido</option>
                    </select>
                    <button type="submit" class="btn-primary-custom w-100 justify-content-center btn-sm">
                        Actualizar estado
                    </button>
                </form>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-header"><h6>Acciones</h6></div>
            <div class="dash-card-body d-grid gap-2">
                <?php if ($contact['phone']): ?>
                <a href="https://wa.me/52<?= preg_replace('/\D/','',$contact['phone']) ?>?text=Hola+<?= urlencode($contact['name']) ?>%2C+soy+del+equipo+CoffeeBells%2C+te+contactamos+por+tu+consulta."
                   class="btn-wa-green justify-content-center btn-sm" target="_blank">
                    <i class="bi bi-whatsapp"></i> Responder por WhatsApp
                </a>
                <?php endif; ?>
                <?php if ($contact['email']): ?>
                <a href="mailto:<?= e($contact['email']) ?>?subject=Re: <?= urlencode($contact['subject'] ?? 'Tu consulta en CoffeeBells') ?>"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-envelope me-1"></i> Responder por email
                </a>
                <?php endif; ?>
                <form method="POST" onsubmit="return confirm('¿Eliminar este contacto?');">
                    <input type="hidden" name="delete_id" value="<?= $contact['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                        <i class="bi bi-trash3 me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Listado contactos -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="dash-page-title mb-0">Contactos / Leads</h4>
    <small class="text-muted"><?= $total_count ?> registros</small>
</div>

<div class="d-flex gap-2 mb-3 flex-wrap">
    <?php
    $contact_tabs = [''=>'Todos','new'=>'🔵 Nuevos','read'=>'👁 Leídos','contacted'=>'📞 Contactados','quoted'=>'📋 Cotizados','closed'=>'✅ Ganados','lost'=>'❌ Perdidos'];
    foreach ($contact_tabs as $key => $label):
    ?>
    <a href="contactos.php?status=<?= $key ?>" class="btn btn-sm <?= $status===$key?'btn-coffee':'btn-outline-secondary' ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="dash-card">
    <div class="table-responsive">
        <table class="table table-hover dash-table mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Servicio</th>
                    <th>Presupuesto</th>
                    <th>Origen</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)): ?>
                    <?php foreach ($contacts as $c): ?>
                    <tr <?= $c['status']==='new' ? 'style="background:rgba(107,143,113,.05);font-weight:600;"' : '' ?>>
                        <td>
                            <?= e($c['name']) ?>
                            <?php if ($c['status']==='new'): ?>
                            <span class="badge ms-1" style="background:var(--terracota);color:#fff;font-size:.62rem;">Nuevo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($c['phone']): ?>
                            <a href="https://wa.me/52<?= preg_replace('/\D/','',$c['phone']) ?>"
                               style="color:#25D366;" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i><?= e($c['phone']) ?>
                            </a>
                            <?php else: ?> — <?php endif; ?>
                        </td>
                        <td style="font-size:.82rem;"><?= e(mb_substr($c['service'] ?: $c['subject'] ?: '—', 0, 25)) ?></td>
                        <td style="font-size:.82rem;"><?= e($c['budget'] ?: '—') ?></td>
                        <td><span class="badge bg-secondary" style="font-size:.65rem;"><?= e(ucfirst($c['source'] ?: 'web')) ?></span></td>
                        <td><?= contactStatusBadge($c['status']) ?></td>
                        <td style="font-size:.78rem;color:var(--gray-500);">
                            <?= date('d/m H:i', strtotime($c['created_at'])) ?>
                        </td>
                        <td>
                            <a href="contactos.php?view=<?= $c['id'] ?>" class="btn btn-xs"
                               style="background:var(--beige);color:var(--coffee);border-radius:6px;padding:.3rem .6rem;">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin contactos en esta categoría</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/dash_footer.php'; ?>