-- =====================================================
-- THE OBSCURED INDEX - COMPLETE DATABASE SCHEMA
-- =====================================================
-- Run this SQL to create all tables from scratch
-- Each user will have their own separate library
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if they exist (in correct order due to foreign keys)
DROP TABLE IF EXISTS Reread_History;
DROP TABLE IF EXISTS Secret_Manhwas;
DROP TABLE IF EXISTS Secret_Shelf_Access;
DROP TABLE IF EXISTS User_Reading_Status;
DROP TABLE IF EXISTS Manhwas;
DROP TABLE IF EXISTS Current_Users;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- USERS TABLE
-- =====================================================
CREATE TABLE Current_Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MANHWAS TABLE (with user_id for per-user libraries)
-- =====================================================
CREATE TABLE Manhwas (
    manhwa_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100),
    status ENUM('Ongoing', 'Completed', 'Dropped', 'Hiatus') DEFAULT 'Ongoing',
    genre VARCHAR(100),
    description TEXT,
    cover_image VARCHAR(255),
    reading_link VARCHAR(500),
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_private TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES Current_Users(user_id) ON DELETE CASCADE,
    INDEX idx_manhwas_user_id (user_id),
    INDEX idx_manhwas_title (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- USER READING STATUS TABLE
-- =====================================================
CREATE TABLE User_Reading_Status (
    status_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    manhwa_id INT NOT NULL,
    reading_status ENUM('Plan to Read', 'Currently Reading', 'Done', 'Reread') DEFAULT 'Plan to Read',
    start_reading_date DATE,
    finish_reading_date DATE,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Current_Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (manhwa_id) REFERENCES Manhwas(manhwa_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_manhwa (user_id, manhwa_id),
    INDEX idx_reading_status (reading_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- REREAD HISTORY TABLE
-- =====================================================
CREATE TABLE Reread_History (
    reread_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    manhwa_id INT NOT NULL,
    start_date DATE NOT NULL,
    finish_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Current_Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (manhwa_id) REFERENCES Manhwas(manhwa_id) ON DELETE CASCADE,
    INDEX idx_reread_user_manhwa (user_id, manhwa_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECRET SHELF ACCESS TABLE
-- =====================================================
CREATE TABLE Secret_Shelf_Access (
    access_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    granted_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Current_Users(user_id) ON DELETE CASCADE,
    INDEX idx_secret_access_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECRET MANHWAS TABLE
-- =====================================================
CREATE TABLE Secret_Manhwas (
    secret_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    manhwa_id INT NOT NULL,
    added_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES Current_Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (manhwa_id) REFERENCES Manhwas(manhwa_id) ON DELETE CASCADE,
    UNIQUE KEY user_manhwa_unique (user_id, manhwa_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- END OF SCHEMA
-- =====================================================
