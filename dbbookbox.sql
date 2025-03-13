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
  `aluCpf` varchar(11) DEFAULT NULL,
  `aluEmail` varchar(319) NOT NULL,
  `aluTelefone` varchar(20) DEFAULT NULL,
  `aluBloqueado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`aluId`),
  UNIQUE KEY `aluCPF` (`aluCpf`),
  UNIQUE KEY `aluEmail` (`aluEmail`),
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
  KEY `fkAluId` (`fkAluId`),
  KEY `fkTurId` (`fkTurId`),
  CONSTRAINT `tbalunos_turmas_ibfk_1` FOREIGN KEY (`fkAluId`) REFERENCES `tbalunos` (`aluId`),
  CONSTRAINT `tbalunos_turmas_ibfk_2` FOREIGN KEY (`fkTurId`) REFERENCES `tbturmas` (`turId`)
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
  `empDataInicio` date DEFAULT curdate(),
  `empDataFim` date NOT NULL,
  `empDataDevolucao` date DEFAULT NULL,
  `empAtivo` tinyint(1) DEFAULT 1,
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
  `exDisponibilidade` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`exId`),
  KEY `fkLivId` (`fkLivId`),
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
  `livIbsn` varchar(13) DEFAULT NULL,
  `livTitulo` varchar(255) NOT NULL,
  `livAutor` varchar(300) NOT NULL,
  `fkGenId` binary(16) NOT NULL,
  `livEditora` varchar(150) NOT NULL,
  PRIMARY KEY (`livId`),
  UNIQUE KEY `livIBSN` (`livIbsn`),
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
  `turPeriodo` int(11) NOT NULL,
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
  UNIQUE KEY `usuEmail` (`usuEmail`),
  UNIQUE KEY `usuEmail_2` (`usuEmail`),
  UNIQUE KEY `usuEmail_3` (`usuEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-13 19:07:49
