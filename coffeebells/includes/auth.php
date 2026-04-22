<?php
// ============================================================
// AUTH.PHP — Autenticación y autorización
// ============================================================

function requireLogin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) {
        setFlash('error', 'Debes iniciar sesión para continuar.');
        header('Location: /coffeebells/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function requireAdmin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_id']) || empty($_SESSION['is_admin'])) {
        header('Location: /coffeebells/login.php?admin=1');
        exit;
    }
}

function loginUser(PDO $pdo, string $email, string $password): array {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND active = 1 LIMIT 1");
    $stmt->execute([':email' => trim($email)]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Correo o contraseña incorrectos.'];
    }

    // Actualizar último login
    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")
        ->execute([':id' => $user['id']]);

    // Iniciar sesión
    if (session_status() === PHP_SESSION_NONE) session_start();
    session_regenerate_id(true);

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];

    if ($user['role'] === 'admin') {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['is_admin'] = true;
    }

    return ['success' => true, 'user' => $user];
}

function logoutUser(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function registerUser(PDO $pdo, array $data): array {
    $name     = trim($data['name']  ?? '');
    $email    = trim($data['email'] ?? '');
    $password = $data['password']   ?? '';
    $phone    = trim($data['phone'] ?? '');

    if (!$name || !$email || !$password) {
        return ['success' => false, 'message' => 'Todos los campos son requeridos.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Correo no válido.'];
    }
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.'];
    }

    // Verificar si ya existe
    $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        return ['success' => false, 'message' => 'Ya existe una cuenta con ese correo.'];
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, phone, role, active, created_at)
        VALUES (:name, :email, :password, :phone, 'customer', 1, NOW())
    ");
    $stmt->execute([
        ':name'     => htmlspecialchars($name,  ENT_QUOTES, 'UTF-8'),
        ':email'    => $email,
        ':password' => $hash,
        ':phone'    => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'),
    ]);

    return ['success' => true, 'user_id' => $pdo->lastInsertId()];
}

function changePassword(PDO $pdo, int $user_id, string $current, string $new): array {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password'])) {
        return ['success' => false, 'message' => 'Contraseña actual incorrecta.'];
    }
    if (strlen($new) < 6) {
        return ['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres.'];
    }

    $pdo->prepare("UPDATE users SET password = :pwd, updated_at = NOW() WHERE id = :id")
        ->execute([':pwd' => password_hash($new, PASSWORD_BCRYPT), ':id' => $user_id]);

    return ['success' => true, 'message' => 'Contraseña actualizada correctamente.'];
}

function generateToken(int $user_id, string $type = 'reset'): string {
    $token = bin2hex(random_bytes(32));
    // Guardar token hasheado en BD con expiración
    return $token;
}