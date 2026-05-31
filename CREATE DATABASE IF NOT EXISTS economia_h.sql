CREATE DATABASE IF NOT EXISTS economia_hogar;
USE economia_hogar;

CREATE TABLE usuarios (
    usuario_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE bancos (
    banco_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    debito TINYINT(1) DEFAULT 0,
    credito TINYINT(1) DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE
);

CREATE TABLE categorias (
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('ingreso', 'gasto') NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE
);

CREATE TABLE ingresos (
    ingreso_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    banco_id INT DEFAULT NULL,
    tipo_pago ENUM('debito', 'credito') NOT NULL DEFAULT 'debito',
    tarjeta_id INT DEFAULT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE,
    FOREIGN KEY (banco_id) REFERENCES bancos(banco_id) ON DELETE CASCADE,
    FOREIGN KEY (tarjeta_id) REFERENCES tarjetas_credito(tarjeta_id) ON DELETE SET NULL
);

CREATE TABLE saldos_actuales (
    saldo_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    banco_id INT NOT NULL,
    saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (banco_id) REFERENCES bancos(banco_id) ON DELETE CASCADE
);

CREATE TABLE tarjetas_credito (
    tarjeta_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    limite DECIMAL(10,2) NOT NULL,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE
);

CREATE TABLE transacciones (
    transaccion_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    banco_id INT,
    tarjeta_id INT,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    descripcion TEXT,
    tipo_pago ENUM('debito', 'credito') NOT NULL,
    cuotas INT DEFAULT NULL,
    cuota_actual INT DEFAULT NULL,
    fecha_cierre DATE DEFAULT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE,
    FOREIGN KEY (banco_id) REFERENCES bancos(banco_id) ON DELETE SET NULL,
    FOREIGN KEY (tarjeta_id) REFERENCES tarjetas_credito(tarjeta_id) ON DELETE SET NULL
);

CREATE TABLE monedas_crypto_dolares (
    moneda_id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE transacciones_dolares (
    transaccion_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    banco_id INT NOT NULL,
    tarjeta_id INT,
    moneda_id INT,
    monto DECIMAL(15,2) NOT NULL,
    fecha DATE NOT NULL,
    descripcion TEXT,
    tipo_pago ENUM('debito', 'credito') NOT NULL,
    cuotas INT DEFAULT NULL,
    cuota_actual INT DEFAULT NULL,
    fecha_cierre DATE DEFAULT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE,
    FOREIGN KEY (banco_id) REFERENCES bancos(banco_id) ON DELETE CASCADE,
    FOREIGN KEY (tarjeta_id) REFERENCES tarjetas_credito(tarjeta_id) ON DELETE SET NULL,
    FOREIGN KEY (moneda_id) REFERENCES monedas_crypto_dolares(moneda_id) ON DELETE SET NULL
);

CREATE TABLE ingresos_crypto_dolares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    banco_id INT NOT NULL,
    moneda_id INT NOT NULL,
    monto DECIMAL(15,2) NOT NULL,
    fecha DATE NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE,
    FOREIGN KEY (banco_id) REFERENCES bancos(banco_id) ON DELETE CASCADE,
    FOREIGN KEY (moneda_id) REFERENCES monedas_crypto_dolares(moneda_id) ON DELETE CASCADE
);

CREATE TABLE saldos_crypto_dolares (
    saldocd_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    banco_id INT NOT NULL,
    moneda_id INT NOT NULL,
    saldo_cd DECIMAL(15,9) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (banco_id) REFERENCES bancos(banco_id) ON DELETE CASCADE,
    FOREIGN KEY (moneda_id) REFERENCES monedas_crypto_dolares(moneda_id) ON DELETE CASCADE
);