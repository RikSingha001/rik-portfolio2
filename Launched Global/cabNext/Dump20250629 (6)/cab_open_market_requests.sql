-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: cab
-- ------------------------------------------------------
-- Server version	8.0.42

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
-- Table structure for table `open_market_requests`
--

DROP TABLE IF EXISTS `open_market_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `open_market_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `release_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'open',
  `accepted_by` varchar(100) DEFAULT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `whitelisted_only` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `open_market_requests`
--

LOCK TABLES `open_market_requests` WRITE;
/*!40000 ALTER TABLE `open_market_requests` DISABLE KEYS */;
INSERT INTO `open_market_requests` VALUES (1,2,'2025-06-28 20:24:39','accepted','vendor_123','2025-06-28 20:31:33',1),(2,1,'2025-06-28 20:24:39','accepted','vendor_123','2025-06-28 20:31:24',1),(3,3,'2025-06-28 20:26:43','accepted','vendor_123','2025-06-28 20:27:24',1),(4,4,'2025-06-28 20:33:29','accepted','vendor_123','2025-06-28 20:34:07',1),(5,5,'2025-06-28 20:33:29','accepted','vendor_123','2025-06-28 20:33:50',1),(6,6,'2025-06-28 20:37:24','accepted','vendor_123','2025-06-28 20:37:32',1),(7,7,'2025-06-28 20:48:52','accepted','vendor_123','2025-06-28 20:49:04',1),(8,8,'2025-06-29 03:55:40','accepted','vendor_123','2025-06-29 03:56:02',1),(9,9,'2025-06-29 05:45:51','accepted','vendor_123','2025-06-29 05:48:51',1),(10,10,'2025-06-29 05:49:32','accepted','vendor_123','2025-06-29 05:50:11',1);
/*!40000 ALTER TABLE `open_market_requests` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-29 18:37:06
