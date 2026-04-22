<?php
// ============================================================
// INDEX.PHP — HOME PRINCIPAL
// CoffeeBells & Home
// ============================================================
$meta_title       = 'CoffeeBells & Home | Iluminación, Decoración, Jardines y Café Premium en Salamanca';
$meta_description = 'Iluminamos tu hogar, damos vida a tus espacios y despertamos tus sentidos. Servicios de electricidad, decoración de interiores, jardinería y cafetería Coffee Bells en Salamanca, Guanajuato.';

require_once 'includes/header.php';
require_once 'includes/navbar.php';

$pdo = getDB();

// Obtener datos dinámicos de la BD
$featured_products = getFeaturedProducts($pdo, 8);
$services          = getServices($pdo, 6);
$testimonials      = getTestimonials($pdo, 6);
$faqs              = getFAQs($pdo);
$recent_posts      = getRecentPosts($pdo, 3);
?>

<!-- ============================================================
     HERO / CARRUSEL PRINCIPAL (4 SLIDES)
     ============================================================ -->
 <style>/* ============================================================
   STYLE.CSS — CoffeeBells & Home
   Paleta: Verde salvia | Café oscuro | Terracota | Amarillo cálido | Beige | Blanco elegante
   ============================================================ */

/* ── VARIABLES ─────────────────────────────────────────────── */
:root {
    --primary:      #6B8F71;   /* Verde salvia */
    --primary-dark: #4A6B50;
    --primary-light:#8BAD91;
    --coffee:       #3E2723;   /* Café oscuro */
    --coffee-mid:   #6D4C41;
    --coffee-light: #A1887F;
    --terracota:    #C1674B;   /* Terracota */
    --terracota-light: #E08C74;
    --warm-yellow:  #E8A838;   /* Amarillo cálido */
    --beige:        #F5EDD7;   /* Beige / crema */
    --beige-dark:   #EDD9B0;
    --white-elegant:#FAFAF8;
    --dark:         #1A1A1A;   /* Negro suave */
    --gray-100:     #F8F5F0;
    --gray-200:     #EDE8E0;
    --gray-500:     #8C8276;
    --gray-700:     #4A4440;

    --font-title:   'Playfair Display', Georgia, serif;
    --font-body:    'Inter', 'Lato', sans-serif;

    --shadow-sm:    0 2px 8px rgba(62,39,35,.08);
    --shadow-md:    0 6px 24px rgba(62,39,35,.12);
    --shadow-lg:    0 16px 48px rgba(62,39,35,.15);
    --shadow-xl:    0 32px 80px rgba(62,39,35,.20);

    --radius-sm:    8px;
    --radius-md:    16px;
    --radius-lg:    28px;
    --radius-xl:    48px;

    --transition:   all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --header-h:     70px;
}

/* ── RESET & BASE ────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { scroll-behavior: smooth; font-size: 16px; }

body {
    font-family: var(--font-body);
    color: var(--dark);
    background: var(--white-elegant);
    overflow-x: hidden;
    line-height: 1.7;
}

a { text-decoration: none; color: inherit; transition: var(--transition); }

img { max-width: 100%; height: auto; display: block; }

/* ── TIPOGRAFÍA ──────────────────────────────────────────────── */
h1, h2, h3, h4, h5 {
    font-family: var(--font-title);
    font-weight: 700;
    line-height: 1.25;
    color: var(--coffee);
}
h1 { font-size: clamp(2rem, 5vw, 3.5rem); }
h2 { font-size: clamp(1.6rem, 3.5vw, 2.5rem); }
h3 { font-size: clamp(1.2rem, 2.5vw, 1.8rem); }

.section-tag {
    display: inline-block;
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .15em;
    text-transform: uppercase;
    color: var(--terracota);
    background: rgba(193,103,75,.1);
    padding: .3rem .9rem;
    border-radius: 50px;
    margin-bottom: .75rem;
}
.section-title {
    font-family: var(--font-title);
    font-size: clamp(1.7rem, 3vw, 2.4rem);
    color: var(--coffee);
    margin-bottom: .5rem;
}
.section-subtitle {
    color: var(--gray-500);
    font-size: 1.05rem;
    max-width: 600px;
    margin: 0 auto 2.5rem;
}
.text-center .section-subtitle { text-align: center; }

/* ── LOADER ──────────────────────────────────────────────────── */
#page-loader {
    position: fixed; inset: 0;
    background: var(--coffee);
    z-index: 99999;
    display: flex; align-items: center; justify-content: center;
    transition: opacity .5s, visibility .5s;
}
#page-loader.loader-hidden { opacity: 0; visibility: hidden; }
.loader-inner { text-align: center; color: var(--beige); }
.loader-icon { font-size: 3.5rem; display: block; animation: pulse 1.2s infinite; }
.loader-inner p { font-family: var(--font-title); font-size: 1.2rem; margin-top: .5rem; opacity: .85; }
.loader-bar {
    width: 200px; height: 3px;
    background: rgba(255,255,255,.2);
    border-radius: 50px; margin: 1rem auto 0;
    overflow: hidden;
}
.loader-progress {
    height: 100%; width: 0;
    background: var(--warm-yellow);
    border-radius: 50px;
    animation: loadProgress 1s ease-out forwards;
}
@keyframes loadProgress { to { width: 100%; } }
@keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.1)} }

/* ── TOP BAR ─────────────────────────────────────────────────── */
.top-bar {
    background: var(--coffee);
    color: rgba(245,237,215,.8);
    font-size: .82rem;
    padding: .45rem 0;
}
.top-bar-link {
    color: rgba(245,237,215,.8);
    transition: var(--transition);
}
.top-bar-link:hover { color: var(--warm-yellow); }

/* ── NAVBAR ──────────────────────────────────────────────────── */
.main-nav {
    background: var(--coffee);
    padding: .6rem 0;
    transition: var(--transition);
    box-shadow: none;
    position: sticky; top: 0; z-index: 1000;
}
.main-nav.scrolled {
    background: rgba(62,39,35,.97);
    box-shadow: var(--shadow-md);
    backdrop-filter: blur(10px);
    padding: .35rem 0;
}

.brand-logo { display: flex; align-items: center; gap: .75rem; }
.brand-icon {
    width: 44px; height: 44px;
    background: var(--warm-yellow);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: var(--coffee);
    transition: var(--transition);
}
.brand-logo:hover .brand-icon { transform: rotate(-10deg) scale(1.1); }
.brand-name { font-family: var(--font-title); font-size: 1.3rem; color: #fff; font-weight: 700; display: block; line-height: 1.1; }
.brand-sub  { font-size: .7rem; color: var(--warm-yellow); letter-spacing: .1em; }

.main-nav .nav-link {
    color: rgba(255,255,255,.85) !important;
    font-weight: 500;
    font-size: .9rem;
    padding: .5rem .85rem !important;
    border-radius: var(--radius-sm);
    transition: var(--transition);
    position: relative;
}
.main-nav .nav-link::after {
    content: '';
    position: absolute; bottom: 2px; left: 50%; right: 50%;
    height: 2px; background: var(--warm-yellow);
    border-radius: 2px; transition: var(--transition);
}
.main-nav .nav-link:hover, .main-nav .nav-link.active {
    color: #fff !important;
    background: rgba(255,255,255,.08);
}
.main-nav .nav-link:hover::after, .main-nav .nav-link.active::after {
    left: .85rem; right: .85rem;
}

.btn-icon-nav {
    background: rgba(255,255,255,.1);
    color: #fff; border: none;
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    transition: var(--transition);
    position: relative;
}
.btn-icon-nav:hover { background: var(--warm-yellow); color: var(--coffee); }

.cart-count-badge {
    position: absolute; top: -4px; right: -4px;
    background: var(--terracota); color: #fff;
    font-size: .65rem; font-weight: 700;
    width: 18px; height: 18px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}

.btn-call-nav {
    background: rgba(107,143,113,.2);
    color: var(--primary-light) !important;
    border: 1px solid rgba(107,143,113,.3);
    padding: .4rem .9rem;
    border-radius: 50px;
    font-size: .85rem;
    font-weight: 500;
    display: flex; align-items: center; gap: .3rem;
    transition: var(--transition);
}
.btn-call-nav:hover { background: var(--primary); color: #fff !important; border-color: var(--primary); }

.btn-primary-nav {
    background: var(--terracota);
    color: #fff !important;
    padding: .5rem 1.3rem;
    border-radius: 50px;
    font-size: .88rem;
    font-weight: 600;
    transition: var(--transition);
    border: none;
}
.btn-primary-nav:hover {
    background: var(--terracota-light);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(193,103,75,.4);
}

/* Mega menú */
.mega-dropdown .dropdown-menu.mega-menu {
    width: 100%;
    left: 0; right: 0;
    border: none;
    border-radius: 0 0 var(--radius-md) var(--radius-md);
    box-shadow: var(--shadow-xl);
    background: var(--white-elegant);
    padding: 0;
}
.mega-title {
    font-family: var(--font-title);
    color: var(--coffee);
    font-size: .95rem;
    font-weight: 700;
    padding-left: 1rem;
    margin-bottom: .5rem;
}
.mega-menu .dropdown-item {
    color: var(--gray-700);
    font-size: .88rem;
    padding: .3rem 1rem;
    border-radius: var(--radius-sm);
}
.mega-menu .dropdown-item:hover { background: var(--beige); color: var(--primary-dark); }
.mega-cta-box {
    background: linear-gradient(135deg, var(--coffee), var(--coffee-mid));
    color: #fff;
    padding: 1.25rem;
    border-radius: var(--radius-md);
}
.mega-cta-box h6 { color: var(--warm-yellow); margin-bottom: .4rem; }
.mega-cta-box p  { font-size: .85rem; opacity: .85; margin-bottom: .75rem; }

/* Buscador global */
.search-bar-global {
    background: var(--coffee-mid);
    border-top: 1px solid rgba(255,255,255,.1);
}

/* ── BOTONES GLOBALES ────────────────────────────────────────── */
.btn-primary-custom {
    background: var(--primary);
    color: #fff;
    padding: .75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: .95rem;
    border: none;
    transition: var(--transition);
    display: inline-flex; align-items: center; gap: .5rem;
}
.btn-primary-custom:hover {
    background: var(--primary-dark);
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(107,143,113,.4);
}
.btn-terracota {
    background: var(--terracota);
    color: #fff;
    padding: .75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    border: none;
    transition: var(--transition);
    display: inline-flex; align-items: center; gap: .5rem;
}
.btn-terracota:hover {
    background: var(--terracota-light);
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(193,103,75,.4);
}
.btn-coffee {
    background: var(--coffee);
    color: #fff;
    padding: .75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    border: none;
    transition: var(--transition);
    display: inline-flex; align-items: center; gap: .5rem;
}
.btn-coffee:hover { background: var(--coffee-mid); color: #fff; transform: translateY(-3px); }
.btn-outline-custom {
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
    padding: .7rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex; align-items: center; gap: .5rem;
}
.btn-outline-custom:hover { background: var(--primary); color: #fff; transform: translateY(-3px); }
.btn-wa-green {
    background: #25D366;
    color: #fff;
    padding: .75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    border: none;
    transition: var(--transition);
    display: inline-flex; align-items: center; gap: .5rem;
}
.btn-wa-green:hover { background: #128C7E; color: #fff; transform: translateY(-3px); }

/* ── HERO / CARRUSEL ─────────────────────────────────────────── */
.hero-carousel { min-height: 92vh; position: relative; }
.hero-carousel .carousel-item {
    min-height: 92vh;
    position: relative;
    overflow: hidden;
}
.carousel-bg {
    position: absolute; inset: 0;
    background-size: cover;
    background-position: center;
    transform: scale(1.05);
    transition: transform 6s ease;
    filter: brightness(.5);
}
.carousel-item.active .carousel-bg { transform: scale(1); }
.carousel-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(62,39,35,.75) 0%, rgba(0,0,0,.2) 100%);
}
.carousel-content {
    position: relative; z-index: 2;
    display: flex; align-items: center;
    min-height: 92vh;
}
.carousel-badge {
    display: inline-block;
    font-size: .75rem; font-weight: 700;
    letter-spacing: .15em; text-transform: uppercase;
    background: var(--warm-yellow); color: var(--coffee);
    padding: .35rem 1rem; border-radius: 50px;
    margin-bottom: 1rem;
}
.carousel-content h1 {
    color: #fff; font-size: clamp(2.2rem, 5vw, 4rem);
    text-shadow: 0 2px 20px rgba(0,0,0,.3);
    margin-bottom: 1rem;
}
.carousel-content p {
    color: rgba(255,255,255,.9);
    font-size: clamp(1rem, 2vw, 1.2rem);
    max-width: 580px;
    margin-bottom: 2rem;
}
.carousel-actions { display: flex; flex-wrap: wrap; gap: 1rem; }
.hero-carousel .carousel-control-prev,
.hero-carousel .carousel-control-next {
    width: 50px; height: 50px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    top: 50%; transform: translateY(-50%);
    margin: 0 1.5rem;
    opacity: .8; transition: var(--transition);
}
.hero-carousel .carousel-control-prev:hover,
.hero-carousel .carousel-control-next:hover {
    background: var(--warm-yellow); opacity: 1;
}
.hero-carousel .carousel-indicators [data-bs-target] {
    width: 10px; height: 10px; border-radius: 50%;
    background: rgba(255,255,255,.5); border: none;
    transition: var(--transition);
}
.hero-carousel .carousel-indicators .active {
    background: var(--warm-yellow); transform: scale(1.4);
}

/* Slide 1 – Electricidad */
.slide-electricidad .carousel-bg {
    background-image: linear-gradient(to right, rgba(62,39,35,.7), rgba(232,168,56,.3)),
    url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1600&q=80');
}
/* Slide 2 – Decoración */
.slide-decoracion .carousel-bg {
    background-image: linear-gradient(to right, rgba(62,39,35,.7), rgba(107,143,113,.3)),
    url('https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1600&q=80');
}
/* Slide 3 – Jardinería */
.slide-jardineria .carousel-bg {
    background-image: linear-gradient(to right, rgba(62,39,35,.7), rgba(74,107,80,.3)),
    url('https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=1600&q=80');
}
/* Slide 4 – Coffee Bells */
.slide-coffee .carousel-bg {
    background-image: linear-gradient(to right, rgba(62,39,35,.85), rgba(109,76,65,.3)),
    url('https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=1600&q=80');
}

/* ── SCROLL-DOWN INDICADOR ───────────────────────────────────── */
.scroll-down-hint {
    position: absolute; bottom: 2rem; left: 50%;
    transform: translateX(-50%); z-index: 3;
    color: rgba(255,255,255,.7); text-align: center;
    animation: bounce 2s infinite;
    cursor: pointer;
}
@keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(8px)} }

/* ── SECCIONES ───────────────────────────────────────────────── */
.section-pad { padding: 5rem 0; }
.section-pad-sm { padding: 3rem 0; }
.bg-beige  { background: var(--beige); }
.bg-coffee { background: var(--coffee); }
.bg-sage   { background: var(--primary); }
.bg-gray   { background: var(--gray-100); }

/* Brand intro */
.brand-intro { background: linear-gradient(135deg, var(--coffee) 0%, var(--coffee-mid) 100%); color: #fff; padding: 5rem 0; }
.brand-intro-icon { font-size: 3.5rem; color: var(--warm-yellow); margin-bottom: 1rem; }
.brand-intro h2 { color: #fff; }
.brand-intro p  { color: rgba(255,255,255,.8); font-size: 1.1rem; }
.brand-intro-stat { text-align: center; padding: 1.5rem; }
.brand-intro-stat .stat-num { font-family: var(--font-title); font-size: 2.5rem; color: var(--warm-yellow); font-weight: 800; }
.brand-intro-stat .stat-label { font-size: .85rem; color: rgba(255,255,255,.7); margin-top: .25rem; }

/* Beneficios */
.benefit-card {
    background: #fff;
    border-radius: var(--radius-md);
    padding: 2rem 1.5rem;
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    border-bottom: 3px solid transparent;
    height: 100%;
}
.benefit-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
    border-bottom-color: var(--primary);
}
.benefit-icon {
    width: 70px; height: 70px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; margin: 0 auto 1.25rem;
    transition: var(--transition);
}
.benefit-card:hover .benefit-icon { transform: scale(1.15) rotate(-5deg); }
.bi-green  { background: rgba(107,143,113,.12); color: var(--primary); }
.bi-terrac { background: rgba(193,103,75,.12); color: var(--terracota); }
.bi-coffee-c { background: rgba(62,39,35,.1); color: var(--coffee); }
.bi-yellow { background: rgba(232,168,56,.15); color: var(--warm-yellow); }

/* Servicios cards */
.service-card {
    background: #fff;
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    height: 100%;
}
.service-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); }
.service-card-img {
    height: 220px; overflow: hidden;
}
.service-card-img img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .5s ease;
}
.service-card:hover .service-card-img img { transform: scale(1.08); }
.service-card-body { padding: 1.5rem; }
.service-card-icon {
    width: 46px; height: 46px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; margin-bottom: 1rem;
}
.service-card h5 { font-family: var(--font-title); font-size: 1.2rem; color: var(--coffee); }
.service-card p  { color: var(--gray-500); font-size: .9rem; line-height: 1.6; }
.service-card-wa {
    display: inline-flex; align-items: center; gap: .4rem;
    color: #25D366; font-size: .85rem; font-weight: 600;
    margin-top: .5rem;
    transition: var(--transition);
}
.service-card-wa:hover { color: #128C7E; gap: .6rem; }

/* Productos cards */
.product-card {
    background: #fff;
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    height: 100%;
    position: relative;
}
.product-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); }
.product-card-img {
    height: 240px; overflow: hidden;
    position: relative;
}
.product-card-img img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .5s ease;
}
.product-card:hover .product-card-img img { transform: scale(1.07); }
.product-badge {
    position: absolute; top: .75rem; left: .75rem;
    font-size: .7rem; font-weight: 700; padding: .25rem .7rem;
    border-radius: 50px; text-transform: uppercase; letter-spacing: .05em;
}
.badge-new    { background: var(--primary); color: #fff; }
.badge-sale   { background: var(--terracota); color: #fff; }
.badge-hot    { background: var(--warm-yellow); color: var(--coffee); }
.product-wishlist {
    position: absolute; top: .75rem; right: .75rem;
    width: 36px; height: 36px;
    background: rgba(255,255,255,.9);
    border-radius: 50%; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; color: var(--gray-500);
    transition: var(--transition);
    opacity: 0;
}
.product-card:hover .product-wishlist { opacity: 1; }
.product-wishlist:hover, .product-wishlist.active { color: var(--terracota); }
.product-card-body { padding: 1.25rem; }
.product-category { font-size: .75rem; color: var(--primary); font-weight: 600; text-transform: uppercase; letter-spacing: .08em; }
.product-name { font-family: var(--font-title); font-size: 1rem; color: var(--coffee); margin: .3rem 0; }
.product-price { font-size: 1.2rem; font-weight: 700; color: var(--terracota); }
.product-price-old { font-size: .85rem; text-decoration: line-through; color: var(--gray-500); }
.product-actions { display: flex; gap: .5rem; margin-top: .75rem; }
.btn-add-cart {
    flex: 1; background: var(--coffee); color: #fff;
    border: none; border-radius: var(--radius-sm);
    padding: .6rem; font-size: .85rem; font-weight: 600;
    transition: var(--transition);
}
.btn-add-cart:hover { background: var(--primary); color: #fff; }
.btn-add-cart.btn-added { background: var(--primary); }
.btn-wa-product {
    background: #25D366; color: #fff; border: none;
    border-radius: var(--radius-sm);
    width: 42px; display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; transition: var(--transition);
}
.btn-wa-product:hover { background: #128C7E; color: #fff; }

/* Paquetes */
.package-card {
    background: #fff;
    border-radius: var(--radius-lg);
    padding: 2.5rem 2rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    height: 100%; border: 2px solid transparent;
    position: relative; overflow: hidden;
}
.package-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(to right, var(--primary), var(--terracota));
}
.package-card.featured {
    border-color: var(--warm-yellow);
    box-shadow: var(--shadow-xl);
    transform: scale(1.03);
}
.package-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-xl); border-color: var(--primary); }
.package-card.featured:hover { transform: scale(1.03) translateY(-8px); }
.package-badge-popular {
    position: absolute; top: 1rem; right: 1rem;
    background: var(--warm-yellow); color: var(--coffee);
    font-size: .7rem; font-weight: 700; padding: .25rem .75rem;
    border-radius: 50px; text-transform: uppercase;
}
.package-icon { font-size: 3rem; margin-bottom: 1rem; }
.package-price { font-size: 2.5rem; font-weight: 800; color: var(--terracota); font-family: var(--font-title); }
.package-price span { font-size: 1rem; font-weight: 400; color: var(--gray-500); }
.package-features { list-style: none; padding: 0; margin: 1.25rem 0; }
.package-features li {
    padding: .45rem 0;
    border-bottom: 1px solid var(--gray-200);
    font-size: .9rem;
    display: flex; align-items: center; gap: .6rem;
}
.package-features li i { color: var(--primary); font-size: 1rem; flex-shrink: 0; }

/* Testimonios */
.testimonial-card {
    background: #fff;
    border-radius: var(--radius-md);
    padding: 2rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    height: 100%;
    position: relative;
}
.testimonial-card::before {
    content: '\201C';
    position: absolute; top: 1rem; right: 1.5rem;
    font-size: 5rem; font-family: Georgia, serif;
    color: var(--beige-dark); line-height: 1;
    pointer-events: none;
}
.testimonial-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
.testimonial-stars { color: var(--warm-yellow); font-size: 1.1rem; margin-bottom: .75rem; }
.testimonial-text { font-style: italic; color: var(--gray-700); line-height: 1.7; margin-bottom: 1.25rem; }
.testimonial-author { display: flex; align-items: center; gap: .75rem; }
.testimonial-avatar {
    width: 50px; height: 50px; border-radius: 50%;
    background: var(--beige-dark);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: var(--coffee);
    flex-shrink: 0;
    overflow: hidden;
}
.testimonial-avatar img { width: 100%; height: 100%; object-fit: cover; }
.testimonial-name { font-weight: 700; color: var(--coffee); font-size: .95rem; }
.testimonial-role { font-size: .8rem; color: var(--primary); }

/* Counters */
.counter-section { background: linear-gradient(135deg, var(--coffee) 0%, var(--coffee-mid) 100%); padding: 4rem 0; color: #fff; }
.counter-card { text-align: center; }
.counter-number { font-family: var(--font-title); font-size: 3rem; font-weight: 800; color: var(--warm-yellow); line-height: 1; }
.counter-suffix { font-size: 2rem; color: var(--warm-yellow); font-weight: 700; }
.counter-label { color: rgba(255,255,255,.75); margin-top: .4rem; font-size: .9rem; }

/* FAQ Acordeón */
.faq-accordion .accordion-button {
    font-family: var(--font-body);
    font-weight: 600;
    color: var(--coffee);
    background: #fff;
    font-size: .95rem;
    box-shadow: none;
}
.faq-accordion .accordion-button:not(.collapsed) {
    color: var(--primary-dark);
    background: var(--beige);
}
.faq-accordion .accordion-button::after {
    filter: none;
}
.faq-accordion .accordion-button:focus { box-shadow: 0 0 0 .2rem rgba(107,143,113,.2); }
.faq-accordion .accordion-item {
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm) !important;
    margin-bottom: .5rem;
    overflow: hidden;
}

/* Blog */
.blog-card {
    background: #fff;
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    height: 100%;
}
.blog-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); }
.blog-card-img {
    height: 200px; overflow: hidden;
}
.blog-card-img img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .5s;
}
.blog-card:hover .blog-card-img img { transform: scale(1.07); }
.blog-card-body { padding: 1.5rem; }
.blog-cat { font-size: .75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: .08em; }
.blog-title { font-family: var(--font-title); color: var(--coffee); margin: .4rem 0 .6rem; font-size: 1.1rem; }
.blog-title:hover { color: var(--primary-dark); }
.blog-meta { font-size: .8rem; color: var(--gray-500); }

/* Contacto sección rápida */
.quick-contact { background: var(--beige); padding: 5rem 0; }
.quick-form-card {
    background: #fff;
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    box-shadow: var(--shadow-md);
}

/* Mapa */
.map-wrapper {
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    height: 400px;
}
.map-wrapper iframe { width: 100%; height: 100%; border: none; }

/* ── FOOTER ─────────────────────────────────────────────────── */
.footer-newsletter {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    padding: 3rem 0;
}
.newsletter-title { font-family: var(--font-title); color: #fff; font-size: 1.4rem; }
.newsletter-sub   { color: rgba(255,255,255,.8); margin: 0; }
.newsletter-form .form-control {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff; border-radius: 50px 0 0 50px;
    padding: .75rem 1.5rem;
}
.newsletter-form .form-control::placeholder { color: rgba(255,255,255,.6); }
.newsletter-form .form-control:focus { box-shadow: none; border-color: var(--warm-yellow); background: rgba(255,255,255,.2); }
.btn-newsletter {
    background: var(--warm-yellow); color: var(--coffee);
    font-weight: 700; border: none;
    border-radius: 0 50px 50px 0;
    padding: .75rem 1.5rem;
    transition: var(--transition);
}
.btn-newsletter:hover { background: #d4932e; color: #fff; }

.footer-main { background: var(--coffee); color: rgba(245,237,215,.8); padding: 4rem 0; }
.footer-logo  { display: flex; align-items: center; gap: .75rem; color: #fff; font-family: var(--font-title); font-size: 1.2rem; }
.footer-logo-icon { font-size: 2rem; color: var(--warm-yellow); }
.footer-desc  { font-size: .88rem; line-height: 1.7; color: rgba(245,237,215,.7); }
.footer-socials { display: flex; gap: .5rem; }
.social-link {
    width: 38px; height: 38px;
    background: rgba(255,255,255,.1);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: rgba(245,237,215,.8); font-size: 1rem;
    transition: var(--transition);
}
.social-link:hover { background: var(--warm-yellow); color: var(--coffee); transform: translateY(-3px); }

.footer-heading {
    color: #fff; font-family: var(--font-title);
    font-size: .95rem; font-weight: 700;
    margin-bottom: 1rem; padding-bottom: .5rem;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
.footer-links { list-style: none; padding: 0; }
.footer-links li { margin-bottom: .4rem; }
.footer-links a {
    color: rgba(245,237,215,.65); font-size: .88rem;
    display: flex; align-items: center; gap: .3rem;
    transition: var(--transition);
}
.footer-links a:hover { color: var(--warm-yellow); gap: .5rem; }
.footer-links a i { font-size: .7rem; }

.footer-contact { list-style: none; padding: 0; }
.footer-contact li { display: flex; gap: .75rem; margin-bottom: .75rem; align-items: flex-start; font-size: .88rem; }
.footer-contact li i { color: var(--warm-yellow); margin-top: .15rem; flex-shrink: 0; }
.footer-contact li a { color: rgba(245,237,215,.65); }
.footer-contact li a:hover { color: var(--warm-yellow); }
.footer-contact li span { color: rgba(245,237,215,.65); }

.footer-bottom {
    background: rgba(0,0,0,.25);
    padding: 1.1rem 0;
    font-size: .82rem; color: rgba(245,237,215,.5);
}
.footer-bottom a { color: rgba(245,237,215,.5); }
.footer-bottom a:hover { color: var(--warm-yellow); }

/* ── BOTÓN VOLVER ARRIBA ─────────────────────────────────────── */
#backToTop {
    position: fixed; bottom: 5.5rem; right: 1.5rem; z-index: 9000;
    width: 44px; height: 44px;
    background: var(--coffee); color: #fff;
    border: none; border-radius: 50%;
    font-size: 1.4rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: var(--shadow-md);
    opacity: 0; visibility: hidden;
    transform: translateY(10px);
    transition: var(--transition);
    cursor: pointer;
}
#backToTop.visible { opacity: 1; visibility: visible; transform: translateY(0); }
#backToTop:hover { background: var(--primary); transform: translateY(-3px); }

/* ── WHATSAPP FLOTANTE ───────────────────────────────────────── */
.whatsapp-float {
    position: fixed; bottom: 8.5rem; right: 1.3rem; z-index: 9000;
    width: 54px; height: 54px;
    background: #25D366; color: #fff;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.7rem;
    box-shadow: 0 4px 20px rgba(37,211,102,.5);
    transition: var(--transition);
    animation: waPulse 2.5s infinite;
}
.whatsapp-float:hover { transform: scale(1.12); color: #fff; box-shadow: 0 6px 28px rgba(37,211,102,.7); }
@keyframes waPulse {
    0%,100% { box-shadow: 0 4px 20px rgba(37,211,102,.5); }
    50%      { box-shadow: 0 4px 32px rgba(37,211,102,.8); }
}
.wa-tooltip {
    position: absolute; right: 65px;
    background: #25D366; color: #fff;
    padding: .35rem .75rem; border-radius: 50px;
    font-size: .8rem; font-weight: 600; white-space: nowrap;
    opacity: 0; transition: var(--transition);
    pointer-events: none;
}
.whatsapp-float:hover .wa-tooltip { opacity: 1; }

/* ── BOTÓN LLAMAR ────────────────────────────────────────────── */
.call-float {
    position: fixed; bottom: 1.3rem; right: 1.3rem; z-index: 9000;
    width: 54px; height: 54px;
    background: var(--terracota); color: #fff;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    box-shadow: 0 4px 20px rgba(193,103,75,.5);
    transition: var(--transition);
}
.call-float:hover { transform: scale(1.12); color: #fff; background: var(--terracota-light); }

/* ── CHATBOT ─────────────────────────────────────────────────── */
#chatbot-container { position: fixed; bottom: 1.3rem; left: 1.3rem; z-index: 9001; }

#chatbot-toggle {
    width: 58px; height: 58px;
    background: linear-gradient(135deg, var(--coffee), var(--coffee-mid));
    color: #fff; border: none; border-radius: 50%;
    font-size: 1.5rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: var(--shadow-lg);
    cursor: pointer; transition: var(--transition);
    position: relative;
}
#chatbot-toggle:hover { transform: scale(1.1); background: linear-gradient(135deg, var(--primary-dark), var(--primary)); }
.chatbot-badge {
    position: absolute; top: -3px; right: -3px;
    width: 18px; height: 18px;
    background: var(--terracota); color: #fff;
    border-radius: 50%; font-size: .65rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    animation: waPulse 2s infinite;
}

#chatbot-window {
    position: absolute; bottom: 70px; left: 0;
    width: 340px; max-height: 520px;
    background: #fff;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
    flex-direction: column;
    overflow: hidden;
}
.chatbot-header {
    background: linear-gradient(135deg, var(--coffee), var(--coffee-mid));
    color: #fff; padding: 1rem 1.25rem;
    display: flex; align-items: center; gap: .75rem;
}
.chatbot-avatar {
    width: 40px; height: 40px;
    background: var(--warm-yellow); color: var(--coffee);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
.chatbot-header strong { font-size: .95rem; display: block; line-height: 1.1; }
.chatbot-header small  { font-size: .75rem; color: rgba(255,255,255,.7); display: flex; align-items: center; gap: .3rem; }
.chatbot-header small::before { content: ''; width: 6px; height: 6px; background: #4ade80; border-radius: 50%; display: inline-block; }
#chatbot-close {
    margin-left: auto; background: none; border: none; color: rgba(255,255,255,.7); font-size: 1rem; cursor: pointer;
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; transition: var(--transition);
}
#chatbot-close:hover { background: rgba(255,255,255,.2); color: #fff; }

.chatbot-messages {
    flex: 1; overflow-y: auto; padding: 1rem;
    display: flex; flex-direction: column; gap: .6rem;
    max-height: 340px;
}
.msg { max-width: 85%; }
.msg.bot { align-self: flex-start; }
.msg.user { align-self: flex-end; }
.msg-bubble {
    padding: .65rem 1rem;
    border-radius: var(--radius-md);
    font-size: .88rem; line-height: 1.55;
}
.msg.bot .msg-bubble  { background: var(--gray-100); color: var(--dark); border-radius: 4px var(--radius-md) var(--radius-md) var(--radius-md); }
.msg.user .msg-bubble { background: var(--coffee); color: #fff; border-radius: var(--radius-md) 4px var(--radius-md) var(--radius-md); }
.msg-options { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .5rem; }
.opt-btn {
    background: #fff; border: 1.5px solid var(--primary);
    color: var(--primary-dark); border-radius: 50px;
    padding: .3rem .8rem; font-size: .78rem; font-weight: 600;
    cursor: pointer; transition: var(--transition);
}
.opt-btn:hover { background: var(--primary); color: #fff; }

.chatbot-input-area { padding: .75rem 1rem; border-top: 1px solid var(--gray-200); }
.chatbot-input-area .form-control {
    border-radius: 50px 0 0 50px; font-size: .88rem;
    border-color: var(--gray-200);
}
.chatbot-input-area .form-control:focus { box-shadow: none; border-color: var(--primary); }
.btn-chat-send {
    background: var(--coffee); color: #fff;
    border: none; border-radius: 0 50px 50px 0;
    padding: .5rem 1rem;
}
/* ── CHATBOT (continuación) ────────────────────────────────── */
.btn-chat-send {
    background: var(--coffee); color: #fff;
    border: none; border-radius: 0 50px 50px 0;
    padding: .5rem 1rem; transition: var(--transition);
}
.btn-chat-send:hover { background: var(--primary); }

.chat-lead-form { padding: .5rem 0; }
.chat-lead-form p { color: var(--gray-500); }

/* ── MODAL PROMOCIÓN ───────────────────────────────────────── */
.promo-modal-content {
    border: none;
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.promo-modal-img {
    background: linear-gradient(135deg, var(--coffee), var(--coffee-mid)),
                url('https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600&q=80');
    background-size: cover;
    background-blend-mode: multiply;
    min-height: 300px;
}
.promo-close {
    position: absolute; top: 1rem; right: 1rem; z-index: 10;
    background: rgba(0,0,0,.4); border-radius: 50%;
    padding: .5rem;
}
.promo-modal-title {
    font-family: var(--font-title);
    color: var(--coffee); font-size: 1.6rem;
    margin-bottom: .5rem;
}
.promo-modal-desc { color: var(--gray-500); font-size: .95rem; }

/* ── SECCIÓN "POR QUÉ ELEGIRNOS" ──────────────────────────── */
.why-us-section { background: var(--beige); padding: 5rem 0; }
.why-icon-box {
    display: flex; gap: 1.25rem; align-items: flex-start;
    background: #fff; padding: 1.5rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    height: 100%;
}
.why-icon-box:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
.why-icon {
    width: 56px; height: 56px; flex-shrink: 0;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff;
}
.why-icon-box h6 { font-family: var(--font-title); color: var(--coffee); font-size: 1rem; margin-bottom: .3rem; }
.why-icon-box p  { font-size: .88rem; color: var(--gray-500); margin: 0; line-height: 1.6; }

/* ── PROCESO DE TRABAJO ────────────────────────────────────── */
.process-section { padding: 5rem 0; }
.process-step {
    text-align: center; padding: 1.5rem 1rem;
    position: relative;
}
.process-step-num {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--terracota), var(--terracota-light));
    color: #fff;
    font-family: var(--font-title); font-size: 1.5rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
    box-shadow: 0 6px 20px rgba(193,103,75,.35);
    position: relative; z-index: 1;
    transition: var(--transition);
}
.process-step:hover .process-step-num { transform: scale(1.1); }
.process-step h6 { font-family: var(--font-title); color: var(--coffee); margin-bottom: .4rem; }
.process-step p  { font-size: .88rem; color: var(--gray-500); }

/* Línea conectora entre pasos */
.process-step + .process-step::before {
    content: '';
    position: absolute; top: 32px; left: -50%;
    width: 100%; height: 2px;
    background: var(--gray-200);
    z-index: 0;
}

/* ── GALERÍA DE PROYECTOS ──────────────────────────────────── */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: auto;
    gap: 1rem;
}
.gallery-item {
    border-radius: var(--radius-md);
    overflow: hidden;
    position: relative; cursor: pointer;
}
.gallery-item.gallery-wide { grid-column: span 2; }
.gallery-item img {
    width: 100%; height: 280px; object-fit: cover;
    transition: transform .5s ease;
}
.gallery-item.gallery-wide img { height: 280px; }
.gallery-item:hover img { transform: scale(1.07); }
.gallery-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(62,39,35,.8), transparent);
    opacity: 0; transition: var(--transition);
    display: flex; align-items: flex-end; justify-content: space-between;
    padding: 1.25rem;
}
.gallery-item:hover .gallery-overlay { opacity: 1; }
.gallery-overlay span { color: #fff; font-weight: 600; font-size: .9rem; }
.gallery-overlay i  { color: var(--warm-yellow); font-size: 1.5rem; }

/* ── SECCIÓN COFFEE BELLS MINI (HOME) ──────────────────────── */
.coffee-home-section {
    background: linear-gradient(135deg, var(--coffee) 50%, transparent 50%);
    padding: 5rem 0;
}
.coffee-home-card {
    background: var(--coffee-mid);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.coffee-home-card::after {
    content: '\2615';
    position: absolute; right: -1rem; bottom: -1rem;
    font-size: 8rem; opacity: .08; pointer-events: none;
}
.coffee-home-card h3 { font-family: var(--font-title); color: var(--warm-yellow); }
.coffee-home-card p  { color: rgba(255,255,255,.8); }

/* ── INDICADORES DE CONFIANZA ──────────────────────────────── */
.trust-bar {
    background: var(--beige-dark);
    padding: 1.5rem 0;
    border-top: 1px solid var(--gray-200);
    border-bottom: 1px solid var(--gray-200);
}
.trust-item {
    display: flex; align-items: center; gap: .75rem;
    justify-content: center;
}
.trust-item i    { font-size: 1.5rem; color: var(--primary); }
.trust-item span { font-size: .88rem; font-weight: 600; color: var(--gray-700); }

/* ── FORMULARIO DE CONTACTO RÁPIDO ─────────────────────────── */
.form-control-custom {
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-sm);
    padding: .75rem 1rem;
    font-size: .9rem;
    transition: var(--transition);
    background: #fff;
}
.form-control-custom:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 .2rem rgba(107,143,113,.2);
    outline: none;
}
.form-select-custom {
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-sm);
    padding: .75rem 1rem;
    font-size: .9rem;
    background: #fff;
    transition: var(--transition);
    cursor: pointer;
}
.form-select-custom:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 .2rem rgba(107,143,113,.2);
}

/* ── BREADCRUMBS ───────────────────────────────────────────── */
.breadcrumb-wrap {
    background: var(--gray-100);
    padding: .75rem 0;
    border-bottom: 1px solid var(--gray-200);
}
.breadcrumb-item a { color: var(--primary); font-size: .88rem; }
.breadcrumb-item.active { color: var(--gray-500); font-size: .88rem; }
.breadcrumb-item + .breadcrumb-item::before { color: var(--gray-500); }

/* ── PAGINACIÓN ────────────────────────────────────────────── */
.pagination .page-link {
    color: var(--coffee);
    border-color: var(--gray-200);
    border-radius: var(--radius-sm) !important;
    margin: 0 .2rem;
    padding: .5rem .85rem;
    font-size: .88rem;
    transition: var(--transition);
}
.pagination .page-link:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.pagination .page-item.active .page-link { background: var(--coffee); border-color: var(--coffee); color: #fff; }

/* ── ALERTAS VISUALES ──────────────────────────────────────── */
.alert-stock-low {
    background: rgba(232,168,56,.15);
    border-left: 4px solid var(--warm-yellow);
    border-radius: var(--radius-sm);
    padding: .6rem 1rem;
    font-size: .85rem; color: var(--coffee-mid);
    display: flex; align-items: center; gap: .5rem;
}

/* ── SECCIÓN MARCAS / ALIADOS ──────────────────────────────── */
.brands-section { padding: 3rem 0; background: var(--gray-100); }
.brand-logo-item {
    display: flex; align-items: center; justify-content: center;
    padding: 1rem 1.5rem;
    filter: grayscale(1); opacity: .5;
    transition: var(--transition);
}
.brand-logo-item:hover { filter: none; opacity: 1; transform: scale(1.05); }
.brand-logo-item img { max-height: 48px; object-fit: contain; }

/* ── CARRUSEL TESTIMONIOS ──────────────────────────────────── */
.testimonials-carousel .carousel-inner { padding-bottom: 2.5rem; }
.testimonials-carousel .carousel-indicators [data-bs-target] {
    background: var(--gray-500); width: 8px; height: 8px;
    border-radius: 50%; border: none;
}
.testimonials-carousel .carousel-indicators .active { background: var(--primary); }

/* ── SECCIÓN RESERVACIONES ─────────────────────────────────── */
.reservation-card {
    background: linear-gradient(135deg, var(--coffee), var(--coffee-mid));
    border-radius: var(--radius-lg);
    padding: 3rem;
    color: #fff;
}
.reservation-card h3 { color: var(--warm-yellow); font-family: var(--font-title); }
.reservation-card p  { color: rgba(255,255,255,.8); }
.reservation-card .form-control {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff; border-radius: var(--radius-sm);
}
.reservation-card .form-control::placeholder { color: rgba(255,255,255,.55); }
.reservation-card .form-control:focus {
    background: rgba(255,255,255,.22);
    border-color: var(--warm-yellow);
    box-shadow: none; color: #fff;
}

/* ── PAGE HERO INTERIOR (páginas internas) ─────────────────── */
.page-hero {
    background: linear-gradient(135deg, var(--coffee) 0%, var(--coffee-mid) 100%);
    padding: 5rem 0 4rem;
    position: relative;
    overflow: hidden;
}
.page-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 70% 50%, rgba(232,168,56,.15) 0%, transparent 60%);
}
.page-hero h1 { color: #fff; position: relative; z-index: 1; }
.page-hero p  { color: rgba(255,255,255,.8); position: relative; z-index: 1; }
.page-hero-tag {
    display: inline-block;
    background: var(--warm-yellow); color: var(--coffee);
    font-size: .75rem; font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase;
    padding: .3rem .9rem; border-radius: 50px;
    margin-bottom: .75rem;
    position: relative; z-index: 1;
}

/* ── SIDEBAR TIENDA ────────────────────────────────────────── */
.shop-sidebar { position: sticky; top: 90px; }
.filter-card {
    background: #fff; border-radius: var(--radius-md);
    padding: 1.5rem; box-shadow: var(--shadow-sm); margin-bottom: 1.25rem;
}
.filter-card h6 {
    font-family: var(--font-title); color: var(--coffee);
    font-size: .95rem; font-weight: 700;
    border-bottom: 1px solid var(--gray-200);
    padding-bottom: .5rem; margin-bottom: 1rem;
}
.filter-card .form-check-label { font-size: .88rem; color: var(--gray-700); cursor: pointer; }
.filter-card .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
.price-range { accent-color: var(--primary); }

/* ── CARRITO ───────────────────────────────────────────────── */
.cart-item-card {
    background: #fff; border-radius: var(--radius-md);
    padding: 1.25rem; box-shadow: var(--shadow-sm);
    display: flex; gap: 1rem; align-items: flex-start;
    margin-bottom: 1rem; transition: var(--transition);
}
.cart-item-card:hover { box-shadow: var(--shadow-md); }
.cart-item-img {
    width: 90px; height: 90px; flex-shrink: 0;
    border-radius: var(--radius-sm); overflow: hidden;
}
.cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
.cart-item-name  { font-family: var(--font-title); color: var(--coffee); font-size: 1rem; }
.cart-item-price { color: var(--terracota); font-weight: 700; font-size: 1.05rem; }
.qty-control { display: flex; align-items: center; gap: .5rem; }
.qty-btn {
    width: 30px; height: 30px;
    border-radius: 50%; border: 1.5px solid var(--gray-200);
    background: #fff; color: var(--coffee);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: var(--transition); font-size: .9rem;
}
.qty-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.qty-input {
    width: 40px; text-align: center;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-sm); padding: .2rem;
    font-size: .9rem; font-weight: 600;
}

.order-summary-card {
    background: #fff; border-radius: var(--radius-md);
    padding: 1.75rem; box-shadow: var(--shadow-sm);
    position: sticky; top: 90px;
}
.order-summary-card h5 {
    font-family: var(--font-title); color: var(--coffee);
    border-bottom: 1px solid var(--gray-200); padding-bottom: .75rem; margin-bottom: 1rem;
}
.summary-row {
    display: flex; justify-content: space-between;
    font-size: .9rem; margin-bottom: .5rem; color: var(--gray-700);
}
.summary-row.total {
    font-weight: 700; font-size: 1.15rem; color: var(--coffee);
    border-top: 1px solid var(--gray-200); padding-top: .75rem; margin-top: .5rem;
}

/* ── DASHBOARD ─────────────────────────────────────────────── */
.dashboard-wrapper { display: flex; min-height: 100vh; background: var(--gray-100); }
.dashboard-sidebar {
    width: 260px; flex-shrink: 0;
    background: var(--coffee);
    min-height: 100vh;
    position: sticky; top: 0;
    overflow-y: auto;
    transition: var(--transition);
}
.dashboard-sidebar .sidebar-brand {
    padding: 1.5rem 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,.1);
    display: flex; align-items: center; gap: .75rem;
    color: #fff;
}
.sidebar-brand-icon { font-size: 1.8rem; color: var(--warm-yellow); }
.sidebar-brand-name { font-family: var(--font-title); font-size: 1rem; font-weight: 700; line-height: 1.2; }
.sidebar-brand-sub  { font-size: .7rem; color: rgba(255,255,255,.5); }

.dashboard-sidebar .nav-section-title {
    font-size: .65rem; font-weight: 700;
    letter-spacing: .15em; text-transform: uppercase;
    color: rgba(255,255,255,.35);
    padding: 1.25rem 1.25rem .4rem;
}
.dashboard-sidebar .nav-link {
    color: rgba(255,255,255,.7) !important;
    padding: .6rem 1.25rem;
    display: flex; align-items: center; gap: .75rem;
    font-size: .88rem; border-radius: var(--radius-sm);
    margin: .1rem .5rem; transition: var(--transition);
}
.dashboard-sidebar .nav-link i { font-size: 1rem; width: 20px; text-align: center; }
.dashboard-sidebar .nav-link:hover { background: rgba(255,255,255,.1); color: #fff !important; }
.dashboard-sidebar .nav-link.active { background: var(--primary); color: #fff !important; }

.dashboard-content { flex: 1; min-width: 0; }
.dashboard-topbar {
    background: #fff; padding: 1rem 1.5rem;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: var(--shadow-sm); position: sticky; top: 0; z-index: 100;
}
.dashboard-topbar h5 { font-family: var(--font-title); color: var(--coffee); margin: 0; }

.stat-widget {
    background: #fff; border-radius: var(--radius-md);
    padding: 1.5rem; box-shadow: var(--shadow-sm);
    display: flex; gap: 1rem; align-items: center;
    transition: var(--transition);
}
.stat-widget:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.stat-widget-icon {
    width: 56px; height: 56px; flex-shrink: 0;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}
.stat-widget h3 { font-family: var(--font-title); font-size: 1.8rem; color: var(--coffee); margin: 0; line-height: 1; }
.stat-widget p  { font-size: .8rem; color: var(--gray-500); margin: .2rem 0 0; }
.stat-trend-up   { font-size: .78rem; color: #22c55e; font-weight: 600; }
.stat-trend-down { font-size: .78rem; color: var(--terracota); font-weight: 600; }

.admin-card {
    background: #fff; border-radius: var(--radius-md);
    padding: 1.5rem; box-shadow: var(--shadow-sm);
}
.admin-card-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.25rem;
    border-bottom: 1px solid var(--gray-200); padding-bottom: .75rem;
}
.admin-card-header h6 { font-family: var(--font-title); color: var(--coffee); margin: 0; }

.table-admin { font-size: .88rem; }
.table-admin th { background: var(--gray-100); color: var(--gray-700); font-weight: 600; font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; }
.table-admin td { vertical-align: middle; color: var(--gray-700); }
.table-admin tbody tr:hover { background: var(--beige); }

.badge-status-active   { background: rgba(107,143,113,.15); color: var(--primary-dark); }
.badge-status-inactive { background: rgba(193,103,75,.12); color: var(--terracota); }
.badge-status-pending  { background: rgba(232,168,56,.15); color: #b8860b; }

/* ── RESPONSIVE ────────────────────────────────────────────── */
@media (max-width: 991.98px) {
    .mega-dropdown .dropdown-menu.mega-menu { position: static; box-shadow: none; padding: .5rem; }
    .mega-menu .container { padding: 0; }
    .top-bar { display: none !important; }
    .hero-carousel { min-height: 70vh; }
    .hero-carousel .carousel-item, .carousel-content { min-height: 70vh; }
    .gallery-grid { grid-template-columns: repeat(2, 1fr); }
    .gallery-item.gallery-wide { grid-column: span 2; }
    .dashboard-sidebar { width: 220px; }
    .package-card.featured { transform: scale(1); }
}

@media (max-width: 767.98px) {
    :root { --header-h: 60px; }
    h1 { font-size: 2rem; }
    h2 { font-size: 1.6rem; }
    .section-pad { padding: 3rem 0; }
    .hero-carousel { min-height: 85vh; }
    .hero-carousel .carousel-item, .carousel-content { min-height: 85vh; }
    .carousel-content h1 { font-size: 1.9rem; }
    .gallery-grid { grid-template-columns: 1fr; }
    .gallery-item.gallery-wide { grid-column: span 1; }
    #chatbot-window { width: 300px; }
    .dashboard-wrapper { flex-direction: column; }
    .dashboard-sidebar { width: 100%; min-height: auto; position: relative; }
    .cart-item-card { flex-wrap: wrap; }
    .process-step + .process-step::before { display: none; }
    .whatsapp-float { bottom: 7.5rem; }
    #backToTop { bottom: 5rem; }
}

@media (max-width: 575.98px) {
    .brand-name { font-size: 1.1rem; }
    .carousel-actions { flex-direction: column; }
    .carousel-actions .btn-primary-custom,
    .carousel-actions .btn-outline-custom { width: 100%; justify-content: center; }
    #chatbot-window { width: calc(100vw - 2rem); left: -0.5rem; }
    .counter-number { font-size: 2.2rem; }
    .package-card { padding: 1.5rem 1.25rem; }
    .quick-form-card { padding: 1.5rem; }
}

/* ── MODO OSCURO OPCIONAL ──────────────────────────────────── */
@media (prefers-color-scheme: dark) {
    /* Activar si se desea modo oscuro automático:
    :root {
        --white-elegant: #1e1a17;
        --gray-100: #2a2320;
        --gray-200: #3a342f;
        --dark: #f0ebe3;
    }
    body { background: var(--white-elegant); }
    */
}

/* ── UTILIDADES EXTRA ──────────────────────────────────────── */
.rounded-custom  { border-radius: var(--radius-md); }
.rounded-large   { border-radius: var(--radius-lg); }
.shadow-custom   { box-shadow: var(--shadow-md); }
.text-coffee     { color: var(--coffee) !important; }
.text-primary-custom { color: var(--primary) !important; }
.text-terracota  { color: var(--terracota) !important; }
.text-warm       { color: var(--warm-yellow) !important; }
.bg-coffee-grad  { background: linear-gradient(135deg, var(--coffee), var(--coffee-mid)); }
.bg-sage-grad    { background: linear-gradient(135deg, var(--primary-dark), var(--primary)); }
.bg-beige-section { background: var(--beige); }
.divider-coffee  { height: 3px; background: linear-gradient(to right, var(--terracota), var(--warm-yellow), var(--primary)); border: none; border-radius: 50px; }
.img-cover       { object-fit: cover; width: 100%; }
.overflow-hidden { overflow: hidden; }
.z-1 { z-index: 1; position: relative; }</style>
     <section class="hero-carousel">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5500">

        <!-- Indicadores -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
        </div>

        <div class="carousel-inner">

            <!-- SLIDE 1: Electricidad e Iluminación -->
            <div class="carousel-item active slide-electricidad">
                <div class="carousel-bg"></div>
                <div class="carousel-overlay"></div>
                <div class="container carousel-content">
                    <div class="row">
                        <div class="col-lg-7" data-aos="fade-right" data-aos-delay="200">
                            <span class="carousel-badge">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Electricidad & Iluminación
                            </span>
                            <h1>Transforma tu hogar<br>con la <em>luz perfecta</em></h1>
                            <p>Instalaciones eléctricas, iluminación de diseño y Smart Home. Proyectos residenciales y comerciales con garantía de por vida.</p>
                            <div class="carousel-actions">
                                <a href="servicios.php#electricidad" class="btn-primary-custom">
                                    <i class="bi bi-lightning-charge"></i> Ver servicios
                                </a>
                                <a href="https://wa.me/524641234567?text=Hola%2C+quiero+cotizar+instalaci%C3%B3n+el%C3%A9ctrica" class="btn-wa-green" target="_blank">
                                    <i class="bi bi-whatsapp"></i> Cotizar ahora
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2: Decoración de Interiores -->
            <div class="carousel-item slide-decoracion">
                <div class="carousel-bg"></div>
                <div class="carousel-overlay"></div>
                <div class="container carousel-content">
                    <div class="row">
                        <div class="col-lg-7" data-aos="fade-right" data-aos-delay="200">
                            <span class="carousel-badge">
                                <i class="bi bi-house-heart-fill me-1"></i> Decoración de Interiores
                            </span>
                            <h1>Espacios que <em>inspiran</em><br>y enamoran</h1>
                            <p>Interiorismo, selección de muebles, asesoría de color y decoración personalizada para cada rincón de tu hogar u oficina.</p>
                            <div class="carousel-actions">
                                <a href="servicios.php#decoracion" class="btn-primary-custom">
                                    <i class="bi bi-palette"></i> Ver proyectos
                                </a>
                                <a href="contacto.php" class="btn-outline-custom" style="border-color:#fff;color:#fff;">
                                    <i class="bi bi-calendar-check"></i> Agendar asesoría
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3: Jardinería y Paisajismo -->
            <div class="carousel-item slide-jardineria">
                <div class="carousel-bg"></div>
                <div class="carousel-overlay"></div>
                <div class="container carousel-content">
                    <div class="row">
                        <div class="col-lg-7" data-aos="fade-right" data-aos-delay="200">
                            <span class="carousel-badge">
                                <i class="bi bi-tree-fill me-1"></i> Jardinería & Paisajismo
                            </span>
                            <h1>Dale vida a tus<br><em>espacios verdes</em></h1>
                            <p>Diseño de jardines, paisajismo, huertos urbanos, sistemas de riego y mantenimiento mensual para hogares y empresas.</p>
                            <div class="carousel-actions">
                                <a href="servicios.php#jardines" class="btn-primary-custom">
                                    <i class="bi bi-flower1"></i> Ver servicios
                                </a>
                                <a href="tienda.php?cat=jardineria" class="btn-coffee">
                                    <i class="bi bi-shop"></i> Tienda jardín
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 4: Coffee Bells -->
            <div class="carousel-item slide-coffee">
                <div class="carousel-bg"></div>
                <div class="carousel-overlay"></div>
                <div class="container carousel-content">
                    <div class="row">
                        <div class="col-lg-7" data-aos="fade-right" data-aos-delay="200">
                            <span class="carousel-badge">
                                <i class="bi bi-cup-hot-fill me-1"></i> Coffee Bells Café
                            </span>
                            <h1>Despierta tus<br><em>sentidos</em> cada día</h1>
                            <p>Cafetería premium, café de especialidad en grano y molido, accesorios y experiencias únicas. El lugar donde el café se convierte en arte.</p>
                            <div class="carousel-actions">
                                <a href="coffee-bells.php" class="btn-primary-custom">
                                    <i class="bi bi-cup-hot"></i> Visitar cafetería
                                </a>
                                <a href="tienda.php?cat=cafe" class="btn-terracota">
                                    <i class="bi bi-bag-heart"></i> Comprar café
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /carousel-inner -->

        <!-- Controles -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <i class="bi bi-chevron-left fs-4"></i>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <i class="bi bi-chevron-right fs-4"></i>
        </button>
    </div>

    <!-- Scroll down hint -->
    <div class="scroll-down-hint" onclick="document.getElementById('trust-bar').scrollIntoView({behavior:'smooth'})">
        <i class="bi bi-chevron-double-down fs-5 d-block"></i>
        <small style="font-size:.72rem;">Descubrir más</small>
    </div>
</section>


<!-- ============================================================
     BARRA DE CONFIANZA
     ============================================================ -->
<div class="trust-bar" id="trust-bar">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <i class="bi bi-award-fill"></i>
                    <span>+10 años de experiencia</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <i class="bi bi-shield-check-fill"></i>
                    <span>Trabajo garantizado</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <i class="bi bi-people-fill"></i>
                    <span>+500 clientes felices</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <i class="bi bi-star-fill"></i>
                    <span>Calificación 4.9/5</span>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================
     INTRO DE MARCA
     ============================================================ -->
<section class="brand-intro">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="brand-intro-icon"><i class="bi bi-cup-hot-fill"></i></div>
                <h2 class="mb-3">Iluminamos tu hogar,<br>damos vida a tus espacios<br>y <span style="color:var(--warm-yellow)">despertamos tus sentidos.</span></h2>
                <p class="mb-4">Somos un ecosistema de servicios y experiencias diseñado para transformar tu entorno. Desde la instalación eléctrica perfecta hasta el jardín de tus sueños, pasando por interiores que enamoran y el café que te despierta cada mañana.</p>
                <p class="mb-4" style="color:rgba(255,255,255,.75)">En CoffeeBells & Home no vendemos servicios separados. Creamos experiencias de vida completas, conectadas por un mismo hilo: el confort, la estética y el bienestar en cada rincón de tu hogar.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="servicios.php" class="btn-primary-custom">
                        <i class="bi bi-grid-1x2"></i> Conocer servicios
                    </a>
                    <a href="contacto.php" class="btn-outline-custom" style="border-color:rgba(255,255,255,.4);color:#fff;">
                        <i class="bi bi-chat-dots"></i> Hablar con nosotros
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="brand-intro-stat p-3" style="background:rgba(255,255,255,.07);border-radius:16px;">
                            <div class="stat-num counter-number" data-target="500">0</div>
                            <div class="stat-label">Clientes satisfechos</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="brand-intro-stat p-3" style="background:rgba(255,255,255,.07);border-radius:16px;">
                            <div class="stat-num counter-number" data-target="350">0</div>
                            <div class="stat-label">Proyectos completados</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="brand-intro-stat p-3" style="background:rgba(255,255,255,.07);border-radius:16px;">
                            <div class="stat-num counter-number" data-target="10">0</div>
                            <div class="stat-label">Años de experiencia</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="brand-intro-stat p-3" style="background:rgba(255,255,255,.07);border-radius:16px;">
                            <div class="stat-num counter-number" data-target="12000">0</div>
                            <div class="stat-label">Tazas de café servidas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     BENEFICIOS PRINCIPALES
     ============================================================ -->
<section class="section-pad bg-gray">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">¿Por qué elegirnos?</span>
            <h2 class="section-title">Todo lo que necesitas,<br>en un solo lugar</h2>
            <p class="section-subtitle">No somos una empresa más. Somos tu aliado para transformar cada espacio en una experiencia.</p>
        </div>
        <div class="row g-4">
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="benefit-card">
                    <div class="benefit-icon bi-green"><i class="bi bi-patch-check-fill"></i></div>
                    <h6>Calidad garantizada</h6>
                    <p class="text-muted small">Todos nuestros trabajos cuentan con garantía escrita y seguimiento post-servicio.</p>
                </div>
            </div>
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="benefit-card">
                    <div class="benefit-icon bi-terrac"><i class="bi bi-clock-history"></i></div>
                    <h6>Respuesta inmediata</h6>
                    <p class="text-muted small">Cotización gratuita en menos de 24 horas. Atención por WhatsApp los 7 días.</p>
                </div>
            </div>
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="benefit-card">
                    <div class="benefit-icon bi-coffee-c"><i class="bi bi-person-workspace"></i></div>
                    <h6>Equipo profesional</h6>
                    <p class="text-muted small">Técnicos certificados, diseñadores con experiencia y personal comprometido con tu satisfacción.</p>
                </div>
            </div>
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="benefit-card">
                    <div class="benefit-icon bi-yellow"><i class="bi bi-gem"></i></div>
                    <h6>Experiencia premium</h6>
                    <p class="text-muted small">Desde el primer contacto hasta la entrega final, cuidamos cada detalle de tu experiencia.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     SERVICIOS DESTACADOS
     ============================================================ -->
<section class="section-pad">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Lo que hacemos</span>
            <h2 class="section-title">Nuestros Servicios</h2>
            <p class="section-subtitle">Cuatro áreas de especialización unidas por una sola misión: hacer de tu espacio el lugar perfecto.</p>
        </div>

        <!-- Si hay servicios en BD, se muestran dinámicos; si no, se muestran los estáticos -->
        <?php if (!empty($services)): ?>
        <div class="row g-4">
            <?php foreach($services as $srv): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="service-card h-100">
                    <div class="service-card-img">
                        <img src="<?= e($srv['image'] ?: 'assets/img/service-default.jpg') ?>"
                             alt="<?= e($srv['name']) ?>" loading="lazy">
                    </div>
                    <div class="service-card-body">
                        <div class="service-card-icon bi-green">
                            <i class="bi bi-<?= e($srv['icon'] ?: 'gear') ?>"></i>
                        </div>
                        <span class="section-tag" style="font-size:.65rem;"><?= e($srv['category_name'] ?? '') ?></span>
                        <h5 class="mt-1"><?= e($srv['name']) ?></h5>
                        <p><?= e(mb_substr($srv['description'], 0, 120)) ?>...</p>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <a href="servicios.php?id=<?= $srv['id'] ?>" class="btn btn-sm btn-coffee">Ver más</a>
                            <a href="https://wa.me/524641234567?text=Hola%2C+me+interesa+el+servicio:+<?= urlencode($srv['name']) ?>"
                               class="service-card-wa" target="_blank">
                                <i class="bi bi-whatsapp"></i> Cotizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <!-- Servicios estáticos demo si BD está vacía -->
        <div class="row g-4">

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                <div class="service-card h-100">
                    <div class="service-card-img">
                        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80" alt="Electricidad" loading="lazy">
                    </div>
                    <div class="service-card-body">
                        <div class="service-card-icon" style="background:rgba(232,168,56,.15);color:var(--warm-yellow);">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <span class="section-tag" style="font-size:.65rem;">Electricidad</span>
                        <h5>Instalaciones Eléctricas</h5>
                        <p>Instalaciones residenciales y comerciales, tableros, circuitos, mantenimiento preventivo y correctivo con garantía.</p>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <a href="servicios.php#electricidad" class="btn btn-sm btn-coffee">Ver más</a>
                            <a href="https://wa.me/524641234567?text=Hola%2C+quiero+cotizar+instalaci%C3%B3n+el%C3%A9ctrica" class="service-card-wa" target="_blank">
                                <i class="bi bi-whatsapp"></i> Cotizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card h-100">
                    <div class="service-card-img">
                        <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=600&q=80" alt="Decoración" loading="lazy">
                    </div>
                    <div class="service-card-body">
                        <div class="service-card-icon" style="background:rgba(193,103,75,.12);color:var(--terracota);">
                            <i class="bi bi-house-heart-fill"></i>
                        </div>
                        <span class="section-tag" style="font-size:.65rem;">Decoración</span>
                        <h5>Interiorismo y Diseño</h5>
                        <p>Transformamos cada espacio con estilo y funcionalidad: salas, recámaras, terrazas y oficinas con identidad única.</p>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <a href="servicios.php#decoracion" class="btn btn-sm btn-coffee">Ver más</a>
                            <a href="https://wa.me/524641234567?text=Hola%2C+quiero+asesor%C3%ADa+de+decoraci%C3%B3n" class="service-card-wa" target="_blank">
                                <i class="bi bi-whatsapp"></i> Cotizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card h-100">
                    <div class="service-card-img">
                        <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80" alt="Jardinería" loading="lazy">
                    </div>
                    <div class="service-card-body">
                        <div class="service-card-icon bi-green">
                            <i class="bi bi-tree-fill"></i>
                        </div>
                        <span class="section-tag" style="font-size:.65rem;">Jardinería</span>
                        <h5>Diseño de Jardines</h5>
                        <p>Jardines a medida, paisajismo, huertos urbanos, plantas ornamentales y sistemas de riego automatizados.</p>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <a href="servicios.php#jardines" class="btn btn-sm btn-coffee">Ver más</a>
                            <a href="https://wa.me/524641234567?text=Hola%2C+quiero+dise%C3%B1o+de+jard%C3%ADn" class="service-card-wa" target="_blank">
                                <i class="bi bi-whatsapp"></i> Cotizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card h-100">
                    <div class="service-card-img">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80" alt="Smart Home" loading="lazy">
                    </div>
                    <div class="service-card-body">
                        <div class="service-card-icon" style="background:rgba(107,143,113,.12);color:var(--primary);">
                            <i class="bi bi-house-gear-fill"></i>
                        </div>
                        <span class="section-tag" style="font-size:.65rem;">Tecnología</span>
                        <h5>Smart Home</h5>
                        <p>Domótica, iluminación inteligente, control por voz y automatización del hogar para mayor confort y ahorro energético.</p>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <a href="servicios.php#smarthome" class="btn btn-sm btn-coffee">Ver más</a>
                            <a href="https://wa.me/524641234567?text=Hola%2C+me+interesa+Smart+Home" class="service-card-wa" target="_blank">
                                <i class="bi bi-whatsapp"></i> Cotizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                <div class="service-card h-100">
                    <div class="service-card-img">
                        <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600&q=80" alt="Coffee Bells" loading="lazy">
                    </div>
                    <div class="service-card-body">
                        <div class="service-card-icon" style="background:rgba(62,39,35,.1);color:var(--coffee);">
                            <i class="bi bi-cup-hot-fill"></i>
                        </div>
                        <span class="section-tag" style="font-size:.65rem;">Café</span>
                        <h5>Coffee Bells Café</h5>
                        <p>Cafetería de especialidad, café en grano y molido, barras de café para eventos y accesorios premium.</p>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <a href="coffee-bells.php" class="btn btn-sm btn-coffee">Ver más</a>
                            <a href="https://wa.me/524641234567?text=Hola%2C+quiero+info+de+Coffee+Bells" class="service-card-wa" target="_blank">
                                <i class="bi bi-whatsapp"></i> Info
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                <div class="service-card h-100">
                    <div class="service-card-img">
                        <img src="https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=600&q=80" alt="Mantenimiento" loading="lazy">
                    </div>
                    <div class="service-card-body">
                        <div class="service-card-icon" style="background:rgba(232,168,56,.15);color:var(--warm-yellow);">
                            <i class="bi bi-tools"></i>
                        </div>
                        <span class="section-tag" style="font-size:.65rem;">Mantenimiento</span>
                        <h5>Planes de Mantenimiento</h5>
                        <p>Planes mensuales de mantenimiento eléctrico, de jardín y del hogar. Prevención, ahorro y tranquilidad todo el año.</p>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <a href="paquetes.php" class="btn btn-sm btn-coffee">Ver planes</a>
                            <a href="https://wa.me/524641234567?text=Hola%2C+quiero+un+plan+de+mantenimiento" class="service-card-wa" target="_blank">
                                <i class="bi bi-whatsapp"></i> Cotizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php endif; ?>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="servicios.php" class="btn-primary-custom">
                <i class="bi bi-grid-3x3-gap"></i> Ver todos los servicios
            </a>
        </div>
    </div>
</section>


<!-- ============================================================
     CÓMO TRABAJAMOS — PROCESO
     ============================================================ -->
<section class="process-section bg-beige-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Nuestro proceso</span>
            <h2 class="section-title">Así trabajamos contigo</h2>
            <p class="section-subtitle">Simple, transparente y orientado a resultados desde el primer contacto.</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3 process-step" data-aos="fade-up" data-aos-delay="0">
                <div class="process-step-num">1</div>
                <h6>Primer contacto</h6>
                <p>Nos escribes por WhatsApp, formulario o llamada. Sin compromiso.</p>
            </div>
            <div class="col-6 col-md-3 process-step" data-aos="fade-up" data-aos-delay="100">
                <div class="process-step-num">2</div>
                <h6>Diagnóstico gratis</h6>
                <p>Visitamos tu espacio o analizamos tu proyecto sin costo adicional.</p>
            </div>
            <div class="col-6 col-md-3 process-step" data-aos="fade-up" data-aos-delay="200">
                <div class="process-step-num">3</div>
                <h6>Propuesta clara</h6>
                <p>Te presentamos un presupuesto detallado, plazos y materiales por escrito.</p>
            </div>
            <div class="col-6 col-md-3 process-step" data-aos="fade-up" data-aos-delay="300">
                <div class="process-step-num">4</div>
                <h6>Entrega con garantía</h6>
                <p>Ejecutamos, entregamos y hacemos seguimiento. Tu satisfacción es nuestra firma.</p>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     COFFEE BELLS — SECCIÓN ESPECIAL
     ============================================================ -->
<section class="section-pad" style="background:var(--coffee);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div style="position:relative;border-radius:var(--radius-lg);overflow:hidden;">
                    <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=700&q=80"
                         alt="Coffee Bells Cafetería"
                         style="width:100%;height:480px;object-fit:cover;">
                    <div style="position:absolute;bottom:1.5rem;left:1.5rem;background:var(--warm-yellow);color:var(--coffee);padding:.6rem 1.2rem;border-radius:50px;font-weight:700;font-size:.88rem;">
                        <i class="bi bi-cup-hot-fill me-1"></i> Abierto hoy • 9:00–20:00
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <span class="section-tag">Coffee Bells</span>
                <h2 style="color:#fff;" class="mb-3">Más que una cafetería.<br>Una <span style="color:var(--warm-yellow)">experiencia de vida.</span></h2>
                <p style="color:rgba(255,255,255,.8);" class="mb-3">Coffee Bells nació de la pasión por el café de especialidad y la creencia de que cada taza debe contar una historia. Nuestro café viene de las mejores regiones cafetaleras de México y el mundo, seleccionado con rigor y tostado con amor.</p>
                <p style="color:rgba(255,255,255,.75);" class="mb-4">Visítanos en nuestra cafetería física, pide tu café favorito y lleva a casa las mejores mezclas en grano o molido. También vendemos online con envíos a toda la república.</p>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.07);padding:1rem;border-radius:12px;">
                            <div style="color:var(--warm-yellow);font-size:1.3rem;margin-bottom:.3rem;"><i class="bi bi-cup-hot-fill"></i></div>
                            <strong style="color:#fff;font-size:.9rem;">Café de especialidad</strong>
                            <p style="color:rgba(255,255,255,.6);font-size:.8rem;margin:0;">Grano y molido, seleccionado de origen</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.07);padding:1rem;border-radius:12px;">
                            <div style="color:var(--warm-yellow);font-size:1.3rem;margin-bottom:.3rem;"><i class="bi bi-gift"></i></div>
                            <strong style="color:#fff;font-size:.9rem;">Gift Cards</strong>
                            <p style="color:rgba(255,255,255,.6);font-size:.8rem;margin:0;">El regalo perfecto para los amantes del café</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.07);padding:1rem;border-radius:12px;">
                            <div style="color:var(--warm-yellow);font-size:1.3rem;margin-bottom:.3rem;"><i class="bi bi-calendar-event"></i></div>
                            <strong style="color:#fff;font-size:.9rem;">Eventos & Barras</strong>
                            <p style="color:rgba(255,255,255,.6);font-size:.8rem;margin:0;">Llevamos la experiencia a tu evento</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.07);padding:1rem;border-radius:12px;">
                            <div style="color:var(--warm-yellow);font-size:1.3rem;margin-bottom:.3rem;"><i class="bi bi-truck"></i></div>
                            <strong style="color:#fff;font-size:.9rem;">Envíos a México</strong>
                            <p style="color:rgba(255,255,255,.6);font-size:.8rem;margin:0;">Tu café favorito donde estés</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="coffee-bells.php" class="btn-primary-custom">
                        <i class="bi bi-cup-hot"></i> Ver cafetería
                    </a>
                    <a href="tienda.php?cat=cafe" class="btn-terracota">
                        <i class="bi bi-bag-heart"></i> Comprar café online
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     PRODUCTOS DESTACADOS
     ============================================================ -->
<section class="section-pad bg-gray">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Tienda Online</span>
            <h2 class="section-title">Productos Destacados</h2>
            <p class="section-subtitle">Café, jardinería, decoración e iluminación. Todo lo que necesitas con la calidad CoffeeBells.</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($featured_products)): ?>
                <?php foreach(array_slice($featured_products, 0, 8) as $i => $prod): ?>
                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 80 ?>">
                    <div class="product-card">
                        <div class="product-card-img">
                            <img src="<?= e($prod['image'] ?: 'assets/img/product-default.jpg') ?>"
                                 alt="<?= e($prod['name']) ?>" loading="lazy">
                            <?php if ($prod['badge']): ?>
                            <span class="product-badge badge-<?= e($prod['badge']) ?>">
                                <?= $prod['badge'] === 'new' ? 'Nuevo' : ($prod['badge'] === 'sale' ? 'Oferta' : 'Hot') ?>
                            </span>
                            <?php endif; ?>
                            <button class="product-wishlist" data-id="<?= $prod['id'] ?>" title="Agregar a favoritos">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                        <div class="product-card-body">
                            <div class="product-category"><?= e($prod['category_name'] ?? '') ?></div>
                            <div class="product-name"><?= e($prod['name']) ?></div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="product-price"><?= formatPrice($prod['price']) ?></span>
                                <?php if ($prod['price_old']): ?>
                                <span class="product-price-old"><?= formatPrice($prod['price_old']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn-add-cart" data-id="<?= $prod['id'] ?>">
                                    <i class="bi bi-cart-plus me-1"></i> Agregar
                                </button>
                                <a href="https://wa.me/524641234567?text=Hola%2C+quiero+comprar:+<?= urlencode($prod['name']) ?>"
                                   class="btn-wa-product" target="_blank" title="Pedir por WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php else: ?>
            <!-- Productos estáticos demo -->
            <?php
            $demo_products = [
                ['name'=>'Café Blend Especial 250g', 'cat'=>'Café', 'price'=>'$189', 'badge'=>'hot', 'img'=>'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=400&q=80'],
                ['name'=>'Maceta Terracota Premium', 'cat'=>'Jardinería', 'price'=>'$349', 'badge'=>'new', 'img'=>'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400&q=80'],
                ['name'=>'Lámpara Colgante Diseño', 'cat'=>'Iluminación', 'price'=>'$1,290', 'badge'=>'', 'img'=>'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?w=400&q=80'],
                ['name'=>'Cactus Ornamental', 'cat'=>'Jardinería', 'price'=>'$220', 'badge'=>'new', 'img'=>'https://images.unsplash.com/photo-1463936575829-25148e1db1b8?w=400&q=80'],
                ['name'=>'Prensa Francesa Café', 'cat'=>'Accesorios', 'price'=>'$580', 'badge'=>'sale', 'img'=>'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&q=80'],
                ['name'=>'Cuadro Abstracto 60x80', 'cat'=>'Decoración', 'price'=>'$890', 'badge'=>'', 'img'=>'https://images.unsplash.com/photo-1549887552-cb1071d3e5ca?w=400&q=80'],
                ['name'=>'Kit Huerto Urbano', 'cat'=>'Jardinería', 'price'=>'$750', 'badge'=>'hot', 'img'=>'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&q=80'],
                ['name'=>'Café Origen Ethiopia 250g', 'cat'=>'Café', 'price'=>'$245', 'badge'=>'new', 'img'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&q=80'],
            ];
            foreach($demo_products as $i => $p):
            ?>
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 80 ?>">
                <div class="product-card">
                    <div class="product-card-img">
                        <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>" loading="lazy">
                        <?php if ($p['badge']): ?>
                        <span class="product-badge badge-<?= $p['badge'] ?>">
                            <?= $p['badge']==='new' ? 'Nuevo' : ($p['badge']==='sale' ? 'Oferta' : '🔥 Hot') ?>
                        </span>
                        <?php endif; ?>
                        <button class="product-wishlist" title="Agregar a favoritos"><i class="bi bi-heart"></i></button>
                    </div>
                    <div class="product-card-body">
                        <div class="product-category"><?= $p['cat'] ?></div>
                        <div class="product-name"><?= $p['name'] ?></div>
                        <div class="product-price"><?= $p['price'] ?> <small style="font-size:.7rem;color:var(--gray-500)">MXN</small></div>
                        <div class="product-actions">
                            <button class="btn-add-cart" data-id="<?= $i+1 ?>">
                                <i class="bi bi-cart-plus me-1"></i> Agregar
                            </button>
                            <a href="https://wa.me/524641234567?text=Hola%2C+quiero:+<?= urlencode($p['name']) ?>"
                               class="btn-wa-product" target="_blank"><i class="bi bi-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="tienda.php" class="btn-primary-custom me-3">
                <i class="bi bi-shop"></i> Ver tienda completa
            </a>
            <a href="tienda.php?cat=cafe" class="btn-coffee">
                <i class="bi bi-cup-hot"></i> Café online
            </a>
        </div>
    </div>
</section>


<!-- ============================================================
     PAQUETES COMERCIALES
     ============================================================ -->
<section class="section-pad">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Paquetes especiales</span>
            <h2 class="section-title">Soluciones completas<br>para tu espacio</h2>
            <p class="section-subtitle">Combinamos servicios estratégicamente para darte más valor, mejor precio y una experiencia integral.</p>
        </div>
        <div class="row g-4 align-items-stretch">

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="package-card h-100">
                    <div class="package-icon">🌿</div>
                    <h5>Terraza Perfecta</h5>
                    <p class="text-muted small">Todo para tu terraza: iluminación, plantas, decoración y una cafetería al aire libre.</p>
                    <div class="package-price">$4,500 <span>/ proyecto</span></div>
                    <ul class="package-features">
                        <li><i class="bi bi-check-circle-fill"></i> Instalación eléctrica exterior</li>
                        <li><i class="bi bi-check-circle-fill"></i> Iluminación ambiental LED</li>
                        <li><i class="bi bi-check-circle-fill"></i> Diseño de jardín en terraza</li>
                        <li><i class="bi bi-check-circle-fill"></i> Decoración de mobiliario</li>
                        <li><i class="bi bi-check-circle-fill"></i> Kit café para terraza</li>
                    </ul>
                    <a href="https://wa.me/524641234567?text=Hola%2C+me+interesa+el+paquete+Terraza+Perfecta"
                       class="btn-wa-green w-100 justify-content-center" target="_blank">
                        <i class="bi bi-whatsapp"></i> Contratar ahora
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="package-card featured h-100">
                    <span class="package-badge-popular">⭐ Más popular</span>
                    <div class="package-icon">🏠</div>
                    <h5>Hogar y Aroma</h5>
                    <p class="text-muted small">Transforma tu hogar completo con electricidad, decoración y el aroma del café perfecto.</p>
                    <div class="package-price">$8,900 <span>/ proyecto</span></div>
                    <ul class="package-features">
                        <li><i class="bi bi-check-circle-fill"></i> Revisión eléctrica completa</li>
                        <li><i class="bi bi-check-circle-fill"></i> Iluminación interior diseñada</li>
                        <li><i class="bi bi-check-circle-fill"></i> Asesoría de decoración</li>
                        <li><i class="bi bi-check-circle-fill"></i> Selección de plantas de interior</li>
                        <li><i class="bi bi-check-circle-fill"></i> Kit café de especialidad</li>
                        <li><i class="bi bi-check-circle-fill"></i> Garantía 6 meses</li>
                    </ul>
                    <a href="https://wa.me/524641234567?text=Hola%2C+me+interesa+el+paquete+Hogar+y+Aroma"
                       class="btn-primary-custom w-100 justify-content-center" target="_blank">
                        <i class="bi bi-lightning-charge"></i> Contratar ahora
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="package-card h-100">
                    <div class="package-icon">📚</div>
                    <h5>Rincón de Lectura</h5>
                    <p class="text-muted small">Crea el rincón perfecto: iluminación cálida, decoración acogedora y el café ideal.</p>
                    <div class="package-price">$3,200 <span>/ proyecto</span></div>
                    <ul class="package-features">
                        <li><i class="bi bi-check-circle-fill"></i> Punto de luz de lectura</li>
                        <li><i class="bi bi-check-circle-fill"></i> Lámpara de diseño incluida</li>
                        <li><i class="bi bi-check-circle-fill"></i> Asesoría decoración</li>
                        <li><i class="bi bi-check-circle-fill"></i> Planta de interior</li>
                        <li><i class="bi bi-check-circle-fill"></i> Café surtido 3 meses</li>
                    </ul>
                    <a href="https://wa.me/524641234567?text=Hola%2C+me+interesa+el+paquete+Rinc%C3%B3n+de+Lectura"
                       class="btn-wa-green w-100 justify-content-center" target="_blank">
                        <i class="bi bi-whatsapp"></i> Contratar ahora
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="package-card h-100">
                    <div class="package-icon">🌱</div>
                    <h5>Jardín Inteligente</h5>
                    <p class="text-muted small">El jardín de tus sueños con riego automático, iluminación y mantenimiento mensual.</p>
                    <div class="package-price">$6,500 <span>/ proyecto</span></div>
                    <ul class="package-features">
                        <li><i class="bi bi-check-circle-fill"></i> Diseño de jardín personalizado</li>
                        <li><i class="bi bi-check-circle-fill"></i> Sistema de riego automático</li>
                        <li><i class="bi bi-check-circle-fill"></i> Iluminación exterior LED</li>
                        <li><i class="bi bi-check-circle-fill"></i> Plantas y sustrato incluido</li>
                        <li><i class="bi bi-check-circle-fill"></i> 3 visitas de mantenimiento</li>
                    </ul>
                    <a href="https://wa.me/524641234567?text=Hola%2C+me+interesa+el+paquete+Jard%C3%ADn+Inteligente"
                       class="btn-wa-green w-100 justify-content-center" target="_blank">
                        <i class="bi bi-whatsapp"></i> Contratar ahora
                    </a>
                </div>
            </div>

        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="paquetes.php" class="btn-primary-custom">
                <i class="bi bi-grid-3x3-gap"></i> Ver todos los paquetes
            </a>
        </div>
    </div>
</section>


<!-- ============================================================
     GALERÍA DE PROYECTOS
     ============================================================ -->
<section class="section-pad bg-beige-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Nuestros proyectos</span>
            <h2 class="section-title">Trabajos que hablan<br>por nosotros</h2>
            <p class="section-subtitle">Cada proyecto es único. Mira lo que hemos logrado para nuestros clientes.</p>
        </div>
        <div class="gallery-grid" data-aos="fade-up">
            <div class="gallery-item gallery-wide">
                <a href="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=80" class="glightbox" data-gallery="gallery1">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80" alt="Proyecto Electricidad">
                    <div class="gallery-overlay">
                        <span>Instalación eléctrica residencial</span>
                        <i class="bi bi-zoom-in"></i>
                    </div>
                </a>
            </div>
            <div class="gallery-item">
                <a href="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80" class="glightbox" data-gallery="gallery1">
                    <img src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=80" alt="Decoración">
                    <div class="gallery-overlay">
                        <span>Decoración sala moderna</span>
                        <i class="bi bi-zoom-in"></i>
                    </div>
                </a>
            </div>
            <div class="gallery-item">
                <a href="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&q=80" class="glightbox" data-gallery="gallery1">
                    <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80" alt="Jardín">
                    <div class="gallery-overlay">
                        <span>Diseño jardín residencial</span>
                        <i class="bi bi-zoom-in"></i>
                    </div>
                </a>
            </div>
            <div class="gallery-item">
                <a href="https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800&q=80" class="glightbox" data-gallery="gallery1">
                    <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=600&q=80" alt="Decoración recámara">
                    <div class="gallery-overlay">
                        <span>Recámara con iluminación diseñada</span>
                        <i class="bi bi-zoom-in"></i>
                    </div>
                </a>
            </div>
            <div class="gallery-item gallery-wide">
                <a href="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=1200&q=80" class="glightbox" data-gallery="gallery1">
                    <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&q=80" alt="Coffee Bells">
                    <div class="gallery-overlay">
                        <span>Coffee Bells — Cafetería</span>
                        <i class="bi bi-zoom-in"></i>
                    </div>
                </a>
            </div>
            <div class="gallery-item">
                <a href="https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=800&q=80" class="glightbox" data-gallery="gallery1">
                    <img src="https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=600&q=80" alt="Smart Home">
                    <div class="gallery-overlay">
                        <span>Smart Home instalado</span>
                        <i class="bi bi-zoom-in"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     TESTIMONIOS
     ============================================================ -->
<section class="section-pad bg-gray">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Lo que dicen de nosotros</span>
            <h2 class="section-title">Clientes que confían<br>en CoffeeBells & Home</h2>
        </div>

        <?php if (!empty($testimonials)): ?>
        <div class="row g-4">
            <?php foreach(array_slice($testimonials,0,6) as $i => $t): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i%3)*80 ?>">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <?php for($s=0;$s<$t['stars'];$s++) echo '<i class="bi bi-star-fill"></i>'; ?>
                    </div>
                    <p class="testimonial-text">"<?= e($t['content']) ?>"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">
                            <?php if($t['avatar']): ?>
                            <img src="<?= e($t['avatar']) ?>" alt="<?= e($t['name']) ?>">
                            <?php else: ?>
                            <i class="bi bi-person-fill"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="testimonial-name"><?= e($t['name']) ?></div>
                            <div class="testimonial-role"><?= e($t['role'] ?? 'Cliente verificado') ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <!-- Testimonios demo estáticos -->
        <div class="row g-4">
            <?php
            $testi_demo = [
                ['stars'=>5,'text'=>'Contratamos el paquete Hogar y Aroma y quedamos fascinados. La iluminación transformó nuestra sala por completo. El equipo es muy profesional y ordenado.','name'=>'María González','role'=>'Arquitecta • Salamanca','icon'=>'👩'],
                ['stars'=>5,'text'=>'Me diseñaron el jardín de mi casa desde cero. Incluyeron sistema de riego automático y las plantas que ellos recomendaron están perfectas 6 meses después.','name'=>'Carlos Mendoza','role'=>'Empresario • Salamanca','icon'=>'👨'],
                ['stars'=>5,'text'=>'El café de Coffee Bells es increíble. Compro en línea cada mes y me llega fresquísimo. El blend especial ya es parte de mi rutina matutina.','name'=>'Ana Ruiz','role'=>'Diseñadora • León, Gto.','icon'=>'👩'],
                ['stars'=>5,'text'=>'Instalaron la electricidad completa de mi oficina en un día. Trabajo limpio, presupuesto exacto y cero sorpresas. Sin duda los recomiendo.','name'=>'Roberto Pérez','role'=>'Contador • Irapuato','icon'=>'👨'],
                ['stars'=>5,'text'=>'La asesoría de decoración fue un acierto total. Tenía el espacio pero no sabía cómo aprovecharlo. Ellos lo transformaron en algo que amo.','name'=>'Sofía Torres','role'=>'Profesora • Salamanca','icon'=>'👩'],
                ['stars'=>5,'text'=>'Pedimos una barra de café para el evento de la empresa y fue un éxito rotundo. Coffee Bells elevó la experiencia de todos los asistentes.','name'=>'Diego Herrera','role'=>'Gerente RRHH • Celaya','icon'=>'👨'],
            ];
            foreach($testi_demo as $i => $t):
            ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i%3)*80 ?>">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <?php for($s=0;$s<$t['stars'];$s++) echo '<i class="bi bi-star-fill"></i>'; ?>
                    </div>
                    <p class="testimonial-text">"<?= $t['text'] ?>"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar" style="font-size:1.5rem;"><?= $t['icon'] ?></div>
                        <div>
                            <div class="testimonial-name"><?= $t['name'] ?></div>
                            <div class="testimonial-role"><?= $t['role'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>


<!-- ============================================================
     CONTADORES
     ============================================================ -->
<section class="counter-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="0">
                <div class="counter-card">
                    <div><span class="counter-number" data-target="500">0</span><span class="counter-suffix">+</span></div>
                    <div class="counter-label">Clientes satisfechos</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="counter-card">
                    <div><span class="counter-number" data-target="350">0</span><span class="counter-suffix">+</span></div>
                    <div class="counter-label">Proyectos entregados</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="counter-card">
                    <div><span class="counter-number" data-target="12000">0</span><span class="counter-suffix">+</span></div>
                    <div class="counter-label">Tazas de café servidas</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="counter-card">
                    <div><span class="counter-number" data-target="10">0</span><span class="counter-suffix"> años</span></div>
                    <div class="counter-label">De experiencia</div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     BLOG / ARTÍCULOS RECIENTES
     ============================================================ -->
<section class="section-pad bg-beige-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Blog de inspiración</span>
            <h2 class="section-title">Ideas para tu hogar,<br>jardín y taza de café</h2>
            <p class="section-subtitle">Consejos, tendencias y guías de expertos para que siempre tengas el mejor espacio y el mejor café.</p>
        </div>
        <div class="row g-4">
            <?php if (!empty($recent_posts)): ?>
                <?php foreach($recent_posts as $i => $post): ?>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $i*80 ?>">
                    <div class="blog-card">
                        <div class="blog-card-img">
                            <img src="<?= e($post['image'] ?: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&q=80') ?>"
                                 alt="<?= e($post['title']) ?>" loading="lazy">
                        </div>
                        <div class="blog-card-body">
                            <div class="blog-cat"><?= e($post['category'] ?? 'Inspiración') ?></div>
                            <a href="articulo.php?slug=<?= e($post['slug']) ?>">
                                <h5 class="blog-title"><?= e($post['title']) ?></h5>
                            </a>
                            <p class="text-muted small"><?= e(mb_substr($post['excerpt'] ?? '', 0, 100)) ?>...</p>
                            <div class="blog-meta mt-2">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= date('d M Y', strtotime($post['created_at'])) ?>
                                &nbsp;·&nbsp;
                                <i class="bi bi-clock me-1"></i> 5 min lectura
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <!-- Blog demo estático -->
            <?php
            $blog_demo = [
                ['cat'=>'Iluminación','title'=>'5 ideas de iluminación para transformar tu sala sin gastar de más','img'=>'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?w=600&q=80','date'=>'15 Abr 2026'],
                ['cat'=>'Jardinería','title'=>'Cómo crear un huerto urbano en tu balcón o terraza en 7 pasos','img'=>'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80','date'=>'10 Abr 2026'],
                ['cat'=>'Café','title'=>'Guía definitiva: cómo preparar el mejor café en casa según los baristas','img'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&q=80','date'=>'5 Abr 2026'],
            ];
            foreach($blog_demo as $i => $b):
            ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $i*80 ?>">
                <div class="blog-card">
                    <div class="blog-card-img">
                        <img src="<?= $b['img'] ?>" alt="<?= $b['title'] ?>" loading="lazy">
                    </div>
                    <div class="blog-card-body">
                        <div class="blog-cat"><?= $b['cat'] ?></div>
                        <a href="blog.php"><h5 class="blog-title"><?= $b['title'] ?></h5></a>
                        <div class="blog-meta mt-2">
                            <i class="bi bi-calendar3 me-1"></i><?= $b['date'] ?>
                            &nbsp;·&nbsp;<i class="bi bi-clock me-1"></i> 5 min
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="blog.php" class="btn-primary-custom">
                <i class="bi bi-journal-text"></i> Ver todos los artículos
            </a>
        </div>
    </div>
</section>


<!-- ============================================================
     FAQ — PREGUNTAS FRECUENTES
     ============================================================ -->
<section class="section-pad">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4" data-aos="fade-right">
                <span class="section-tag">¿Tienes dudas?</span>
                <h2 class="section-title">Preguntas<br>frecuentes</h2>
                <p class="text-muted">Aquí resolvemos las dudas más comunes. Si no encuentras tu respuesta, escríbenos sin compromiso.</p>
                <a href="https://wa.me/524641234567?text=Hola%2C+tengo+una+pregunta" class="btn-wa-green mt-3" target="_blank">
                    <i class="bi bi-whatsapp"></i> Preguntar ahora
                </a>
            </div>
            <div class="col-lg-8" data-aos="fade-left">
                <div class="accordion faq-accordion" id="faqAccordion">
                    <?php if (!empty($faqs)): ?>
                        <?php foreach($faqs as $i => $faq): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
                                    <?= e($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted"><?= e($faq['answer']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <!-- FAQs demo estáticas -->
                    <?php
                    $faq_demo = [
                        ['q'=>'¿Hacen instalaciones eléctricas en casas y negocios?','a'=>'Sí, trabajamos proyectos residenciales, comerciales e industriales. Desde un punto de luz hasta instalaciones completas de edificios.'],
                        ['q'=>'¿Cuánto tarda una cotización?','a'=>'Respondemos en menos de 24 horas hábiles. Por WhatsApp generalmente contestamos en menos de 2 horas en horario de atención.'],
                        ['q'=>'¿El café de Coffee Bells se puede pedir online?','a'=>'Sí, tenemos nuestra tienda online con envíos a toda la república mexicana. El café lo enviamos recién tostado.'],
                        ['q'=>'¿Ofrecen planes de mantenimiento de jardín?','a'=>'Sí, tenemos planes mensuales de mantenimiento que incluyen riego, poda, fertilización y revisión general de tus plantas.'],
                        ['q'=>'¿Trabajan en toda la zona de Salamanca y alrededores?','a'=>'Atendemos Salamanca, Irapuato, Celaya, León, Pénjamo y municipios cercanos. Para zonas más lejanas consultamos disponibilidad.'],
                        ['q'=>'¿Puedo contratar decoración sin tener que comprar muebles nuevos?','a'=>'Por supuesto. Nuestro servicio de asesoría de decoración incluye reorganización, paleta de colores, iluminación y accesorios. No siempre hay que comprar todo nuevo.'],
                    ];
                    foreach($faq_demo as $i => $faq):
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
                                <?= $faq['q'] ?>
                            </button>
                        </h2>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i===0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted"><?= $faq['a'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     FORMULARIO RÁPIDO DE CONTACTO + MAPA
     ============================================================ -->
<section class="quick-contact" id="contacto-rapido">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="section-tag">Contacto rápido</span>
                <h2 class="section-title">¿Listo para transformar<br>tu espacio?</h2>
                <p class="text-muted mb-4">Déjanos tus datos y te contactamos en menos de 2 horas. Cotización 100% gratuita, sin compromiso.</p>

                <?= showFlash() ?>

                <div class="quick-form-card">
                    <form action="actions/save_contact.php" method="POST" id="quickContactForm">
                        <input type="hidden" name="action" value="contact">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control form-control-custom" placeholder="Tu nombre completo" required>
                            </div>
                            <div class="col-md-6">
                                <input type="tel" name="phone" class="form-control form-control-custom" placeholder="Tu teléfono" required>
                            </div>
                            <div class="col-12">
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="Tu correo electrónico">
                            </div>
                            <div class="col-12">
                                <select name="service" class="form-select form-select-custom">
                                    <option value="">¿Qué servicio necesitas?</option>
                                    <option>Instalación eléctrica</option>
                                    <option>Iluminación de diseño</option>
                                    <option>Smart Home</option>
                                    <option>Decoración de interiores</option>
                                    <option>Diseño de jardín</option>
                                    <option>Mantenimiento de jardín</option>
                                    <option>Café / Coffee Bells</option>
                                    <option>Paquete completo</option>
                                    <option>Otro</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control form-control-custom" rows="3" placeholder="Cuéntanos brevemente qué necesitas..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-primary-custom w-100 justify-content-center">
                                    <i class="bi bi-send-fill"></i> Enviar solicitud gratis
                                </button>
                            </div>
                            <div class="col-12">
                                <a href="https://wa.me/524641234567?text=Hola%2C+quiero+una+cotizaci%C3%B3n+gratuita" class="btn-wa-green w-100 justify-content-center" target="_blank">
                                    <i class="bi bi-whatsapp"></i> O escríbenos directo a WhatsApp
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <div class="map-wrapper mb-4">
                    <!-- Reemplaza con tu iframe de Google Maps real -->
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59914.21836!2d-101.1948!3d20.5703!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842be3e3e3e3e3e3%3A0x0!2sSalamanca%2C+Guanajuato!5e0!3m2!1ses!2smx!4v1"
                        allowfullscreen loading="lazy"
                        title="Ubicación CoffeeBells & Home — Salamanca, Guanajuato">
                    </iframe>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="why-icon-box">
                            <div class="why-icon"><i class="bi bi-telephone-fill"></i></div>
                            <div>
                                <h6>Teléfono</h6>
                                <p><a href="tel:+524641234567">+52 464 123 4567</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="why-icon-box">
                            <div class="why-icon"><i class="bi bi-whatsapp"></i></div>
                            <div>
                                <h6>WhatsApp</h6>
                                <p><a href="https://wa.me/524641234567" target="_blank">Escribir ahora</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="why-icon-box">
                            <div class="why-icon" style="background:var(--beige-dark);"><i class="bi bi-clock-fill" style="color:var(--coffee);"></i></div>
                            <div>
                                <h6>Horarios</h6>
                                <p>Lun–Sáb 9–20h<br>Dom 10–15h</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="why-icon-box">
                            <div class="why-icon" style="background:rgba(193,103,75,.1);"><i class="bi bi-geo-alt-fill" style="color:var(--terracota);"></i></div>
                            <div>
                                <h6>Ubicación</h6>
                                <p>Salamanca,<br>Guanajuato, Méx.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     SECCIÓN CTA FINAL
     ============================================================ -->
<section class="section-pad" style="background:linear-gradient(135deg,var(--primary-dark) 0%,var(--primary) 100%);">
    <div class="container text-center" data-aos="fade-up">
        <h2 style="color:#fff;font-size:clamp(1.8rem,3.5vw,2.8rem);">¿Listo para empezar?</h2>
        <p style="color:rgba(255,255,255,.85);font-size:1.1rem;max-width:580px;margin:.75rem auto 2rem;">
            Un mensaje es todo lo que necesitas. Cotización gratuita, respuesta en menos de 2 horas y proyectos que hablan por sí solos.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="https://wa.me/524641234567?text=Hola%2C+quiero+una+cotizaci%C3%B3n+gratuita" class="btn-wa-green" target="_blank" style="font-size:1.05rem;padding:.85rem 2.2rem;">
                <i class="bi bi-whatsapp"></i> WhatsApp ahora
            </a>
            <a href="contacto.php" class="btn-outline-custom" style="border-color:#fff;color:#fff;font-size:1.05rem;padding:.85rem 2.2rem;">
                <i class="bi bi-send"></i> Enviar solicitud
            </a>
            <a href="tel:+524641234567" class="btn-coffee" style="font-size:1.05rem;padding:.85rem 2.2rem;">
                <i class="bi bi-telephone-fill"></i> Llamar ahora
            </a>
        </div>
    </div>
</section>
<script>
  // Ocultar loader cuando la página termina de cargar
  window.addEventListener('load', function () {
    const loader = document.getElementById('page-loader');
    if (loader) {
      loader.classList.add('loader-hidden');
      // Eliminarlo del DOM después de la transición para no bloquear clics
      setTimeout(() => loader.remove(), 600);
    }
  });

  // Fallback por si el evento 'load' ya disparó (ej. caché)
  if (document.readyState === 'complete') {
    const loader = document.getElementById('page-loader');
    if (loader) {
      loader.classList.add('loader-hidden');
      setTimeout(() => loader.remove(), 600);
    }
  }
</script>


<?php require_once 'includes/footer.php'; ?>