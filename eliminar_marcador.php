<?php
require 'init.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    exit("No autorizado");
}

$id = $_GET['id'];
$usuarioId = $_SESSION['usuario_id'];


$stmt = $pdo->prepare("SELECT usuario_id FROM marcadores WHERE id = ?");
$stmt->execute([$id]);
$marcador = $stmt->fetch();

if (!$marcador) {
    exit("Marcador no encontrado");
}

if ($_SESSION['rol'] !== 'admin' && $marcador['usuario_id'] != $usuarioId) {
    exit("No tienes permiso");
}

$stmt = $pdo->prepare("DELETE FROM marcadores WHERE id = ?");
$stmt->execute([$id]);

echo "Marcador eliminado";
