-- MySQL dump 10.13  Distrib 8.0.37, for Win64 (x86_64)
--
-- Host: localhost    Database: atomic_app
-- ------------------------------------------------------
-- Server version	8.0.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account` (
  `id_akun` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_akun`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES (1,'dayat1','$2y$10$MeHc0MXHsa7fju7OJVkcauEZFVRATNGf6Zi8ApkyTvP3VVYTjXbs.','2024-10-17 17:17:37'),(2,'zikaharun','$2y$10$epNIEJtunjy.06lzz0lfru5QtnKT0hPjxEbG1v8lvrAJbPlHFeJm6','2024-10-17 17:18:33'),(3,'raihan','$2y$10$zsDkvs1aoF9EnnC6EXlCzOrUZ4ZbKjU0AZ.14YLa7Q203jl/DIL8a','2024-10-18 02:44:07'),(4,'kaka','$2y$10$yheuQbDD0G3YJzifEeH/Ve/OUi.q.9a5YLO77vhIEFB.NeFI6SISe','2024-10-18 13:51:54'),(5,'fadhil123','$2y$10$wJWFkDu1oQw.c37Tx6qSIOzwrd375OQfrLlt2jWG5oFxKr7K9q17G','2024-10-22 13:03:15'),(6,'vincent13','$2y$10$klGL/fwKNlmavDrvS0iR2efNrV9wzA1hV/lQZPQiC1akgwZmffBk.','2024-10-22 13:04:50'),(7,'galih123','$2y$10$IdQ9i696cksQKp4DjLJEQ.UOZ1woeQwDlTAnJ/5WcwdSuNg22Muj.','2024-10-26 08:43:40'),(8,'zikaharun_','$2y$10$Vn6oVXS5dLJW9Oy2jzkfJurP2CC5kVneBgSr.zwPGAYyDcX9I6R9a','2024-11-05 05:58:11');
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bad_habit`
--

DROP TABLE IF EXISTS `bad_habit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bad_habit` (
  `id_bad_habit` int NOT NULL AUTO_INCREMENT,
  `id_akun` int DEFAULT NULL,
  `bad_habit_name` varchar(255) DEFAULT NULL,
  `begin_frequency` int DEFAULT NULL,
  `target_frequency` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_bad_habit`),
  KEY `id_akun` (`id_akun`),
  CONSTRAINT `bad_habit_ibfk_1` FOREIGN KEY (`id_akun`) REFERENCES `account` (`id_akun`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bad_habit`
--

LOCK TABLES `bad_habit` WRITE;
/*!40000 ALTER TABLE `bad_habit` DISABLE KEYS */;
INSERT INTO `bad_habit` VALUES (1,5,'smoke',2,0,'2024-11-04 05:59:35'),(2,8,'smoke',5,0,'2024-11-05 06:09:56'),(3,5,'eat sugar',2,0,'2024-11-05 12:07:52');
/*!40000 ALTER TABLE `bad_habit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bad_habit_progress`
--

DROP TABLE IF EXISTS `bad_habit_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bad_habit_progress` (
  `id_bad_habit_progress` int NOT NULL AUTO_INCREMENT,
  `id_bad_habit` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `daily_frequency` int DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id_bad_habit_progress`),
  KEY `id_bad_habit` (`id_bad_habit`),
  CONSTRAINT `bad_habit_progress_ibfk_1` FOREIGN KEY (`id_bad_habit`) REFERENCES `bad_habit` (`id_bad_habit`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bad_habit_progress`
--

LOCK TABLES `bad_habit_progress` WRITE;
/*!40000 ALTER TABLE `bad_habit_progress` DISABLE KEYS */;
INSERT INTO `bad_habit_progress` VALUES (3,1,'2024-11-04',1,'only a cigarette.'),(4,2,'2024-11-05',1,'a cigarette has been smoked!'),(5,2,'2024-11-05',1,'I smoked again :C');
/*!40000 ALTER TABLE `bad_habit_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habit`
--

DROP TABLE IF EXISTS `habit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habit` (
  `id_habit` int NOT NULL AUTO_INCREMENT,
  `id_akun` int DEFAULT NULL,
  `habit_name` varchar(255) DEFAULT NULL,
  `frekuensi_target` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_habit`),
  KEY `id_akun` (`id_akun`),
  CONSTRAINT `habit_ibfk_1` FOREIGN KEY (`id_akun`) REFERENCES `account` (`id_akun`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habit`
--

LOCK TABLES `habit` WRITE;
/*!40000 ALTER TABLE `habit` DISABLE KEYS */;
INSERT INTO `habit` VALUES (4,7,'makan',3,'2024-10-26 08:46:52','kesehatan'),(5,5,'learn php',7,'2024-10-29 14:20:09','productive'),(6,5,'walk',2,'2024-10-30 12:06:32','health'),(7,5,'wake up early',7,'2024-10-30 14:53:25','productive'),(9,5,'workout',5,'2024-11-03 12:46:44','health'),(10,8,'workout',5,'2024-11-05 06:01:31','productive'),(11,8,'writing',1,'2024-11-05 06:02:19','productive');
/*!40000 ALTER TABLE `habit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habit_progress`
--

DROP TABLE IF EXISTS `habit_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habit_progress` (
  `id_habit_progress` int NOT NULL AUTO_INCREMENT,
  `id_habit` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `daily_frequency` int DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id_habit_progress`),
  KEY `id_habit` (`id_habit`),
  CONSTRAINT `habit_progress_ibfk_1` FOREIGN KEY (`id_habit`) REFERENCES `habit` (`id_habit`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habit_progress`
--

LOCK TABLES `habit_progress` WRITE;
/*!40000 ALTER TABLE `habit_progress` DISABLE KEYS */;
INSERT INTO `habit_progress` VALUES (16,6,'2024-10-31',1,'I\'m tired!'),(22,7,'2024-10-31',2,'Wake up early!'),(29,7,'2024-11-01',1,'I woke up early in the morning.'),(30,5,'2024-11-01',1,'learn php 2 hours.'),(33,5,'2024-11-03',1,'learn php for an hour.'),(34,9,'2024-11-04',1,'I did 30 push-up this morning.'),(35,10,'2024-11-05',1,'I did pull-up 5 reps 3 sets this morning.'),(36,10,'2024-11-04',1,'Yesterday, I did push-up 30 reps.');
/*!40000 ALTER TABLE `habit_progress` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-06 15:44:32
