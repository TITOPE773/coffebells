<?php
// ============================================================
// REGISTER_ACTION.PHP — Registro de usuarios front
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../register.php');
}

$name     = htmlspecialchars(trim($_POST['name']     ?? ''), ENT_QUOTES, 'UTF-8');
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone    = htmlspecialchars(trim($_POST['phone']    ?? ''), ENT_QUOTES, 'UTF-8');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm']  ?? '';

// Validaciones
if (!$name || !$email || !$password) {
    setFlash('error', 'Todos los campos obligatorios deben completarse.');
    redirect('../register.php');
}
if (strlen($password) < 8) {
    setFlash('error', 'La contraseña debe tener al menos 8 caracteres.');
    redirect('../register.php');
}
if ($password !== $confirm) {
    setFlash('error', 'Las contraseñas no coinciden.');
    redirect('../register.php');
}

$pdo = getDB();

// Verificar si ya existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute([':email' => $email]);
if ($stmt->fetch()) {
    setFlash('error', 'Ya existe una cuenta con ese correo.');
    redirect('../register.php');
}

// Crear usuario con contraseña hasheada
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("
    INSERT INTO users (name, email, phone, password, active, created_at)
    VALUES (:name, :email, :phone, :password, 1, NOW())
");
$stmt->execute([
    ':name'     => $name,
    ':email'    => $email,
    ':phone'    => $phone,
    ':password' => $hash,
]);

$_SESSION['user_id']    = $pdo->lastInsertId();
$_SESSION['user_name']  = $name;
$_SESSION['user_email'] = $email;

setFlash('success', "¡Bienvenido {$name}! Tu cuenta fue creada exitosamente.");
redirect('../tienda.php');