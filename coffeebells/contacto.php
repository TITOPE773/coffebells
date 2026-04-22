<?php
// ============================================================
// CONTACTO.PHP — Página completa de contacto
// ============================================================
require_once 'includes/db.php';
require_once 'includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$pdo = getDB();
$faqs = getFAQs($pdo);

$meta_title       = 'Contacto — CoffeeBells & Home | Salamanca, Guanajuato';
$meta_description = 'Contáctanos para cotizaciones de electricidad, decoración, jardinería y café. Respondemos en menos de 2 horas.';

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <span class="page-hero-tag"><i class="bi bi-envelope-heart me-1"></i> Contacto</span>
        <h1>¿Cómo podemos<br>ayudarte hoy?</h1>
        <p>Respondemos en menos de 2 horas. Cotización siempre gratuita.</p>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active">Contacto</li>
        </ol></nav>
    </div>
</div>

<!-- Canales rápidos -->
<section class="section-pad-sm bg-gray">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-md-3" data-aos="fade-up">
                <a href="https://wa.me/524641234567" target="_blank"
                   class="d-flex flex-column align-items-center gap-2 p-3 rounded text-center text-decoration-none"
                   style="background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);transition:all .2s;"
                   onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform=''">
                    <i class="bi bi-whatsapp fs-2" style="color:#25D366;"></i>
                    <strong style="color:var(--coffee);font-size:.9rem;">WhatsApp</strong>
                    <small class="text-muted">Respuesta inmediata</small>
                </a>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <a href="tel:+524641234567"
                   class="d-flex flex-column align-items-center gap-2 p-3 rounded text-center text-decoration-none"
                   style="background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);transition:all .2s;"
                   onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform=''">
                    <i class="bi bi-telephone-fill fs-2" style="color:var(--primary);"></i>
                    <strong style="color:var(--coffee);font-size:.9rem;">Llamar</strong>
                    <small class="text-muted">+52 464 123 4567</small>
                </a>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <a href="mailto:hola@coffeebells.mx"
                   class="d-flex flex-column align-items-center gap-2 p-3 rounded text-center text-decoration-none"
                   style="background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);transition:all .2s;"
                   onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform=''">
                    <i class="bi bi-envelope-fill fs-2" style="color:var(--terracota);"></i>
                    <strong style="color:var(--coffee);font-size:.9rem;">Email</strong>
                    <small class="text-muted">hola@coffeebells.mx</small>
                </a>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="d-flex flex-column align-items-center gap-2 p-3 rounded text-center"
                     style="background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);">
                    <i class="bi bi-clock-fill fs-2" style="color:var(--warm-yellow);"></i>
                    <strong style="color:var(--coffee);font-size:.9rem;">Horarios</strong>
                    <small class="text-muted">Lun–Sáb 9–20h<br>Dom 10–15h</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formulario + Mapa -->
<section class="section-pad">
    <div class="container">
        <div class="row g-5">

            <!-- Formulario completo -->
            <div class="col-lg-7" data-aos="fade-right">
                <span class="section-tag">Formulario de contacto</span>
                <h2 class="section-title mb-4">Cuéntanos tu proyecto</h2>

                <?= showFlash() ?>
                <div id="formSuccessMsg" style="display:none;" class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    ¡Mensaje recibido! Te contactaremos en menos de 2 horas.
                </div>

                <form action="actions/save_contact.php" method="POST" id="mainContactForm">
                    <input type="hidden" name="action" value="contact">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-custom"
                                   placeholder="Tu nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control form-control-custom"
                                   placeholder="+52 464 000 0000" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Correo electrónico</label>
                            <input type="email" name="email" class="form-control form-control-custom"
                                   placeholder="tu@correo.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asunto</label>
                            <input type="text" name="subject" class="form-control form-control-custom"
                                   placeholder="¿En qué te ayudamos?" value="Solicitud de información">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Servicio de interés</label>
                            <select name="service" class="form-select form-select-custom">
                                <option value="">Seleccionar servicio</option>
                                <option>Instalación eléctrica</option>
                                <option>Iluminación de diseño</option>
                                <option>Smart Home / Domótica</option>
                                <option>Mantenimiento eléctrico</option>
                                <option>Decoración de interiores</option>
                                <option>Asesoría de color</option>
                                <option>Diseño de jardín</option>
                                <option>Mantenimiento de jardín</option>
                                <option>Huerto urbano</option>
                                <option>Sistema de riego</option>
                                <option>Coffee Bells / Café</option>
                                <option>Paquete completo</option>
                                <option>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Presupuesto estimado</label>
                            <select name="budget" class="form-select form-select-custom">
                                <option value="">Seleccionar rango</option>
                                <option>Menos de $2,000</option>
                                <option>$2,000 – $5,000</option>
                                <option>$5,000 – $10,000</option>
                                <option>$10,000 – $25,000</option>
                                <option>Más de $25,000</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prefiero que me contacten por</label>
                            <select name="contact_pref" class="form-select form-select-custom">
                                <option value="">Sin preferencia</option>
                                <option>WhatsApp</option>
                                <option>Llamada telefónica</option>
                                <option>Correo electrónico</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mensaje</label>
                            <textarea name="message" class="form-control form-control-custom" rows="4"
                                      placeholder="Descríbenos tu proyecto, espacio, necesidades específicas..."></textarea>
                        </div>

                        <!-- Anti-spam honeypot -->
                        <input type="text" name="website" style="display:none;" tabindex="-1" autocomplete="off">

                        <div class="col-12">
                            <button type="submit" class="btn-primary-custom w-100 justify-content-center"
                                    style="font-size:1rem;padding:.85rem;">
                                <i class="bi bi-send-fill"></i> Enviar mensaje
                            </button>
                        </div>
                        <div class="col-12">
                            <a href="https://wa.me/524641234567?text=Hola+CoffeeBells%2C+quiero+una+cotizaci%C3%B3n"
                               class="btn-wa-green w-100 justify-content-center" target="_blank">
                                <i class="bi bi-whatsapp"></i> O contáctanos por WhatsApp
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Mapa + Info -->
            <div class="col-lg-5" data-aos="fade-left">
                <div class="map-wrapper mb-4" id="mapa">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59914.21836!2d-101.1948!3d20.5703!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842be3e3e3e3e3e3%3A0x0!2sSalamanca%2C+Guanajuato!5e0!3m2!1ses!2smx!4v1"
                        allowfullscreen loading="lazy" title="Mapa CoffeeBells">
                    </iframe>
                </div>

                <div class="quick-form-card">
                    <h5 style="font-family:var(--font-title);color:var(--coffee);margin-bottom:1.5rem;">
                        Información de contacto
                    </h5>
                    <ul class="footer-contact" style="list-style:none;padding:0;">
                        <li class="mb-3">
                            <i class="bi bi-geo-alt-fill" style="color:var(--terracota);"></i>
                            <span>Salamanca, Guanajuato, México<br>
                            <small class="text-muted">Atendemos a domicilio en toda la zona</small></span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone-fill" style="color:var(--primary);"></i>
                            <span><a href="tel:+524641234567" style="color:var(--coffee);">+52 464 123 4567</a></span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-whatsapp" style="color:#25D366;"></i>
                            <span><a href="https://wa.me/524641234567" target="_blank" style="color:var(--coffee);">Escribir por WhatsApp</a></span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-envelope-fill" style="color:var(--warm-yellow);"></i>
                            <span><a href="mailto:hola@coffeebells.mx" style="color:var(--coffee);">hola@coffeebells.mx</a></span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-clock-fill" style="color:var(--coffee);"></i>
                            <span>
                                Lunes a Sábado: 9:00 – 20:00<br>
                                Domingos: 10:00 – 15:00
                            </span>
                        </li>
                    </ul>

                    <!-- Redes sociales -->
                    <div class="mt-3">
                        <p class="fw-semibold mb-2" style="color:var(--coffee);">Síguenos en redes</p>
                        <div class="footer-socials">
                            <a href="https://facebook.com/coffeebells" target="_blank" class="social-link"
                               style="background:var(--beige);color:var(--coffee);">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://instagram.com/coffeebells" target="_blank" class="social-link"
                               style="background:var(--beige);color:var(--coffee);">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="https://tiktok.com/@coffeebells" target="_blank" class="social-link"
                               style="background:var(--beige);color:var(--coffee);">
                                <i class="bi bi-tiktok"></i>
                            </a>
                            <a href="https://youtube.com/@coffeebells" target="_blank" class="social-link"
                               style="background:var(--beige);color:var(--coffee);">
                                <i class="bi bi-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- AJAX para formulario de contacto -->
<script>
document.getElementById('mainContactForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn  = this.querySelector('[type="submit"]');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Enviando...';
    btn.disabled  = true;

    fetch('actions/save_contact.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('formSuccessMsg').style.display = 'block';
            this.reset();
            window.scrollTo({ top: document.getElementById('formSuccessMsg').offsetTop - 100, behavior: 'smooth' });
        } else {
            showToast('❌ ' + data.message, 'danger');
        }
    })
    .finally(() => {
        btn.innerHTML = orig;
        btn.disabled  = false;
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>