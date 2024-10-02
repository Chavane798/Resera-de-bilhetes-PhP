-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 02, 2024 at 09:36 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reserva_bilhetes`
--

-- --------------------------------------------------------

--
-- Table structure for table `carrinho`
--

DROP TABLE IF EXISTS `carrinho`;
CREATE TABLE IF NOT EXISTS `carrinho` (
  `id_usuario` int(11) NOT NULL,
  `id_viagem` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_viagem`),
  KEY `id_viagem` (`id_viagem`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE IF NOT EXISTS `reservas` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_viagem` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `data_reserva` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('ativa','cancelada') DEFAULT 'ativa',
  PRIMARY KEY (`id_reserva`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_viagem` (`id_viagem`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_usuario`, `id_viagem`, `quantidade`, `data_reserva`, `status`) VALUES
(1, 7, 1, 1, '2024-10-02 23:33:54', 'ativa'),
(2, 7, 1, 2, '2024-10-02 23:35:11', 'ativa');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `is_gestor` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `senha`, `is_gestor`) VALUES
(7, 'patrÃ­cia', 'gervasiochavane@gmail.com', '$2y$10$fNhK3Y3MwZRrHh6Ti6axKOWWel3ZD0r2omqHwNUyZga3kt5JAhL9.', 0),
(6, 'GervÃ¡sio', 'gervasiochavane798@gmail.com', '$2y$10$8TKX54qS1DNl4XW26a/mkOqiE1aa3uqjGVdwIL7pQ.8WaA5p3255a', 1);

-- --------------------------------------------------------

--
-- Table structure for table `viagens`
--

DROP TABLE IF EXISTS `viagens`;
CREATE TABLE IF NOT EXISTS `viagens` (
  `id_viagem` int(11) NOT NULL AUTO_INCREMENT,
  `destino` varchar(100) NOT NULL,
  `data_hora` datetime NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `bilhetes_disponiveis` int(11) NOT NULL,
  PRIMARY KEY (`id_viagem`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `viagens`
--

INSERT INTO `viagens` (`id_viagem`, `destino`, `data_hora`, `preco`, `bilhetes_disponiveis`) VALUES
(1, 'gaza', '2024-10-17 09:00:00', '800.00', 4);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
