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
-- Table structure for table `vendor_labor`
--

DROP TABLE IF EXISTS `vendor_labor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendor_labor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employeeID` varchar(50) NOT NULL,
  `driver_name` varchar(100) NOT NULL,
  `date_of_join` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `vehicleType` varchar(50) NOT NULL,
  `vehicleNumber` varchar(50) NOT NULL,
  `vehicleModel` varchar(100) NOT NULL,
  `availability` varchar(50) NOT NULL,
  `condition_check_status` varchar(50) NOT NULL,
  `vehicleInsurance` varchar(100) NOT NULL,
  `panNumber` varchar(20) NOT NULL,
  `aadharNumber` varchar(20) NOT NULL,
  `licenseNumber` varchar(50) NOT NULL,
  `phoneNumber` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `salary` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `bankAccountNumber` varchar(50) NOT NULL,
  `ifscCode` varchar(20) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `company_email` varchar(100) NOT NULL,
  `company_licence` varchar(100) NOT NULL,
  `company_address` text NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_contact` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employeeID` (`employeeID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor_labor`
--

LOCK TABLES `vendor_labor` WRITE;
/*!40000 ALTER TABLE `vendor_labor` DISABLE KEYS */;
INSERT INTO `vendor_labor` VALUES (1,'ww','ww','2025-07-05','ww','ww','ww','ww','ww','ww','ww','ww','ww','ww','66','ww@ww','ww','ww','ww','ww','ww','ww','ww@ww','ww','ww','ww','ww','2025-06-28 05:48:44');
/*!40000 ALTER TABLE `vendor_labor` ENABLE KEYS */;
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
