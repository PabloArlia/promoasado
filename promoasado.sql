-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-04-2026 a las 22:55:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `promoasado`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bares`
--

CREATE TABLE `bares` (
  `id` int(10) UNSIGNED NOT NULL,
  `cadena_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bares`
--

INSERT INTO `bares` (`id`, `cadena_id`, `nombre`, `direccion`, `latitud`, `longitud`, `horario`) VALUES
(1, 1, 'Mataderos', 'Av. Emilio Castro 7432, Mataderos, CABA', -34.6550804, -58.5218523, '17:30-02:00. Vie-Sab: 03:00'),
(2, 1, 'Paseo de la Plaza', 'Av. Corrientes 1660, Argentina', -34.6044134, -58.3902253, '17:00-01:00. Vie-Sab: 02:00'),
(3, 1, 'Urquiza', 'Olazabal 4800, Urquiza', -34.5747950, -58.4826820, '17:00-01:00. Vie-Sab: 02:00'),
(4, 1, 'Ballester', 'Lamadrid 2547, Ballester, Argentina', -34.5453253, -58.5566770, '17:30-01:00. Vie-Sab: 02:00'),
(5, 1, 'Monte Grande', 'Dardo Rocha 299, Monte Grande, Argentina', -34.8171126, -58.4728509, '17:00-02:00. Vie-Sab: 03:00'),
(6, 2, 'BELGRANO', 'Amenabar 2363,BELGRANO', -34.5605160, -58.4606924, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(7, 2, 'CASEROS', 'Mariano Moreno 4779, CASEROS', -34.6077273, -58.5635046, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(8, 2, 'ALMAGRO', 'Lavalle 3565,ALMAGRO', -34.6005203, -58.4155472, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(9, 2, 'LANUS', 'Del Valle Iberlucea 2675,LANUS', -34.7021067, -58.3934924, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(10, 2, 'MORENO', 'Bernardino Rivadavia 439, MORENO', -34.6441735, -58.7922525, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(11, 2, 'SAN FERNANDO', 'Sarmiento 1249, SAN FERNANDO', -34.4402039, -58.5585284, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(12, 2, 'SAN MARTIN', 'Jose C. Paz 3311, SAN MARTIN', -34.5751594, -58.5486605, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(13, 2, 'SAN MIGUEL', 'Sargento Cabral 1164, SAN MIGUEL', -34.5459087, -58.7062354, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(14, 2, 'VILLA URQUIZA', 'Olazabal 4299, VILLA URQUIZA', -34.5715344, -58.4775422, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(15, 2, 'CABALLITO', 'Bonifacio 464, CABALLITO', -34.6267302, -58.4335147, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(16, 2, 'OLIVOS', 'Av Maipu 3702, OLIVOS', -34.5018395, -58.4957844, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(17, 2, 'RECOLETA', 'French 2913, RECOLETA', -34.5878477, -58.4042166, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(18, 2, 'LINIERS', 'Larrazabal 474, LINIERS', -34.6438248, -58.5114024, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(19, 2, 'MORON', '25 de Mayo 651, MORON', -34.6553278, -58.6199503, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(20, 2, 'SAAVEDRA', 'Garcia del Rio 3602, SAAVEDRA', -34.5522803, -58.4808731, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(21, 3, 'Belgrano', 'Belgrano, Buenos Aires, Argentina', -34.6129580, -58.3776468, '11:00-00:00'),
(22, 3, 'San Isidro', 'San Isidro, Buenos Aires, Argentina', -34.4739792, -58.5264866, '11:00-00:00'),
(23, 3, 'Puerto Madero', 'Puerto Madero, Buenos Aires, Argentina', -34.6103764, -58.3622067, '11:00-00:00'),
(24, 3, 'Lomitas', 'Lomitas, Buenos Aires, Argentina', -34.7638047, -58.4007970, '11:00-00:00'),
(25, 3, 'Boedo', 'Boedo, Buenos Aires, Argentina', -34.6254799, -58.4161163, '11:00-00:00'),
(26, 3, 'San Miguel', 'San Miguel, Buenos Aires, Argentina', -34.5430060, -58.7119660, '11:00-00:00'),
(27, 3, 'Lanus', 'Lanus, Buenos Aires, Argentina', -34.7073871, -58.3905998, '11:00-00:00'),
(28, 3, 'Parque Leloir', 'Parque Leloir, Buenos Aires, Argentina', -34.6208235, -58.6883467, '11:00-00:00'),
(29, 3, 'Corrientes', 'Corrientes, Argentina', -29.0177384, -57.8869739, '11:00-00:00'),
(30, 3, 'Nordelta', 'Nordelta, Buenos Aires, Argentina', -34.4143804, -58.6494578, '11:00-00:00'),
(31, 3, 'Pilar', 'Pilar, Buenos Aires, Argentina', -34.4570918, -58.9141609, '11:00-00:00'),
(32, 3, 'Unicenter', 'Unicenter, Buenos Aires, Argentina', -34.5089901, -58.5225821, '11:00-00:00'),
(33, 3, 'Plaza Oeste', 'Plaza Oeste, Buenos Aires, Argentina', -34.6343933, -58.6296470, '11:00-00:00'),
(34, 3, 'Palermo', 'Palermo, Buenos Aires, Argentina', -34.5803362, -58.4245236, '11:00-00:00'),
(35, 4, 'Parana - Shopping Paso del Parana', 'San Juan 769 Parana, Entre Rios, Argentina', -31.2326977, -59.9826990, '11:00-00:00'),
(36, 4, 'Funes', 'Angelome 2308, Funes, Santa Fe, Argentina', -32.9383730, -60.8150706, '11:00-00:00'),
(37, 4, 'Rosario - Portal Rosario Shopping', 'Nansen 323, Rosario, Santa Fe, Argentina', -32.9104694, -60.6833066, '11:00-00:00'),
(38, 4, 'Burger Sur Rosario', 'Av. San Martin 4854, Rosario, Santa Fe, Argentina', -33.0058545, -60.6500358, '11:00-00:00'),
(39, 4, 'Capitan Bermudez', 'Av. San Lorenzo 599, Capitan Bermudez, Santa Fe, Argentina', -32.8259410, -60.7134425, '11:00-00:00'),
(40, 4, 'Centro Rosario', 'Mendoza 1096, Rosario, Santa Fe, Argentina', -32.9514500, -60.6392894, '11:00-00:00'),
(41, 4, 'Echesortu', 'Mendoza 3025, Rosario, Santa Fe, Argentina', -32.9468706, -60.6658572, '11:00-00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cadenas`
--

CREATE TABLE `cadenas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `identificador` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cadenas`
--

INSERT INTO `cadenas` (`id`, `nombre`, `logo`, `identificador`) VALUES
(1, 'CERVELAR', 'img/cadenas/cervelar.png', 'cervelar'),
(2, 'HORMIGA NEGRA', 'img/cadenas/hormiga-negra.png', 'hormiga-negra'),
(3, 'BIG PONS', 'img/cadenas/big-pons.png', 'big-pons'),
(4, 'JOHN\'S BURGERS', 'img/cadenas/johns-burgers.png', 'johns-burgers');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_botones`
--

CREATE TABLE `intentos_botones` (
  `id` int(10) UNSIGNED NOT NULL,
  `participante_id` int(10) UNSIGNED NOT NULL,
  `numero_boton` tinyint(3) UNSIGNED NOT NULL,
  `resultado` tinyint(1) NOT NULL,
  `creado_en` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participacion`
--

CREATE TABLE `participacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `gano_juego` tinyint(1) NOT NULL DEFAULT 0,
  `preguntas_aprobadas` tinyint(1) DEFAULT NULL,
  `respuestas_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`respuestas_json`)),
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `bar_id` int(10) UNSIGNED DEFAULT NULL,
  `distancia_km` decimal(10,2) DEFAULT NULL,
  `fecha_participacion` date NOT NULL,
  `fecha_respondio` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `premio`
--

CREATE TABLE `premio` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `ganaste` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `premio`
--

INSERT INTO `premio` (`id`, `nombre`, `imagen`, `ganaste`) VALUES
(1, 'LCD', NULL, 'GANASTE UN LCD'),
(2, 'Torre sonido', NULL, 'GANASTE UNA TORRE DE SONIDO'),
(3, 'Proyector 4K Android 13 Netmak Wifi Bluetooth Parlante', NULL, 'GANASTE UN PROYECTOR 4K ANDROID 13 NETMAK WIFI BLUETOOTH PARLANTE'),
(4, 'Camiseta Adidas', 'camiseta.png', 'GANASTE UNA CAMISETA ADIDAS'),
(5, 'Pelotas Mundial 2026', NULL, 'GANASTE UNA PELOTA MUNDIAL 2026'),
(6, 'Bolso Adidas selección Argentina', NULL, 'GANASTE UN BOLSO ADIDAS SELECCIÓN ARGENTINA'),
(7, 'Medias Adidas selección Argentina', NULL, 'GANASTE UNAS MEDIAS ADIDAS SELECCIÓN ARGENTINA'),
(8, 'Pack kit asado (caja + producto Hellmann´s + tarjeta)', NULL, 'GANASTE UN PACK KIT ASADO (CAJA + PRODUCTO HELLMANN´S + TARJETA)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semillas_horarias`
--

CREATE TABLE `semillas_horarias` (
  `id` int(10) UNSIGNED NOT NULL,
  `franja_semilla` datetime NOT NULL,
  `premio` int(10) UNSIGNED DEFAULT NULL,
  `cadena` int(10) UNSIGNED DEFAULT NULL,
  `participante_ganador_id` int(10) UNSIGNED DEFAULT NULL,
  `ganado_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `semillas_horarias`
--

INSERT INTO `semillas_horarias` (`id`, `franja_semilla`, `premio`, `cadena`, `participante_ganador_id`, `ganado_en`) VALUES
(1, '2026-06-18 19:26:00', 1, 1, NULL, NULL),
(2, '2026-06-17 19:32:00', 2, 1, NULL, NULL),
(3, '2026-06-24 22:50:00', 2, 1, NULL, NULL),
(4, '2026-06-13 14:34:00', 3, 1, NULL, NULL),
(5, '2026-06-20 19:20:00', 3, 1, NULL, NULL),
(6, '2026-06-25 20:02:00', 3, 1, NULL, NULL),
(7, '2026-06-11 18:55:00', 4, 1, NULL, NULL),
(8, '2026-06-14 17:30:00', 4, 1, NULL, NULL),
(9, '2026-06-16 19:25:00', 4, 1, NULL, NULL),
(10, '2026-06-17 20:00:00', 4, 1, NULL, NULL),
(11, '2026-06-19 17:30:00', 4, 1, NULL, NULL),
(12, '2026-06-22 18:40:00', 4, 1, NULL, NULL),
(13, '2026-06-24 20:30:00', 4, 1, NULL, NULL),
(14, '2026-06-25 20:50:00', 4, 1, NULL, NULL),
(15, '2026-06-25 21:24:00', 4, 1, NULL, NULL),
(16, '2026-06-26 21:10:00', 4, 1, NULL, NULL),
(17, '2026-06-13 18:40:00', 5, 1, NULL, NULL),
(18, '2026-06-15 20:15:00', 5, 1, NULL, NULL),
(19, '2026-06-16 20:40:00', 5, 1, NULL, NULL),
(20, '2026-06-18 19:50:00', 5, 1, NULL, NULL),
(21, '2026-06-18 19:51:00', 5, 1, NULL, NULL),
(22, '2026-06-19 20:59:00', 5, 1, NULL, NULL),
(23, '2026-06-21 20:50:00', 5, 1, NULL, NULL),
(24, '2026-06-23 20:50:00', 5, 1, NULL, NULL),
(25, '2026-06-24 18:50:00', 5, 1, NULL, NULL),
(26, '2026-06-26 20:15:00', 5, 1, NULL, NULL),
(27, '2026-06-12 19:40:00', 6, 1, NULL, NULL),
(28, '2026-06-14 20:10:00', 6, 1, NULL, NULL),
(29, '2026-06-20 20:10:00', 6, 1, NULL, NULL),
(30, '2026-06-21 18:40:00', 6, 1, NULL, NULL),
(31, '2026-06-23 21:10:00', 6, 1, NULL, NULL),
(32, '2026-06-26 19:40:00', 6, 1, NULL, NULL),
(33, '2026-06-11 22:30:00', 7, 1, NULL, NULL),
(34, '2026-06-12 20:15:00', 7, 1, NULL, NULL),
(35, '2026-06-16 21:45:00', 7, 1, NULL, NULL),
(36, '2026-06-17 20:30:00', 7, 1, NULL, NULL),
(37, '2026-06-19 21:20:00', 7, 1, NULL, NULL),
(38, '2026-06-22 20:06:00', 7, 1, NULL, NULL),
(39, '2026-06-02 23:05:00', 8, 1, NULL, NULL),
(40, '2026-06-04 18:50:00', 8, 1, NULL, NULL),
(41, '2026-06-05 22:30:00', 8, 1, NULL, NULL),
(42, '2026-06-07 20:01:00', 8, 1, NULL, NULL),
(43, '2026-06-09 20:33:00', 8, 1, NULL, NULL),
(44, '2026-06-12 20:30:00', 8, 1, NULL, NULL),
(45, '2026-06-14 23:05:00', 8, 1, NULL, NULL),
(46, '2026-06-14 18:30:00', 8, 1, NULL, NULL),
(47, '2026-06-15 21:18:00', 8, 1, NULL, NULL),
(48, '2026-06-17 21:15:00', 8, 1, NULL, NULL),
(49, '2026-06-18 20:18:00', 8, 1, NULL, NULL),
(50, '2026-06-19 21:40:00', 8, 1, NULL, NULL),
(51, '2026-06-19 21:25:00', 8, 1, NULL, NULL),
(52, '2026-06-20 23:30:00', 8, 1, NULL, NULL),
(53, '2026-06-21 21:15:00', 8, 1, NULL, NULL),
(54, '2026-06-22 22:30:00', 8, 1, NULL, NULL),
(55, '2026-06-23 21:55:00', 8, 1, NULL, NULL),
(56, '2026-06-24 19:50:00', 8, 1, NULL, NULL),
(57, '2026-06-24 22:06:00', 8, 1, NULL, NULL),
(58, '2026-06-25 20:10:00', 8, 1, NULL, NULL),
(98, '2026-06-13 18:10:00', 1, 2, NULL, NULL),
(99, '2026-06-20 21:10:00', 1, 2, NULL, NULL),
(100, '2026-06-18 19:42:00', 2, 2, NULL, NULL),
(101, '2026-06-24 22:10:00', 2, 2, NULL, NULL),
(102, '2026-06-13 19:44:00', 3, 2, NULL, NULL),
(103, '2026-06-20 19:50:00', 3, 2, NULL, NULL),
(104, '2026-06-25 20:02:00', 3, 2, NULL, NULL),
(105, '2026-06-11 19:55:00', 4, 2, NULL, NULL),
(106, '2026-06-16 19:50:00', 4, 2, NULL, NULL),
(107, '2026-06-17 20:00:00', 4, 2, NULL, NULL),
(108, '2026-06-18 18:50:00', 4, 2, NULL, NULL),
(109, '2026-06-19 20:30:00', 4, 2, NULL, NULL),
(110, '2026-06-22 18:40:00', 4, 2, NULL, NULL),
(111, '2026-06-23 18:50:00', 4, 2, NULL, NULL),
(112, '2026-06-24 20:30:00', 4, 2, NULL, NULL),
(113, '2026-06-25 20:50:00', 4, 2, NULL, NULL),
(114, '2026-06-26 21:10:00', 4, 2, NULL, NULL),
(115, '2026-06-12 19:40:00', 5, 2, NULL, NULL),
(116, '2026-06-13 18:40:00', 5, 2, NULL, NULL),
(117, '2026-06-15 20:15:00', 5, 2, NULL, NULL),
(118, '2026-06-16 20:40:00', 5, 2, NULL, NULL),
(119, '2026-06-18 19:50:00', 5, 2, NULL, NULL),
(120, '2026-06-19 20:59:00', 5, 2, NULL, NULL),
(121, '2026-06-20 20:50:00', 5, 2, NULL, NULL),
(122, '2026-06-20 20:58:00', 5, 2, NULL, NULL),
(123, '2026-06-24 19:50:00', 5, 2, NULL, NULL),
(124, '2026-06-26 21:35:00', 5, 2, NULL, NULL),
(125, '2026-06-13 19:29:00', 6, 2, NULL, NULL),
(126, '2026-06-14 20:10:00', 6, 2, NULL, NULL),
(127, '2026-06-17 21:10:00', 6, 2, NULL, NULL),
(128, '2026-06-20 18:10:00', 6, 2, NULL, NULL),
(129, '2026-06-23 21:10:00', 6, 2, NULL, NULL),
(130, '2026-06-26 19:40:00', 6, 2, NULL, NULL),
(131, '2026-06-11 22:40:00', 7, 2, NULL, NULL),
(132, '2026-06-12 20:25:00', 7, 2, NULL, NULL),
(133, '2026-06-14 21:45:00', 7, 2, NULL, NULL),
(134, '2026-06-17 20:30:00', 7, 2, NULL, NULL),
(135, '2026-06-21 21:20:00', 7, 2, NULL, NULL),
(136, '2026-06-24 20:06:00', 7, 2, NULL, NULL),
(137, '2026-06-02 20:30:00', 8, 2, NULL, NULL),
(138, '2026-06-04 21:38:00', 8, 2, NULL, NULL),
(139, '2026-06-05 23:38:00', 8, 2, NULL, NULL),
(140, '2026-06-07 22:09:00', 8, 2, NULL, NULL),
(141, '2026-06-09 23:16:00', 8, 2, NULL, NULL),
(142, '2026-06-11 17:45:00', 8, 2, NULL, NULL),
(143, '2026-06-12 20:30:00', 8, 2, NULL, NULL),
(144, '2026-06-13 22:15:00', 8, 2, NULL, NULL),
(145, '2026-06-14 19:15:00', 8, 2, NULL, NULL),
(146, '2026-06-15 22:30:00', 8, 2, NULL, NULL),
(147, '2026-06-17 18:15:00', 8, 2, NULL, NULL),
(148, '2026-06-18 21:15:00', 8, 2, NULL, NULL),
(149, '2026-06-19 21:40:00', 8, 2, NULL, NULL),
(150, '2026-06-20 22:30:00', 8, 2, NULL, NULL),
(151, '2026-06-21 18:50:00', 8, 2, NULL, NULL),
(152, '2026-06-22 21:30:00', 8, 2, NULL, NULL),
(153, '2026-06-23 19:55:00', 8, 2, NULL, NULL),
(154, '2026-06-24 18:50:00', 8, 2, NULL, NULL),
(155, '2026-06-25 23:10:00', 8, 2, NULL, NULL),
(156, '2026-06-26 21:40:00', 8, 2, NULL, NULL),
(157, '2026-06-03 21:15:00', 8, 3, NULL, NULL),
(158, '2026-06-04 22:01:00', 8, 3, NULL, NULL),
(159, '2026-06-05 19:49:00', 8, 3, NULL, NULL),
(160, '2026-06-07 19:49:00', 5, 3, NULL, NULL),
(161, '2026-06-07 23:05:00', 8, 3, NULL, NULL),
(162, '2026-06-09 23:18:00', 8, 3, NULL, NULL),
(163, '2026-06-11 20:44:00', 3, 3, NULL, NULL),
(164, '2026-06-11 20:00:00', 4, 3, NULL, NULL),
(165, '2026-06-11 22:40:00', 7, 3, NULL, NULL),
(166, '2026-06-12 18:20:00', 1, 3, NULL, NULL),
(167, '2026-06-12 19:50:00', 4, 3, NULL, NULL),
(168, '2026-06-12 18:49:00', 5, 3, NULL, NULL),
(169, '2026-06-12 20:25:00', 7, 3, NULL, NULL),
(170, '2026-06-12 20:30:00', 8, 3, NULL, NULL),
(171, '2026-06-13 20:55:00', 4, 3, NULL, NULL),
(172, '2026-06-13 18:40:00', 5, 3, NULL, NULL),
(173, '2026-06-13 19:29:00', 6, 3, NULL, NULL),
(174, '2026-06-13 21:15:00', 8, 3, NULL, NULL),
(175, '2026-06-14 20:50:00', 4, 3, NULL, NULL),
(176, '2026-06-14 20:10:00', 6, 3, NULL, NULL),
(177, '2026-06-14 21:45:00', 7, 3, NULL, NULL),
(178, '2026-06-14 19:15:00', 8, 3, NULL, NULL),
(179, '2026-06-15 21:30:00', 8, 3, NULL, NULL),
(180, '2026-06-16 20:40:00', 5, 3, NULL, NULL),
(181, '2026-06-16 21:30:00', 8, 3, NULL, NULL),
(182, '2026-06-17 20:15:00', 5, 3, NULL, NULL),
(183, '2026-06-17 20:16:00', 6, 3, NULL, NULL),
(184, '2026-06-17 20:30:00', 7, 3, NULL, NULL),
(185, '2026-06-17 18:19:00', 8, 3, NULL, NULL),
(186, '2026-06-18 20:42:00', 2, 3, NULL, NULL),
(187, '2026-06-18 18:50:00', 4, 3, NULL, NULL),
(188, '2026-06-18 21:15:00', 8, 3, NULL, NULL),
(189, '2026-06-19 18:50:00', 3, 3, NULL, NULL),
(190, '2026-06-19 20:30:00', 4, 3, NULL, NULL),
(191, '2026-06-19 20:59:00', 5, 3, NULL, NULL),
(192, '2026-06-19 19:10:00', 6, 3, NULL, NULL),
(193, '2026-06-19 21:40:00', 8, 3, NULL, NULL),
(194, '2026-06-20 21:40:00', 5, 3, NULL, NULL),
(195, '2026-06-20 19:18:00', 5, 3, NULL, NULL),
(196, '2026-06-20 22:30:00', 8, 3, NULL, NULL),
(197, '2026-06-20 23:05:00', 8, 3, NULL, NULL),
(198, '2026-06-21 18:50:00', 4, 3, NULL, NULL),
(199, '2026-06-21 21:20:00', 7, 3, NULL, NULL),
(200, '2026-06-22 18:40:00', 4, 3, NULL, NULL),
(201, '2026-06-22 21:30:00', 8, 3, NULL, NULL),
(202, '2026-06-23 19:10:00', 2, 3, NULL, NULL),
(203, '2026-06-23 21:10:00', 6, 3, NULL, NULL),
(204, '2026-06-23 19:55:00', 8, 3, NULL, NULL),
(205, '2026-06-24 20:40:00', 4, 3, NULL, NULL),
(206, '2026-06-24 19:50:00', 5, 3, NULL, NULL),
(207, '2026-06-24 20:06:00', 7, 3, NULL, NULL),
(208, '2026-06-25 19:45:00', 5, 3, NULL, NULL),
(209, '2026-06-25 22:55:00', 8, 3, NULL, NULL),
(210, '2026-06-26 21:20:00', 3, 3, NULL, NULL),
(211, '2026-06-26 21:18:00', 4, 3, NULL, NULL),
(212, '2026-06-26 19:40:00', 6, 3, NULL, NULL),
(213, '2026-06-26 20:06:00', 8, 3, NULL, NULL),
(214, '2026-06-26 21:40:00', 8, 3, NULL, NULL),
(215, '2026-06-03 18:05:00', 8, 4, NULL, NULL),
(216, '2026-06-05 22:49:00', 6, 4, NULL, NULL),
(217, '2026-06-05 19:38:00', 8, 4, NULL, NULL),
(218, '2026-06-07 20:18:00', 8, 4, NULL, NULL),
(219, '2026-06-08 21:15:00', 8, 4, NULL, NULL),
(220, '2026-06-09 18:55:00', 4, 4, NULL, NULL),
(221, '2026-06-11 19:20:00', 3, 4, NULL, NULL),
(222, '2026-06-11 20:59:00', 5, 4, NULL, NULL),
(223, '2026-06-11 23:05:00', 8, 4, NULL, NULL),
(224, '2026-06-12 20:15:00', 7, 4, NULL, NULL),
(225, '2026-06-12 23:39:00', 8, 4, NULL, NULL),
(226, '2026-06-13 19:34:00', 3, 4, NULL, NULL),
(227, '2026-06-13 18:55:00', 4, 4, NULL, NULL),
(228, '2026-06-13 18:40:00', 5, 4, NULL, NULL),
(229, '2026-06-13 22:30:00', 7, 4, NULL, NULL),
(230, '2026-06-14 18:40:00', 6, 4, NULL, NULL),
(231, '2026-06-14 17:30:00', 8, 4, NULL, NULL),
(232, '2026-06-15 20:15:00', 5, 4, NULL, NULL),
(233, '2026-06-15 22:30:00', 8, 4, NULL, NULL),
(234, '2026-06-16 19:25:00', 1, 4, NULL, NULL),
(235, '2026-06-16 19:50:00', 4, 4, NULL, NULL),
(236, '2026-06-16 20:40:00', 5, 4, NULL, NULL),
(237, '2026-06-16 21:45:00', 7, 4, NULL, NULL),
(238, '2026-06-16 21:15:00', 8, 4, NULL, NULL),
(239, '2026-06-17 19:32:00', 2, 4, NULL, NULL),
(240, '2026-06-17 20:00:00', 4, 4, NULL, NULL),
(241, '2026-06-17 23:40:00', 6, 4, NULL, NULL),
(242, '2026-06-17 20:30:00', 7, 4, NULL, NULL),
(243, '2026-06-17 23:15:00', 8, 4, NULL, NULL),
(244, '2026-06-18 19:50:00', 5, 4, NULL, NULL),
(245, '2026-06-18 21:40:00', 8, 4, NULL, NULL),
(246, '2026-06-19 20:30:00', 4, 4, NULL, NULL),
(247, '2026-06-19 21:20:00', 7, 4, NULL, NULL),
(248, '2026-06-20 21:39:00', 4, 4, NULL, NULL),
(249, '2026-06-20 20:10:00', 6, 4, NULL, NULL),
(250, '2026-06-20 23:30:00', 8, 4, NULL, NULL),
(251, '2026-06-21 21:50:00', 5, 4, NULL, NULL),
(252, '2026-06-22 18:40:00', 4, 4, NULL, NULL),
(253, '2026-06-22 20:06:00', 7, 4, NULL, NULL),
(254, '2026-06-22 22:30:00', 8, 4, NULL, NULL),
(255, '2026-06-23 22:06:00', 5, 4, NULL, NULL),
(256, '2026-06-23 20:50:00', 5, 4, NULL, NULL),
(257, '2026-06-23 21:10:00', 6, 4, NULL, NULL),
(258, '2026-06-23 21:55:00', 8, 4, NULL, NULL),
(259, '2026-06-24 22:50:00', 2, 4, NULL, NULL),
(260, '2026-06-24 20:30:00', 4, 4, NULL, NULL),
(261, '2026-06-24 18:50:00', 5, 4, NULL, NULL),
(262, '2026-06-24 19:50:00', 8, 4, NULL, NULL),
(263, '2026-06-24 23:08:00', 8, 4, NULL, NULL),
(264, '2026-06-25 20:02:00', 3, 4, NULL, NULL),
(265, '2026-06-25 20:50:00', 4, 4, NULL, NULL),
(266, '2026-06-25 23:02:00', 8, 4, NULL, NULL),
(267, '2026-06-25 20:10:00', 8, 4, NULL, NULL),
(268, '2026-06-26 21:10:00', 4, 4, NULL, NULL),
(269, '2026-06-26 20:15:00', 5, 4, NULL, NULL),
(270, '2026-06-26 19:40:00', 6, 4, NULL, NULL),
(271, '2026-06-26 22:05:00', 8, 4, NULL, NULL),
(272, '2026-06-26 21:40:00', 8, 4, NULL, NULL),
(273, '2026-04-28 17:00:00', 6, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varbinary(255) NOT NULL,
  `apellido` varbinary(255) NOT NULL,
  `email` varbinary(255) NOT NULL,
  `email_hash` char(64) NOT NULL,
  `celular` varbinary(255) NOT NULL,
  `dni` varbinary(255) NOT NULL,
  `dni_hash` char(64) NOT NULL,
  `acepta_bases` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bares`
--
ALTER TABLE `bares`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cadenas`
--
ALTER TABLE `cadenas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_cadenas_identificador` (`identificador`);

--
-- Indices de la tabla `intentos_botones`
--
ALTER TABLE `intentos_botones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_intentos_botones_participante_boton` (`participante_id`,`numero_boton`);

--
-- Indices de la tabla `participacion`
--
ALTER TABLE `participacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_participacion_usuario_fecha` (`usuario_id`,`fecha_participacion`);

--
-- Indices de la tabla `premio`
--
ALTER TABLE `premio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_email_hash` (`email_hash`),
  ADD KEY `idx_dni_hash` (`dni_hash`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bares`
--
ALTER TABLE `bares`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `cadenas`
--
ALTER TABLE `cadenas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `intentos_botones`
--
ALTER TABLE `intentos_botones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `participacion`
--
ALTER TABLE `participacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `premio`
--
ALTER TABLE `premio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=274;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
