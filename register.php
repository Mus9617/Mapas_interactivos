<?php
require 'init.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$nombre || !$email || !$password) {
        echo "Todos los campos son obligatorios.";
        exit;
    }


    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "Este correo ya está registrado.";
        exit;
    }


    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'user')");
    $stmt->execute([$nombre, $email, $hashed]);


    $usuario_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO grupos (nombre, usuario_id) VALUES ('Personal', ?)");
    $stmt->execute([$usuario_id]);

    echo "ok";
    exit;
}
?>
