<?php
// ============================================================
// REGISTER.PHP — Registro de usuarios del frontend
// ============================================================
require_once 'includes/db.php';
require_once 'includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$meta_title = 'Crear cuenta — CoffeeBells & Home';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item active">Crear cuenta</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-pad bg-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">

                <div class="text-center mb-4">
                    <span class="section-tag">Bienvenido</span>
                    <h2 class="section-title">Crea tu cuenta</h2>
                    <p class="text-muted">Accede a tu historial de pedidos, favoritos y ofertas exclusivas.</p>
                </div>

                <?= showFlash() ?>

                <div class="quick-form-card">
                    <form action="actions/register_action.php" method="POST" id="registerForm">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-custom"
                                   placeholder="Tu nombre" required
                                   value="<?= e($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-custom"
                                   placeholder="correo@ejemplo.com" required
                                   value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="tel" name="phone" class="form-control form-control-custom"
                                   placeholder="+52 464 000 0000"
                                   value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-custom"
                                   placeholder="Mínimo 8 caracteres" required minlength="8">
                            <div class="form-text">Mínimo 8 caracteres.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirmar contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="confirm" class="form-control form-control-custom"
                                   placeholder="Repite tu contraseña" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="termsCheck" required>
                            <label class="form-check-label small" for="termsCheck">
                                Acepto los <a href="#">términos de uso</a> y el <a href="#">aviso de privacidad</a>.
                            </label>
                        </div>

                        <button type="submit" class="btn-primary-custom w-100 justify-content-center">
                            <i class="bi bi-person-check-fill"></i> Crear mi cuenta
                        </button>
                    </form>

                    <hr class="my-3">
                    <p class="text-center text-muted small mb-0">
                        ¿Ya tienes cuenta? <a href="login.php" style="color:var(--primary);font-weight:600;">Inicia sesión</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>