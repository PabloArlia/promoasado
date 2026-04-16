-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-04-2026 a las 00:43:37
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
(1, 1, 'Mataderos', 'Av. Emilio Castro 7432', NULL, NULL, '17:30-02:00. Vie-Sab: 03:00'),
(2, 1, 'Paseo de la Plaza', 'Av. Corrientes 1660', NULL, NULL, '17:00-01:00. Vie-Sab: 02:00'),
(3, 1, 'Urquiza', 'Olazabal 4800', NULL, NULL, '17:00-01:00. Vie-Sab: 02:00'),
(4, 1, 'Ballester', 'Lamadrid 2547', NULL, NULL, '17:30-01:00. Vie-Sab: 02:00'),
(5, 1, 'Monte Grande', 'Dardo Rocha 299', NULL, NULL, '17:00-02:00. Vie-Sab: 03:00'),
(6, 2, 'BELGRANO', 'Amenabar 2363', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(7, 2, 'CASEROS', 'Mariano Moreno 4779', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(8, 2, 'ALMAGRO', 'Lavalle 3565', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(9, 2, 'LANUS', 'Del Valle Iberlucea 2675', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(10, 2, 'MORENO', 'Bernardino Rivadavia 439', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(11, 2, 'SAN FERNANDO', 'Sarmiento 1249', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(12, 2, 'SAN MARTIN', 'Jose C. Paz 3311', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(13, 2, 'SAN MIGUEL', 'Sargento Cabral 1164', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(14, 2, 'VILLA URQUIZA', 'Olazabal 4299', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(15, 2, 'CABALLITO', 'Bonifacio 464', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(16, 2, 'OLIVOS', 'Av Maipu 3702', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(17, 2, 'RECOLETA', 'French 2913', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(18, 2, 'LINIERS', 'Larrazabal 474', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(19, 2, 'MORON', '25 de Mayo 651', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(20, 2, 'SAAVEDRA', 'Garcia del Rio 3602', NULL, NULL, 'Todos los dias desde las 17hs. Lun-Jue: 17hs. Vie-Dom: 12am.'),
(21, 3, 'Belgrano', 'Belgrano, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(22, 3, 'San Isidro', 'San Isidro, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(23, 3, 'Puerto Madero', 'Puerto Madero, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(24, 3, 'Lomitas', 'Lomitas, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(25, 3, 'Boedo', 'Boedo, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(26, 3, 'San Miguel', 'San Miguel, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(27, 3, 'Lanus', 'Lanus, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(28, 3, 'Parque Leloir', 'Parque Leloir, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(29, 3, 'Corrientes', 'Corrientes, Argentina', NULL, NULL, '11:00-00:00'),
(30, 3, 'Nordelta', 'Nordelta, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(31, 3, 'Pilar', 'Pilar, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(32, 3, 'Unicenter', 'Unicenter, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(33, 3, 'Plaza Oeste', 'Plaza Oeste, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(34, 3, 'Palermo', 'Palermo, Buenos Aires, Argentina', NULL, NULL, '11:00-00:00'),
(35, 4, 'Parana - Shopping Paso del Parana', 'San Juan 769 Local 75, Parana, Entre Rios, Argentina', NULL, NULL, '11:00-00:00'),
(36, 4, 'Funes', 'Angelome 2308, Funes, Santa Fe, Argentina', NULL, NULL, '11:00-00:00'),
(37, 4, 'Rosario - Portal Rosario Shopping', 'Nansen 323, Rosario, Santa Fe, Argentina', NULL, NULL, '11:00-00:00'),
(38, 4, 'Burger Sur Rosario', 'Av. San Martin 4854, Rosario, Santa Fe, Argentina', NULL, NULL, '11:00-00:00'),
(39, 4, 'Capitan Bermudez', 'Av. San Lorenzo 599, Capitan Bermudez, Santa Fe, Argentina', NULL, NULL, '11:00-00:00'),
(40, 4, 'Centro Rosario', 'Mendoza 1096, Rosario, Santa Fe, Argentina', NULL, NULL, '11:00-00:00'),
(41, 4, 'Echesortu', 'Mendoza 3025, Rosario, Santa Fe, Argentina', NULL, NULL, '11:00-00:00');

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
(1, 'CERVELAR', NULL, 'cervelar'),
(2, 'HORMIGA NEGRA', NULL, 'hormiga-negra'),
(3, 'BIG PONS', NULL, 'big-pons'),
(4, 'JOHN\'S BURGERS', NULL, 'johns-burgers');

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

--
-- Volcado de datos para la tabla `intentos_botones`
--

INSERT INTO `intentos_botones` (`id`, `participante_id`, `numero_boton`, `resultado`, `creado_en`) VALUES
(5, 12, 1, 1, '2026-04-16 19:00:29'),
(6, 12, 2, 1, '2026-04-16 19:00:30'),
(7, 12, 3, 1, '2026-04-16 19:00:32'),
(8, 12, 4, 1, '2026-04-16 19:00:33'),
(9, 13, 1, 1, '2026-04-16 19:04:28'),
(10, 13, 2, 1, '2026-04-16 19:04:29'),
(11, 13, 3, 0, '2026-04-16 19:04:30'),
(12, 14, 1, 1, '2026-04-16 19:08:56'),
(13, 14, 2, 0, '2026-04-16 19:08:58');

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
  `fecha_participacion` date NOT NULL,
  `fecha_respondio` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `participacion`
--

INSERT INTO `participacion` (`id`, `usuario_id`, `gano_juego`, `preguntas_aprobadas`, `respuestas_json`, `fecha_participacion`, `fecha_respondio`) VALUES
(12, 12, 1, 1, '{\"q1\":\"a\",\"q2\":\"a\",\"q3\":\"a\"}', '2026-04-15', '2026-04-15 19:00:40'),
(13, 12, 0, NULL, NULL, '2026-04-16', NULL),
(14, 13, 0, NULL, NULL, '2026-04-16', NULL);

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

--
-- Volcado de datos para la tabla `semillas_horarias`
--

INSERT INTO `semillas_horarias` (`id`, `franja_semilla`, `participante_ganador_id`, `ganado_en`) VALUES
(1, '2026-04-15 09:45:00', 12, '2026-04-16 19:00:33');

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
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `celular`, `dni`, `acepta_bases`, `fecha_registro`) VALUES
(12, 'Pablo', 'Arlia', 'pablo.arlia@lumia.com.ar', '69582906', '31655361', 1, '2026-04-16 19:00:25'),
(13, 'Pablo2', 'Arlia2', 'info@mentaproject.com.ar', '695829062', '31655362', 1, '2026-04-16 19:08:53');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bares`
--
ALTER TABLE `bares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bares_cadena` (`cadena_id`);

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
  ADD UNIQUE KEY `uniq_semillas_horarias_franja` (`franja_semilla`),
  ADD KEY `fk_semillas_horarias_participante` (`participante_ganador_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `participacion`
--
ALTER TABLE `participacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bares`
--
ALTER TABLE `bares`
  ADD CONSTRAINT `fk_bares_cadena` FOREIGN KEY (`cadena_id`) REFERENCES `cadenas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `intentos_botones`
--
ALTER TABLE `intentos_botones`
  ADD CONSTRAINT `fk_intentos_botones_participacion` FOREIGN KEY (`participante_id`) REFERENCES `participacion` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `participacion`
--
ALTER TABLE `participacion`
  ADD CONSTRAINT `fk_participacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  ADD CONSTRAINT `fk_semillas_horarias_participacion` FOREIGN KEY (`participante_ganador_id`) REFERENCES `participacion` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
