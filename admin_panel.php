<?php
require 'init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Obtener grupos
$grupos = $pdo->query("SELECT * FROM grupos ORDER BY id DESC")->fetchAll();

// Insertar nueva localización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $stmt = $pdo->prepare("INSERT INTO localizaciones (titulo, descripcion, x, y, grupo_id, tipo, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['titulo'],
        $_POST['descripcion'],
        $_POST['x'],
        $_POST['y'],
        $_POST['grupo_id'],
        $_POST['tipo'],
        $_SESSION['usuario_id']
    ]);
    header("Location: admin_panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Agregar Localizaciones</title>
    <link rel="stylesheet" href="leaflet/leaflet.css">
    <style>
        #map { height: 500px; margin-bottom: 1rem; }
        form { max-width: 500px; }
        input, select, textarea { width: 100%; margin-bottom: 10px; }
    </style>
</head>
<body>

<h2>Panel de administración</h2>

<form method="POST">
    <label>Título:</label>
    <input type="text" name="titulo" required>

    <label>Descripción:</label>
    <textarea name="descripcion" required></textarea>

    <label>Tipo:</label>
    <select name="tipo" required>
        <option value="animal">Animal legendario</option>
        <option value="tesoro">Tesoro</option>
        <option value="campamento">Campamento</option>
        <option value="planta">Planta medicinal</option>
        <option value="npc">NPC o misión</option>
    </select>

    <label>Grupo:</label>
    <select name="grupo_id" required>
        <?php foreach ($grupos as $g): ?>
            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
        <?php endforeach; ?>
    </select>

    <input type="hidden" name="x" id="coord_x">
    <input type="hidden" name="y" id="coord_y">

    <p><strong>Haz clic en el mapa para colocar el marcador</strong></p>

    <div id="map"></div>

    <button type="submit">Guardar localización</button>
</form>

<script src="leaflet/leaflet.js"></script>
<script>
    const map = L.map('map', {
        crs: L.CRS.Simple,
        minZoom: -2
    });

    const bounds = [[0,0], [6000,7680]];
    const image = L.imageOverlay('assets/mapa_redm.jpg', bounds).addTo(map);
    map.fitBounds(bounds);

    let marcador;
    map.on('click', function (e) {
        if (marcador) {
            map.removeLayer(marcador);
        }
        marcador = L.marker(e.latlng).addTo(map);
        document.getElementById('coord_x').value = e.latlng.lng;
        document.getElementById('coord_y').value = e.latlng.lat;
    });
</script>

<a href="index.php">← Volver al mapa</a>
</body>
</html>
