-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 25-10-2013 a las 21:12:13
-- Versión del servidor: 5.5.24-log
-- Versión de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `repositorioqti`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE IF NOT EXISTS `pregunta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(25) COLLATE utf8_spanish_ci NOT NULL,
  `autor` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `institucion` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `herramienta_autor` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL,
  `compatible_mobile` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `nivel_educacion` varchar(8) COLLATE utf8_spanish_ci NOT NULL,
  `materia` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL,
  `curso` int(11) DEFAULT NULL,
  `concepto` varchar(40) COLLATE utf8_spanish_ci NOT NULL,
  `ruta_descarga` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `numero_descargas` int(11) NOT NULL DEFAULT '0',
  `tamano` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=83 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `relacion_tags`
--

CREATE TABLE IF NOT EXISTS `relacion_tags` (
  `fk_id` int(11) NOT NULL,
  `fk_palabra` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  KEY `fk_id` (`fk_id`,`fk_palabra`),
  KEY `fk_palabra` (`fk_palabra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `palabra` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`palabra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `relacion_tags`
--
ALTER TABLE `relacion_tags`
  ADD CONSTRAINT `relacion_tags_ibfk_3` FOREIGN KEY (`fk_id`) REFERENCES `pregunta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relacion_tags_ibfk_4` FOREIGN KEY (`fk_palabra`) REFERENCES `tag` (`palabra`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
