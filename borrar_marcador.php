<?php
require 'init.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo "No autorizado";
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo "ID faltante";
    exit;
}


$stmt = $pdo->prepare("SELECT usuario_id FROM marcadores WHERE id = ?");
$stmt->execute([$id]);
$marcador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$marcador) {
    http_response_code(404);
    echo "Marcador no encontrado";
    exit;
}


if ($_SESSION['rol'] === 'admin' || $_SESSION['usuario_id'] == $marcador['usuario_id']) {
    $stmt = $pdo->prepare("DELETE FROM marcadores WHERE id = ?");
    $stmt->execute([$id]);
    echo "ok";
} else {
    http_response_code(403);
    echo "No autorizado para borrar este marcador";
}
