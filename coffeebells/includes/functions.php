<?php
// ============================================================
// FUNCTIONS.PHP — Funciones globales del sistema
// ============================================================

// ── SANITIZACIÓN ──────────────────────────────────────────
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function slugify(string $text): string {
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = preg_replace('/[áàäâ]/u', 'a', $text);
    $text = preg_replace('/[éèëê]/u', 'e', $text);
    $text = preg_replace('/[íìïî]/u', 'i', $text);
    $text = preg_replace('/[óòöô]/u', 'o', $text);
    $text = preg_replace('/[úùüû]/u', 'u', $text);
    $text = preg_replace('/[ñ]/u',    'n', $text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ── FORMATO ───────────────────────────────────────────────
function formatPrice(float $price, string $currency = 'MXN'): string {
    return '$' . number_format($price, 2, '.', ',') . ' ' . $currency;
}

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)      return 'hace ' . $diff . ' seg';
    if ($diff < 3600)    return 'hace ' . floor($diff/60) . ' min';
    if ($diff < 86400)   return 'hace ' . floor($diff/3600) . ' h';
    if ($diff < 604800)  return 'hace ' . floor($diff/86400) . ' días';
    return date('d/m/Y', strtotime($datetime));
}

function truncate(string $text, int $length = 100): string {
    return mb_strlen($text) > $length
        ? mb_substr($text, 0, $length, 'UTF-8') . '...'
        : $text;
}

// ── FLASH MESSAGES ────────────────────────────────────────
function setFlash(string $type, string $message): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function showFlash(): string {
    if (empty($_SESSION['flash'])) return '';
    $f    = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $type = $f['type'] === 'success' ? 'success' : ($f['type'] === 'error' ? 'danger' : $f['type']);
    $icon = $f['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
    return "<div class='alert alert-{$type} alert-dismissible alert-auto-dismiss d-flex align-items-center gap-2 fade show' role='alert'>
                <i class='bi bi-{$icon}'></i>
                <span>" . e($f['message']) . "</span>
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

// ── REDIRECCIÓN ───────────────────────────────────────────
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// ── BADGES DE ESTADO ──────────────────────────────────────
function orderStatusBadge(string $status, bool $large = false): string {
    $map = [
        'pending'    => ['label' => '⏳ Pendiente',  'bg' => '#fff3cd', 'color' => '#856404'],
        'confirmed'  => ['label' => '✅ Confirmado', 'bg' => '#d1e7dd', 'color' => '#0f5132'],
        'processing' => ['label' => '🔧 En proceso', 'bg' => '#cfe2ff', 'color' => '#084298'],
        'shipped'    => ['label' => '🚚 Enviado',    'bg' => '#e2d9f3', 'color' => '#432874'],
        'delivered'  => ['label' => '📦 Entregado',  'bg' => '#d1e7dd', 'color' => '#0a3622'],
        'cancelled'  => ['label' => '❌ Cancelado',  'bg' => '#f8d7da', 'color' => '#842029'],
    ];
    $s    = $map[$status] ?? ['label' => ucfirst($status), 'bg' => '#e9ecef', 'color' => '#495057'];
    $size = $large ? 'font-size:.95rem;padding:.45rem 1rem;' : 'font-size:.72rem;padding:.25rem .65rem;';
    return "<span style='background:{$s['bg']};color:{$s['color']};border-radius:50px;font-weight:600;{$size}'>{$s['label']}</span>";
}

function contactStatusBadge(string $status): string {
    $map = [
        'new'       => ['label' => '🔵 Nuevo',      'bg' => '#cfe2ff', 'color' => '#084298'],
        'read'      => ['label' => '👁 Leído',       'bg' => '#e9ecef', 'color' => '#495057'],
        'contacted' => ['label' => '📞 Contactado', 'bg' => '#fff3cd', 'color' => '#856404'],
        'quoted'    => ['label' => '📋 Cotizado',   'bg' => '#e2d9f3', 'color' => '#432874'],
        'closed'    => ['label' => '✅ Ganado',      'bg' => '#d1e7dd', 'color' => '#0f5132'],
        'lost'      => ['label' => '❌ Perdido',     'bg' => '#f8d7da', 'color' => '#842029'],
    ];
    $s = $map[$status] ?? ['label' => ucfirst($status), 'bg' => '#e9ecef', 'color' => '#495057'];
    return "<span style='background:{$s['bg']};color:{$s['color']};border-radius:50px;font-weight:600;font-size:.72rem;padding:.25rem .65rem;'>{$s['label']}</span>";
}

// ── SUBIDA DE IMÁGENES ────────────────────────────────────
function uploadImage(array $file, string $dest_dir, int $max_size = 2097152): string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Error al subir la imagen.');
    }
    if ($file['size'] > $max_size) {
        throw new RuntimeException('La imagen supera el tamaño máximo de 2MB.');
    }
    $allowed_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed_mime)) {
        throw new RuntimeException('Formato de imagen no permitido.');
    }
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . strtolower($ext);
    if (!is_dir($dest_dir)) {
        mkdir($dest_dir, 0755, true);
    }
    $dest_path = rtrim($dest_dir, '/') . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
        throw new RuntimeException('No se pudo mover el archivo subido.');
    }
    return ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath($dest_path)), '/');
}

// ── CONSULTAS DE BASE DE DATOS ────────────────────────────
function getFeaturedProducts(PDO $pdo, int $limit = 8): array {
    $stmt = $pdo->prepare("
        SELECT p.*, pc.name AS category_name
        FROM products p
        LEFT JOIN product_categories pc ON p.category_id = pc.id
        WHERE p.active = 1 AND p.featured = 1
        ORDER BY p.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getNewProducts(PDO $pdo, int $limit = 8): array {
    $stmt = $pdo->prepare("
        SELECT p.*, pc.name AS category_name
        FROM products p
        LEFT JOIN product_categories pc ON p.category_id = pc.id
        WHERE p.active = 1 AND p.badge = 'new'
        ORDER BY p.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getSaleProducts(PDO $pdo, int $limit = 8): array {
    $stmt = $pdo->prepare("
        SELECT p.*, pc.name AS category_name
        FROM products p
        LEFT JOIN product_categories pc ON p.category_id = pc.id
        WHERE p.active = 1 AND p.badge = 'sale' AND p.price_old > 0
        ORDER BY (1 - p.price / p.price_old) DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getServices(PDO $pdo, ?string $category = null, int $limit = 20): array {
    $where  = 'WHERE s.active = 1';
    $params = [];
    if ($category) {
        $where          .= ' AND sc.slug = :cat';
        $params[':cat']  = $category;
    }
    $stmt = $pdo->prepare("
        SELECT s.*, sc.name AS category_name, sc.slug AS category_slug, sc.icon AS category_icon
        FROM services s
        LEFT JOIN service_categories sc ON s.category_id = sc.id
        {$where}
        ORDER BY s.sort_order ASC, s.created_at DESC
        LIMIT :limit
    ");
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getFeaturedServices(PDO $pdo, int $limit = 6): array {
    $stmt = $pdo->prepare("
        SELECT s.*, sc.name AS category_name, sc.slug AS category_slug, sc.icon AS category_icon
        FROM services s
        LEFT JOIN service_categories sc ON s.category_id = sc.id
        WHERE s.active = 1 AND s.featured = 1
        ORDER BY s.sort_order ASC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// ✅ CORREGIDO — eliminado alias 'f.' del WHERE
function getFAQs(PDO $pdo, ?string $category = null, int $limit = 20): array {
    $where  = 'WHERE active = 1';
    $params = [];
    if ($category) {
        $where          .= ' AND category = :cat';
        $params[':cat']  = $category;
    }
    $stmt = $pdo->prepare("
        SELECT * FROM faqs
        {$where}
        ORDER BY sort_order ASC, id ASC
        LIMIT :limit
    ");
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTestimonials(PDO $pdo, int $limit = 6): array {
    $stmt = $pdo->prepare("
        SELECT * FROM testimonials
        WHERE active = 1
        ORDER BY featured DESC, created_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getBlogPosts(PDO $pdo, bool $featured_only = false, int $limit = 6): array {
    $where = 'WHERE bp.published = 1' . ($featured_only ? ' AND bp.featured = 1' : '');
    $stmt  = $pdo->prepare("
        SELECT bp.*, bc.name AS category_name, bc.slug AS category_slug,
               u.name AS author_name
        FROM blog_posts bp
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id
        LEFT JOIN users u ON bp.author_id = u.id
        {$where}
        ORDER BY bp.published_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// ✅ AGREGADO — alias requerido por index.php línea 19
function getRecentPosts(PDO $pdo, int $limit = 3): array {
    return getBlogPosts($pdo, false, $limit);
}

function getSiteConfig(PDO $pdo): array {
    $stmt   = $pdo->query("SELECT cfg_key, cfg_value FROM site_config");
    $config = [];
    while ($row = $stmt->fetch()) {
        $config[$row['cfg_key']] = $row['cfg_value'];
    }
    return $config;
}

// ── CARRITO (SESIÓN) ──────────────────────────────────────
function getCart(): array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return $_SESSION['cart'] ?? [];
}

function getCartCount(): int {
    return array_sum(array_column(getCart(), 'qty'));
}

function getCartTotal(): float {
    return array_sum(array_map(fn($i) => $i['price'] * $i['qty'], getCart()));
}

function addToCart(int $product_id, string $name, float $price, int $qty = 1, string $image = ''): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = [
            'id'    => $product_id,
            'name'  => $name,
            'price' => $price,
            'qty'   => $qty,
            'image' => $image,
        ];
    }
}

function removeFromCart(int $product_id): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    unset($_SESSION['cart'][$product_id]);
}

function clearCart(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['cart'] = [];
}

function generateFolio(): string {
    return 'CB-' . strtoupper(date('ymd')) . '-' . strtoupper(substr(uniqid(), -4));
}

// ── AUTENTICACIÓN HELPERS ─────────────────────────────────
function isLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return !empty($_SESSION['user_id']);
}

function isAdmin(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['is_admin']);
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name']  ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['user_role']  ?? 'customer',
    ];
    // ============================================================
// ALIAS — getRecentPosts() → getBlogPosts()
// index.php llama a esta función; internamente usa getBlogPosts()
// ============================================================
function getRecentPosts(PDO $pdo, int $limit = 3): array {
    return getBlogPosts($pdo, false, $limit);
}
}