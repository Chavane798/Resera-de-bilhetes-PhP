-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 11, 2024 at 11:29 PM
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_viagem` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_viagem` (`id_viagem`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `carrinho`
--

INSERT INTO `carrinho` (`id`, `id_usuario`, `id_viagem`, `quantidade`) VALUES
(28, 2, 1, 1),
(27, 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE IF NOT EXISTS `reservas` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `id_viagem` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `data_hora_reserva` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reserva`),
  KEY `id_viagem` (`id_viagem`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_viagem`, `id_usuario`, `data_hora_reserva`) VALUES
(1, 2, 2, '2024-09-11 20:47:30'),
(2, 2, 2, '2024-09-11 20:47:42'),
(3, 2, 2, '2024-09-11 21:01:47');

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
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `senha`, `criado_em`) VALUES
(2, 'Gervasio Bernardo Chavane', 'gervasiochavane798@gmail.com', '$2y$10$t2gmv9Vz0FIz2sC5U6v3tOouuLxcXDHLGWAaEW6MIGVx5L5ZFSqCK', '2024-09-11 20:46:59');

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
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bilhetes_disponiveis` int(11) DEFAULT '15',
  PRIMARY KEY (`id_viagem`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `viagens`
--

INSERT INTO `viagens` (`id_viagem`, `destino`, `data_hora`, `preco`, `criado_em`, `bilhetes_disponiveis`) VALUES
(1, 'maputo', '2024-09-11 20:06:43', '800.00', '2024-09-11 20:06:58', 6),
(2, 'gaza', '2024-09-11 20:07:27', '800.00', '2024-09-11 20:07:45', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
