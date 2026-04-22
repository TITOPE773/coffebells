<?php
// ============================================================
// SAVE_CONTACT.PHP — Guarda contactos, newsletter y leads
// ============================================================
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$action = trim($_POST['action'] ?? '');
$pdo    = getDB();

// ── NEWSLETTER ────────────────────────────────────────────
if ($action === 'newsletter') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Correo no válido.']);
        exit;
    }
    // Verificar si ya existe
    $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => true, 'message' => '¡Ya estás suscrito! Gracias 😊']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, created_at) VALUES (:email, NOW())");
    $stmt->execute([':email' => $email]);
    echo json_encode(['success' => true, 'message' => '¡Suscrito con éxito! Bienvenido a CoffeeBells 🎉']);
    exit;
}

// ── LEAD DE POPUP PROMOCIONAL ─────────────────────────────
if ($action === 'promo_lead') {
    $name  = htmlspecialchars(trim($_POST['name']  ?? ''), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    if (!$name || !$email) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
        exit;
    }
    // Guardar en newsletter + contacts
    $stmt = $pdo->prepare("INSERT IGNORE INTO newsletter_subscribers (email, name, source, created_at) VALUES (:email, :name, 'popup_promo', NOW())");
    $stmt->execute([':email' => $email, ':name' => $name]);

    $stmt2 = $pdo->prepare("INSERT INTO contacts (name, email, subject, message, source, created_at) VALUES (:name, :email, 'Lead Popup Promo', 'Lead captado desde popup de promoción', 'popup', NOW())");
    $stmt2->execute([':name' => $name, ':email' => $email]);

    echo json_encode(['success' => true, 'message' => '¡Listo! Revisa tu correo.']);
    exit;
}

// ── FORMULARIO DE CONTACTO PRINCIPAL ─────────────────────
if ($action === 'contact') {
    $name     = htmlspecialchars(trim($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8');
    $phone    = htmlspecialchars(trim($_POST['phone']   ?? ''), ENT_QUOTES, 'UTF-8');
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $service  = htmlspecialchars(trim($_POST['service'] ?? ''), ENT_QUOTES, 'UTF-8');
    $message  = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
    $subject  = htmlspecialchars(trim($_POST['subject'] ?? 'Solicitud de información'), ENT_QUOTES, 'UTF-8');
    $budget   = htmlspecialchars(trim($_POST['budget']  ?? ''), ENT_QUOTES, 'UTF-8');
    $pref     = htmlspecialchars(trim($_POST['contact_pref'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Validación mínima
    if (!$name || !$phone) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Nombre y teléfono son obligatorios.']);
        } else {
            setFlash('error', 'Nombre y teléfono son obligatorios.');
            header('Location: ../contacto.php');
        }
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO contacts
            (name, phone, email, subject, service, message, budget, contact_pref, source, status, created_at)
        VALUES
            (:name, :phone, :email, :subject, :service, :message, :budget, :pref, 'web_form', 'new', NOW())
    ");
    $stmt->execute([
        ':name'    => $name,
        ':phone'   => $phone,
        ':email'   => $email ?: null,
        ':subject' => $subject,
        ':service' => $service,
        ':message' => $message,
        ':budget'  => $budget,
        ':pref'    => $pref,
    ]);

    // Respuesta según tipo de request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['success' => true, 'message' => '¡Mensaje recibido! Te contactamos pronto.']);
    } else {
        setFlash('success', '¡Gracias! Recibimos tu solicitud. Te contactaremos en menos de 2 horas.');
        header('Location: ../contacto.php');
    }
    exit;
}

// ── COTIZACIÓN RÁPIDA ─────────────────────────────────────
if ($action === 'quick_quote') {
    $name    = htmlspecialchars(trim($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8');
    $phone   = htmlspecialchars(trim($_POST['phone']   ?? ''), ENT_QUOTES, 'UTF-8');
    $service = htmlspecialchars(trim($_POST['service'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (!$name || !$phone) {
        echo json_encode(['success' => false, 'message' => 'Nombre y teléfono requeridos.']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO contacts (name, phone, service, subject, source, status, created_at)
        VALUES (:name, :phone, :service, 'Cotización rápida', 'quick_form', 'new', NOW())
    ");
    $stmt->execute([':name' => $name, ':phone' => $phone, ':service' => $service]);

    echo json_encode([
        'success' => true,
        'message' => '¡Recibido! Un asesor te llama pronto.',
        'whatsapp' => "https://wa.me/524641234567?text=Hola%2C+soy+{$name}+y+necesito+cotizaci%C3%B3n+de:+{$service}"
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);