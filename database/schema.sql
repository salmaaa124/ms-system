-- ===================================================================
-- MS Detection System - Database Schema
-- Graduation Project
-- ===================================================================

CREATE DATABASE IF NOT EXISTS ms_detection_db
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ms_detection_db;

-- -----------------------------
-- Users Table (Doctors)
-- -----------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------
-- Images Table (MRI uploads)
-- -----------------------------
CREATE TABLE IF NOT EXISTS images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    original_name VARCHAR(255) DEFAULT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------
-- Results Table (AI detection outputs)
-- -----------------------------
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_id INT NOT NULL,
    result ENUM('Positive','Negative') NOT NULL,
    confidence_score DECIMAL(5,2) NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------
-- Seed Default Doctor Account
-- email    : doctor@ms-detect.com
-- password : doctor123
-- (Hashed with PHP password_hash BCRYPT)
-- -----------------------------
INSERT INTO users (name, email, password)
VALUES (
    'Dr. Ahmed Hassan',
    'doctor@ms-detect.com',
    '$2y$10$e0NRMP9hM3DpJ4A8Zq4d7eHqFZl9XgH6YJu2P3R5TqV7Wn.aBcDeF'
)
ON DUPLICATE KEY UPDATE email = email;
-- NOTE: The above hash is a placeholder. The install.php script will
-- generate the correct hash at runtime for maximum portability.
