-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18/11/2025 às 01:38
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

--
-- Despejando dados para a tabela `escolha`
--

INSERT INTO `escolha` (`idForms`, `id`, `idLinguagem`) VALUES
(1, 3, 1),
(2, 7, 1),
(10, 8, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `licao`
--

CREATE TABLE `licao` (
  `idLicao` int(11) NOT NULL,
  `idLinguagem` int(11) NOT NULL,
  `etapa` enum('intro','l1','l2') NOT NULL DEFAULT 'intro',
  `titulo` varchar(255) NOT NULL,
  `conteudo` longtext NOT NULL,
  `dataCriacao` datetime DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 1,
  `ativa` tinyint(1) DEFAULT 1
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
-- Estrutura para tabela `professor_linguagens`
--

CREATE TABLE `professor_linguagens` (
  `idProfessor` int(11) NOT NULL,
  `idLinguagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professor_linguagens`
--

INSERT INTO `professor_linguagens` (`idProfessor`, `idLinguagem`) VALUES
(6, 1),
(6, 2),
(6, 3),
(6, 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `progresso`
--

CREATE TABLE `progresso` (
  `idProgresso` int(11) NOT NULL,
  `idAluno` int(11) NOT NULL,
  `idLicao` int(11) NOT NULL,
  `concluida` tinyint(1) DEFAULT 0,
  `resposta` text DEFAULT NULL,
  `dataConclusao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nomeUsuario` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` enum('aluno','professor') NOT NULL DEFAULT 'aluno',
  `codigo_professor` varchar(20) DEFAULT NULL,
  `dataCadastro` datetime DEFAULT current_timestamp(),
  `progresso` int(11) NOT NULL DEFAULT 0,
  `ultima_baia` int(11) NOT NULL DEFAULT 0,
  `idLinguagem` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nomeUsuario`, `email`, `senha`, `tipo_usuario`, `codigo_professor`, `dataCadastro`, `progresso`, `ultima_baia`, `idLinguagem`) VALUES
(1, 'Jones', 'jones@gmail.com', '$2y$10$6GEXjHb5MtVZMgHsjYgbwORgrQ6Q.FTNfFtpKynQ3glwmKuWb72Zu', 'aluno', NULL, '2025-11-14 21:18:32', 0, 0, NULL),
(2, 'Joao Pedro', 'jones123@gmail.com', '$2y$10$q6rVYoceMSWyftZ.BTTsoOiLDVgKB.aJdsaIxfm14p/oDi9ngIXhS', 'aluno', NULL, '2025-11-14 21:38:16', 0, 0, NULL),
(3, 'JoaoPedroSMO', 'jpsmo@gmail.com', '$2y$10$MkQYKFPcTz.dkQiEp2oCO.XuSjpQ/RiazI94kh8W4gDCQR49Nwgyq', 'aluno', NULL, '2025-11-14 21:43:35', 67, 1, NULL),
(4, 'Jose Guilherme', 'joseguilher@gmail.com', '$2y$10$44WGEjw/quBpn.iT6K/v7uToPRj0OhJw9x3g.E76m4sbUN20xagXO', 'professor', NULL, '2025-11-15 17:48:07', 0, 0, NULL),
(5, 'Jose Guilherme2', 'joseguilherme2@gmail.com', '$2y$10$I06KnPrDA0AWVhuZQfxsN.wBUo2Yo9yBTrLWaeJgH77nuJOUVUsri', 'professor', NULL, '2025-11-15 18:22:22', 0, 0, NULL),
(6, 'Jose Guilherme3', 'joseguilherme3@gmail.com', '$2y$10$2974RSCp26arjBVcpu7nzuV6Dj83Oy2V6ACIZvU.QEwYWWWofmvGO', 'professor', NULL, '2025-11-15 18:28:10', 0, 0, NULL),
(7, 'joseguilhermito', 'joseguilhermeALUNO@gmail.com', '$2y$10$tkWUWbaoBcYqqcyOUGEoOOzmHU3fxkn4jis3etMd.rPH3njelJFvS', 'aluno', NULL, '2025-11-17 20:31:23', 0, 0, NULL),
(8, 'Matheus Nogueira', 'matheusnoggers@gmail.com', '$2y$10$Jy32cvEDlrB5xCh8gRaVmegO1MHYoBI7k.WeVtrzkhzZVn8hnd1x2', 'aluno', NULL, '2025-11-17 21:13:40', 0, 0, 1);

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
-- Índices de tabela `licao`
--
ALTER TABLE `licao`
  ADD PRIMARY KEY (`idLicao`),
  ADD KEY `fk_licao_linguagem` (`idLinguagem`);

--
-- Índices de tabela `linguagem`
--
ALTER TABLE `linguagem`
  ADD PRIMARY KEY (`idLinguagem`);

--
-- Índices de tabela `professor_linguagens`
--
ALTER TABLE `professor_linguagens`
  ADD PRIMARY KEY (`idProfessor`,`idLinguagem`),
  ADD KEY `idLinguagem` (`idLinguagem`);

--
-- Índices de tabela `progresso`
--
ALTER TABLE `progresso`
  ADD PRIMARY KEY (`idProgresso`),
  ADD KEY `idAluno` (`idAluno`),
  ADD KEY `progresso_ibfk_2` (`idLicao`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_linguagem` (`idLinguagem`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `escolha`
--
ALTER TABLE `escolha`
  MODIFY `idForms` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `licao`
--
ALTER TABLE `licao`
  MODIFY `idLicao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `linguagem`
--
ALTER TABLE `linguagem`
  MODIFY `idLinguagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `progresso`
--
ALTER TABLE `progresso`
  MODIFY `idProgresso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `escolha`
--
ALTER TABLE `escolha`
  ADD CONSTRAINT `escolha_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `escolha_ibfk_2` FOREIGN KEY (`idLinguagem`) REFERENCES `linguagem` (`idLinguagem`);

--
-- Restrições para tabelas `licao`
--
ALTER TABLE `licao`
  ADD CONSTRAINT `fk_licao_linguagem` FOREIGN KEY (`idLinguagem`) REFERENCES `linguagem` (`idLinguagem`) ON DELETE CASCADE;

--
-- Restrições para tabelas `professor_linguagens`
--
ALTER TABLE `professor_linguagens`
  ADD CONSTRAINT `professor_linguagens_ibfk_1` FOREIGN KEY (`idProfessor`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `professor_linguagens_ibfk_2` FOREIGN KEY (`idLinguagem`) REFERENCES `linguagem` (`idLinguagem`);

--
-- Restrições para tabelas `progresso`
--
ALTER TABLE `progresso`
  ADD CONSTRAINT `progresso_ibfk_1` FOREIGN KEY (`idAluno`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `progresso_ibfk_2` FOREIGN KEY (`idLicao`) REFERENCES `licao` (`idLicao`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_linguagem` FOREIGN KEY (`idLinguagem`) REFERENCES `linguagem` (`idLinguagem`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
