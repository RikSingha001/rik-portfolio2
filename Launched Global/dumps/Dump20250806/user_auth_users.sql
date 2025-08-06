-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: user_auth
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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','suspended','banned') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_admin` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'riksingha615@gmail.c','loadedmp3rik@gmail.com','$2y$12$Z1ewgoMtzNs7wyXExjLet.Hh1HWxlHCiqNCzuKknsxtvNvxFK3GUu','2025-08-01 02:49:54','09134882160','active',NULL,'2025-08-05 02:12:24',1),(3,'riksingha','riksingha420@gmail.com','$2y$12$9M7rFVO7q.ZpARq6/7XoDOLpidafH7tcwjLtiuMP2iMhin/OGz9/C','2025-08-01 02:55:10','09475166496','active',NULL,'2025-08-05 01:49:21',0),(6,'aaaaaa','aaaaaaa@WWW','aaaaaaa@WWW','2025-08-01 02:58:22','0913aaaa','active',NULL,'2025-08-05 00:41:52',0),(7,'aaaaaa','aaaaaaa@WWWs','aaaaaaa@WWWa','2025-08-01 03:00:32','0913aaaaa','active',NULL,'2025-08-05 00:41:52',0),(8,'aaaa','aa@aa','aa@aa','2025-08-01 03:04:02','09134882160','active',NULL,'2025-08-05 00:41:52',0),(9,'dddd','aa@aad','aa@aad','2025-08-01 03:09:38','091348821602','active',NULL,'2025-08-05 00:41:52',0),(10,'aaaaa','aa@aada','aa@aada','2025-08-01 03:11:16','0913488216022','active',NULL,'2025-08-05 00:41:52',0),(11,'abc','riksingh0@gmail.com','$2y$12$MChYmNR70PcIZB6IrpJc6.3l.jVAyGiHwnrN5VXoPB2rLUD2Dd336','2025-08-03 22:30:47','9134882160','active',NULL,'2025-08-05 00:41:52',0),(12,'def','riksin@gmail.com','$2y$12$R32sjvgPRsVcEUfPmYZD9.y/7gGcxxL68RaguRn69zXeL6rOepi6u','2025-08-03 22:38:40','9134882160','active',NULL,'2025-08-05 00:41:52',0),(13,'aaa','riksingha@gmail.com','$2y$12$bR0rwjgxdEKNrghp8tbLo.zdUq4MUolWGKQW2BQBeXjf/z1tMGXb.','2025-08-04 22:07:46','0913488216022','active',NULL,'2025-08-05 00:41:52',0),(14,'hello','riksingh@gmail.com','$2y$12$y7RRDel0xN1/zM18Hy4D5uqXOcU07TDoJtdj0uTK7EgsOM7/vbSNa','2025-08-04 22:14:10','556461656562','active',NULL,'2025-08-05 00:41:52',0),(15,'$hashedPassword','n@gmail.com','$2y$12$Ze8O.CqKt5AaHIvdcvQN0OktIS3eAHWDV8YunCHEUzo76XtGfZbHG','2025-08-04 23:42:04','09475166496','active',NULL,'2025-08-05 01:49:00',0),(16,'2413020002','rik@gmail.com','$2y$12$I9HfgQU9urlMNZ8KxzCicOIQ43XtcG0iZekMWQb44vrhfz2q6u4yu','2025-08-05 02:05:18','555555555555555555','active',NULL,'2025-08-05 00:41:52',0),(17,'2413020002a','riha420@gmail.com','$2y$12$CuB2VpX3tFBcrLR8UekqvOguee.xKurSH5CgPImZDK9wYoc9XEywa','2025-08-05 02:20:43','5564616565625','active',NULL,'2025-08-05 18:07:46',0),(18,'riksing','riksingh5@gmail.com','$2y$12$Z6wxIpji3.o2WFvfk2kv1.vhMhG0WhTDrVWr4GHdC.lCOa5G0xjUe','2025-08-05 15:20:33','136111202116','active',NULL,'2025-08-05 18:03:31',0),(19,'aaaaaa','Rffdfd@dfdf.cld','$2y$12$aR98SGCtsaz5uM/glKV/X.oZadQtaa0eFYI65J.Hnz90WCGqh5EhS','2025-08-05 23:09:02','16646131616','active',NULL,'2025-08-05 17:39:02',0),(20,'Rik Singha','riksi@gmail.com','$2y$12$B6r.EMJk5jR7n0XLFdWZO.kHlvUQPcwJjdweAG6cN7e/u2fxhtHRu','2025-08-05 23:17:47','dddd','active',NULL,'2025-08-05 17:47:47',0),(21,'$hashedPassword','riksingha1440@gmail.com','$2y$12$kU5Bh8.YGwP3BmzIzPdSU.DwFlj.rqe0c.nI.LxS5PqAVeqVY7m.e','2025-08-05 23:52:10','09475166496','active',NULL,'2025-08-05 18:22:10',0),(22,'sss','jddjdwjlawmd@dcsmds.com','$2y$12$kdXuZwZUywSQNtJUTqAxhekom7bG6X3uuHLNaUkv9knoqlCHpHPYK','2025-08-05 23:56:35','56326565','active',NULL,'2025-08-05 18:26:35',0),(23,'hellosss','hellosss@fjd.com','$2y$12$2EVq1cqX/SY.8JmPbEKS5uAuyIyN2Uhk2zXddq7jsEnwgXNy6ek8y','2025-08-06 00:02:41','34343677677','active',NULL,'2025-08-05 18:32:41',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-06 18:52:25
