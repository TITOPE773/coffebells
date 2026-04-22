<?php
// Navbar sticky premium con mega-menú
$current = basename($_SERVER['PHP_SELF']);
?>
<header id="main-header">
    <!-- Top bar -->
    <div class="top-bar d-none d-md-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>
                        <i class="bi bi-geo-alt-fill me-1"></i> Salamanca, Guanajuato, México &nbsp;|&nbsp;
                        <i class="bi bi-clock-fill me-1"></i> Lun–Sáb 9:00–20:00 | Dom 10:00–15:00
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small>
                        <a href="tel:+524641234567" class="top-bar-link me-3">
                            <i class="bi bi-telephone-fill me-1"></i> +52 464 123 4567
                        </a>
                        <a href="mailto:hola@coffeebells.mx" class="top-bar-link me-3">
                            <i class="bi bi-envelope-fill me-1"></i> hola@coffeebells.mx
                        </a>
                        <a href="https://facebook.com/coffeebells" class="top-bar-link me-2" target="_blank"><i class="bi bi-facebook"></i></a>
                        <a href="https://instagram.com/coffeebells" class="top-bar-link me-2" target="_blank"><i class="bi bi-instagram"></i></a>
                        <a href="https://tiktok.com/@coffeebells" class="top-bar-link" target="_blank"><i class="bi bi-tiktok"></i></a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar principal -->
    <nav class="navbar navbar-expand-lg navbar-dark main-nav" id="mainNavbar">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand brand-logo" href="/coffeebells/index.php">
                <span class="brand-icon"><i class="bi bi-cup-hot-fill"></i></span>
                <span class="brand-text">
                    <span class="brand-name">CoffeeBells</span>
                    <span class="brand-sub">& Home</span>
                </span>
            </a>

            <!-- Carrito (móvil) + Toggle -->
            <div class="d-flex align-items-center d-lg-none gap-2">
                <a href="/coffeebells/carrito.php" class="btn btn-cart-mobile">
                    <i class="bi bi-cart3"></i>
                    <span class="cart-badge"><?= getCartCount() ?></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Menú -->
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $current=='index.php'?'active':'' ?>" href="/coffeebells/index.php">Inicio</a>
                    </li>

                    <!-- Mega menú Servicios -->
                    <li class="nav-item dropdown mega-dropdown">
                        <a class="nav-link dropdown-toggle <?= $current=='servicios.php'?'active':'' ?>" href="#" role="button" data-bs-toggle="dropdown">Servicios</a>
                        <div class="dropdown-menu mega-menu">
                            <div class="container">
                                <div class="row g-4 py-3">
                                    <div class="col-md-3">
                                        <h6 class="mega-title"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Electricidad</h6>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#electricidad">Instalaciones eléctricas</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#iluminacion">Iluminación interior/exterior</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#smarthome">Smart Home</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#mantenimiento">Mantenimiento preventivo</a>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="mega-title"><i class="bi bi-house-heart-fill text-danger me-2"></i>Decoración</h6>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#decoracion">Interiorismo</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#muebles">Selección de muebles</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#color">Asesoría de color</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#asesoria">Asesoría personalizada</a>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="mega-title"><i class="bi bi-tree-fill text-success me-2"></i>Jardinería</h6>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#jardines">Diseño de jardines</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#mantenimiento-jard">Mantenimiento</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#huertos">Huertos urbanos</a>
                                        <a class="dropdown-item" href="/coffeebells/servicios.php#riego">Sistemas de riego</a>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mega-cta-box">
                                            <h6>¿Necesitas un servicio?</h6>
                                            <p>Te damos cotización gratuita en 24 hrs.</p>
                                            <a href="/coffeebells/contacto.php" class="btn btn-primary btn-sm w-100">Solicitar cotización</a>
                                            <a href="https://wa.me/524641234567?text=Hola%2C%20quiero%20cotizar%20un%20servicio" class="btn btn-success btn-sm w-100 mt-2" target="_blank">
                                                <i class="bi bi-whatsapp me-1"></i> WhatsApp directo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $current=='coffee-bells.php'?'active':'' ?>" href="/coffeebells/coffee-bells.php">
                            <i class="bi bi-cup-hot me-1"></i>Coffee Bells
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $current=='tienda.php'?'active':'' ?>" href="/coffeebells/tienda.php">Tienda</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $current=='paquetes.php'?'active':'' ?>" href="/coffeebells/paquetes.php">Paquetes</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $current=='blog.php'?'active':'' ?>" href="/coffeebells/blog.php">Blog</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $current=='contacto.php'?'active':'' ?>" href="/coffeebells/contacto.php">Contacto</a>
                    </li>
                </ul>

                <!-- Buscador + Carrito + CTA -->
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-icon-nav" id="searchToggle" title="Buscar">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="/coffeebells/carrito.php" class="btn btn-icon-nav position-relative" title="Carrito">
                        <i class="bi bi-cart3"></i>
                        <span class="cart-count-badge" id="cartCountBadge"><?= getCartCount() ?></span>
                    </a>
                    <a href="tel:+524641234567" class="btn btn-call-nav d-none d-xl-inline-flex">
                        <i class="bi bi-telephone-fill me-1"></i> Llamar
                    </a>
                    <a href="/coffeebells/contacto.php" class="btn btn-primary-nav">
                        Cotizar gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Barra de búsqueda global oculta -->
    <div class="search-bar-global" id="globalSearchBar" style="display:none;">
        <div class="container">
            <form action="/coffeebells/tienda.php" method="GET" class="d-flex gap-2 py-3">
                <input type="text" name="q" class="form-control form-control-lg" placeholder="Busca productos, servicios, artículos...">
                <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
                <button type="button" class="btn btn-outline-secondary" id="searchClose"><i class="bi bi-x-lg"></i></button>
            </form>
        </div>
    </div>
</header>