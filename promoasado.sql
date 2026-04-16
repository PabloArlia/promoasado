-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-04-2026 a las 00:33:10
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
(1, '2026-04-15 09:45:00', 1, '2026-04-15 12:56:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE `participantes` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `apellido` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `celular` varchar(40) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `acepta_terminos` tinyint(1) NOT NULL DEFAULT 1,
  `acepta_bases` tinyint(1) NOT NULL DEFAULT 1,
  `gano_juego` tinyint(1) NOT NULL DEFAULT 0,
  `preguntas_aprobadas` tinyint(1) DEFAULT NULL,
  `respuestas_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`respuestas_json`)),
  `registrado_en` datetime NOT NULL,
  `respondido_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
--
-- Indices de la tabla `intentos_botones`
--
ALTER TABLE `intentos_botones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_intentos_botones_participante_boton` (`participante_id`,`numero_boton`);

--
-- Indices de la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_semillas_horarias_franja` (`franja_semilla`),
  ADD KEY `fk_semillas_horarias_participante` (`participante_ganador_id`);

--
-- Indices de la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_participantes_email` (`email`),
  ADD UNIQUE KEY `uniq_participantes_dni` (`dni`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `intentos_botones`
--
ALTER TABLE `intentos_botones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `intentos_botones`
--
ALTER TABLE `intentos_botones`
  ADD CONSTRAINT `fk_intentos_botones_participante` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `semillas_horarias`
--
ALTER TABLE `semillas_horarias`
  ADD CONSTRAINT `fk_semillas_horarias_participante` FOREIGN KEY (`participante_ganador_id`) REFERENCES `participantes` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
