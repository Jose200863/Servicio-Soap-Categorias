CREATE DATABASE IF NOT EXISTS ventas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ventas;

CREATE TABLE IF NOT EXISTS categoria (
    codigo INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

INSERT IGNORE INTO categoria (codigo, nombre) VALUES
    (1, 'Computadoras'),
    (2, 'Telefonos'),
    (3, 'Impresoras'),
    (4, 'Accesorios');

CREATE TABLE IF NOT EXISTS Programadores (
    Cedula VARCHAR(20) PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Contrasena VARCHAR(20) NOT NULL
);

INSERT INTO Programadores (Cedula, Nombre, Contrasena) VALUES
    ('1', 'Luis', '01'),
    ('2', 'Ana', '02'),
    ('3', 'Maria', '03');