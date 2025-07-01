<?php
require 'init.php';
header('Content-Type: application/json');

$codigo = $_GET['codigo'] ?? 'todos';

try {
    $usuarioId = $_SESSION['usuario_id'] ?? null;

    if ($codigo === 'publico') {
        $stmt = $pdo->query("
            SELECT m.id, m.x, m.y, m.titulo, m.descripcion, m.grupo AS grupo_id, m.icono, g.nombre
            FROM marcadores m
            JOIN grupos g ON m.grupo = g.id
            WHERE m.codigo = 'publico'
        ");
        $publicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $publicos = array_map(fn($m) => $m + ['tipo' => 'publico'], $publicos);
        echo json_encode($publicos);
        exit;
    }

    if ($codigo === 'privado' && $usuarioId) {
        $stmt = $pdo->prepare("
            SELECT m.id, m.x, m.y, m.titulo, m.descripcion, m.grupo AS grupo_id, m.icono, g.nombre
            FROM marcadores m
            JOIN grupos g ON m.grupo = g.id
            WHERE m.codigo = 'privado' AND m.usuario_id = ?
        ");
        $stmt->execute([$usuarioId]);
        $privados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $privados = array_map(fn($m) => $m + ['tipo' => 'privado'], $privados);
        echo json_encode($privados);
        exit;
    }

    if ($codigo === 'compartido' && $usuarioId) {
        $stmt = $pdo->prepare("
            SELECT m.id, m.x, m.y, m.titulo, m.descripcion, m.grupo AS grupo_id, m.icono, g.nombre
            FROM marcadores m
            JOIN compartidos c ON m.id = c.marcador_id
            JOIN grupos g ON m.grupo = g.id
            WHERE c.usuario_id = ?
        ");
        $stmt->execute([$usuarioId]);
        $compartidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $compartidos = array_map(fn($m) => $m + ['tipo' => 'compartido'], $compartidos);
        echo json_encode($compartidos);
        exit;
    }

    if ($codigo === 'todos' && $usuarioId) {
        
        $stmtPublicos = $pdo->query("
            SELECT m.id, m.x, m.y, m.titulo, m.descripcion, m.grupo AS grupo_id, m.icono, g.nombre
            FROM marcadores m
            JOIN grupos g ON m.grupo = g.id
            WHERE m.codigo = 'publico'
        ");
        $publicos = $stmtPublicos->fetchAll(PDO::FETCH_ASSOC);
        $publicos = array_map(fn($m) => $m + ['tipo' => 'publico'], $publicos);

      
        $stmtPrivados = $pdo->prepare("
            SELECT m.id, m.x, m.y, m.titulo, m.descripcion, m.grupo AS grupo_id, m.icono, g.nombre
            FROM marcadores m
            JOIN grupos g ON m.grupo = g.id
            WHERE m.codigo = 'privado' AND m.usuario_id = ?
        ");
        $stmtPrivados->execute([$usuarioId]);
        $privados = $stmtPrivados->fetchAll(PDO::FETCH_ASSOC);
        $privados = array_map(fn($m) => $m + ['tipo' => 'privado'], $privados);

        echo json_encode(array_merge($publicos, $privados));
        exit;
    }

   
    if (!$usuarioId) {
        $stmt = $pdo->query("
            SELECT m.id, m.x, m.y, m.titulo, m.descripcion, m.grupo AS grupo_id, m.icono, g.nombre
            FROM marcadores m
            JOIN grupos g ON m.grupo = g.id
            WHERE m.codigo = 'publico'
        ");
        $publicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $publicos = array_map(fn($m) => $m + ['tipo' => 'publico'], $publicos);
        echo json_encode($publicos);
        exit;
    }

    echo json_encode([]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener los marcadores: ' . $e->getMessage()]);
}
