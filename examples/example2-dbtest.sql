-- phpMyAdmin SQL Dump
-- version 4.3.7
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 14-02-2015 a las 18:57:14
-- Versión del servidor: 5.5.38
-- Versión de PHP: 5.5.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `dbtest`
--
CREATE DATABASE IF NOT EXISTS `dbtest` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `dbtest`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auto`
--

CREATE TABLE IF NOT EXISTS `auto` (
  `id` int(11) NOT NULL,
  `model_family` varchar(20) NOT NULL,
  `color` varchar(20) NOT NULL,
  `state` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `auto`
--

INSERT INTO `auto` (`id`, `model_family`, `color`, `state`) VALUES
(1, 'Sedan', 'Blue', 2),
(2, 'Sedan', 'Blue', 2),
(3, 'Sedan', 'Blue', 1),
(4, 'Sedan', 'Green', 1),
(5, 'Sedan', 'Green', 1),
(6, 'Sedan', 'Green', 2),
(7, 'Sedan', 'Green', 1),
(8, 'Sedan', 'Green', 1),
(9, 'Sedan', 'Red', 2),
(10, 'Sedan', 'Red', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auto`
--
ALTER TABLE `auto`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auto`
--
ALTER TABLE `auto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
