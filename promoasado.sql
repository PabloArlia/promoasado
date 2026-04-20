-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-04-2026 a las 22:30:50
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
-- Estructura de tabla para la tabla `semillas_horarias`
--

CREATE TABLE `semillas_horarias` (
  `id` int(10) UNSIGNED NOT NULL,
  `franja_semilla` datetime NOT NULL,
  `participante_ganador_id` int(10) UNSIGNED DEFAULT NULL,
  `ganado_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `apellido` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `celular` varchar(40) NOT NULL,
  `dni` varchar(20) NOT NULL,
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
-- Indices de la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_semillas_horarias_franja` (`franja_semilla`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_usuarios_email` (`email`),
  ADD UNIQUE KEY `uniq_usuarios_dni` (`dni`);

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
-- AUTO_INCREMENT de la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
