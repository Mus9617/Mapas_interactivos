<?php
$codigo = $_GET['codigo'] ?? null;
if (!$codigo) {
  die("Código no proporcionado.");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mapa Privado RedM</title>
  <link rel="stylesheet" href="leaflet/leaflet.css" />
  <style>
    #map { width: 100%; height: 100vh; }
  </style>
</head>
<body>
  <div id="map"></div>

  <script src="leaflet/leaflet.js"></script>
  <script>
    const codigo = "<?= htmlspecialchars($codigo) ?>";
    const map = L.map('map', {
      crs: L.CRS.Simple,
      minZoom: -2
    });

    const bounds = [[0,0], [1000,1000]];
    const image = L.imageOverlay('assets/mapa_redm.jpg', bounds).addTo(map);
    map.fitBounds(bounds);

    fetch(`obtener_marcadores.php?codigo=${codigo}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(m => {
          L.marker([m.y, m.x]).addTo(map).bindPopup(m.titulo);
        });
      });

    map.on('click', e => {
      const titulo = prompt("Título del marcador:");
      if (!titulo) return;
      fetch('guardar_marcador.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `x=${e.latlng.lng}&y=${e.latlng.lat}&titulo=${titulo}&codigo=${codigo}`
      }).then(() => location.reload());
    });
  </script>
</body>
</html>
