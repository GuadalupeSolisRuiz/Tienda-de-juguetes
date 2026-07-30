-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-07-2026 a las 12:40:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda_virtual`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_entrada` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre_categoria`) VALUES
(1, 'Niños'),
(2, 'Niñas'),
(3, 'Bebes'),
(4, 'Peluches'),
(5, 'Educativos'),
(6, 'Vehículos'),
(7, 'Electrónicos'),
(8, 'Muñecas'),
(9, 'Exterior');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_carrito`
--

CREATE TABLE `detalle_carrito` (
  `id_detalle_carrito` int(11) NOT NULL,
  `cantidad_carrito` int(11) NOT NULL,
  `id_productos` int(11) NOT NULL,
  `id_carrito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id_detallepedido` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id_direcciones` int(11) NOT NULL,
  `calle` varchar(255) NOT NULL,
  `ciudad` varchar(60) NOT NULL,
  `codigo_postal` varchar(55) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disponibilidad`
--

CREATE TABLE `disponibilidad` (
  `id_disponible` int(11) NOT NULL,
  `tipo_disp` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `disponibilidad`
--

INSERT INTO `disponibilidad` (`id_disponible`, `tipo_disp`) VALUES
(1, 'Disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id_estado` int(11) NOT NULL,
  `tipo_estado` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `cantidad_actual` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodopago` int(11) NOT NULL,
  `tipo_metodo` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `id_pedidos` int(11) NOT NULL,
  `id_metodopago` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedidos` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_metodopago` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_productos` int(11) NOT NULL,
  `nombre_producto` varchar(55) NOT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_disponible` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_productos`, `nombre_producto`, `descripcion`, `precio`, `stock`, `imagen`, `id_categoria`, `id_disponible`) VALUES
(8, 'Oso de peluche', 'Un adorable oso de peluche diseñado para brindar compañía y ternura a los más pequeños. Su textura suave y agradable lo convierte en un compañero ideal para jugar, abrazar y acompañar al bebé durante sus momentos de descanso.', 250.00, 100, '{\"frente\":\"Juguetes/prod_8_frente_1785395438.webp\",\"izquierda\":\"Juguetes/prod_8_izquierda_1785395438.webp\",\"derecha\":\"Juguetes/prod_8_derecha_1785395438.webp\"}', 4, 1),
(11, 'Sonajero', 'Divertida y adorable sonaja con forma de oso, diseñada especialmente para estimular los sentidos del bebé mientras juega. Su diseño fácil de sujetar y sus sonidos suaves ayudan a despertar la curiosidad y mantener al pequeño entretenido.', 159.00, 100, '{\"frente\":\"Juguetes/prod_1785395143_frente_334.webp\",\"izquierda\":\"Juguetes/prod_1785395143_izquierda_680.webp\",\"derecha\":\"Juguetes/prod_1785395143_derecha_555.webp\"}', 5, 1),
(12, 'Capitan Pulpo', 'Un adorable pulpo sensorial diseñado para estimular los sentidos del bebé mientras juega. Sus diferentes texturas, colores y suaves tentáculos ofrecen una experiencia divertida y entretenida, ayudando al pequeño a explorar mediante el tacto y la interacción.', 300.00, 100, '{\"frente\":\"Juguetes/prod_1785395671_frente_958.webp\",\"izquierda\":\"Juguetes/prod_1785395671_izquierda_427.webp\",\"derecha\":\"Juguetes/prod_1785395671_derecha_628.webp\"}', 5, 1),
(13, 'Control juega y aprende', 'Un divertido control de videojuegos diseñado especialmente para estimular los sentidos de los bebés mientras juegan. Cuenta con botones, colores y elementos interactivos que ayudan a despertar su curiosidad y favorecer la exploración mediante el tacto, la vista y el oído.', 450.00, 100, '{\"frente\":\"Juguetes/prod_1785395782_frente_639.webp\",\"izquierda\":\"Juguetes/prod_1785395782_izquierda_209.webp\",\"derecha\":\"Juguetes/prod_1785395782_derecha_349.webp\"}', 7, 1),
(14, 'Robot Amigable', '¡Un robot lleno de diversión y tecnología! Este adorable robot interactivo cuenta con ojos LED expresivos, brazos articulados y un diseño amigable que encantará a los pequeños. Perfecto para estimular la imaginación y el interés por la robótica. Incluye sonidos divertidos y movimientos programables. Ideal para niños de 5 años en adelante que aman la tecnología y los juegos creativos.', 349.00, 100, '{\"frente\":\"Juguetes/prod_1785396562_frente_969.webp\",\"izquierda\":\"Juguetes/prod_1785396562_izquierda_408.webp\",\"derecha\":\"Juguetes/prod_1785396562_derecha_532.webp\"}', 7, 1),
(15, 'Pony Magico', 'Pony Mágico es un adorable juguete diseñado para estimular la imaginación y la creatividad. Gracias a su suave melena y cola, pueden peinarlo y crear diferentes estilos con el cepillo incluido.', 279.00, 100, '{\"frente\":\"Juguetes/prod_1785397834_frente_375.webp\",\"izquierda\":\"Juguetes/prod_1785397834_izquierda_189.webp\",\"derecha\":\"Juguetes/prod_1785397834_derecha_325.webp\"}', 8, 1),
(16, 'Auto de carreras RC', '¡Velocidad y emoción en un solo juguete! Este increíble auto de carreras a control remoto ofrece una experiencia de conducción emocionante con luces LED brillantes y sonidos realistas de motor. Su diseño aerodinámico y ruedas de alto agarre lo hacen perfecto para carreras indoor y outdoor. Control remoto incluido con alcance de hasta 20 metros. Recomendado para niños de 6 años en adelante.', 399.00, 100, '{\"frente\":\"Juguetes/prod_1785403943_frente_115.webp\",\"izquierda\":\"Juguetes/prod_1785403943_izquierda_616.webp\",\"derecha\":\"Juguetes/prod_1785403943_derecha_360.webp\"}', 6, 1),
(17, 'Cohete Espacial', '¡Despega hacia la aventura espacial! Este impresionante cohete espacial combina diversión y aprendizaje con su sistema de lanzamiento a presión de agua. Incluye base de lanzamiento, ventanas coloridas y detalles realistas. Perfecto para explorar conceptos de física y astronomía de forma práctica. Estimula el interés por la ciencia y la exploración espacial. Ideal para niños de 7 años en adelante.', 319.00, 100, '{\"frente\":\"Juguetes/prod_1785404017_frente_944.webp\",\"izquierda\":\"Juguetes/prod_1785404017_izquierda_683.webp\",\"derecha\":\"Juguetes/prod_1785404017_derecha_479.webp\"}', 9, 1),
(18, 'Bicicleta', '¡La bicicleta perfecta para los pequeños aventureros! Diseñada especialmente para niños, esta bicicleta de juguete destaca por su estilo deportivo, colores llamativos y acabados realistas. Su estructura resistente y sus ruedas de gran tamaño permiten que los pequeños disfruten de horas de diversión mientras desarrollan su imaginación creando emocionantes paseos y nuevas aventuras. Es un juguete seguro, atractivo y perfecto para acompañar el crecimiento de los niños mediante el juego creativo.', 899.00, 100, '{\"frente\":\"Juguetes/prod_1785404098_frente_713.webp\",\"izquierda\":\"Juguetes/prod_1785404098_izquierda_608.webp\",\"derecha\":\"Juguetes/prod_1785404098_derecha_599.webp\"}', 9, 1),
(19, 'Avion', '¡Los pequeños pilotos estarán listos para despegar! Este avión de juguete ha sido diseñado especialmente para niños que disfrutan imaginar increíbles aventuras en el cielo. Su diseño aerodinámico, inspirado en un avión moderno en pleno vuelo, junto con sus colores llamativos y acabados detallados, hacen de este juguete la opción ideal para estimular la creatividad y la diversión durante horas.', 299.00, 100, '{\"frente\":\"Juguetes/prod_1785404163_frente_289.webp\",\"izquierda\":\"Juguetes/prod_1785404163_izquierda_850.webp\",\"derecha\":\"Juguetes/prod_1785404163_derecha_131.webp\"}', 6, 1),
(20, 'Pistola', '¡Diversión intergaláctica para los pequeños exploradores! La Galaxy Blaster ha sido creada especialmente para niños, con un diseño futurista, colores vivos y una apariencia completamente infantil inspirada en el espacio. Es perfecta para recrear aventuras de ciencia ficción, desarrollar la imaginación y disfrutar de emocionantes juegos de roles. Fabricada con materiales resistentes y seguros, brinda horas de entretenimiento en un ambiente de diversión.', 249.00, 100, '{\"frente\":\"Juguetes/prod_1785404237_frente_738.webp\",\"izquierda\":\"Juguetes/prod_1785404237_izquierda_332.webp\",\"derecha\":\"Juguetes/prod_1785404237_derecha_737.webp\"}', 9, 1),
(21, 'Princesa de la Estrella Mágica', 'La Princesa de la Estrella Mágica es una hermosa muñeca de fantasía con un elegante vestido brillante y accesorios reales. Perfecta para crear historias mágicas y vivir aventuras llenas de imaginación.', 329.00, 100, '{\"frente\":\"Juguetes/prod_1785404328_frente_632.webp\",\"izquierda\":\"Juguetes/prod_1785404329_izquierda_298.webp\",\"derecha\":\"Juguetes/prod_1785404329_derecha_798.webp\"}', 8, 1),
(22, 'Princesa Rosa', 'Princesa Rosa es una encantadora muñeca con un delicado vestido rosa y una elegante corona dorada. Su diseño clásico la convierte en la compañera ideal para crear cuentos de hadas y momentos llenos de diversión.', 249.00, 100, '{\"frente\":\"Juguetes/prod_1785404380_frente_672.webp\",\"izquierda\":\"Juguetes/prod_1785404380_izquierda_873.webp\",\"derecha\":\"Juguetes/prod_1785404380_derecha_281.webp\"}', 8, 1),
(23, 'Centro de actividades interactivo \"Conejito Musical', 'Descripción: Es un centro de actividades diseñado para estimular el desarrollo sensorial y motriz del bebé mediante sonidos, luces, botones y piezas móviles.  Características: Conejito sonriente como figura principal. Teléfono de juguete. Teclado con números y figuras. Botones de colores con sonidos. Rodillo giratorio con imágenes. Material plástico resistente y bordes redondeados. Favorece la coordinación mano-ojo y el reconocimiento de colores y números.', 329.00, 100, '{\"frente\":\"Juguetes/prod_1785404704_frente_521.webp\",\"izquierda\":\"Juguetes/prod_1785404704_izquierda_686.webp\",\"derecha\":\"Juguetes/prod_1785404704_derecha_201.webp\"}', 5, 1),
(24, 'Volante interactivo con avión', 'Descripción: Juguete interactivo con volante y accesorios que simula la conducción de un vehículo y estimula la imaginación mediante sonidos y movimientos.  Características: Volante giratorio. Avión de juguete integrado. Hélice móvil. Interruptor lateral. Sonidos y efectos interactivos. Fácil de sujetar para manos pequeñas. Favorece la coordinación y el juego de imitación.', 259.00, 100, '{\"frente\":\"Juguetes/prod_1785404759_frente_917.webp\",\"izquierda\":\"Juguetes/prod_1785404759_izquierda_615.webp\",\"derecha\":\"Juguetes/prod_1785404759_derecha_744.webp\"}', 7, 1),
(25, 'Gimnasio musical para bebé', 'Descripción: Tapete de actividades para bebés con arco de juguetes colgantes y piano musical que estimula el desarrollo físico y sensorial desde los primeros meses.  Características: Tapete acolchado. Arco con juguetes colgantes. Piano musical con luces y sonidos. Ayuda al desarrollo de brazos, piernas y coordinación. Estimula la percepción visual y auditiva.', 699.00, 100, '{\"frente\":\"Juguetes/prod_1785404989_frente_612.webp\",\"izquierda\":\"Juguetes/prod_1785404989_izquierda_813.webp\",\"derecha\":\"Juguetes/prod_1785404989_derecha_255.webp\"}', 5, 1),
(26, 'Dinosaurio de Peluche', 'Un suave y simpático dinosaurio de peluche diseñado para acompañar las grandes aventuras imaginarias de los niños. Con detalles bordados, colores llamativos y una textura afelpada ultra suave, es perfecto para abrazar a la hora de dormir y fomentar el juego creativo.', 249.00, 100, '{\"frente\":\"Juguetes/prod_1785405104_frente_923.webp\",\"izquierda\":\"Juguetes/prod_1785405104_izquierda_355.webp\",\"derecha\":\"Juguetes/prod_1785405104_derecha_808.webp\"}', 4, 1),
(27, 'Leon de Peluche', 'Un majestuoso y tierno león de peluche con una melena esponjosa y acabados de alta calidad. Este adorable compañero estimula el afecto y la imaginación en los niños, brindándoles seguridad y momentos de diversión ininterrumpida.', 269.00, 100, '{\"frente\":\"Juguetes/prod_1785405153_frente_752.webp\",\"izquierda\":\"Juguetes/prod_1785405154_izquierda_356.webp\",\"derecha\":\"Juguetes/prod_1785405154_derecha_591.webp\"}', 4, 1),
(28, 'Soldado de juguete', 'Figura de acción de soldado articulada, diseñada con accesorios y detalles realistas para horas de entretenimiento táctico. Ideal para desarrollar el juego de rol, la estrategia y la imaginación en niños apasionados por la aventura.', 229.00, 100, '{\"frente\":\"Juguetes/prod_1785405197_frente_836.webp\",\"izquierda\":\"Juguetes/prod_1785405198_izquierda_909.webp\",\"derecha\":\"Juguetes/prod_1785405198_derecha_614.webp\"}', 8, 1),
(29, 'Pista de carreras', 'Emocionante pista de carreras con curvas veloces y rampas de despegue. Fácil de ensamblar y compatible con múltiples vehículos de juguete, promueve la coordinación motriz, la destreza y la diversión competitiva entre amigos.', 499.00, 100, '{\"frente\":\"Juguetes/prod_1785405267_frente_122.webp\",\"izquierda\":\"Juguetes/prod_1785405267_izquierda_313.webp\",\"derecha\":\"Juguetes/prod_1785405267_derecha_777.webp\"}', 6, 1),
(30, 'Conejo Rosa', 'Un encantador conejo de peluche en tono rosa pastel con orejas largas y textura aterciopelada. Ideal como regalo tierno, ayuda a desarrollar la empatía y brindar confort durante el juego o la hora de la siesta.', 250.00, 100, '{\"frente\":\"Juguetes/prod_1785405461_frente_544.webp\",\"izquierda\":\"Juguetes/prod_1785405462_izquierda_616.webp\",\"derecha\":\"Juguetes/prod_1785405462_derecha_425.webp\"}', 4, 1),
(31, 'Auto Rosa', 'Vehículo de juguete estilo convertible rosa con acabados brillantes y detalles modernos. Diseñado para transportar muñecas y figuras pequeñas, estimulando la creatividad, el movimiento libre y la creación de historias fantásticas.', 300.00, 100, '{\"frente\":\"Juguetes/prod_1785405497_frente_650.webp\",\"izquierda\":\"Juguetes/prod_1785405497_izquierda_304.webp\",\"derecha\":\"Juguetes/prod_1785405497_derecha_900.webp\"}', 6, 1),
(32, 'Moto Rosada', 'Elegante motocicleta de juguete en color rosa brillante con ruedas giratorias suaves y diseño aerodinámico. Un juguete genial para impulsar el juego simbólico, la imaginación activa y las divertidas carreras de muñecas.', 450.00, 100, '{\"frente\":\"Juguetes/prod_1785405534_frente_949.webp\",\"izquierda\":\"Juguetes/prod_1785405534_izquierda_843.webp\",\"derecha\":\"Juguetes/prod_1785405534_derecha_144.webp\"}', 6, 1),
(33, 'Casa de juguete', 'Espaciosa casa de juguete decorada con detalles coloridos, varias habitaciones y accesorios divertidos. Perfecta para fomentar la interacción social, el aprendizaje de dinámicas del hogar y la imaginación en grupo.', 600.00, 100, '{\"frente\":\"Juguetes/prod_1785405605_frente_824.webp\",\"izquierda\":\"Juguetes/prod_1785405605_izquierda_406.webp\",\"derecha\":\"Juguetes/prod_1785405605_derecha_623.webp\"}', 8, 1),
(34, 'Castillo de juguete', 'Un mágico castillo de cuentos de hadas con torres, puertas abatibles y elegantes salas reales. Estimula la fantasía y la creación de narrativas épicas donde reyes, reinas y princesas viven grandes historias.', 400.00, 100, '{\"frente\":\"Juguetes/prod_1785405643_frente_612.webp\",\"izquierda\":\"Juguetes/prod_1785405643_izquierda_691.webp\",\"derecha\":\"Juguetes/prod_1785405643_derecha_601.webp\"}', 8, 1),
(35, 'Cocina de juguete', 'Completa cocina de juguete equipada con accesorios de cocina, perillas giratorias y compartimentos de almacenamiento. Ayuda a desarrollar la motricidad fina, el trabajo en equipo y el amor por la gastronomía a través del juego simbólico.', 550.00, 99, '{\"frente\":\"Juguetes/prod_35_frente_1785406155.webp\",\"izquierda\":\"Juguetes/prod_35_izquierda_1785406155.webp\",\"derecha\":\"Juguetes/prod_35_derecha_1785406155.webp\"}', 5, 1),
(36, 'Muñeca de juguete', 'Hermosa muñeca articulada con vestuario removible y cabello suave para peinar. Un clásico indispensable que fomenta la afectividad, el cuidado y la imaginación de las más pequeñas mientras crean inolvidables historias.', 330.00, 99, '{\"frente\":\"Juguetes/prod_1785405727_frente_140.webp\",\"izquierda\":\"Juguetes/prod_36_izquierda_1785406210.webp\",\"derecha\":\"Juguetes/prod_36_derecha_1785406210.webp\"}', 8, 1),
(37, 'Mesedora', 'Una adorable mecedora con diseño de jirafa, creada para brindar diversión y comodidad a los más pequeños. Su diseño tierno y colorido invita al bebé a disfrutar de momentos de juego mientras desarrolla el equilibrio y la coordinación de forma entretenida.', 1199.00, 99, '{\"frente\":\"Juguetes/prod_1785405792_frente_912.webp\",\"izquierda\":\"Juguetes/prod_1785405792_izquierda_298.webp\",\"derecha\":\"Juguetes/prod_1785405792_derecha_644.webp\"}', 9, 1),
(38, 'Perro de Peluche', 'Un tierno y suave compañero diseñado para acompañar a los más pequeños durante sus juegos y momentos de descanso. Su adorable diseño de perrito y textura agradable lo convierten en un juguete perfecto para abrazar, explorar y desarrollar el vínculo afectivo del bebé.', 429.00, 100, '{\"frente\":\"Juguetes/prod_1785405869_frente_749.webp\",\"izquierda\":\"Juguetes/prod_1785405869_izquierda_854.webp\",\"derecha\":\"Juguetes/prod_1785405870_derecha_186.webp\"}', 4, 1),
(39, 'Xilofono', 'Un colorido y divertido xilófono diseñado para que los más pequeños descubran el mundo de la música mientras juegan. Sus teclas de diferentes colores permiten al bebé experimentar con sonidos y desarrollar su curiosidad de manera entretenida.', 299.00, 100, '{\"frente\":\"Juguetes/prod_1785405926_frente_485.webp\",\"izquierda\":\"Juguetes/prod_1785405926_izquierda_439.webp\",\"derecha\":\"Juguetes/prod_1785405926_derecha_216.webp\"}', 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre_rol`) VALUES
(1, 'cliente'),
(2, 'editor'),
(3, 'administrador'),
(4, 'inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(55) NOT NULL,
  `apellido` varchar(55) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `telefono` varchar(70) NOT NULL,
  `fecha_registro` date NOT NULL,
  `id_rol` int(11) NOT NULL,
  `sexo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contraseña`, `telefono`, `fecha_registro`, `id_rol`, `sexo`) VALUES
(2, 'Vanesa', 'Garcia', 'vane@mail.com', '$2y$10$N5cykhe.HJXgBFnKPnKsBeNDKiHiHcf7WTvV.w9cbvsiSN6o8gm2.', '1234567890', '2026-06-01', 2, NULL),
(3, 'Edson', 'Quintana', 'edson@mail.com', '$2y$10$3DczX9LsqwHlMhlyJnJeUeQBaex9LvL.fb4xGbLrAozQ9HqFpNCX6', '1234567890', '2026-06-01', 3, NULL),
(4, 'Ruben', 'Fuentes', 'ruben@mail.com', '$2y$10$zy2G.X5KW3F31SU0rz81EufF4WQm2iJeGpyfdSXfkIYmT/WmupGxK', '1234567890', '2026-06-02', 1, NULL),
(5, 'Guadalupe', 'Solis', 'lupe@ejemplo.com', '$2y$10$ieNze1LufVzWWeFTCGXaT.DR/c1GyL2.Wkua5y/6ojdXedyBibWoO', '123456', '2026-06-04', 1, NULL),
(6, 'Alexis', 'Gonzalez', 'alex.glez19@outlook.com', '$2y$10$F5fygdquvUYksXdZz6tcSeYucSKv6nyg7xldeehNS7qu7/Hn5oSni', '5577374656', '2026-07-06', 3, NULL),
(9, 'Canelo', 'Perez', 'canelo@mail.com', '$2y$10$0HE8jvT4ZoHPP9w2UnkSCuPPG1XQdum3Yq5YOeVWUmYvrZQ/bfo/W', '1234567890', '2026-07-09', 2, NULL),
(10, 'Carlitos', 'Lechuga', 'carlitos@mail.com', '$2y$10$Cp/80oh9jXQZIjW7k7W2MOy.3.4t86OjwLhWVHMA5WTPDzvNcYXk6', '1234567890', '2026-07-09', 1, NULL),
(12, 'Usuario', 'Pruebas', 'user@mail.com', '$2y$10$unxtl2P1a08B7TvBO6u3MuSZ0OBsw0Z1Vqh2BIq9mfRD54nEG7TYa', '5567834958', '2026-07-25', 3, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  ADD PRIMARY KEY (`id_detalle_carrito`),
  ADD KEY `id_productos` (`id_productos`),
  ADD KEY `id_carrito` (`id_carrito`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id_detallepedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id_direcciones`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  ADD PRIMARY KEY (`id_disponible`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodopago`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_pedidos` (`id_pedidos`),
  ADD KEY `id_metodopago` (`id_metodopago`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedidos`),
  ADD KEY `id_metodopago` (`id_metodopago`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_productos`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_disponible` (`id_disponible`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  MODIFY `id_detalle_carrito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id_detallepedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direcciones` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  MODIFY `id_disponible` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id_metodopago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedidos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_productos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  ADD CONSTRAINT `detalle_carrito_ibfk_1` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_productos`),
  ADD CONSTRAINT `detalle_carrito_ibfk_2` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`);

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`);

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`),
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_pedidos`) REFERENCES `pedidos` (`id_pedidos`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_metodopago`) REFERENCES `metodo_pago` (`id_metodopago`),
  ADD CONSTRAINT `pagos_ibfk_3` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_metodopago`) REFERENCES `metodo_pago` (`id_metodopago`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`id_disponible`) REFERENCES `disponibilidad` (`id_disponible`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
