<?php
// ============================================================
// DASHBOARD/CONFIG.PHP — Configuración general del sitio
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
requireAdmin();

$pdo = getDB();

// Cargar config actual
$config_stmt = $pdo->query("SELECT cfg_key, cfg_value FROM site_config");
$config = [];
while ($row = $config_stmt->fetch()) {
    $config[$row['cfg_key']] = $row['cfg_value'];
}

// Guardar config
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'site_name', 'site_tagline', 'site_phone', 'site_whatsapp',
        'site_email', 'site_address', 'site_city',
        'fb_url', 'ig_url', 'tiktok_url', 'yt_url',
        'hours_week', 'hours_weekend',
        'free_shipping_min', 'shipping_cost',
        'google_maps_embed', 'meta_description',
        'popup_enabled', 'popup_title', 'popup_discount',
        'admin_email_notifications',
    ];
    $upsert = $pdo->prepare("
        INSERT INTO site_config (cfg_key, cfg_value) VALUES (:k, :v)
        ON DUPLICATE KEY UPDATE cfg_value = :v2
    ");
    foreach ($fields as $field) {
        $val = trim($_POST[$field] ?? '');
        $upsert->execute([':k' => $field, ':v' => $val, ':v2' => $val]);
    }
    setFlash('success', '✅ Configuración guardada correctamente.');
    redirect('config.php');
}

$page_title = 'Configuración';
require_once 'includes/dash_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="dash-page-title mb-0"><i class="bi bi-gear-fill me-2"></i>Configuración del sitio</h4>
</div>

<?= showFlash() ?>

<form action="config.php" method="POST">
    <!-- Nav tabs configuración -->
    <ul class="nav nav-tabs mb-4" id="configTabs">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cfg-general">General</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cfg-contacto">Contacto</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cfg-tienda">Tienda</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cfg-marketing">Marketing</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cfg-seo">SEO</button></li>
    </ul>

    <div class="tab-content">

        <!-- GENERAL -->
        <div class="tab-pane fade show active" id="cfg-general">
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>Información del negocio</h6></div>
                <div class="dash-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del sitio</label>
                            <input type="text" name="site_name" class="form-control form-control-custom"
                                   value="<?= e($config['site_name'] ?? 'CoffeeBells & Home') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tagline</label>
                            <input type="text" name="site_tagline" class="form-control form-control-custom"
                                   value="<?= e($config['site_tagline'] ?? 'Iluminamos tu hogar, despertamos tus sentidos') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Horario Lun–Sáb</label>
                            <input type="text" name="hours_week" class="form-control form-control-custom"
                                   placeholder="9:00 – 20:00"
                                   value="<?= e($config['hours_week'] ?? '9:00 – 20:00') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Horario Domingos</label>
                            <input type="text" name="hours_weekend" class="form-control form-control-custom"
                                   placeholder="10:00 – 15:00"
                                   value="<?= e($config['hours_weekend'] ?? '10:00 – 15:00') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTACTO -->
        <div class="tab-pane fade" id="cfg-contacto">
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>Datos de contacto y redes sociales</h6></div>
                <div class="dash-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="site_phone" class="form-control form-control-custom"
                                   value="<?= e($config['site_phone'] ?? '+52 464 123 4567') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">WhatsApp (solo números)</label>
                            <input type="text" name="site_whatsapp" class="form-control form-control-custom"
                                   placeholder="524641234567"
                                   value="<?= e($config['site_whatsapp'] ?? '524641234567') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email de contacto</label>
                            <input type="email" name="site_email" class="form-control form-control-custom"
                                   value="<?= e($config['site_email'] ?? 'hola@coffeebells.mx') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notificaciones admin (email)</label>
                            <input type="email" name="admin_email_notifications" class="form-control form-control-custom"
                                   placeholder="admin@coffeebells.mx"
                                   value="<?= e($config['admin_email_notifications'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dirección</label>
                            <input type="text" name="site_address" class="form-control form-control-custom"
                                   value="<?= e($config['site_address'] ?? 'Salamanca, Guanajuato') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="site_city" class="form-control form-control-custom"
                                   value="<?= e($config['site_city'] ?? 'Salamanca, Guanajuato, MX') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Facebook URL</label>
                            <input type="url" name="fb_url" class="form-control form-control-custom"
                                   value="<?= e($config['fb_url'] ?? 'https://facebook.com/coffeebells') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Instagram URL</label>
                            <input type="url" name="ig_url" class="form-control form-control-custom"
                                   value="<?= e($config['ig_url'] ?? 'https://instagram.com/coffeebells') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">TikTok URL</label>
                            <input type="url" name="tiktok_url" class="form-control form-control-custom"
                                   value="<?= e($config['tiktok_url'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">YouTube URL</label>
                            <input type="url" name="yt_url" class="form-control form-control-custom"
                                   value="<?= e($config['yt_url'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Embed Google Maps (iframe src)</label>
                            <textarea name="google_maps_embed" class="form-control form-control-custom" rows="2"
                                      placeholder="https://www.google.com/maps/embed?..."><?= e($config['google_maps_embed'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TIENDA -->
        <div class="tab-pane fade" id="cfg-tienda">
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>Configuración de tienda y envíos</h6></div>
                <div class="dash-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mínimo para envío gratis (MXN)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="free_shipping_min" class="form-control"
                                       value="<?= e($config['free_shipping_min'] ?? '800') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Costo de envío estándar (MXN)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="shipping_cost" class="form-control"
                                       value="<?= e($config['shipping_cost'] ?? '99') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MARKETING -->
        <div class="tab-pane fade" id="cfg-marketing">
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>Popup de promoción</h6></div>
                <div class="dash-card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Activar popup</label>
                            <select name="popup_enabled" class="form-select">
                                <option value="1" <?= ($config['popup_enabled'] ?? '0')==='1' ? 'selected' : '' ?>>Sí</option>
                                <option value="0" <?= ($config['popup_enabled'] ?? '0')==='0' ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Título del popup</label>
                            <input type="text" name="popup_title" class="form-control form-control-custom"
                                   value="<?= e($config['popup_title'] ?? '¡Bienvenido! 10% OFF en tu primer pedido') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">% de descuento</label>
                            <input type="number" name="popup_discount" class="form-control form-control-custom"
                                   value="<?= e($config['popup_discount'] ?? '10') ?>" min="1" max="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO -->
        <div class="tab-pane fade" id="cfg-seo">
            <div class="dash-card mb-4">
                <div class="dash-card-header"><h6>SEO Global</h6></div>
                <div class="dash-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Meta description global</label>
                        <textarea name="meta_description" class="form-control form-control-custom" rows="3"
                                  maxlength="160"><?= e($config['meta_description'] ?? 'CoffeeBells & Home — Electricidad, decoración, jardinería y café de especialidad en Salamanca, Guanajuato.') ?></textarea>
                        <div class="form-text">Máximo 160 caracteres.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón guardar -->
    <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn-primary-custom" style="padding:.75rem 2rem;font-size:1rem;">
            <i class="bi bi-floppy-fill me-2"></i> Guardar configuración
        </button>
    </div>
</form>

<?php require_once 'includes/dash_footer.php'; ?>