<?php
require 'init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marcador_id = $_POST['marcador_id'] ?? null;
    $usuario_id = $_POST['usuario_id'] ?? null;

    if ($marcador_id && $usuario_id) {
        $stmt = $pdo->prepare("INSERT INTO compartidos (marcador_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$marcador_id, $usuario_id]);
        echo "Marcador compartido correctamente.";
    } else {
        echo "Faltan datos.";
    }
}
?>
