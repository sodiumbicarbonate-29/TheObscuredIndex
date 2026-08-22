-- =====================================================
-- THE OBSCURED INDEX - DATABASE MIGRATION
-- =====================================================
-- Migration: 001_create_tables.sql
-- Description: Creates all tables with proper structure
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- DROP EXISTING TABLES (in correct order for FK)
-- =====================================================
DROP TABLE IF EXISTS `Reread_History`;
DROP TABLE IF EXISTS `Secret_Shelf_Access`;
DROP TABLE IF EXISTS `User_Reading_Status`;
DROP TABLE IF EXISTS `Manhwas`;
DROP TABLE IF EXISTS `Current_Users`;

-- =====================================================
-- TABLE: Current_Users
-- =====================================================
CREATE TABLE `Current_Users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: Manhwas
-- =====================================================
CREATE TABLE `Manhwas` (
  `manhwa_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `genre` enum('BL','Straight','No Romance') DEFAULT 'No Romance',
  `author` varchar(100) DEFAULT NULL,
  `status` enum('Ongoing','Completed','Hiatus','Dropped') DEFAULT 'Ongoing',
  `description` text DEFAULT NULL,
  `upload_date` datetime DEFAULT current_timestamp(),
  `cover_image` varchar(255) DEFAULT NULL,
  `reading_link` varchar(255) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`manhwa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: Reread_History
-- =====================================================
CREATE TABLE `Reread_History` (
  `reread_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `manhwa_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `finish_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reread_id`),
  KEY `user_id` (`user_id`),
  KEY `manhwa_id` (`manhwa_id`),
  CONSTRAINT `Reread_History_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Current_Users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `Reread_History_ibfk_2` FOREIGN KEY (`manhwa_id`) REFERENCES `Manhwas` (`manhwa_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: Secret_Shelf_Access
-- =====================================================
CREATE TABLE `Secret_Shelf_Access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `granted_date` datetime NOT NULL,
  PRIMARY KEY (`access_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `Secret_Shelf_Access_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Current_Users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABLE: User_Reading_Status
-- =====================================================
CREATE TABLE `User_Reading_Status` (
  `user_id` int(11) NOT NULL,
  `manhwa_id` int(11) NOT NULL,
  `reading_status` enum('Plan to Read','Currently Reading','Done') NOT NULL,
  `start_reading_date` date DEFAULT NULL,
  `finish_reading_date` date DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`,`manhwa_id`),
  KEY `manhwa_id` (`manhwa_id`),
  CONSTRAINT `user_reading_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Current_Users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `user_reading_status_ibfk_2` FOREIGN KEY (`manhwa_id`) REFERENCES `Manhwas` (`manhwa_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
