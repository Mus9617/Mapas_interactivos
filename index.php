<?php
require 'init.php';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Mapa RedM Interactivo</title>
  <link rel="stylesheet" href="leaflet/leaflet.css" />
 <style>
  body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #f8f1e7;
    color: #2e2e2e;
  }

  #topbar {
    background: #3a2d1d;
    color: #fdf6e3;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 24px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
  }

  .logo-titulo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 22px;
    font-weight: bold;
  }

  .logo-titulo img {
    vertical-align: middle;
    height: 42px;
  }

  .usuario a {
    color: #fdf6e3;
    text-decoration: none;
    margin-left: 14px;
    font-weight: 500;
  }

  .usuario a:hover {
    text-decoration: underline;
  }

  #main {
    display: flex;
    height: calc(100vh - 60px);
  }

  #sidebar {
    width: 270px;
    background: #fdf6e3;
    padding: 1.2rem;
    border-right: 2px solid #d0c4b1;
    overflow-y: auto;
  }

  #sidebar h3 {
    font-size: 18px;
    margin-bottom: 0.8rem;
    color: #3a2d1d;
  }

  #sidebar select {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #bbb;
    margin-bottom: 1rem;
  }

  #form-usuario, #form-admin {
    display: none;
    margin-top: 1rem;
    font-size: 14px;
  }

  #form-usuario input, #form-usuario textarea, #form-usuario select,
  #form-admin input, #form-admin textarea, #form-admin select {
    width: 100%;
    padding: 6px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
  }

  button {
    background-color: #3a2d1d;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
  }

  button:hover {
    background-color: #5a3e2b;
  }

  #map {
    flex-grow: 1;
    background: #000;
  }

  #modal-fondo {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 999;
  }

  #modal {
    background: #fdf6e3;
    padding: 30px;
    width: 640px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.4);
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  #cerrar-modal {
    position: absolute;
    top: 10px;
    right: 16px;
    font-size: 24px;
    cursor: pointer;
    color: #333;
  }

  .modal-header {
    text-align: center;
    margin-bottom: 20px;
  }

  .modal-logo {
    height: 55px;
    margin-bottom: 10px;
  }

  #modal-contenido input {
    width: 100%;
    padding: 10px;
    margin-bottom: 14px;
    border: 1px solid #bbb;
    border-radius: 6px;
  }

  #modal-contenido button {
    padding: 12px;
    background-color: #3a2d1d;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
  }

  #modal-contenido button:hover {
    background-color: #5a3e2b;
  }

  #info-marcador {
    margin-top: 1rem;
    font-size: 14px;
    color: #333;
  }

  #compartir-container {
    margin-top: 1rem;
    font-size: 14px;
  }

  #compartir-container input,
  #compartir-container select {
    margin-bottom: 6px;
    padding: 6px;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  #compartir-container button {
    width: 100%;
    padding: 8px;
    font-size: 14px;
  }

  ::-webkit-scrollbar {
    width: 8px;
  }
  ::-webkit-scrollbar-thumb {
    background-color: #a88f6c;
    border-radius: 4px;
  }
  ::-webkit-scrollbar-track {
    background: #fdf6e3;
  }
</style>

</head>
<body>
<div id="topbar">
  <div class="logo-titulo">
    <img src="assets/logo.png" alt="Logo Redención" height="40">
    <span>Redención - Mapa Interactivo</span>
  </div>
  <div class="usuario">
    <?php if (isset($_SESSION['usuario_id'])): ?>
      Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? ($_SESSION['rol'] === 'admin' ? 'Admin' : 'Usuario')) ?></strong>
      | <a href="logout.php">Cerrar sesión</a>
      <?php if ($_SESSION['rol'] === 'admin'): ?>
        | <a href="#" onclick="mostrarFormularioAdmin()">Panel Admin</a>
      <?php endif; ?>
    <?php else: ?>
      <a href="#" onclick="abrirModal('login')">Iniciar sesión</a> |
      <a href="#" onclick="abrirModal('registro')">Registrarse</a>
    <?php endif; ?>
  </div>
</div>

<div id="main">
  <div id="sidebar">
    <h3>Categorías</h3>
<select id="grupo_usuario" onchange="filtrarMarcadores()">
  <option value="todos">Mostrar todos</option>
  <option value="privados">Privados</option>
  <option value="compartidos">Compartidos</option>

      <?php
      if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
   $stmt = $pdo->prepare("SELECT id, nombre, usuario_id FROM grupos WHERE usuario_id IS NULL OR usuario_id = ? ORDER BY nombre");
    $stmt->execute([$usuario_id]);
} else {
    $stmt = $pdo->query("SELECT id, nombre FROM grupos WHERE usuario_id IS NULL ORDER BY nombre");
}

      $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
     /// print_r($grupos); exit;
      foreach ($grupos as $grupo) {
          echo '<option value="'.htmlspecialchars($grupo['id']).'">'.htmlspecialchars($grupo['nombre']).'</option>';
      }
      ?>
    </select>

    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'admin'): ?>
      <div id="form-usuario">
        <h4>Añadir Localización Privada</h4>
        <input type="text" id="titulo" placeholder="Título">
        <textarea id="descripcion" placeholder="Descripción"></textarea>
<select id="grupo">
  <?php
    foreach ($grupos as $grupo) {
    
      if (
        $_SESSION['rol'] === 'admin' || 
        !isset($grupo['usuario_id']) || 
        (isset($_SESSION['usuario_id']) && $grupo['usuario_id'] == $_SESSION['usuario_id'])
      ) {
        echo '<option value="'.htmlspecialchars($grupo['id']).'">'.htmlspecialchars($grupo['nombre']).'</option>';
      }
    }
  ?>
</select>

        </select>
        <select id="icono_usuario">
          <option value="tesoro.png">Tesoro</option>
          <option value="bath-solid.svg">Baño</option>
          <option value="beer-mug-empty-solid.svg">Cerveza</option>
          <option value="campground-solid.svg">Campamento</option>
          <option value="gun-solid.svg">Arma</option>
          <option value="handcuffs-solid.svg">Esposas</option>
          <option value="hat-cowboy-solid.svg">Sombrero Vaquero</option>
          <option value="horse-head-solid.svg">Caballo</option>
          <option value="house-medical-solid.svg">Hospital</option>
          <option value="leaf-solid.svg">Hoja</option>
          <option value="people-robbery-solid.svg">Atraco</option>
          <option value="person-solid.svg">Persona</option>
          <option value="sailboat-solid.svg">Barco</option>
          <option value="shirt-solid.svg">Ropa</option>
          <option value="spa-solid.svg">Spa</option>
          <option value="store-solid.svg">Tienda</option>
          <option value="tent-solid.svg">Tienda de campaña</option>
          <option value="icons8-banco-30.png">Banco</option>
<option value="icons8-carro-pioneer-50.png">Carro Pionero</option>
<option value="icons8-establo-50.png">Establo</option>
<option value="icons8-tijeras-50.png">Tijeras</option>
<option value="icons8-herramientas-50.png">Herramientas</option>
<option value="icons8-casa-64.png">Casa</option>
<option value="icons8-muebles-50.png">Muebles</option>
<option value="icons8-blackjack-62.png">Blackjack</option>
<option value="icons8-theater-50.png">Teatro</option>
<option value="icons8-cruz-48.png">Marca Personalizada</option>
<option value="icons8-cruz-30.png">Iglesia</option>

        </select>
        <img id="preview_usuario" src="assets/icons/tesoro.png" alt="Preview Icono" style="width:32px;height:32px;margin-bottom:8px;">

        <button onclick="activarAgregar()">Marcar en el mapa</button>
      </div>
    <?php elseif (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
      <button onclick="mostrarFormularioAdmin()">➕ Añadir Localización Pública</button>
      <div id="form-admin">
        <input type="text" id="titulo_admin" placeholder="Título">
        <textarea id="descripcion_admin" placeholder="Descripción"></textarea>
        <select id="grupo_admin">
          <?php foreach ($grupos as $grupo): ?>
            <option value="<?= htmlspecialchars($grupo['id']) ?>"><?= htmlspecialchars($grupo['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="icono_admin">
          <option value="tesoro.png">Tesoro</option>
          <option value="bath-solid.svg">Baño</option>
          <option value="beer-mug-empty-solid.svg">Cerveza</option>
          <option value="campground-solid.svg">Campamento</option>
          <option value="gun-solid.svg">Arma</option>
          <option value="handcuffs-solid.svg">Esposas</option>
          <option value="hat-cowboy-solid.svg">Sombrero Vaquero</option>
          <option value="horse-head-solid.svg">Caballo</option>
          <option value="house-medical-solid.svg">Hospital</option>
          <option value="leaf-solid.svg">Hoja</option>
          <option value="people-robbery-solid.svg">Atraco</option>
          <option value="person-solid.svg">Persona</option>
          <option value="sailboat-solid.svg">Barco</option>
          <option value="shirt-solid.svg">Ropa</option>
          <option value="spa-solid.svg">Spa</option>
          <option value="store-solid.svg">Tienda</option>
          <option value="tent-solid.svg">Tienda de campaña</option>
          <option value="icons8-banco-30.png">Banco</option>
<option value="icons8-carro-pioneer-50.png">Carro Pionero</option>
<option value="icons8-establo-50.png">Establo</option>
<option value="icons8-tijeras-50.png">Tijeras</option>
<option value="icons8-herramientas-50.png">Herramientas</option>
<option value="icons8-casa-64.png">Casa</option>
<option value="icons8-muebles-50.png">Muebles</option>
<option value="icons8-blackjack-62.png">Blackjack</option>
<option value="icons8-theater-50.png">Teatro</option>
<option value="icons8-cruz-48.png">Marca Personalizada</option>
<option value="icons8-cruz-30.png">Iglesia</option>

        </select>
        <img id="preview_admin" src="assets/icons/tesoro.png" alt="Preview Icono" style="width:32px;height:32px;margin-bottom:8px;">
            
        <button onclick="activarAgregarAdmin()">📍 Marcar en el mapa</button>
          
      </div>


    <?php endif; ?>
    <div id="info-marcador" style="margin-top: 1rem; font-size: 14px;"></div>
<div id="compartir-container" style="display:none; margin-top: 1rem;">
  <label>Compartir con:</label>
  <input type="text" id="buscar-usuario" placeholder="Buscar usuario..." style="width: 100%; margin-bottom: 5px;">
  <select id="select-usuarios" size="6" style="width: 100%;"></select>
  <button onclick="compartirMarcador()" style="margin-top: 5px;">Compartir</button>
</div>


  </div>
  <div id="map"></div>


</div>

<div id="modal-fondo">
  <div id="modal">
    <span id="cerrar-modal" onclick="cerrarModal()">&times;</span>
    <div class="modal-header">
      <img src="assets/logo.png" alt="Logo" class="modal-logo">
      <h2 id="modal-titulo">Redención</h2>
    </div>
    <div id="modal-contenido"></div>
  </div>
</div>
  <div id="compartirModal" style="display:none; position:fixed; top:20%; left:40%; background:#fff; padding:10px; border:1px solid #ccc; z-index: 9999;">
  <h4>Compartir Marcador</h4>
  <label for="usuarioCompartir">ID del Usuario:</label>
  <input type="number" id="usuarioCompartir">
  <input type="hidden" id="marcadorCompartir">
  <br><br>
  <button onclick="confirmarCompartir()">Compartir</button>
  <button onclick="document.getElementById('compartirModal').style.display='none'">Cancelar</button>
</div>



<script src="leaflet/leaflet.js"></script>
<script>
const mapWidth = 7680, mapHeight = 6000;
const map = L.map('map', { crs: L.CRS.Simple, minZoom: -2, maxZoom: 2 });
const bounds = [[0,0], [mapHeight,mapWidth]];
L.imageOverlay('assets/mapa_redm.jpg', bounds).addTo(map);
map.fitBounds(bounds);

function obtenerIcono(nombreArchivo) {
  return L.icon({
    iconUrl: `assets/icons/${nombreArchivo}`,
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32]
  });
}



const iconos = {
<?php foreach ($grupos as $grupo): ?>
  <?= json_encode($grupo['nombre']) ?>: L.icon({
    iconUrl: 'assets/icons/tesoro.png',
    iconSize: [32, 32]
  }),
<?php endforeach; ?>
  default: L.icon({ iconUrl: 'assets/icons/tesoro.png', iconSize: [32, 32] })
};


let marcadoresGlobales = [];

function cargarMarcadores() {
  marcadoresGlobales = [];


  fetch('obtener_marcadores.php?codigo=publico')
    .then(res => res.json())
    .then(data => {
      if (Array.isArray(data)) {
        marcadoresGlobales = marcadoresGlobales.concat(data);
        mostrarMarcadores(marcadoresGlobales);
      }
    });

  <?php if (isset($_SESSION['usuario_id']) && $_SESSION['rol'] !== 'admin'): ?>

    fetch('obtener_marcadores.php?codigo=privado')
      .then(res => res.json())
      .then(data => {
        if (Array.isArray(data)) {
          marcadoresGlobales = marcadoresGlobales.concat(data);
          mostrarMarcadores(marcadoresGlobales);
        }
      });

 
    fetch('obtener_marcadores.php?codigo=compartido')
      .then(res => res.json())
      .then(data => {
        if (Array.isArray(data)) {
          marcadoresGlobales = marcadoresGlobales.concat(data);
          mostrarMarcadores(marcadoresGlobales);
        }
      });
  <?php endif; ?>
}

function mostrarMarcadores(marcadores) {
  map.eachLayer(layer => {
    if (layer instanceof L.Marker) {
      map.removeLayer(layer);
    }
  });

  const filtroGrupo = document.getElementById('grupo_usuario').value;

  marcadores.forEach(m => {
    if (filtroGrupo === 'todos') {
      agregarMarcador(m);
    } else if (filtroGrupo === 'privados' && m.tipo === 'privado') {
      agregarMarcador(m);
    } else if (filtroGrupo === 'compartidos' && m.tipo === 'compartido') {
      agregarMarcador(m);
    } else if (m.grupo_id == filtroGrupo) {
      agregarMarcador(m);
    }
  });
}

function agregarMarcador(m) {
  const icono = m.icono ? obtenerIcono(m.icono) : iconos['default'];
  const marker = L.marker([m.y, m.x], { icon: icono }).addTo(map);


  marker.on('click', () => {
    marcadorSeleccionado = m.id;
    const info = `
      <strong>${m.titulo}</strong><br>
      ${m.descripcion || ''}<br>
      <em>Grupo: ${m.nombre || 'N/A'}</em><br>
      <button onclick="mostrarCompartir(${m.id})">🔗 Compartir</button>
    `;
    document.getElementById('info-marcador').innerHTML = info;
  });


  marker.on('contextmenu', () => {
    if (confirm("¿Seguro que quieres borrar este marcador?")) {
      fetch(`eliminar_marcador.php?id=${m.id}`, { method: 'GET' })
        .then(res => res.text())
        .then(data => {
          alert(data.trim());
          cargarMarcadores();
          setTimeout(filtrarMarcadores, 300);
        })
        .catch(err => alert("Error al eliminar: " + err.message));
    }
  });
}


function filtrarMarcadores() {
  const filtro = document.getElementById('grupo_usuario').value;

  if (filtro === 'compartidos') {
    fetch('obtener_marcadores.php?codigo=compartido')
      .then(res => res.json())
      .then(data => mostrarMarcadores(data))
      .catch(err => console.error("Error cargando compartidos:", err));
  } else if (filtro === 'privados') {
    fetch('obtener_marcadores.php?codigo=privado')
      .then(res => res.json())
      .then(data => mostrarMarcadores(data))
      .catch(err => console.error("Error cargando privados:", err));
  } else {
   
    cargarMarcadores();
  }
}

let modoAgregar = false;
function activarAgregar() {
  modoAgregar = true;
  alert("Haz clic en el mapa para colocar la localización privada");
}

let modoAdmin = false;
function mostrarFormularioAdmin() {
  const form = document.getElementById('form-admin');
  form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function activarAgregarAdmin() {
  modoAdmin = true;
  alert("Haz clic en el mapa para colocar la localización pública");
}

map.on('click', function(e) {
  const x = e.latlng.lng;
  const y = e.latlng.lat;

  if (modoAgregar) {
    const titulo = document.getElementById("titulo").value;
    const descripcion = document.getElementById("descripcion").value;
    const grupo = document.getElementById("grupo").value;
    const icono = document.getElementById("icono_usuario").value;

    if (!titulo || !grupo || !icono) {
      alert("Completa todos los campos");
      return;
    }

    fetch('guardar_marcador.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `x=${x}&y=${y}&titulo=${encodeURIComponent(titulo)}&descripcion=${encodeURIComponent(descripcion)}&grupo_id=${grupo}&tipo=privado&icono=${encodeURIComponent(icono)}`
    }).then(() => {
      modoAgregar = false;
      cargarMarcadores();
      setTimeout(filtrarMarcadores, 500);
    });
  }

  if (modoAdmin) {
    const titulo = document.getElementById("titulo_admin").value;
    const descripcion = document.getElementById("descripcion_admin").value;
    const grupo = document.getElementById("grupo_admin").value;
    const icono = document.getElementById("icono_admin").value;

    if (!titulo || !grupo || !icono) {
      alert("Completa todos los campos");
      return;
    }

    fetch('guardar_marcador.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `x=${x}&y=${y}&titulo=${encodeURIComponent(titulo)}&descripcion=${encodeURIComponent(descripcion)}&grupo_id=${grupo}&tipo=publico&icono=${encodeURIComponent(icono)}`
    }).then(() => {
      modoAdmin = false;
      cargarMarcadores();
      setTimeout(filtrarMarcadores, 500);
    });
  }
});


function abrirModal(tipo) {
  const cont = document.getElementById("modal-contenido");
  document.getElementById("modal-fondo").style.display = "flex";
  document.getElementById("modal-titulo").innerText = tipo === 'login' ? 'Iniciar sesión' : 'Registro';

  if (tipo === 'login') {
    cont.innerHTML = `
      <input type="email" id="email" placeholder="Correo">
      <input type="password" id="password" placeholder="Contraseña">
      <button onclick="enviarLogin()">Entrar</button>
    `;
  } else {
    cont.innerHTML = `
      <input type="text" id="nombre" placeholder="Nombre">
      <input type="email" id="email" placeholder="Correo">
      <input type="password" id="password" placeholder="Contraseña">
      <button onclick="enviarRegistro()">Crear cuenta</button>
    `;
  }
}

function cerrarModal() {
  document.getElementById("modal-fondo").style.display = "none";
}

function enviarLogin() {
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;

  fetch('login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `email=${email}&password=${password}`
  }).then(res => res.text())
    .then(data => {
      if (data.trim() === 'ok') {
        location.reload();
      } else {
        alert("Credenciales incorrectas");
      }
    });
}



document.getElementById('icono_usuario')?.addEventListener('change', function() {
  const valor = this.value;
  document.getElementById('preview_usuario').src = `assets/icons/${valor}`;
});


document.getElementById('icono_admin')?.addEventListener('change', function() {
  const valor = this.value;
  document.getElementById('preview_admin').src = `assets/icons/${valor}`;
});

<?php if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'admin'): ?>
  document.getElementById("form-usuario").style.display = "block";
<?php endif; ?>



function enviarRegistro() {
  const nombre = document.getElementById("nombre").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;

  fetch('register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `nombre=${nombre}&email=${email}&password=${password}`
  }).then(res => res.text())
    .then(data => {
      if (data.trim() === 'ok') {
        alert("Registrado. Ahora inicia sesión.");
        cerrarModal();
      } else {
        alert("Error: " + data);
      }
    });
}
function mostrarCompartir(marcadorId) {
  marcadorSeleccionado = marcadorId;
  document.getElementById('compartir-container').style.display = 'block';
  cargarUsuarios();
}

function confirmarCompartir() {
  const marcadorId = document.getElementById('marcadorCompartir').value;
  const usuarioId = document.getElementById('usuarioCompartir').value;

  fetch('compartir.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `marcador_id=${marcadorId}&usuario_id=${usuarioId}`
  })
  .then(res => res.text())
  .then(msg => {
    alert(msg);
    document.getElementById('compartirModal').style.display = 'none';
  })
  .catch(err => alert("Error al compartir: " + err.message));
}
function cargarUsuarios() {
  fetch('obtener_usuarios.php')
    .then(res => res.json())
    .then(usuarios => {
      const select = document.getElementById('select-usuarios');
      select.innerHTML = '<option value="">Selecciona usuario</option>';
      usuarios.forEach(u => {
        const option = document.createElement('option');
        option.value = u.id;
        option.textContent = u.nombre;
        select.appendChild(option);
      });
    });
}


document.getElementById('buscar-usuario')?.addEventListener('input', function() {
  const filtro = this.value.toLowerCase();
  const select = document.getElementById('select-usuarios');
  Array.from(select.options).forEach(option => {
    const visible = option.textContent.toLowerCase().includes(filtro);
    option.style.display = visible ? '' : 'none';
  });
});



let marcadorSeleccionado = null;

function cargarUsuarios() {
  fetch('obtener_usuarios.php')
    .then(res => res.json())
    .then(usuarios => {
      const select = document.getElementById('select-usuarios');
      select.innerHTML = '<option value="">Selecciona usuario</option>';
      usuarios.forEach(u => {
        const option = document.createElement('option');
        option.value = u.id;
        option.textContent = u.nombre;
        select.appendChild(option);
      });
    });
}

function compartirMarcador() {
  const usuario_id = document.getElementById('select-usuarios').value;
  if (!usuario_id || !marcadorSeleccionado) return alert('Selecciona un usuario');

  fetch('compartir_marcador.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `marcador_id=${marcadorSeleccionado}&usuario_id=${usuario_id}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Marcador compartido correctamente');
      } else {
        alert('Error al compartir: ' + (data.error || 'desconocido'));
      }
    })
    .catch(err => {
      console.error("Fallo en la petición:", err);
      alert("Error al compartir: " + err.message);
    });
}

cargarMarcadores();
</script>


</body>
</html>
