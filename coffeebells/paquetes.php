<?php
// ============================================================
// PAQUETES.PHP — Paquetes y planes comerciales
// ============================================================
require_once 'includes/db.php';
require_once 'includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$meta_title       = 'Paquetes y Planes — CoffeeBells & Home';
$meta_description = 'Paquetes integrales que combinan electricidad, decoración, jardinería y café. Mejor precio, mayor valor.';

require_once 'includes/header.php';
require_once 'includes/navbar.php';

$packages = [
    [
        'icon'    => '🌿',
        'name'    => 'Terraza Perfecta',
        'desc'    => 'Todo para transformar tu terraza en el espacio que siempre soñaste.',
        'price'   => 4500,
        'color'   => 'var(--primary)',
        'popular' => false,
        'wa_msg'  => 'Terraza+Perfecta',
        'features'=> [
            'Instalación eléctrica exterior completa',
            'Iluminación ambiental LED incluida',
            'Diseño de jardín en terraza',
            'Decoración y mobiliario',
            'Kit café para terraza',
            'Garantía 3 meses',
        ],
    ],
    [
        'icon'    => '🏠',
        'name'    => 'Hogar y Aroma',
        'desc'    => 'La transformación completa de tu hogar: luz, estilo, naturaleza y café.',
        'price'   => 8900,
        'color'   => 'var(--terracota)',
        'popular' => true,
        'wa_msg'  => 'Hogar+y+Aroma',
        'features'=> [
            'Revisión y corrección eléctrica completa',
            'Iluminación interior de diseño',
            'Asesoría decoración 2 ambientes',
            'Selección plantas de interior',
            'Kit café especialidad 3 meses',
            'Garantía 6 meses',
            'Visita de seguimiento incluida',
        ],
    ],
    [
        'icon'    => '📚',
        'name'    => 'Rincón de Lectura',
        'desc'    => 'El rincón perfecto: iluminación cálida, ambiente acogedor y el café ideal.',
        'price'   => 3200,
        'color'   => 'var(--coffee)',
        'popular' => false,
        'wa_msg'  => 'Rinc%C3%B3n+de+Lectura',
        'features'=> [
            'Punto de luz específico de lectura',
            'Lámpara de diseño incluida',
            'Asesoría decoración del rincón',
            'Planta de interior seleccionada',
            'Café surtido 3 meses',
            'Garantía 3 meses',
        ],
    ],
    [
        'icon'    => '🌱',
        'name'    => 'Jardín Inteligente',
        'desc'    => 'El jardín de tus sueños con tecnología, plantas y mantenimiento incluido.',
        'price'   => 6500,
        'color'   => 'var(--primary-dark)',
        'popular' => false,
        'wa_msg'  => 'Jard%C3%ADn+Inteligente',
        'features'=> [
            'Diseño de jardín personalizado',
            'Sistema de riego automatizado',
            'Iluminación exterior LED',
            'Plantas y sustrato incluidos',
            '3 visitas de mantenimiento',
            'Garantía 6 meses',
        ],
    ],
    [
        'icon'    => '⚡',
        'name'    => 'Smart Home Starter',
        'desc'    => 'Automatiza tu hogar con domótica, iluminación inteligente y control total.',
        'price'   => 12000,
        'color'   => 'var(--warm-yellow)',
        'popular' => false,
        'wa_msg'  => 'Smart+Home+Starter',
        'features'=> [
            'Instalación eléctrica preparada para domótica',
            'Central de control Smart Home',
            'Iluminación RGB inteligente x4 ambientes',
            'Control por voz y app',
            'Asesoría personalizada',
            'Soporte técnico 1 año',
        ],
    ],
    [
        'icon'    => '☕',
        'name'    => 'Coffee Premium Pack',
        'desc'    => 'La experiencia Coffee Bells completa para los amantes del café de especialidad.',
        'price'   => 1800,
        'color'   => 'var(--coffee-mid)',
        'popular' => false,
        'wa_msg'  => 'Coffee+Premium+Pack',
        'features'=> [
            '3 variedades café 250g c/u',
            'Prensa francesa premium',
            'Molinillo manual',
            'Guía de preparación impresa',
            'Gift card $200 en la cafetería',
            'Envío gratis incluido',
        ],
    ],
];
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container text-center">
        <span class="page-hero-tag"><i class="bi bi-grid-1x2 me-1"></i> Paquetes especiales</span>
        <h1>Soluciones completas<br>para cada necesidad</h1>
        <p>Combinamos servicios para darte más valor, mejor precio y una experiencia integral.</p>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active">Paquetes</li>
        </ol></nav>
    </div>
</div>

<!-- Intro -->
<section class="section-pad-sm bg-gray">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4" data-aos="fade-up">
                <div class="trust-item justify-content-center gap-2">
                    <i class="bi bi-percent fs-3" style="color:var(--primary);"></i>
                    <div>
                        <strong style="color:var(--coffee);">Ahorra hasta 25%</strong>
                        <p class="mb-0 text-muted small">vs contratar servicios por separado</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="trust-item justify-content-center gap-2">
                    <i class="bi bi-calendar-check fs-3" style="color:var(--terracota);"></i>
                    <div>
                        <strong style="color:var(--coffee);">Un solo coordinador</strong>
                        <p class="mb-0 text-muted small">Gestionamos todo por ti sin complicaciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="trust-item justify-content-center gap-2">
                    <i class="bi bi-shield-check fs-3" style="color:var(--warm-yellow);"></i>
                    <div>
                        <strong style="color:var(--coffee);">Garantía extendida</strong>
                        <p class="mb-0 text-muted small">Todos los paquetes incluyen garantía de servicio</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Paquetes -->
<section class="section-pad">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Elige tu plan</span>
            <h2 class="section-title">Paquetes diseñados<br>para transformar tu vida</h2>
            <p class="section-subtitle">Cada paquete es ajustable a tus necesidades. Contáctanos para personalizar.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($packages as $i => $pkg): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i%3)*80 ?>">
                <div class="package-card h-100 <?= $pkg['popular'] ? 'featured' : '' ?>">
                    <?php if ($pkg['popular']): ?>
                    <span class="package-badge-popular">⭐ Más popular</span>
                    <?php endif; ?>
                    <div class="package-icon"><?= $pkg['icon'] ?></div>
                    <h4 style="font-family:var(--font-title);color:var(--coffee);margin-bottom:.4rem;">
                        <?= e($pkg['name']) ?>
                    </h4>
                    <p class="text-muted small mb-3"><?= e($pkg['desc']) ?></p>
                    <div class="package-price" style="color:<?= $pkg['color'] ?>;">
                        <?= formatPrice($pkg['price']) ?>
                        <span>/ proyecto</span>
                    </div>
                    <ul class="package-features">
                        <?php foreach ($pkg['features'] as $feat): ?>
                        <li>
                            <i class="bi bi-check-circle-fill" style="color:var(--primary);"></i>
                            <?= e($feat) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex gap-2 flex-wrap mt-auto">
                        <a href="https://wa.me/524641234567?text=Hola%2C+me+interesa+el+paquete+<?= $pkg['wa_msg'] ?>"
                           class="btn-wa-green flex-grow-1 justify-content-center" target="_blank">
                            <i class="bi bi-whatsapp"></i> Contratar
                        </a>
                        <a href="contacto.php?paquete=<?= urlencode($pkg['name']) ?>"
                           class="btn-outline-custom justify-content-center" style="padding:.6rem 1rem;">
                            <i class="bi bi-info-circle"></i> Info
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Formulario de lead por paquete -->
<section class="section-pad bg-beige-section" id="solicitar">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center mb-4" data-aos="fade-up">
                    <span class="section-tag">¿Quieres un paquete personalizado?</span>
                    <h2 class="section-title">Cuéntanos tu proyecto</h2>
                    <p class="section-subtitle">Si ningún paquete encaja exactamente con lo que necesitas, lo creamos juntos.</p>
                </div>
                <div class="quick-form-card" data-aos="fade-up">
                    <form action="actions/save_contact.php" method="POST">
                        <input type="hidden" name="action" value="contact">
                        <input type="hidden" name="subject" value="Solicitud de paquete personalizado">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control form-control-custom"
                                       placeholder="Tu nombre" required
                                       value="<?= e($_GET['nombre'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="tel" name="phone" class="form-control form-control-custom"
                                       placeholder="Tu teléfono" required>
                            </div>
                            <div class="col-12">
                                <select name="service" class="form-select form-select-custom">
                                    <option value="">¿Qué paquete te interesa?</option>
                                    <?php foreach ($packages as $pkg): ?>
                                    <option <?= ($_GET['paquete'] ?? '') === $pkg['name'] ? 'selected' : '' ?>>
                                        <?= e($pkg['name']) ?> — <?= formatPrice($pkg['price']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <option>Paquete personalizado</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control form-control-custom" rows="3"
                                          placeholder="Cuéntanos tu espacio, necesidades y presupuesto aproximado..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-primary-custom w-100 justify-content-center">
                                    <i class="bi bi-send-fill"></i> Enviar solicitud
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ paquetes -->
<section class="section-pad">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4" data-aos="fade-right">
                <span class="section-tag">Dudas sobre paquetes</span>
                <h2 class="section-title">Preguntas frecuentes</h2>
                <p class="text-muted">Respondemos tus dudas más comunes sobre nuestros paquetes y planes.</p>
            </div>
            <div class="col-lg-8" data-aos="fade-left">
                <div class="accordion faq-accordion" id="faqPaquetes">
                    <?php
                    $faq_paq = [
                        ['q'=>'¿Los precios son fijos o pueden variar?','a'=>'Los precios mostrados son referenciales. El costo final depende del tamaño del espacio, materiales específicos y complejidad del proyecto. Siempre presentamos un presupuesto detallado antes de iniciar.'],
                        ['q'=>'¿Cuánto tiempo tarda la ejecución de un paquete?','a'=>'Depende del paquete. Proyectos pequeños como Rincón de Lectura pueden completarse en 1-2 días. Hogar y Aroma o Jardín Inteligente pueden requerir 1-2 semanas.'],
                        ['q'=>'¿Puedo personalizar un paquete existente?','a'=>'Sí, todos los paquetes son ajustables. Podemos quitar o agregar servicios según tu presupuesto y necesidades específicas.'],
                        ['q'=>'¿Qué incluye la garantía de los paquetes?','a'=>'Cubre defectos en materiales y mano de obra. Si algo falla dentro del período de garantía, lo corregimos sin costo adicional.'],
                    ];
                    foreach ($faq_paq as $i => $f):
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i>0?'collapsed':'' ?>"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#fpaq<?= $i ?>">
                                <?= e($f['q']) ?>
                            </button>
                        </h2>
                        <div id="fpaq<?= $i ?>" class="accordion-collapse collapse <?= $i===0?'show':'' ?>" data-bs-parent="#faqPaquetes">
                            <div class="accordion-body text-muted"><?= e($f['a']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>