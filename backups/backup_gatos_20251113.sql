-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: gatos_mallorca
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Ayuntamiento`
--

DROP TABLE IF EXISTS `Ayuntamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ayuntamiento` (
  `idAyuntamiento` int(11) NOT NULL AUTO_INCREMENT,
  `nombreLocalidad` varchar(70) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasenya` varchar(255) NOT NULL,
  `idProvincia` int(11) NOT NULL,
  PRIMARY KEY (`idAyuntamiento`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `idProvincia` (`idProvincia`),
  CONSTRAINT `Ayuntamiento_ibfk_1` FOREIGN KEY (`idProvincia`) REFERENCES `Provincia` (`idProvincia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ayuntamiento`
--

LOCK TABLES `Ayuntamiento` WRITE;
/*!40000 ALTER TABLE `Ayuntamiento` DISABLE KEYS */;
INSERT INTO `Ayuntamiento` VALUES (1,'Palma','palma','palma123',1),(2,'Calvià','calvia','calvia123',1),(3,'Manacor','manacor','manacor123',1);
/*!40000 ALTER TABLE `Ayuntamiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BolsaMunicipal`
--

DROP TABLE IF EXISTS `BolsaMunicipal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BolsaMunicipal` (
  `idBolsaMunicipal` int(11) NOT NULL AUTO_INCREMENT,
  `idAyuntamiento` int(11) NOT NULL,
  PRIMARY KEY (`idBolsaMunicipal`),
  KEY `idAyuntamiento` (`idAyuntamiento`),
  CONSTRAINT `BolsaMunicipal_ibfk_1` FOREIGN KEY (`idAyuntamiento`) REFERENCES `Ayuntamiento` (`idAyuntamiento`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BolsaMunicipal`
--

LOCK TABLES `BolsaMunicipal` WRITE;
/*!40000 ALTER TABLE `BolsaMunicipal` DISABLE KEYS */;
INSERT INTO `BolsaMunicipal` VALUES (1,1),(2,2),(3,3);
/*!40000 ALTER TABLE `BolsaMunicipal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Colonia`
--

DROP TABLE IF EXISTS `Colonia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Colonia` (
  `idColonia` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `idUbicacion` int(11) NOT NULL,
  `idAyuntamiento` int(11) NOT NULL,
  PRIMARY KEY (`idColonia`),
  KEY `idUbicacion` (`idUbicacion`),
  KEY `idAyuntamiento` (`idAyuntamiento`),
  CONSTRAINT `Colonia_ibfk_1` FOREIGN KEY (`idUbicacion`) REFERENCES `Ubicacion` (`idUbicacion`) ON DELETE CASCADE,
  CONSTRAINT `Colonia_ibfk_2` FOREIGN KEY (`idAyuntamiento`) REFERENCES `Ayuntamiento` (`idAyuntamiento`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Colonia`
--

LOCK TABLES `Colonia` WRITE;
/*!40000 ALTER TABLE `Colonia` DISABLE KEYS */;
INSERT INTO `Colonia` VALUES (1,'Colonia A - Palma Centro','Cerca de la fuente, gatos amigables.',1,1),(2,'Colonia B - Palma Oeste','Zona portuaria, gatos más esquivos.',2,1),(3,'Colonia C - Calvià Costa','Junto a restaurantes, muchos turistas.',3,2),(4,'Colonia D - Calvià Interior','Zona residencial, gatos bien cuidados.',4,2),(5,'Colonia E - Manacor Norte','Cerca del mercado, gatos callejeros.',5,3),(6,'Colonia F - Manacor Sur','Polígono industrial, gatos salvajes.',6,3);
/*!40000 ALTER TABLE `Colonia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Coordenada`
--

DROP TABLE IF EXISTS `Coordenada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Coordenada` (
  `idCoordenada` int(11) NOT NULL AUTO_INCREMENT,
  `latitud` decimal(9,6) NOT NULL,
  `longitud` decimal(9,6) NOT NULL,
  PRIMARY KEY (`idCoordenada`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Coordenada`
--

LOCK TABLES `Coordenada` WRITE;
/*!40000 ALTER TABLE `Coordenada` DISABLE KEYS */;
INSERT INTO `Coordenada` VALUES (1,39.570000,2.650000),(2,39.560000,2.640000),(3,39.520000,2.530000),(4,39.510000,2.520000),(5,39.570000,3.210000),(6,39.560000,3.200000);
/*!40000 ALTER TABLE `Coordenada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Estancia`
--

DROP TABLE IF EXISTS `Estancia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Estancia` (
  `idEstancia` int(11) NOT NULL AUTO_INCREMENT,
  `fechaInicio` date NOT NULL,
  `fechaFin` date DEFAULT NULL,
  `idGato` int(11) NOT NULL,
  `idColonia` int(11) NOT NULL,
  PRIMARY KEY (`idEstancia`),
  KEY `idGato` (`idGato`),
  KEY `idColonia` (`idColonia`),
  CONSTRAINT `Estancia_ibfk_1` FOREIGN KEY (`idGato`) REFERENCES `Gato` (`idGato`) ON DELETE CASCADE,
  CONSTRAINT `Estancia_ibfk_2` FOREIGN KEY (`idColonia`) REFERENCES `Colonia` (`idColonia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Estancia`
--

LOCK TABLES `Estancia` WRITE;
/*!40000 ALTER TABLE `Estancia` DISABLE KEYS */;
INSERT INTO `Estancia` VALUES (1,'2024-01-01',NULL,1,1),(2,'2024-01-01',NULL,2,1),(3,'2024-01-01',NULL,3,1),(4,'2024-01-01',NULL,4,1),(5,'2024-02-01',NULL,5,2),(6,'2024-02-01',NULL,6,2),(7,'2024-02-01',NULL,7,2),(8,'2024-02-01',NULL,8,2),(9,'2024-03-01',NULL,9,3),(10,'2024-03-01',NULL,10,3),(11,'2024-03-01',NULL,11,3),(12,'2024-03-01',NULL,12,3),(13,'2024-04-01',NULL,13,4),(14,'2024-04-01',NULL,14,4),(15,'2024-04-01',NULL,15,4),(16,'2024-05-01',NULL,16,5),(17,'2024-05-01',NULL,17,5),(18,'2024-06-01',NULL,18,6),(19,'2024-06-01',NULL,19,6),(20,'2024-06-01',NULL,20,6),(21,'2025-11-13',NULL,21,1),(22,'2025-11-13',NULL,22,1);
/*!40000 ALTER TABLE `Estancia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Gato`
--

DROP TABLE IF EXISTS `Gato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Gato` (
  `idGato` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) DEFAULT NULL,
  `descripcionAspecto` text DEFAULT NULL,
  `numeroChip` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `idSexo` int(11) NOT NULL,
  PRIMARY KEY (`idGato`),
  UNIQUE KEY `numeroChip` (`numeroChip`),
  KEY `idSexo` (`idSexo`),
  CONSTRAINT `Gato_ibfk_1` FOREIGN KEY (`idSexo`) REFERENCES `Sexo` (`idSexo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Gato`
--

LOCK TABLES `Gato` WRITE;
/*!40000 ALTER TABLE `Gato` DISABLE KEYS */;
INSERT INTO `Gato` VALUES (1,'Luna','Gata tricolor, muy cariñosa.','1001','luna.jpg',2),(2,'Sol','Gato atigrado, grande y tranquilo.','1002','sol.jpg',1),(3,'Milo','Gato negro, ojos verdes, juguetón.','1003','milo.jpg',1),(4,'Nala','Gata siamesa, muy activa.','1004','nala.jpg',2),(5,'Simba','Gato naranja, pelaje denso.','1005','simba.jpg',1),(6,'Cleo','Gata blanca y negra, elegante.','1006','cleo.jpg',2),(7,'Leo','Gato gris, con manchas blancas.','1007','leo.jpg',1),(8,'Mia','Gata pequeña, muy asustadiza.','1008','mia.jpg',2),(9,'Max','Gato blanco, con un ojo azul.','1009','max.jpg',1),(10,'Kitty','Gata persa, pelo largo.','1010','kitty.jpg',2),(11,'Rocky','Gato callejero, oreja rota.','1011','rocky.jpg',1),(12,'Daisy','Gata joven, muy curiosa.','1012','daisy.jpg',2),(13,'Tom','Gato gordo, le encanta comer.','1013','tom.jpg',1),(14,'Jerry','Gato pequeño, muy rápido.','1014','jerry.jpg',1),(15,'Bella','Gata de angora, muy mimosa.','1015','bella.jpg',2),(16,'Coco','Gato marrón, ojos ámbar.','1016','coco.jpg',1),(17,'Zoe','Gata negra, muy sigilosa.','1017','zoe.jpg',2),(18,'Oliver','Gato atigrado, muy sociable.','1018','oliver.jpg',1),(19,'Ruby','Gata pelirroja, muy juguetona.','1019','ruby.jpg',2),(20,'Jasper','Gato blanco y gris, tranquilo.','1020','jasper.jpg',1),(21,'cacona','caca','1050','69163e8549db7_pepe.jpg',1),(22,'pepe','pepe','1240','69163ea07eda6_pepe.jpg',1);
/*!40000 ALTER TABLE `Gato` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Grupo`
--

DROP TABLE IF EXISTS `Grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Grupo` (
  `idGrupo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreGrupo` varchar(50) NOT NULL,
  `idAyuntamiento` int(11) NOT NULL,
  PRIMARY KEY (`idGrupo`),
  KEY `idAyuntamiento` (`idAyuntamiento`),
  CONSTRAINT `Grupo_ibfk_1` FOREIGN KEY (`idAyuntamiento`) REFERENCES `Ayuntamiento` (`idAyuntamiento`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Grupo`
--

LOCK TABLES `Grupo` WRITE;
/*!40000 ALTER TABLE `Grupo` DISABLE KEYS */;
INSERT INTO `Grupo` VALUES (1,'Grupo Alfa Palma',1),(2,'Grupo Beta Calvià',2);
/*!40000 ALTER TABLE `Grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Incidencia`
--

DROP TABLE IF EXISTS `Incidencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Incidencia` (
  `idIncidencia` int(11) NOT NULL AUTO_INCREMENT,
  `textoDescriptivo` text NOT NULL,
  PRIMARY KEY (`idIncidencia`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Incidencia`
--

LOCK TABLES `Incidencia` WRITE;
/*!40000 ALTER TABLE `Incidencia` DISABLE KEYS */;
INSERT INTO `Incidencia` VALUES (1,'Gato enfermo, necesita atención veterinaria.'),(2,'Gato nuevo en la colonia, posible abandono.'),(3,'Pelea entre gatos, heridas leves.'),(4,'Falta de comida en el punto de alimentación.'),(5,'Gato desaparecido de la colonia.');
/*!40000 ALTER TABLE `Incidencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IncidenciaVisita`
--

DROP TABLE IF EXISTS `IncidenciaVisita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IncidenciaVisita` (
  `idIncidenciaVisita` int(11) NOT NULL AUTO_INCREMENT,
  `idIncidencia` int(11) NOT NULL,
  `idVisita` int(11) NOT NULL,
  `idGato` int(11) DEFAULT NULL,
  PRIMARY KEY (`idIncidenciaVisita`),
  KEY `idIncidencia` (`idIncidencia`),
  KEY `idVisita` (`idVisita`),
  KEY `idGato` (`idGato`),
  CONSTRAINT `IncidenciaVisita_ibfk_1` FOREIGN KEY (`idIncidencia`) REFERENCES `Incidencia` (`idIncidencia`) ON DELETE CASCADE,
  CONSTRAINT `IncidenciaVisita_ibfk_2` FOREIGN KEY (`idVisita`) REFERENCES `Visita` (`idVisita`) ON DELETE CASCADE,
  CONSTRAINT `IncidenciaVisita_ibfk_3` FOREIGN KEY (`idGato`) REFERENCES `Gato` (`idGato`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IncidenciaVisita`
--

LOCK TABLES `IncidenciaVisita` WRITE;
/*!40000 ALTER TABLE `IncidenciaVisita` DISABLE KEYS */;
INSERT INTO `IncidenciaVisita` VALUES (1,1,1,1),(2,2,1,NULL),(3,3,2,5),(4,4,3,NULL),(5,5,4,13);
/*!40000 ALTER TABLE `IncidenciaVisita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Interesado`
--

DROP TABLE IF EXISTS `Interesado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Interesado` (
  `idInteresado` int(11) NOT NULL AUTO_INCREMENT,
  `DNI` varchar(20) NOT NULL,
  `nombreCompleto` varchar(100) NOT NULL,
  `email` varchar(70) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`idInteresado`),
  UNIQUE KEY `DNI` (`DNI`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Interesado`
--

LOCK TABLES `Interesado` WRITE;
/*!40000 ALTER TABLE `Interesado` DISABLE KEYS */;
INSERT INTO `Interesado` VALUES (1,'11111111A','Juan Pérez García','juan.perez@example.com','600111222'),(2,'22222222B','María López Ruiz','maria.lopez@example.com','600333444'),(3,'33333333C','Carlos Sánchez Martín','carlos.sanchez@example.com','600555666'),(4,'44444444D','Ana Gómez Fernández','ana.gomez@example.com','600777888'),(5,'55555555E','Pedro Rodríguez Díaz','pedro.rodriguez@example.com','600999000'),(6,'66666666F','Laura Martínez Pérez','laura.martinez@example.com','611222333'),(7,'77777777G','David García López','david.garcia@example.com','611444555'),(8,'88888888H','Sofía Ruiz Sánchez','sofia.ruiz@example.com','611666777'),(9,'99999999I','Javier Fernández Gómez','javier.fernandez@example.com','611888999'),(10,'00000000J','Elena Díaz Rodríguez','elena.diaz@example.com','622000111');
/*!40000 ALTER TABLE `Interesado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `InteresadoBolsa`
--

DROP TABLE IF EXISTS `InteresadoBolsa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `InteresadoBolsa` (
  `idInteresadoBolsa` int(11) NOT NULL AUTO_INCREMENT,
  `idBolsaMunicipal` int(11) NOT NULL,
  `idInteresado` int(11) NOT NULL,
  PRIMARY KEY (`idInteresadoBolsa`),
  KEY `idBolsaMunicipal` (`idBolsaMunicipal`),
  KEY `idInteresado` (`idInteresado`),
  CONSTRAINT `InteresadoBolsa_ibfk_1` FOREIGN KEY (`idBolsaMunicipal`) REFERENCES `BolsaMunicipal` (`idBolsaMunicipal`) ON DELETE CASCADE,
  CONSTRAINT `InteresadoBolsa_ibfk_2` FOREIGN KEY (`idInteresado`) REFERENCES `Interesado` (`idInteresado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `InteresadoBolsa`
--

LOCK TABLES `InteresadoBolsa` WRITE;
/*!40000 ALTER TABLE `InteresadoBolsa` DISABLE KEYS */;
INSERT INTO `InteresadoBolsa` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,2,6),(7,2,7),(8,2,8),(9,2,9),(10,2,10),(11,3,1),(12,3,6);
/*!40000 ALTER TABLE `InteresadoBolsa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Pertenencia`
--

DROP TABLE IF EXISTS `Pertenencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Pertenencia` (
  `idPertenencia` int(11) NOT NULL AUTO_INCREMENT,
  `es_responsable` tinyint(1) NOT NULL,
  `idVoluntario` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL,
  PRIMARY KEY (`idPertenencia`),
  KEY `idVoluntario` (`idVoluntario`),
  KEY `idGrupo` (`idGrupo`),
  CONSTRAINT `Pertenencia_ibfk_1` FOREIGN KEY (`idVoluntario`) REFERENCES `Voluntario` (`idVoluntario`) ON DELETE CASCADE,
  CONSTRAINT `Pertenencia_ibfk_2` FOREIGN KEY (`idGrupo`) REFERENCES `Grupo` (`idGrupo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pertenencia`
--

LOCK TABLES `Pertenencia` WRITE;
/*!40000 ALTER TABLE `Pertenencia` DISABLE KEYS */;
INSERT INTO `Pertenencia` VALUES (1,1,1,1),(2,0,2,1),(3,0,3,1),(4,0,4,1),(5,0,5,1),(6,1,6,2),(7,0,7,2),(8,0,8,2),(9,0,9,2),(10,0,10,2);
/*!40000 ALTER TABLE `Pertenencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Provincia`
--

DROP TABLE IF EXISTS `Provincia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Provincia` (
  `idProvincia` int(11) NOT NULL AUTO_INCREMENT,
  `nombreProvincia` varchar(70) NOT NULL,
  PRIMARY KEY (`idProvincia`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Provincia`
--

LOCK TABLES `Provincia` WRITE;
/*!40000 ALTER TABLE `Provincia` DISABLE KEYS */;
INSERT INTO `Provincia` VALUES (1,'Illes Balears');
/*!40000 ALTER TABLE `Provincia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Sexo`
--

DROP TABLE IF EXISTS `Sexo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sexo` (
  `idSexo` int(11) NOT NULL AUTO_INCREMENT,
  `sexo` varchar(30) NOT NULL,
  PRIMARY KEY (`idSexo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Sexo`
--

LOCK TABLES `Sexo` WRITE;
/*!40000 ALTER TABLE `Sexo` DISABLE KEYS */;
INSERT INTO `Sexo` VALUES (1,'Macho'),(2,'Hembra'),(3,'Desconocido');
/*!40000 ALTER TABLE `Sexo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Trabajo`
--

DROP TABLE IF EXISTS `Trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Trabajo` (
  `idTrabajo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcionTrabajo` text NOT NULL,
  `completado` tinyint(1) NOT NULL DEFAULT 0,
  `idAyuntamiento` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL,
  PRIMARY KEY (`idTrabajo`),
  KEY `idAyuntamiento` (`idAyuntamiento`),
  KEY `idGrupo` (`idGrupo`),
  CONSTRAINT `Trabajo_ibfk_1` FOREIGN KEY (`idAyuntamiento`) REFERENCES `Ayuntamiento` (`idAyuntamiento`) ON DELETE CASCADE,
  CONSTRAINT `Trabajo_ibfk_2` FOREIGN KEY (`idGrupo`) REFERENCES `Grupo` (`idGrupo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Trabajo`
--

LOCK TABLES `Trabajo` WRITE;
/*!40000 ALTER TABLE `Trabajo` DISABLE KEYS */;
INSERT INTO `Trabajo` VALUES (1,'Revisar puntos de alimentación en Colonia A',1,1,1),(2,'Censo de gatos en Colonia B',0,1,1),(3,'Limpieza de área de alimentación en Colonia C',1,2,2),(4,'Distribución de folletos informativos en Calvià',0,2,2);
/*!40000 ALTER TABLE `Trabajo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Ubicacion`
--

DROP TABLE IF EXISTS `Ubicacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ubicacion` (
  `idUbicacion` int(11) NOT NULL AUTO_INCREMENT,
  `textoDescriptivo` text NOT NULL,
  `idCoordenada` int(11) NOT NULL,
  PRIMARY KEY (`idUbicacion`),
  KEY `idCoordenada` (`idCoordenada`),
  CONSTRAINT `Ubicacion_ibfk_1` FOREIGN KEY (`idCoordenada`) REFERENCES `Coordenada` (`idCoordenada`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ubicacion`
--

LOCK TABLES `Ubicacion` WRITE;
/*!40000 ALTER TABLE `Ubicacion` DISABLE KEYS */;
INSERT INTO `Ubicacion` VALUES (1,'Parque de la Feixina, Palma',1),(2,'Barrio de Santa Catalina, Palma',2),(3,'Puerto Portals, Calvià',3),(4,'Playa de Palmanova, Calvià',4),(5,'Centro urbano, Manacor',5),(6,'Zona industrial, Manacor',6);
/*!40000 ALTER TABLE `Ubicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Visita`
--

DROP TABLE IF EXISTS `Visita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Visita` (
  `idVisita` int(11) NOT NULL AUTO_INCREMENT,
  `fechaVisita` date NOT NULL,
  `idColonia` int(11) NOT NULL,
  PRIMARY KEY (`idVisita`),
  KEY `idColonia` (`idColonia`),
  CONSTRAINT `Visita_ibfk_1` FOREIGN KEY (`idColonia`) REFERENCES `Colonia` (`idColonia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Visita`
--

LOCK TABLES `Visita` WRITE;
/*!40000 ALTER TABLE `Visita` DISABLE KEYS */;
INSERT INTO `Visita` VALUES (1,'2024-10-26',1),(2,'2024-10-27',2),(3,'2024-10-28',3),(4,'2024-10-29',4),(5,'2024-10-30',5);
/*!40000 ALTER TABLE `Visita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VisitaVoluntario`
--

DROP TABLE IF EXISTS `VisitaVoluntario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VisitaVoluntario` (
  `idVisitaVoluntario` int(11) NOT NULL AUTO_INCREMENT,
  `idVisita` int(11) NOT NULL,
  `idVoluntario` int(11) NOT NULL,
  PRIMARY KEY (`idVisitaVoluntario`),
  KEY `idVisita` (`idVisita`),
  KEY `idVoluntario` (`idVoluntario`),
  CONSTRAINT `VisitaVoluntario_ibfk_1` FOREIGN KEY (`idVisita`) REFERENCES `Visita` (`idVisita`) ON DELETE CASCADE,
  CONSTRAINT `VisitaVoluntario_ibfk_2` FOREIGN KEY (`idVoluntario`) REFERENCES `Voluntario` (`idVoluntario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VisitaVoluntario`
--

LOCK TABLES `VisitaVoluntario` WRITE;
/*!40000 ALTER TABLE `VisitaVoluntario` DISABLE KEYS */;
INSERT INTO `VisitaVoluntario` VALUES (1,1,1),(2,1,2),(3,2,3),(4,3,6),(5,4,7);
/*!40000 ALTER TABLE `VisitaVoluntario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Voluntario`
--

DROP TABLE IF EXISTS `Voluntario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Voluntario` (
  `idVoluntario` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `contrasenya` varchar(255) NOT NULL,
  `idInteresado` int(11) NOT NULL,
  PRIMARY KEY (`idVoluntario`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `idInteresado` (`idInteresado`),
  CONSTRAINT `Voluntario_ibfk_1` FOREIGN KEY (`idInteresado`) REFERENCES `Interesado` (`idInteresado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Voluntario`
--

LOCK TABLES `Voluntario` WRITE;
/*!40000 ALTER TABLE `Voluntario` DISABLE KEYS */;
INSERT INTO `Voluntario` VALUES (1,'jperez','pass123',1),(2,'mlopez','pass123',2),(3,'csanchez','pass123',3),(4,'agomez','pass123',4),(5,'prodriguez','pass123',5),(6,'lmartinez','pass123',6),(7,'dgarcia','pass123',7),(8,'sruiz','pass123',8),(9,'jfernandez','pass123',9),(10,'ediaz','pass123',10);
/*!40000 ALTER TABLE `Voluntario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-13 22:28:40
