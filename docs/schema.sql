
-- TaskShield - Estructura de la base de datos
-- Base de datos: soporte_db

CREATE DATABASE IF NOT EXISTS soporte_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE soporte_db;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- 1. Crear la tabla de técnicos
CREATE TABLE IF NOT EXISTS tecnicos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    tareas_activas INT(11) DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 2. Crear la tabla de incidencias
CREATE TABLE IF NOT EXISTS incidencias (
    id INT(11) NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad VARCHAR(50) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    email_cliente VARCHAR(100) NOT NULL,
    id_tecnico INT(11) DEFAULT NULL,
    fecha_creacion DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_id_tecnico (id_tecnico),
    CONSTRAINT fk_incidencias_tecnicos
        FOREIGN KEY (id_tecnico)
        REFERENCES tecnicos(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 3. Insertar técnicos de prueba si todavía no existen
INSERT INTO tecnicos (nombre, email, tareas_activas)
SELECT 'Pepe', 'pepe@example.com', 0
WHERE NOT EXISTS (
    SELECT 1 FROM tecnicos WHERE nombre = 'Pepe'
);

INSERT INTO tecnicos (nombre, email, tareas_activas)
SELECT 'Christian', 'christian@example.com', 0
WHERE NOT EXISTS (
    SELECT 1 FROM tecnicos WHERE nombre = 'Christian'
);


-- 4. Insertar incidencias de prueba si todavía no existen
INSERT INTO incidencias
    (titulo, descripcion, prioridad, estado, email_cliente, fecha_creacion)
SELECT
    'Error al iniciar sesión',
    'El cliente no puede acceder a su cuenta.',
    'Alta',
    'Pendiente',
    'cliente1@example.com',
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM incidencias
    WHERE titulo = 'Error al iniciar sesión'
    AND email_cliente = 'cliente1@example.com'
);


INSERT INTO incidencias
    (titulo, descripcion, prioridad, estado, email_cliente, fecha_creacion)
SELECT
    'Problema con la impresora',
    'La impresora no responde al enviar documentos.',
    'Media',
    'Pendiente',
    'cliente2@example.com',
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM incidencias
    WHERE titulo = 'Problema con la impresora'
    AND email_cliente = 'cliente2@example.com'
);


INSERT INTO incidencias
    (titulo, descripcion, prioridad, estado, email_cliente, fecha_creacion)
SELECT
    'Error en la plataforma',
    'La página web muestra un error al cargar.',
    'Baja',
    'Pendiente',
    'cliente3@example.com',
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM incidencias
    WHERE titulo = 'Error en la plataforma'
    AND email_cliente = 'cliente3@example.com'
);
