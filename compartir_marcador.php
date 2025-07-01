<?php
require 'init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$marcador_id = $_POST['marcador_id'] ?? null;
$usuario_id = $_POST['usuario_id'] ?? null;

if (!$marcador_id || !$usuario_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO compartidos (marcador_id, usuario_id) VALUES (?, ?)");
    $stmt->execute([$marcador_id, $usuario_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al compartir: ' . $e->getMessage()]);
}
exit;
