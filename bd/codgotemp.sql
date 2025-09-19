-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/09/2025 às 04:49
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `escolha`
--

CREATE TABLE `escolha` (
  `idForms` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `idLinguagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `linguagem`
--

CREATE TABLE `linguagem` (
  `idLinguagem` int(11) NOT NULL,
  `nomeLinguagem` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `linguagem`
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
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nomeUsuario` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `dataCadastro` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nomeUsuario`, `email`, `senha`, `dataCadastro`) VALUES
(1, 'Jones', 'jones@gmail.com', '$2y$10$6GEXjHb5MtVZMgHsjYgbwORgrQ6Q.FTNfFtpKynQ3glwmKuWb72Zu', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `escolha`
--
ALTER TABLE `escolha`
  ADD PRIMARY KEY (`idForms`),
  ADD KEY `id` (`id`),
  ADD KEY `idLinguagem` (`idLinguagem`);

--
-- Índices de tabela `linguagem`
--
ALTER TABLE `linguagem`
  ADD PRIMARY KEY (`idLinguagem`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `escolha`
--
ALTER TABLE `escolha`
  MODIFY `idForms` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `linguagem`
--
ALTER TABLE `linguagem`
  MODIFY `idLinguagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `escolha`
--
ALTER TABLE `escolha`
  ADD CONSTRAINT `escolha_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `escolha_ibfk_2` FOREIGN KEY (`idLinguagem`) REFERENCES `linguagem` (`idLinguagem`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

