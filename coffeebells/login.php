<?php
// ============================================================
// LOGIN.PHP — Página de login para administrador
// ============================================================
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Si ya está logueado, redirigir al dashboard
if (isAdminLoggedIn()) {
    redirect('dashboard/index.php');
}

$meta_title = 'Acceso Administrador — CoffeeBells & Home';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($meta_title) ?></title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: var(--coffee); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card {
            background: #fff; border-radius: 24px;
            padding: 3rem 2.5rem; width: 100%; max-width: 420px;
            box-shadow: 0 32px 80px rgba(0,0,0,.3);
        }
        .login-logo { text-align: center; margin-bottom: 2rem; }
        .login-logo-icon {
            width: 70px; height: 70px; border-radius: 50%;
            background: var(--warm-yellow); color: var(--coffee);
            font-size: 2rem; display: flex; align-items: center; justify-content: center;
            margin: 0 auto .75rem;
        }
        .login-logo h4 { font-family: var(--font-title); color: var(--coffee); margin: 0; }
        .login-logo small { color: var(--gray-500); font-size: .8rem; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="login-logo-icon"><i class="bi bi-cup-hot-fill"></i></div>
        <h4>CoffeeBells & Home</h4>
        <small>Panel de administración</small>
    </div>

    <?= showFlash() ?>

    <form action="actions/login_action.php" method="POST">
        <div class="mb-3">
            <label class="form-label fw-600" style="color:var(--coffee);font-weight:600;">Correo electrónico</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control border-start-0"
                       placeholder="admin@coffeebells.mx" required
                       value="<?= e($_POST['email'] ?? '') ?>">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-600" style="color:var(--coffee);font-weight:600;">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="loginPass" class="form-control border-start-0" placeholder="••••••••" required>
                <button type="button" class="input-group-text bg-light" id="togglePass" title="Mostrar contraseña">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>
        <button type="submit" class="btn-primary-custom w-100 justify-content-center" style="font-size:1rem;padding:.8rem;">
            <i class="bi bi-box-arrow-in-right"></i> Ingresar al panel
        </button>
    </form>

    <hr class="my-4">
    <div class="text-center">
        <a href="index.php" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Volver al sitio
        </a>
    </div>

    <!-- Credenciales demo -->
    <div class="mt-3 p-3 rounded" style="background:rgba(232,168,56,.1);border:1px solid rgba(232,168,56,.3);">
        <small style="color:var(--coffee-mid);">
            <strong>Demo:</strong><br>
            📧 admin@coffeebells.mx<br>
            🔒 Admin2026!
        </small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePass').addEventListener('click', function() {
    const pass = document.getElementById('loginPass');
    const icon = document.getElementById('eyeIcon');
    if (pass.type === 'password') {
        pass.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pass.type = 'password';
        icon.className = 'bi bi-eye';
    }
});
</script>
</body>
</html>