<?php
// ============================================================
// DASH_HEADER.PHP — Layout base del dashboard
// ============================================================
$current_page = basename($_SERVER['PHP_SELF']);
$new_contacts_count = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='new'")->fetchColumn();
$new_orders_count   = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'Dashboard') ?> — CoffeeBells Admin</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dash-body">

<!-- ── SIDEBAR ──────────────────────────────────────────── -->
<aside class="dash-sidebar" id="dashSidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-cup-hot-fill"></i></div>
        <div>
            <div class="sidebar-brand-name">CoffeeBells</div>
            <div class="sidebar-brand-sub">Panel Admin</div>
        </div>
    </div>

    <div class="sidebar-section-label">Principal</div>
    <nav class="sidebar-nav">
        <a href="index.php" class="sidebar-link <?= $current_page==='index.php'?'active':'' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="pedidos.php" class="sidebar-link <?= $current_page==='pedidos.php'?'active':'' ?>">
            <i class="bi bi-cart-check"></i> Pedidos
            <?php if ($new_orders_count > 0): ?>
            <span class="sidebar-badge"><?= $new_orders_count ?></span>
            <?php endif; ?>
        </a>
        <a href="contactos.php" class="sidebar-link <?= $current_page==='contactos.php'?'active':'' ?>">
            <i class="bi bi-envelope"></i> Contactos / Leads
            <?php if ($new_contacts_count > 0): ?>
            <span class="sidebar-badge"><?= $new_contacts_count ?></span>
            <?php endif; ?>
        </a>
    </nav>

    <div class="sidebar-section-label">Tienda</div>
    <nav class="sidebar-nav">
        <a href="productos.php" class="sidebar-link <?= $current_page==='productos.php'?'active':'' ?>">
            <i class="bi bi-box-seam"></i> Productos
        </a>
        <a href="categorias.php" class="sidebar-link <?= $current_page==='categorias.php'?'active':'' ?>">
            <i class="bi bi-grid"></i> Categorías
        </a>
        <a href="cupones.php" class="sidebar-link <?= $current_page==='cupones.php'?'active':'' ?>">
            <i class="bi bi-ticket-perforated"></i> Cupones
        </a>
    </nav>

    <div class="sidebar-section-label">Contenido</div>
    <nav class="sidebar-nav">
        <a href="servicios.php" class="sidebar-link <?= $current_page==='servicios.php'?'active':'' ?>">
            <i class="bi bi-tools"></i> Servicios
        </a>
        <a href="blog.php" class="sidebar-link <?= $current_page==='blog.php'?'active':'' ?>">
            <i class="bi bi-journal-text"></i> Blog
        </a>
        <a href="testimonios.php" class="sidebar-link <?= $current_page==='testimonios.php'?'active':'' ?>">
            <i class="bi bi-star"></i> Testimonios
        </a>
        <a href="galeria.php" class="sidebar-link <?= $current_page==='galeria.php'?'active':'' ?>">
            <i class="bi bi-images"></i> Galería
        </a>
        <a href="faqs.php" class="sidebar-link <?= $current_page==='faqs.php'?'active':'' ?>">
            <i class="bi bi-question-circle"></i> FAQs
        </a>
    </nav>

    <div class="sidebar-section-label">Usuarios</div>
    <nav class="sidebar-nav">
        <a href="usuarios.php" class="sidebar-link <?= $current_page==='usuarios.php'?'active':'' ?>">
            <i class="bi bi-people"></i> Usuarios
        </a>
        <a href="admins.php" class="sidebar-link <?= $current_page==='admins.php'?'active':'' ?>">
            <i class="bi bi-shield-lock"></i> Administradores
        </a>
        <a href="newsletter.php" class="sidebar-link <?= $current_page==='newsletter.php'?'active':'' ?>">
            <i class="bi bi-send"></i> Newsletter
        </a>
    </nav>

    <div class="sidebar-section-label">Sistema</div>
    <nav class="sidebar-nav">
        <a href="config.php" class="sidebar-link <?= $current_page==='config.php'?'active':'' ?>">
            <i class="bi bi-gear"></i> Configuración
        </a>
        <a href="reportes.php" class="sidebar-link <?= $current_page==='reportes.php'?'active':'' ?>">
            <i class="bi bi-bar-chart"></i> Reportes
        </a>
        <a href="../index.php" class="sidebar-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> Ver sitio
        </a>
        <a href="../actions/logout.php" class="sidebar-link sidebar-link-danger">
            <i class="bi bi-box-arrow-left"></i> Cerrar sesión
        </a>
    </nav>
</aside>

<!-- ── CONTENIDO PRINCIPAL ──────────────────────────────── -->
<div class="dash-main">

    <!-- Topbar -->
    <div class="dash-topbar">
        <button class="btn btn-sm btn-light" id="sidebarToggle" title="Menú">
            <i class="bi bi-list fs-5"></i>
        </button>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <?php if (($page_title ?? '') !== 'Dashboard'): ?>
                <li class="breadcrumb-item active"><?= e($page_title ?? '') ?></li>
                <?php endif; ?>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3 ms-auto">
            <span class="text-muted small d-none d-md-inline">
                <?= date('d/m/Y H:i') ?>
            </span>
            <div class="topbar-avatar" title="<?= e($_SESSION['admin_name'] ?? 'Admin') ?>">
                <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= showFlash() ?>

    <div class="dash-content">