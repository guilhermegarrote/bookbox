-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/08/2024 às 02:51
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dbbookbox`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbalunos`
--

CREATE TABLE `tbalunos` (
  `aluId` int(11) NOT NULL,
  `aluNome` varchar(150) NOT NULL,
  `aluEmail` varchar(255) NOT NULL,
  `aluTelefone` varchar(20) NOT NULL,
  `aluStatus` tinyint(1) DEFAULT 1,
  `fkSalaId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbcurso`
--

CREATE TABLE `tbcurso` (
  `curId` int(11) NOT NULL,
  `curNome` varchar(150) NOT NULL,
  `curPeriodo` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbemprestimos`
--

CREATE TABLE `tbemprestimos` (
  `empId` int(11) NOT NULL,
  `empData_Inicial` date DEFAULT curdate(),
  `empData_Final` date NOT NULL,
  `empDias_Atraso` int(11) NOT NULL,
  `fkAlunoId` int(11) NOT NULL,
  `fkLivroId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbgeneros`
--

CREATE TABLE `tbgeneros` (
  `genId` int(11) NOT NULL,
  `genNome` varchar(100) NOT NULL,
  `genCor` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tblivros`
--

CREATE TABLE `tblivros` (
  `livId` int(11) NOT NULL,
  `livCod` int(11) NOT NULL,
  `livTitulo` varchar(255) NOT NULL,
  `livSubtitulo` varchar(255) DEFAULT NULL,
  `livAutor` varchar(200) NOT NULL,
  `livEditora` varchar(150) NOT NULL,
  `livExemplar` int(11) NOT NULL,
  `livDisponibilidade` tinyint(1) DEFAULT 1,
  `fkGeneroId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbsalas`
--

CREATE TABLE `tbsalas` (
  `salId` int(11) NOT NULL,
  `salSerieModulo` int(11) NOT NULL,
  `fkCurId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbusuarios`
--

CREATE TABLE `tbusuarios` (
  `usuId` int(11) NOT NULL,
  `usuNome` varchar(150) NOT NULL,
  `usuSenha` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tbalunos`
--
ALTER TABLE `tbalunos`
  ADD PRIMARY KEY (`aluId`),
  ADD UNIQUE KEY `aluEmail` (`aluEmail`),
  ADD UNIQUE KEY `aluTelefone` (`aluTelefone`),
  ADD KEY `fkSalaId` (`fkSalaId`);

--
-- Índices de tabela `tbcurso`
--
ALTER TABLE `tbcurso`
  ADD PRIMARY KEY (`curId`);

--
-- Índices de tabela `tbemprestimos`
--
ALTER TABLE `tbemprestimos`
  ADD PRIMARY KEY (`empId`),
  ADD UNIQUE KEY `fkLivroId` (`fkLivroId`),
  ADD KEY `fkAlunoId` (`fkAlunoId`);

--
-- Índices de tabela `tbgeneros`
--
ALTER TABLE `tbgeneros`
  ADD PRIMARY KEY (`genId`);

--
-- Índices de tabela `tblivros`
--
ALTER TABLE `tblivros`
  ADD PRIMARY KEY (`livId`),
  ADD UNIQUE KEY `livCod` (`livCod`),
  ADD KEY `fkGeneroId` (`fkGeneroId`);

--
-- Índices de tabela `tbsalas`
--
ALTER TABLE `tbsalas`
  ADD PRIMARY KEY (`salId`),
  ADD KEY `fkCurId` (`fkCurId`);

--
-- Índices de tabela `tbusuarios`
--
ALTER TABLE `tbusuarios`
  ADD PRIMARY KEY (`usuId`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tbalunos`
--
ALTER TABLE `tbalunos`
  MODIFY `aluId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbcurso`
--
ALTER TABLE `tbcurso`
  MODIFY `curId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbemprestimos`
--
ALTER TABLE `tbemprestimos`
  MODIFY `empId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbgeneros`
--
ALTER TABLE `tbgeneros`
  MODIFY `genId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tblivros`
--
ALTER TABLE `tblivros`
  MODIFY `livId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbsalas`
--
ALTER TABLE `tbsalas`
  MODIFY `salId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbusuarios`
--
ALTER TABLE `tbusuarios`
  MODIFY `usuId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tbalunos`
--
ALTER TABLE `tbalunos`
  ADD CONSTRAINT `tbalunos_ibfk_1` FOREIGN KEY (`fkSalaId`) REFERENCES `tbsalas` (`salId`);

--
-- Restrições para tabelas `tbemprestimos`
--
ALTER TABLE `tbemprestimos`
  ADD CONSTRAINT `tbemprestimos_ibfk_1` FOREIGN KEY (`fkAlunoId`) REFERENCES `tbalunos` (`aluId`),
  ADD CONSTRAINT `tbemprestimos_ibfk_2` FOREIGN KEY (`fkLivroId`) REFERENCES `tblivros` (`livId`);

--
-- Restrições para tabelas `tblivros`
--
ALTER TABLE `tblivros`
  ADD CONSTRAINT `tblivros_ibfk_1` FOREIGN KEY (`fkGeneroId`) REFERENCES `tbgeneros` (`genId`);

--
-- Restrições para tabelas `tbsalas`
--
ALTER TABLE `tbsalas`
  ADD CONSTRAINT `tbsalas_ibfk_1` FOREIGN KEY (`fkCurId`) REFERENCES `tbcurso` (`curId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
