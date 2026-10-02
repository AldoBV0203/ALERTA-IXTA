CREATE DATABASE alerta_ixta
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE alerta_ixta;

ALTER TABLE users
ADD telefono VARCHAR(20) NULL,
ADD estatus VARCHAR(20) DEFAULT 'ACTIVO';

CREATE TABLE tipos_alerta (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE operadores (
    id_operador INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(30) NOT NULL,
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE alertas (
    id_alerta INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(50) NOT NULL UNIQUE,
    id_usuario BIGINT UNSIGNED NOT NULL,
    id_tipo INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    latitud DECIMAL(10,7),
    longitud DECIMAL(10,7),
    prioridad VARCHAR(20),
    estado VARCHAR(30) DEFAULT 'NUEVA',

    FOREIGN KEY (id_usuario)
        REFERENCES users(id),

    FOREIGN KEY (id_tipo)
        REFERENCES tipos_alerta(id_tipo)
);

CREATE TABLE ubicaciones_alerta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alerta INT NOT NULL,
    latitud DECIMAL(10,7) NOT NULL,
    longitud DECIMAL(10,7) NOT NULL,
    precision_m DECIMAL(10,2),
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_alerta)
        REFERENCES alertas(id_alerta)
);

CREATE TABLE seguimiento_alerta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alerta INT NOT NULL,
    id_operador INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    observaciones TEXT,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_alerta)
        REFERENCES alertas(id_alerta),

    FOREIGN KEY (id_operador)
        REFERENCES operadores(id_operador)
);

CREATE TABLE auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_sistema VARCHAR(100),
    accion VARCHAR(100),
    entidad VARCHAR(100),
    id_entidad INT,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45)
);