-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: 127.0.0.1    Database: theobscuredindex
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
-- Table structure for table `Current_Users`
--

DROP TABLE IF EXISTS `Current_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Current_Users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Current_Users`
--

LOCK TABLES `Current_Users` WRITE;
/*!40000 ALTER TABLE `Current_Users` DISABLE KEYS */;
INSERT INTO `Current_Users` VALUES (1,'dinnesh','nicolemanondo29@gmail.com','$2y$10$FUAgbnIfVzbCWraGV2YUReqxd7ATQ3jBXA2YbOD1rYJRpFHw/v0mu','2025-05-26 18:08:50','2026-08-22 17:12:31'),(3,'nicole_cm','dinneshmanondo@gmail.com','$2y$10$e.zEOZw9YtYnzEzduYpQxug4971TIi64.vsyeDLajzpb1VcUGdb0.','2025-05-27 01:00:01','2026-08-22 17:07:59'),(4,'klyder','torcyklyne@gmail.com','$2y$10$syZb9QUe/F06GRKsxBz8qOIoFRTQ.jgPZMiHf30b8x9OMkO5im9.i','2025-05-27 17:44:55','2026-08-22 17:07:59'),(5,'razelkaye','rzlkyrns@gmail.com','$2y$10$qx5G5Orf64yIA14CDwwZ7en/jxcTa9g/Ic1Mc.BvprfCweoUVlqVq','2025-05-27 18:05:25','2026-08-22 17:07:59'),(6,'Kal','khalelboydcpareja@gmail.com','$2y$10$5k6PYfL9rVgAMJpO/h/McefRxNKjSx8YIkpmufQMOdIVEGXMu61zS','2025-05-27 22:27:44','2026-08-22 17:07:59'),(7,'FootFungus12','11500384@usc.edu.ph','$2y$10$P77epmpMjcmuH/JG30c8ueI9mX3t2wPQKBFC2RFFe9ynP1E4cGCRm','2025-05-29 08:54:56','2026-08-22 17:07:59'),(8,'aneir','reinaventures@gmail.com','$2y$10$pnNa8E6a2CnCfKFC8DkpQuFMg32lyMflFTTAGiZs.SWYc88EDkCTq','2025-08-30 11:35:42','2026-08-22 17:07:59'),(9,'justin','jstn@email.com','$2y$10$4w2aNMoqN9Lix8tso0guNOhM7UMuvzrvYNZsoXLR5UMDQ.gDNQmLK','2025-11-12 12:36:54','2026-08-22 17:07:59'),(10,'mattematician','mattgoat@gmail.com','$2y$10$pZHqDP/3qjW4Ptd5vegwuuCzemXymxF28aaqtW8DryIS1F8AGQo5.','2025-11-12 14:00:57','2026-08-22 17:07:59'),(11,'zsof','22102596@usc.edu.ph','$2y$10$WO/RhBZ6KKoYsek6mJsK2e.kIBRbEAdpCsweqP3p0hPGsBd2HDD7u','2025-11-13 08:08:51','2026-08-22 17:07:59'),(12,'rose','23101000@usc.edu.ph','$2y$10$KCNI1xb4jiXq78f1DUgKdezgCSBYwKLO2KiyPVm5tOPluoXWzsWcK','2025-11-13 12:42:14','2026-08-22 17:07:59'),(13,'ginabot','07500972@usc.edu.ph','$2y$10$ZnibmxOnEd.lnhH6lzZczO6a/j.5ZCW0cZQs78gKdZSx6h.uaa.Ue','2025-11-13 13:01:39','2026-08-22 17:07:59'),(14,'BIGDICKKID','23100262@usc.edu.ph','$2y$10$63rraqBfgGLhxKEqjtyALuT4nim8o0kOpyxZugIdkv7gEUlV9vjY2','2025-11-13 13:13:51','2026-08-22 17:07:59'),(15,'mrchooey','ronpatrickramas7@gmail.com','$2y$10$89YH3OqygDOnWWBKDglgfuX2..1J2/iKOknllDu//zuNbg8oPVN7q','2025-11-13 13:20:54','2026-08-22 17:07:59'),(16,'ssss','12345678@gmail.com','$2y$10$jfTt/ioqc3ItMEyxtL0vU.xxobVZElqne0fmanmZT/KzWi3HavUdW','2025-11-13 14:05:55','2026-08-22 17:07:59'),(17,'test','test@gmail.com','$2y$10$GOu4I7.02vQ8/nF27gwZF.tLjn8iOyWrI4UwNTvgLAfVs1LKvEoNq','2025-11-14 13:46:26','2026-08-22 17:07:59'),(18,'jared01','gamorajared@gmail.com','$2y$10$ae.JVty7lZlVMb7a8.0.J.lX6ZWHSbq72J6EdXOv7OJIOR4IbQn7y','2025-11-14 15:39:53','2026-08-22 17:07:59'),(19,'username12334','concieciative143@yahoo.com','$2y$10$SN7vjkGpiT57JHb4EXXzUepE6D0u.JvQbMcIY3MEcFKO1XNqQu76.','2025-11-17 12:34:50','2026-08-22 17:07:59'),(20,'loyloy','loyan13aloba@gmail.com','$2y$10$mHNU8LB7bXoRSAG5AwiYRen7YsULhezUz9K5fMzhf7ZBya541deaG','2025-11-17 12:43:06','2026-08-22 17:07:59'),(21,'skyyy','21101425@usc.edu.ph','$2y$10$0YHAB02.Ew7vrnNBwseEvellbgfbshjySyjOnsM4ttgIQQEoUlkZm','2025-11-18 12:43:04','2026-08-22 17:07:59'),(22,'asdasd','asdasd@gmail.com','$2y$10$m2BViC4WwBsAiIgrrVlQ6OvKpYA9vhLwYjvo8zZvB5HCj.FCf0bWq','2025-11-18 14:34:39','2026-08-22 17:07:59'),(23,'domilian','22102038@usc.edu.ph','$2y$10$gztZJ9diwKeHjOjq6sn91u1p/k8BCUh7CrWnJc0PZDxEzs.8IP4fW','2025-11-20 12:16:43','2026-08-22 17:07:59'),(24,'zev','twinkfemboy@gmail.com','$2y$10$IcSHF/HDL7PAHt5lG5jO9OZdzf82NVh3DkUUJsTOWTfrvGflIoT8e','2025-11-26 13:07:20','2026-08-22 17:07:59'),(25,'simon','nigga@gmail.com','$2y$10$up1irPxFOQ/QPKCc9X66Eez7OrGSGxGrThKGTtO/BWkDjyAP/qyIC','2025-11-26 13:50:27','2026-08-22 17:07:59'),(26,'123','123@gmail.com','$2y$10$CXdaY313DvX66UNqmKfTTeKxbuAF8vQtzAoAjDPxSv60j5MbTTPxK','2025-11-26 16:43:04','2026-08-22 17:07:59'),(27,'hestia','21102926@usc.edu.ph','$2y$10$Q6fAqVH2SjUjqSqSe0BQAeI08q4O9O2QXzNjOpI/JUGaCoJzeAehK','2025-11-27 09:50:32','2026-08-22 17:07:59'),(28,'asdf','asdf@gmail.com','$2y$10$atnKJ/tXjJSYicvMTkut2u2Jpyx1aW1YC3ey.Nu7w.J92DD.cMyS6','2025-11-27 13:39:29','2026-08-22 17:07:59'),(29,'bruh1234','bruh@gmail.com','$2y$10$AuW89wdKLahrSRlscJqeTez87P8g8HJR7ps.BvanzbXVURJoIJZka','2025-11-27 16:32:38','2026-08-22 17:07:59'),(30,'bruh12345','bruh1@gmail.com','$2y$10$ZInntQLHrstKRUjrF8kvG.VKeNxFzyZ2ElARZd7J7sPUDtNHiCBvG','2025-11-27 16:33:24','2026-08-22 17:07:59'),(31,'mello','antoinettecabahug@gmail.com','$2y$10$mhphgn6jNScjE8QkZ6x/m.XySJASHRmEeK3NOhlfOiBXgj2K.jla6','2025-12-01 13:16:22','2026-08-22 17:07:59'),(32,'mayls','s23104513@usc.edu.ph','$2y$10$VxOnP5LxZ6MnXba14z45Vu74s7w3OpSMNM/sGnqwjsruVlv/.ppZy','2025-12-02 09:31:21','2026-08-22 17:07:59'),(33,'getfgadsfsdfs','asdfsadf@yahoo.coma','$2y$10$MLGb7FdTSYU5abJmaKU1r.cF4wfyRyYvgjl8H7QZWRCAKXzieM4JC','2026-01-22 14:19:16','2026-08-22 17:07:59'),(34,'lolheight','loly@gmail.com','$2y$10$Mal4qKmUqIJEJAB9FZNfP.XgaZzsfAwXZ/CPU71Q3zEBpBtrFLBpe','2026-01-26 13:11:11','2026-08-22 17:07:59'),(35,'nealveloso','nealveloso@gmail.com','$2y$10$fpGV6j15.lNdNj9mIcugAuFp2dob3vRZZcPTIXxmvoJOrjLJVQTge','2026-01-26 18:05:33','2026-08-22 17:07:59'),(36,'qwe','q@gmail.com','$2y$10$O7RQjne/IR1pD3GOAx/aEubbL9npHOghfkwH2aTff6GcAcDy12e..','2026-01-27 15:54:53','2026-08-22 17:07:59'),(37,'1c9ei0mbvu','1c9ei0mbvu@wnbaldwy.com','$2y$10$JlnKr2Cyt5Kxsi8xmVHNM.WxdWNLcvcRgSDQfnkEz8gGuqg4b5oZW','2026-02-03 13:26:39','2026-08-22 17:07:59'),(38,'asdasd4123','asdas23d@gmail.com','$2y$10$nny8GrQRUoEiMahrzRqrwey.jmgBZKxfQQbwVGN201wn.JLzE6CC2','2026-02-16 16:25:45','2026-08-22 17:07:59'),(39,'Caulleiyx','rey04.antonio14@gmail.com','$2y$10$Q2vLJOeVf.pMMhTQm6WACeodoTYaHs0AFixu408ToKB2JMjzj5esG','2026-02-20 13:47:11','2026-08-22 17:07:59'),(40,'wqeqw','ssadsds@usc.edu.ph','$2y$10$8gEAwHd.tsCdQzuToSAiQupZps6jn74rz06pe9hAskla5Vl7jBi3C','2026-03-02 10:33:59','2026-08-22 17:07:59'),(41,'ASD','asdfgh@gmail.com','$2y$10$rYpEYHmhVeTs2ilKpOBICe7oh3/MTHoITUlpAxiZJkdl1PFCSLG12','2026-03-04 12:24:58','2026-08-22 17:07:59'),(42,'kaila','23104019@usc.edu.ph','$2y$10$OV6xJA0Oc9b8n6Xip.MUse2Jj5E.xxgIrbW5B7NX3njkd0B6qRAJO','2026-03-10 19:59:37','2026-08-22 17:07:59'),(43,'swash','joshufaber06@gmail.com','$2y$10$Igj42aDjghxlt//p7Drc5.l3k7JpLdjULjoSQ4Yth6bsD1hhxXOw.','2026-03-18 13:33:30','2026-08-22 17:07:59'),(44,'baller9000','vanaxel@gmail.com','$2y$10$CccBKdJnOz0HZlVuHRBlR.v3quF9WgtgSvyDNgxtCc2eMbAE/VHm2','2026-04-07 10:29:23','2026-08-22 17:07:59'),(45,'yesbabe','123123@gmail.com','$2y$10$.pzenA1EILfpz6JQLkBGxOumIzCwxrj8U0jLwNVEcvDUmMM31cCLu','2026-04-14 09:54:47','2026-08-22 17:07:59'),(46,'RedMoon','zeroskai08@gmail.com','$2y$10$Jci1vDSm38mx9ZnXksG8.uVIyohVb2xgmQyBGe9skesT3ViLDmyLe','2026-04-20 16:35:45','2026-08-22 17:07:59'),(47,'1234567890','1234567890@gmail.com','$2y$10$OF.wb7aVpdY8Dw78V2zb4Of03WgLbGIsmdHP7Zes58yJoe0YEPZoC','2026-04-23 15:09:32','2026-08-22 17:07:59'),(48,'rwar','rwar@gmail.com','$2y$10$yp4TnF7lsy4u1pwZ//v0zes6DMLk8UqVVhm6dN8ryt.lptcf5PV3i','2026-04-25 15:23:18','2026-08-22 17:07:59');
/*!40000 ALTER TABLE `Current_Users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Manhwas`
--

DROP TABLE IF EXISTS `Manhwas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Manhwas` (
  `manhwa_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `status` enum('Ongoing','Completed','Dropped','Hiatus') DEFAULT 'Ongoing',
  `genre` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `upload_date` datetime DEFAULT current_timestamp(),
  `is_private` tinyint(1) DEFAULT 0,
  `mangadex_id` varchar(64) DEFAULT NULL,
  `comick_hid` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`manhwa_id`),
  KEY `idx_manhwas_user_id` (`user_id`),
  KEY `idx_manhwas_title` (`title`),
  CONSTRAINT `manhwas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Current_Users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Manhwas`
--

LOCK TABLES `Manhwas` WRITE;
/*!40000 ALTER TABLE `Manhwas` DISABLE KEYS */;
INSERT INTO `Manhwas` VALUES (1,1,'The Villainess Turns the Hourglass','Sansobee (산소비)','Completed','Romance','With the marriage of her prostitute mother to the Count, Aria’s status in society skyrocketed immediately. After leading a life of luxury, Aria unfairly meets death because of her sister Mielle’s schemes. And right before she dies, she sees an hourglass fall as if it were a fantasy. And just like that, she was miraculously brought back to the past.\r\n\r\n“I want to become a very elegant person, just like my sister, Mielle.”\r\n\r\nIn order to face the villainess, she must become an even more wicked villainess. This was the new path Aria chose to take revenge on Mielle who murdered both her and her mother.\r\n\r\n---\r\nAfter her lowly mother married a count, Aria enjoyed a life full of luxury while harassing her gentle stepsister Mielle. Several years later, Aria is about to be executed when Mielle reveals that she wickedly tricked Aria into building the bad reputation that ultimately brought her to the scaffold. Just as Aria desperately wishes she could change her fate, she sees a curious hourglass that takes her back into the past. \r\n\r\nNow, Aria can destroy Mielle by using her own tactics against her like a true villainess. The power of the hourglass is on her side... \r\n\r\nCan Aria take everything from Mielle, or will her actions change the past in ways she couldn\'t have imagined? Based on the hit novel.','https://uploads.mangadex.org/covers/73bc69fa-9ba9-4533-a243-ebc11651339f/5bd5b399-bfae-492b-a322-a4085b8cafd2.jpg','2026-09-04 20:44:28',0,'73bc69fa-9ba9-4533-a243-ebc11651339f',NULL);
/*!40000 ALTER TABLE `Manhwas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reread_History`
--

DROP TABLE IF EXISTS `Reread_History`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Reread_History` (
  `reread_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `manhwa_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `finish_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`reread_id`),
  KEY `manhwa_id` (`manhwa_id`),
  KEY `idx_reread_user_manhwa` (`user_id`,`manhwa_id`),
  CONSTRAINT `reread_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Current_Users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `reread_history_ibfk_2` FOREIGN KEY (`manhwa_id`) REFERENCES `Manhwas` (`manhwa_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reread_History`
--

LOCK TABLES `Reread_History` WRITE;
/*!40000 ALTER TABLE `Reread_History` DISABLE KEYS */;
/*!40000 ALTER TABLE `Reread_History` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Secret_Manhwas`
--

DROP TABLE IF EXISTS `Secret_Manhwas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Secret_Manhwas` (
  `secret_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `manhwa_id` int(11) NOT NULL,
  `added_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`secret_id`),
  UNIQUE KEY `user_manhwa_unique` (`user_id`,`manhwa_id`),
  KEY `manhwa_id` (`manhwa_id`),
  CONSTRAINT `secret_manhwas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Current_Users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `secret_manhwas_ibfk_2` FOREIGN KEY (`manhwa_id`) REFERENCES `Manhwas` (`manhwa_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Secret_Manhwas`
--

LOCK TABLES `Secret_Manhwas` WRITE;
/*!40000 ALTER TABLE `Secret_Manhwas` DISABLE KEYS */;
/*!40000 ALTER TABLE `Secret_Manhwas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Secret_Shelf_Access`
--

DROP TABLE IF EXISTS `Secret_Shelf_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Secret_Shelf_Access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `granted_date` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`access_id`),
  KEY `idx_secret_access_user` (`user_id`),
  CONSTRAINT `secret_shelf_access_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Current_Users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Secret_Shelf_Access`
--

LOCK TABLES `Secret_Shelf_Access` WRITE;
/*!40000 ALTER TABLE `Secret_Shelf_Access` DISABLE KEYS */;
INSERT INTO `Secret_Shelf_Access` VALUES (1,1,'2025-06-02 17:50:23'),(2,1,'2025-06-02 17:58:46'),(3,1,'2025-06-02 18:10:12'),(4,1,'2025-06-02 18:13:05'),(5,1,'2025-06-02 18:17:17'),(6,1,'2025-06-02 18:21:16'),(7,1,'2025-06-02 18:22:50'),(8,1,'2025-06-02 18:32:16'),(9,1,'2025-06-02 20:15:40');
/*!40000 ALTER TABLE `Secret_Shelf_Access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User_Reading_Status`
--

DROP TABLE IF EXISTS `User_Reading_Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User_Reading_Status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `manhwa_id` int(11) NOT NULL,
  `reading_status` enum('Plan to Read','Currently Reading','Done','Reread') DEFAULT 'Plan to Read',
  `start_reading_date` date DEFAULT NULL,
  `finish_reading_date` date DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `current_chapter` int(11) DEFAULT 0,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `unique_user_manhwa` (`user_id`,`manhwa_id`),
  KEY `manhwa_id` (`manhwa_id`),
  KEY `idx_reading_status` (`reading_status`),
  CONSTRAINT `user_reading_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Current_Users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `user_reading_status_ibfk_2` FOREIGN KEY (`manhwa_id`) REFERENCES `Manhwas` (`manhwa_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User_Reading_Status`
--

LOCK TABLES `User_Reading_Status` WRITE;
/*!40000 ALTER TABLE `User_Reading_Status` DISABLE KEYS */;
INSERT INTO `User_Reading_Status` VALUES (1,1,1,'Plan to Read',NULL,NULL,'2026-09-04 20:44:28',0);
/*!40000 ALTER TABLE `User_Reading_Status` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-04 21:15:08
