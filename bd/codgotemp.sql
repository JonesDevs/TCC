-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 14-Nov-2025 às 18:26
-- Versão do servidor: 10.1.13-MariaDB
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `codgotemp`
--
CREATE DATABASE IF NOT EXISTS `codgotemp` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `codgotemp`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `escolha`
--

CREATE TABLE `escolha` (
  `idForms` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `idLinguagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `linguagem`
--

CREATE TABLE `linguagem` (
  `idLinguagem` int(11) NOT NULL,
  `nomeLinguagem` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `linguagem`
--

INSERT INTO `linguagem` (`idLinguagem`, `nomeLinguagem`) VALUES
(1, 'HTML'),
(2, 'CSS'),
(3, 'JavaScript'),
(4, 'PHP'),
(5, 'C++'),
(6, 'Outros');

-- --------------------------------------------------------

--
-- Estrutura da tabela `professor`
--

CREATE TABLE `professor` (
  `idProfessor` int(11) NOT NULL,
  `nomeProfessor` varchar(50) NOT NULL,
  `idLinguagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nomeUsuario` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `dataCadastro` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nomeUsuario`, `email`, `senha`, `dataCadastro`) VALUES
(1, 'Jones', 'jones@gmail.com', '$2y$10$6GEXjHb5MtVZMgHsjYgbwORgrQ6Q.FTNfFtpKynQ3glwmKuWb72Zu', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `escolha`
--
ALTER TABLE `escolha`
  ADD PRIMARY KEY (`idForms`),
  ADD KEY `id` (`id`),
  ADD KEY `idLinguagem` (`idLinguagem`);

--
-- Indexes for table `linguagem`
--
ALTER TABLE `linguagem`
  ADD PRIMARY KEY (`idLinguagem`);

--
-- Indexes for table `professor`
--
ALTER TABLE `professor`
  ADD PRIMARY KEY (`idProfessor`),
  ADD KEY `idLinguagem` (`idLinguagem`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `escolha`
--
ALTER TABLE `escolha`
  MODIFY `idForms` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `linguagem`
--
ALTER TABLE `linguagem`
  MODIFY `idLinguagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `professor`
--
ALTER TABLE `professor`
  MODIFY `idProfessor` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `escolha`
--
ALTER TABLE `escolha`
  ADD CONSTRAINT `escolha_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `escolha_ibfk_2` FOREIGN KEY (`idLinguagem`) REFERENCES `linguagem` (`idLinguagem`);

--
-- Limitadores para a tabela `professor`
--
ALTER TABLE `professor`
  ADD CONSTRAINT `professor_ibfk_1` FOREIGN KEY (`idLinguagem`) REFERENCES `linguagem` (`idLinguagem`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
