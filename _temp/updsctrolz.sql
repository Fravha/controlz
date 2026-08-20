-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-08-2026 a las 18:23:29
-- Versión del servidor: 5.6.17
-- Versión de PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `updsctrolz`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE IF NOT EXISTS `areas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_areas_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigos_verificacion`
--

CREATE TABLE IF NOT EXISTS `codigos_verificacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `correo` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `codigo` char(4) COLLATE utf8_spanish_ci NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expira_en` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT '0',
  `intentos` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_correo` (`correo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `codigos_verificacion`
--

INSERT INTO `codigos_verificacion` (`id`, `correo`, `codigo`, `creado_en`, `expira_en`, `usado`, `intentos`) VALUES
(1, 'guebelmorenopay@gmail.com', '4121', '2026-08-20 10:04:51', '2026-08-20 16:14:51', 1, 0),
(2, 'juanjovico63@gmail.com', '5697', '2026-08-20 10:40:07', '2026-08-20 16:50:07', 1, 0),
(3, 'juanjovico63@gmail.com', '3301', '2026-08-20 12:12:29', '2026-08-20 18:22:29', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_r_p`
--

CREATE TABLE IF NOT EXISTS `detalle_r_p` (
  `id` int(150) NOT NULL AUTO_INCREMENT,
  `id_roles` int(150) NOT NULL,
  `id_permiso` int(150) NOT NULL,
  `freg` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `loginerror`
--

CREATE TABLE IF NOT EXISTS `loginerror` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE IF NOT EXISTS `permisos` (
  `id` int(150) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE IF NOT EXISTS `persona` (
  `id` int(150) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `apellidos` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `tipoper` int(4) NOT NULL,
  `ci` int(150) NOT NULL,
  `extension` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `f_nac` date NOT NULL,
  `sexo` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `estcivil` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `freg` datetime NOT NULL,
  `estado` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id`, `nombres`, `apellidos`, `telefono`, `tipoper`, `ci`, `extension`, `f_nac`, `sexo`, `estcivil`, `freg`, `estado`) VALUES
(1, 'JUAN JOSE', 'VICENTE COSSIO', '65858688', 1, 816280, 'sc', '1990-12-09', 'M', 'casado', '2026-08-17 19:52:23', 1),
(2, 'Mario', 'alcazar', '6585868999', 3, 81628799, 'SC', '2000-08-08', 'M', 'soltero', '2026-08-17 19:53:25', 1),
(3, 'FRANCISCO', 'BAILABA', '12345689', 2, 816287700, 'SC', '1999-09-22', 'M', 'casado', '2026-08-17 19:55:00', 1),
(4, 'RENE', 'MORENO', '666225484', 3, 81628700, 'SC', '1997-08-16', 'M', 'casado', '2026-08-20 10:04:17', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(150) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Docente'),
(3, 'Estudiante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(150) NOT NULL AUTO_INCREMENT,
  `id_persona` int(150) NOT NULL,
  `correo` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `contrase` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `estado` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `id_persona`, `correo`, `contrase`, `estado`) VALUES
(1, 1, 'juanjovico63@gmail.com', '$2y$10$hOkBgHo7kHiYI6V4MNbe7eLipqzS2B9uSOTSragha.831NNXryGS6', 1),
(2, 2, 'mario@gmail.com', '$2y$10$qPom8FkRmd5y8CBquIzTNe8mSipL8SU5y09M2id2EBStOF/QHj4Na', 1),
(3, 3, 'francisco@gmail.com', '$2y$10$a9TnQ3hwvPx05rDciXwZQu06a9rhABTM1XZTFO41Sze4k5n8oibF2', 1),
(4, 4, 'guebelmorenopay@gmail.com', '$2y$10$Udwju1A3x6Amlr10/wipm.evpZNvSDeiF8jEx2aVdOejA1NNqHi/W', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
