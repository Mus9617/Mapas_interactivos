<?php
require 'init.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['x'], $_POST['y'], $_POST['titulo'], $_POST['grupo_id'], $_POST['tipo'], $_POST['icono'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos obligatorios']);
        exit;
    }

    $x = $_POST['x'];
    $y = $_POST['y'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'] ?? '';
    $grupo_id = $_POST['grupo_id'];
    $tipo = $_POST['tipo'];
    $icono = $_POST['icono'];
    $codigo = $tipo === 'publico' ? 'publico' : 'privado';

    if ($codigo === 'publico') {
        $stmt = $pdo->prepare("INSERT INTO marcadores (x, y, titulo, descripcion, codigo, grupo, icono) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$x, $y, $titulo, $descripcion, $codigo, $grupo_id, $icono]);
    } elseif ($codigo === 'privado' && isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
        $stmt = $pdo->prepare("INSERT INTO marcadores (x, y, titulo, descripcion, codigo, grupo, icono, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$x, $y, $titulo, $descripcion, $codigo, $grupo_id, $icono, $usuario_id]);
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}
