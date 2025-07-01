# 🗺️ Mapa Interactivo RedM

Este es un sistema web que permite a jugadores del servidor de Red Dead Redemption 2 (RedM) añadir, visualizar, compartir y administrar localizaciones en un mapa interactivo personalizado.

## ✅ Características

- Visualización de marcadores **públicos**, **privados** y **compartidos**
- Inicio de sesión y registro de usuarios
- Rol de **admin** con permisos especiales
- Filtros por categorías y tipos
- Previsualización de iconos al seleccionar
- Compartir marcadores con otros usuarios
- Agregado rápido al mapa haciendo clic
- Soporte para **iconos personalizados**
- Modal de login y registro integrado

## 🛠️ Requisitos

- PHP 7.4 o superior
- Servidor web (Apache, Nginx, XAMPP, Laragon, etc.)
- Base de datos MySQL o MariaDB
- Navegador moderno (Chrome, Firefox, Edge...)

## ⚙️ Instalación

1. **Clona o descarga el proyecto** en tu servidor local o web

2. **Importa la base de datos**  
   Usa el archivo SQL provisto:
   ```
   maparedm.sql
   ```

3. **Configura la conexión a la base de datos**  
   Edita el archivo `init.php` y coloca tus datos:
   ```php
   $pdo = new PDO('mysql:host=localhost;dbname=maparedm;charset=utf8', 'usuario', 'contraseña');
   ```

4. **Abre el navegador y accede a la app**
   ```
   http://localhost/tu-carpeta/index.php
   ```

## 👥 Roles de Usuario

- `admin`: puede añadir marcadores públicos, ver todo y acceder al panel de administración.
- `usuario`: puede añadir marcadores privados, ver públicos y acceder a los compartidos que le otorguen.

## ➕ Añadir nuevos iconos personalizados

### 1. Copia los archivos PNG a la carpeta:
```
assets/icons/
```
Ejemplo:  
`assets/icons/icons8-casa-64.png`

### 2. Registra los iconos en el HTML (`index.php`)

Busca las secciones `<select id="icono_usuario">` y `<select id="icono_admin">`, y añade las nuevas opciones:

```html
<option value="icons8-casa-64.png">Casa</option>
<option value="icons8-teatro-50.png">Teatro</option>
<option value="icons8-herramientas-50.png">Herramientas</option>
```

### 3. Verifica la previsualización del icono

El sistema debe actualizar automáticamente la imagen en el formulario cuando selecciones un nuevo icono. Si no ocurre, asegúrate de tener este código:

```javascript
document.getElementById('icono_usuario')?.addEventListener('change', function() {
  document.getElementById('preview_usuario').src = `assets/icons/${this.value}`;
});
```

## 🧪 Funcionalidades adicionales

- Botón de **compartir marcadores** con usuarios
- **Filtros dinámicos** (mostrar todos, privados, compartidos, por grupo)
- Guardado en tiempo real
- Modal flotante para login / registro
- Soporte para multiusuarios
- Preparado para borrado con clic derecho

## 🔐 Seguridad

- Conexión a base de datos con **PDO y parámetros preparados**
- Escape de contenido con `htmlspecialchars` para evitar XSS
- Control de acceso con `$_SESSION['usuario_id']`
- Código modularizable y seguro

## 🧑‍💻 Autor

Desarrollado por: Zowix  
Repositorio diseñado para facilitar la experiencia de juego mediante un sistema visual y funcional.
