CREATE DATABASE IF NOT EXISTS online_donation_platform
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE online_donation_platform;

-- 1. USERS TABLE
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'ngo', 'donor') NOT NULL DEFAULT 'donor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. NGOS TABLE
CREATE TABLE ngos (
    ngo_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    registration_no VARCHAR(100) NOT NULL UNIQUE,
    contact_email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. CAMPAIGNS TABLE
CREATE TABLE campaigns (
    campaign_id INT AUTO_INCREMENT PRIMARY KEY,
    ngo_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    target_amount DECIMAL(12,2) NOT NULL,
    raised_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ngo_id) REFERENCES ngos(ngo_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CHECK (target_amount > 0),
    CHECK (raised_amount >= 0)
) ENGINE=InnoDB;

-- 4. DONATIONS TABLE
CREATE TABLE donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    donor_name VARCHAR(150) NOT NULL,
    donor_email VARCHAR(150) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    donated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(campaign_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CHECK (amount > 0)
) ENGINE=InnoDB;

-- Confirm table creation
SHOW TABLES;
