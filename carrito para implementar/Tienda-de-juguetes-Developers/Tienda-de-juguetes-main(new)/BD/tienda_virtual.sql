-- ============================================================
--  Tienda Virtual — Script completo de base de datos
--  Ejecutar en PhpMyAdmin: selecciona la BD o crea una nueva
--  y pega este script en la pestaña SQL.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Eliminar y recrear la base de datos
-- ------------------------------------------------------------
DROP DATABASE IF EXISTS `tienda_virtual`;
CREATE DATABASE `tienda_virtual`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `tienda_virtual`;

-- ============================================================
--  TABLAS BASE (sin dependencias externas)
-- ============================================================

-- Roles de usuario
CREATE TABLE `rol` (
  `id_rol`     int(11)     NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rol` (`id_rol`, `nombre_rol`) VALUES
  (1, 'cliente'),
  (2, 'editor'),
  (3, 'administrador');

-- Categorías de producto
CREATE TABLE `categoria` (
  `id_categoria`     int(11)      NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(255) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Disponibilidad de producto
CREATE TABLE `disponibilidad` (
  `id_disponible` int(11)     NOT NULL AUTO_INCREMENT,
  `tipo_disp`     varchar(55) NOT NULL,
  PRIMARY KEY (`id_disponible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `disponibilidad` (`id_disponible`, `tipo_disp`) VALUES
  (1, 'Disponible'),
  (2, 'Agotado'),
  (3, 'Próximamente');

-- Estados de pedido / pago
CREATE TABLE `estado` (
  `id_estado`  int(11)     NOT NULL AUTO_INCREMENT,
  `tipo_estado` varchar(55) NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `estado` (`id_estado`, `tipo_estado`) VALUES
  (1, 'Pendiente'),
  (2, 'En proceso'),
  (3, 'Completado'),
  (4, 'Cancelado');

-- Métodos de pago
CREATE TABLE `metodo_pago` (
  `id_metodopago` int(11)     NOT NULL AUTO_INCREMENT,
  `tipo_metodo`   varchar(55) DEFAULT NULL,
  PRIMARY KEY (`id_metodopago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `metodo_pago` (`id_metodopago`, `tipo_metodo`) VALUES
  (1, 'Tarjeta de crédito'),
  (2, 'Tarjeta de débito'),
  (3, 'Transferencia bancaria'),
  (4, 'Efectivo');

-- ============================================================
--  TABLAS DE PRIMER NIVEL (dependen de tablas base)
-- ============================================================

-- Usuarios
CREATE TABLE `usuarios` (
  `id_usuario`     int(11)      NOT NULL AUTO_INCREMENT,
  `nombre`         varchar(55)  NOT NULL,
  `apellido`       varchar(55)  NOT NULL,
  `correo`         varchar(255) NOT NULL,
  `contrasena`     varchar(255) NOT NULL,
  `telefono`       varchar(70)  NOT NULL DEFAULT '',
  `fecha_registro` date         NOT NULL,
  `id_rol`         int(11)      NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Productos
CREATE TABLE `productos` (
  `id_productos`    int(11)       NOT NULL AUTO_INCREMENT,
  `nombre_producto` varchar(55)   NOT NULL,
  `descripcion`     varchar(255)  NOT NULL,
  `precio`          decimal(10,2) NOT NULL,
  `stock`           int(11)       NOT NULL DEFAULT 0,
  `imagen`          varchar(255)  NOT NULL DEFAULT '',
  `id_categoria`    int(11)       DEFAULT NULL,
  `id_disponible`   int(11)       NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_productos`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_disponible` (`id_disponible`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`)  REFERENCES `categoria`    (`id_categoria`),
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_disponible`) REFERENCES `disponibilidad` (`id_disponible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Direcciones de envío
CREATE TABLE `direcciones` (
  `id_direcciones` int(11)      NOT NULL AUTO_INCREMENT,
  `calle`          varchar(255) NOT NULL,
  `ciudad`         varchar(60)  NOT NULL,
  `codigo_postal`  varchar(55)  DEFAULT NULL,
  `id_usuario`     int(11)      NOT NULL,
  PRIMARY KEY (`id_direcciones`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Carrito de compras
CREATE TABLE `carrito` (
  `id_carrito`    int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario`    int(11) NOT NULL,
  `fecha_entrada` date    NOT NULL,
  PRIMARY KEY (`id_carrito`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Inventario
CREATE TABLE `inventario` (
  `id_inventario`  int(11) NOT NULL AUTO_INCREMENT,
  `cantidad_actual` int(11) NOT NULL DEFAULT 0,
  `id_producto`    int(11) NOT NULL,
  `id_estado`      int(11) NOT NULL,
  PRIMARY KEY (`id_inventario`),
  KEY `id_producto` (`id_producto`),
  KEY `id_estado`   (`id_estado`),
  CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`),
  CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`id_estado`)   REFERENCES `estado`    (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Pedidos
CREATE TABLE `pedidos` (
  `id_pedidos`    int(11)       NOT NULL AUTO_INCREMENT,
  `id_usuario`    int(11)       NOT NULL,
  `fecha`         date          NOT NULL,
  `total`         decimal(10,2) NOT NULL,
  `id_metodopago` int(11)       NOT NULL,
  `id_estado`     int(11)       NOT NULL,
  PRIMARY KEY (`id_pedidos`),
  KEY `id_usuario`    (`id_usuario`),
  KEY `id_metodopago` (`id_metodopago`),
  KEY `id_estado`     (`id_estado`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`)    REFERENCES `usuarios`    (`id_usuario`),
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_metodopago`) REFERENCES `metodo_pago` (`id_metodopago`),
  CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`id_estado`)     REFERENCES `estado`      (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
--  TABLAS DE SEGUNDO NIVEL (dependen de tablas de primer nivel)
-- ============================================================

-- Detalle del carrito
CREATE TABLE `detalle_carrito` (
  `id_detalle_carrito` int(11) NOT NULL AUTO_INCREMENT,
  `cantidad_carrito`   int(11) NOT NULL DEFAULT 1,
  `id_productos`       int(11) NOT NULL,
  `id_carrito`         int(11) NOT NULL,
  PRIMARY KEY (`id_detalle_carrito`),
  KEY `id_productos` (`id_productos`),
  KEY `id_carrito`   (`id_carrito`),
  CONSTRAINT `detalle_carrito_ibfk_1` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_productos`),
  CONSTRAINT `detalle_carrito_ibfk_2` FOREIGN KEY (`id_carrito`)   REFERENCES `carrito`   (`id_carrito`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Detalle de pedidos
CREATE TABLE `detalle_pedidos` (
  `id_detallepedido` int(11)       NOT NULL AUTO_INCREMENT,
  `cantidad`         int(11)       NOT NULL,
  `precio_unitario`  decimal(10,2) NOT NULL,
  `subtotal`         decimal(10,2) NOT NULL,
  `id_producto`      int(11)       NOT NULL,
  `id_pedidos`       int(11)       NOT NULL,
  PRIMARY KEY (`id_detallepedido`),
  KEY `id_producto` (`id_producto`),
  KEY `id_pedidos`  (`id_pedidos`),
  CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`),
  CONSTRAINT `detalle_pedidos_ibfk_2` FOREIGN KEY (`id_pedidos`)  REFERENCES `pedidos`   (`id_pedidos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Pagos
CREATE TABLE `pagos` (
  `id_pago`       int(11)       NOT NULL AUTO_INCREMENT,
  `monto`         decimal(10,2) NOT NULL,
  `id_pedidos`    int(11)       NOT NULL,
  `id_metodopago` int(11)       NOT NULL,
  `id_estado`     int(11)       NOT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id_pedidos`    (`id_pedidos`),
  KEY `id_metodopago` (`id_metodopago`),
  KEY `id_estado`     (`id_estado`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_pedidos`)    REFERENCES `pedidos`     (`id_pedidos`),
  CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_metodopago`) REFERENCES `metodo_pago` (`id_metodopago`),
  CONSTRAINT `pagos_ibfk_3` FOREIGN KEY (`id_estado`)     REFERENCES `estado`      (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
