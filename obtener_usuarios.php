<?php
require 'init.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener usuarios: ' . $e->getMessage()]);
}
exit;
