<?php
// Header HTML global - Se incluye en TODAS las páginas
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Configuración SEO por defecto (puede sobreescribirse antes de incluir el header)
$meta_title       = $meta_title       ?? 'CoffeeBells & Home | Iluminación, Decoración, Jardines y Café Premium';
$meta_description = $meta_description ?? 'Iluminamos tu hogar, damos vida a tus espacios y despertamos tus sentidos. Servicios de electricidad, decoración de interiores, jardinería, cafetería y tienda online.';
$meta_keywords    = $meta_keywords    ?? 'electricidad, iluminación, decoración interiores, jardinería, paisajismo, café, coffee bells, salamanca guanajuato';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($meta_description) ?>">
    <meta name="keywords" content="<?= e($meta_keywords) ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="CoffeeBells & Home">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="<?= e($meta_title) ?>">
    <meta property="og:description" content="<?= e($meta_description) ?>">
    <meta property="og:image"       content="/coffeebells/assets/img/og-image.jpg">
    <meta property="og:url"         content="https://coffeebells.mx">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= e($meta_title) ?>">
    <meta name="twitter:description" content="<?= e($meta_description) ?>">

    <title><?= e($meta_title) ?></title>

    <!-- Preload fuentes críticas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Inter:wght@300;400;500;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Animaciones -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Glightbox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

    <!-- CSS principal -->
    <link rel="stylesheet" href="/coffeebells/assets/css/style.css">

    <!-- Schema.org básico -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "CoffeeBells & Home",
        "description": "<?= e($meta_description) ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Salamanca",
            "addressRegion": "Guanajuato",
            "addressCountry": "MX"
        },
        "telephone": "+52-464-123-4567",
        "url": "https://coffeebells.mx"
    }
    </script>
</head>
<body>

<!-- ===== LOADER DE PÁGINA ===== -->
<div id="page-loader">
    <div class="loader-inner">
        <div class="loader-logo">
            <span class="loader-icon"><i class="bi bi-cup-hot-fill"></i></span>
            <p>CoffeeBells & Home</p>
        </div>
        <div class="loader-bar"><div class="loader-progress"></div></div>
    </div>
</div>