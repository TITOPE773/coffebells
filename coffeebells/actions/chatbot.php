<?php
// ============================================================
// CHATBOT.PHP — Guarda leads capturados desde el chatbot
// ============================================================
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$name    = htmlspecialchars(trim($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8');
$phone   = htmlspecialchars(trim($_POST['phone']   ?? ''), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($_POST['message'] ?? 'Lead capturado desde chatbot'), ENT_QUOTES, 'UTF-8');

if (!$name || !$phone) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

$pdo = getDB();

// Guardar en chatbot_leads
$stmt = $pdo->prepare("
    INSERT INTO chatbot_leads (name, phone, message, created_at)
    VALUES (:name, :phone, :message, NOW())
");
$stmt->execute([':name' => $name, ':phone' => $phone, ':message' => $message]);

// También guardar en contacts para gestión unificada
$stmt2 = $pdo->prepare("
    INSERT INTO contacts (name, phone, subject, message, source, status, created_at)
    VALUES (:name, :phone, 'Lead Chatbot', :message, 'chatbot', 'new', NOW())
");
$stmt2->execute([':name' => $name, ':phone' => $phone, ':message' => $message]);

echo json_encode([
    'success'   => true,
    'message'   => 'Lead guardado correctamente.',
    'whatsapp'  => "https://wa.me/524641234567?text=Hola%2C+soy+{$name}+y+me+interesa+saber+m%C3%A1s"
]);