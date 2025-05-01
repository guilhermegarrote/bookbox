-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: dbbookbox
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbalunos`
--

DROP TABLE IF EXISTS `tbalunos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbalunos` (
  `aluId` binary(16) NOT NULL,
  `aluNome` varchar(100) NOT NULL,
  `aluCpf` varchar(11) NOT NULL,
  `aluEmail` varchar(319) NOT NULL,
  `aluTelefone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`aluId`),
  UNIQUE KEY `aluEmail` (`aluEmail`),
  UNIQUE KEY `aluCpf` (`aluCpf`),
  UNIQUE KEY `aluTelefone` (`aluTelefone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbalunos_turmas`
--

DROP TABLE IF EXISTS `tbalunos_turmas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbalunos_turmas` (
  `aluTurId` binary(16) NOT NULL,
  `fkAluId` binary(16) NOT NULL,
  `fkTurId` binary(16) NOT NULL,
  PRIMARY KEY (`aluTurId`),
  UNIQUE KEY `fkAluId` (`fkAluId`,`fkTurId`),
  KEY `fkTurId` (`fkTurId`),
  CONSTRAINT `tbalunos_turmas_ibfk_1` FOREIGN KEY (`fkAluId`) REFERENCES `tbalunos` (`aluId`),
  CONSTRAINT `tbalunos_turmas_ibfk_2` FOREIGN KEY (`fkTurId`) REFERENCES `tbturmas` (`turId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbcodigos_redefinicao_senha`
--

DROP TABLE IF EXISTS `tbcodigos_redefinicao_senha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbcodigos_redefinicao_senha` (
  `codId` binary(16) NOT NULL,
  `fkUsuId` binary(16) NOT NULL,
  `codValor` varchar(60) NOT NULL,
  `codExpiracao` datetime NOT NULL,
  PRIMARY KEY (`codId`),
  KEY `fkUsuId` (`fkUsuId`),
  CONSTRAINT `tbcodigos_redefinicao_senha_ibfk_1` FOREIGN KEY (`fkUsuId`) REFERENCES `tbusuarios` (`usuId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbconfiguracoes`
--

DROP TABLE IF EXISTS `tbconfiguracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbconfiguracoes` (
  `conId` binary(16) NOT NULL,
  `conChave` varchar(255) NOT NULL,
  `conValor` varchar(255) NOT NULL,
  PRIMARY KEY (`conId`),
  UNIQUE KEY `conChave` (`conChave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbemprestimos`
--

DROP TABLE IF EXISTS `tbemprestimos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbemprestimos` (
  `empId` binary(16) NOT NULL,
  `fkAluId` binary(16) NOT NULL,
  `fkExId` binary(16) NOT NULL,
  `empDataInicio` date NOT NULL DEFAULT curdate(),
  `empDataFim` date NOT NULL,
  `empDataDevolucao` date DEFAULT NULL,
  `empAtivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`empId`),
  UNIQUE KEY `fkExId` (`fkExId`),
  KEY `fkAluId` (`fkAluId`),
  CONSTRAINT `fkAluId` FOREIGN KEY (`fkAluId`) REFERENCES `tbalunos` (`aluId`),
  CONSTRAINT `fkExId` FOREIGN KEY (`fkExId`) REFERENCES `tbexemplares` (`exId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbexemplares`
--

DROP TABLE IF EXISTS `tbexemplares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbexemplares` (
  `exId` binary(16) NOT NULL,
  `fkLivId` binary(16) NOT NULL,
  `exNumero` int(11) NOT NULL,
  `exDisponibilidade` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`exId`),
  UNIQUE KEY `fkLivId` (`fkLivId`,`exNumero`),
  CONSTRAINT `fkLivId` FOREIGN KEY (`fkLivId`) REFERENCES `tblivros` (`livId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbgeneros`
--

DROP TABLE IF EXISTS `tbgeneros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbgeneros` (
  `genId` binary(16) NOT NULL,
  `genNome` varchar(100) NOT NULL,
  `genCorHex` char(7) NOT NULL,
  PRIMARY KEY (`genId`),
  UNIQUE KEY `genNome` (`genNome`),
  UNIQUE KEY `genCorHex` (`genCorHex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblivros`
--

DROP TABLE IF EXISTS `tblivros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblivros` (
  `livId` binary(16) NOT NULL,
  `livIsbn` varchar(13) NOT NULL,
  `livTitulo` varchar(255) NOT NULL,
  `livAutor` varchar(300) NOT NULL,
  `fkGenId` binary(16) NOT NULL,
  `livEditora` varchar(150) NOT NULL,
  PRIMARY KEY (`livId`),
  UNIQUE KEY `livTitulo` (`livTitulo`,`livAutor`,`livEditora`),
  UNIQUE KEY `livIsbn` (`livIsbn`),
  KEY `fkGeneroId` (`fkGenId`),
  CONSTRAINT `fkGeneroId` FOREIGN KEY (`fkGenId`) REFERENCES `tbgeneros` (`genId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbturmas`
--

DROP TABLE IF EXISTS `tbturmas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbturmas` (
  `turId` binary(16) NOT NULL,
  `turCurso` varchar(150) NOT NULL,
  `turHorario` enum('Matutino','Vespertino','Noturno') NOT NULL,
  `turRegime` enum('Anual','Semestral') NOT NULL,
  `turDataInicio` date NOT NULL,
  `turDataFim` date NOT NULL,
  PRIMARY KEY (`turId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbusuarios`
--

DROP TABLE IF EXISTS `tbusuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbusuarios` (
  `usuId` binary(16) NOT NULL,
  `usuNome` varchar(100) NOT NULL,
  `usuEmail` varchar(512) NOT NULL,
  `usuSenha` varchar(60) NOT NULL,
  PRIMARY KEY (`usuId`),
  UNIQUE KEY `usuEmail` (`usuEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `vwalunos`
--

DROP TABLE IF EXISTS `vwalunos`;
/*!50001 DROP VIEW IF EXISTS `vwalunos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwalunos` AS SELECT 
 1 AS `aluId`,
 1 AS `aluNome`,
 1 AS `podeEmprestar`,
 1 AS `turId`,
 1 AS `turCurso`,
 1 AS `turHorario`,
 1 AS `turRegime`,
 1 AS `turPeriodo`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vwexemplares`
--

DROP TABLE IF EXISTS `vwexemplares`;
/*!50001 DROP VIEW IF EXISTS `vwexemplares`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwexemplares` AS SELECT 
 1 AS `exId`,
 1 AS `livId`,
 1 AS `livIsbn`,
 1 AS `livTitulo`,
 1 AS `livAutor`,
 1 AS `genId`,
 1 AS `genNome`,
 1 AS `genCorHex`,
 1 AS `livEditora`,
 1 AS `exNumero`,
 1 AS `exDisponibilidade`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vwlivros`
--

DROP TABLE IF EXISTS `vwlivros`;
/*!50001 DROP VIEW IF EXISTS `vwlivros`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwlivros` AS SELECT 
 1 AS `livId`,
 1 AS `livIsbn`,
 1 AS `livTitulo`,
 1 AS `livAutor`,
 1 AS `genId`,
 1 AS `genNome`,
 1 AS `genCorHex`,
 1 AS `livEditora`,
 1 AS `disponibilidade`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vwturmas`
--

DROP TABLE IF EXISTS `vwturmas`;
/*!50001 DROP VIEW IF EXISTS `vwturmas`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwturmas` AS SELECT 
 1 AS `turId`,
 1 AS `turCurso`,
 1 AS `turHorario`,
 1 AS `turRegime`,
 1 AS `turDataInicio`,
 1 AS `turDataFim`,
 1 AS `turPeriodo`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vwalunos`
--

/*!50001 DROP VIEW IF EXISTS `vwalunos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwalunos` AS select `tbalunos`.`aluId` AS `aluId`,`tbalunos`.`aluNome` AS `aluNome`,case when count(distinct `tbemprestimos`.`empId`) < (select `tbconfiguracoes`.`conValor` from `tbconfiguracoes` where `tbconfiguracoes`.`conChave` = 'livrosMaximosEmprestimo' limit 1) and !exists(select 1 from `tbemprestimos` where `tbemprestimos`.`fkAluId` = `tbalunos`.`aluId` and `tbemprestimos`.`empAtivo` is true and `tbemprestimos`.`empDataDevolucao` < curdate() limit 1) then 1 else 0 end AS `podeEmprestar`,`vwturmas`.`turId` AS `turId`,`vwturmas`.`turCurso` AS `turCurso`,`vwturmas`.`turHorario` AS `turHorario`,`vwturmas`.`turRegime` AS `turRegime`,`vwturmas`.`turPeriodo` AS `turPeriodo` from (((`tbalunos` left join `tbemprestimos` on(`tbemprestimos`.`fkAluId` = `tbalunos`.`aluId` and `tbemprestimos`.`empAtivo` is true)) left join `tbalunos_turmas` on(`tbalunos_turmas`.`fkAluId` = `tbalunos`.`aluId`)) left join `vwturmas` on(`vwturmas`.`turId` = `tbalunos_turmas`.`fkTurId`)) group by `tbalunos`.`aluId`,`tbalunos`.`aluNome`,`tbalunos`.`aluEmail`,`tbalunos`.`aluTelefone`,`vwturmas`.`turId`,`vwturmas`.`turCurso`,`vwturmas`.`turHorario`,`vwturmas`.`turRegime`,`vwturmas`.`turPeriodo` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwexemplares`
--

/*!50001 DROP VIEW IF EXISTS `vwexemplares`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwexemplares` AS select `ex`.`exId` AS `exId`,`l`.`livId` AS `livId`,`l`.`livIsbn` AS `livIsbn`,`l`.`livTitulo` AS `livTitulo`,`l`.`livAutor` AS `livAutor`,`l`.`genId` AS `genId`,`l`.`genNome` AS `genNome`,`l`.`genCorHex` AS `genCorHex`,`l`.`livEditora` AS `livEditora`,`ex`.`exNumero` AS `exNumero`,`ex`.`exDisponibilidade` AS `exDisponibilidade` from (`tbexemplares` `ex` join `vwlivros` `l` on(`ex`.`fkLivId` = `l`.`livId`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwlivros`
--

/*!50001 DROP VIEW IF EXISTS `vwlivros`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwlivros` AS select `l`.`livId` AS `livId`,`l`.`livIsbn` AS `livIsbn`,`l`.`livTitulo` AS `livTitulo`,`l`.`livAutor` AS `livAutor`,`g`.`genId` AS `genId`,`g`.`genNome` AS `genNome`,`g`.`genCorHex` AS `genCorHex`,`l`.`livEditora` AS `livEditora`,concat(sum(case when `ex`.`exDisponibilidade` = 1 then 1 else 0 end),'/',count(0)) AS `disponibilidade` from ((`tblivros` `l` join `tbgeneros` `g` on(`l`.`fkGenId` = `g`.`genId`)) left join `tbexemplares` `ex` on(`ex`.`fkLivId` = `l`.`livId`)) group by `l`.`livId`,`l`.`livIsbn`,`l`.`livTitulo`,`l`.`livAutor`,`g`.`genId`,`g`.`genNome`,`g`.`genCorHex`,`l`.`livEditora` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwturmas`
--

/*!50001 DROP VIEW IF EXISTS `vwturmas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwturmas` AS select `tbturmas`.`turId` AS `turId`,`tbturmas`.`turCurso` AS `turCurso`,`tbturmas`.`turHorario` AS `turHorario`,`tbturmas`.`turRegime` AS `turRegime`,`tbturmas`.`turDataInicio` AS `turDataInicio`,`tbturmas`.`turDataFim` AS `turDataFim`,case when `tbturmas`.`turRegime` = 'Anual' then timestampdiff(YEAR,`tbturmas`.`turDataInicio`,curdate()) when `tbturmas`.`turRegime` = 'Semestral' then timestampdiff(MONTH,`tbturmas`.`turDataInicio`,curdate()) DIV 6 else NULL end AS `turPeriodo` from `tbturmas` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-01 16:49:29
