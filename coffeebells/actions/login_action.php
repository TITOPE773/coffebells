<?php
// ============================================================
// LOGIN_ACTION.PHP — Procesa login de administrador
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../login.php');
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (!$email || !$password) {
    setFlash('error', 'Correo y contraseña son requeridos.');
    redirect('../login.php');
}

if (adminLogin($email, $password)) {
    // Regenerar ID de sesión para prevenir session fixation
    session_regenerate_id(true);
    redirect('../dashboard/index.php');
} else {
    setFlash('error', 'Credenciales incorrectas. Intenta de nuevo.');
    redirect('../login.php');
}