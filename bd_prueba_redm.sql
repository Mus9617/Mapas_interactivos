

CREATE DATABASE IF NOT EXISTS maparedm;
USE maparedm;


CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario'
);


CREATE TABLE IF NOT EXISTS grupos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario_id INT DEFAULT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS marcadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    x FLOAT NOT NULL,
    y FLOAT NOT NULL,
    titulo VARCHAR(255),
    descripcion TEXT,
    grupo INT,
    codigo ENUM('publico', 'privado') DEFAULT 'privado',
    icono VARCHAR(100),
    usuario_id INT,
    FOREIGN KEY (grupo) REFERENCES grupos(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS compartidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marcador_id INT,
    usuario_id INT,
    UNIQUE (marcador_id, usuario_id),
    FOREIGN KEY (marcador_id) REFERENCES marcadores(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Admin', 'admin@mapa.com', 'adminhash', 'admin'),
('Usuario1', 'user1@mapa.com', 'user1hash', 'usuario'),
('Usuario2', 'user2@mapa.com', 'user2hash', 'usuario');


INSERT INTO grupos (nombre, usuario_id) VALUES
('Lugares Públicos', NULL),
('Zonas Secretas', 2);


INSERT INTO marcadores (x, y, titulo, descripcion, grupo, codigo, icono, usuario_id) VALUES
(1500, 1200, 'Tesoro Público', 'Ubicación del tesoro.', 1, 'publico', 'tesoro.png', 1),
(1700, 1600, 'Campamento Privado', 'Campamento de Usuario1.', 2, 'privado', 'campground-solid.svg', 2);


INSERT INTO compartidos (marcador_id, usuario_id) VALUES
(2, 3);
