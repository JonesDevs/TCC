-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21-Maio-2025 às 22:26
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `codgotemp`
--
CREATE DATABASE IF NOT EXISTS `codgotemp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `codgotemp`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `escolha`
--

CREATE TABLE `escolha` (
  `idForms` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `idLinguagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `linguagem`
--

CREATE TABLE `linguagem` (
  `idLinguagem` int(11) NOT NULL,
  `nomeLinguagem` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `escolha`
--
ALTER TABLE `escolha`
  ADD PRIMARY KEY (`idForms`),
  ADD KEY `id` (`id`),
  ADD KEY `idLinguagem` (`idLinguagem`);

--
-- Índices para tabela `linguagem`
--
ALTER TABLE `linguagem`
  ADD PRIMARY KEY (`idLinguagem`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `escolha`
--
ALTER TABLE `escolha`
  MODIFY `idForms` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `linguagem`
--
ALTER TABLE `linguagem`
  MODIFY `idLinguagem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `escolha`
--
ALTER TABLE `escolha`
  ADD CONSTRAINT `escolha_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `escolha_ibfk_2` FOREIGN KEY (`idLinguagem`) REFERENCES `linguagem` (`idLinguagem`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
