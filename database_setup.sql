-- Database Setup for Photography Website
-- Run this in phpMyAdmin (http://localhost/phpmyadmin)

-- Create 'gallery' database
CREATE DATABASE IF NOT EXISTS gallery;
USE gallery;

-- Users/Admin registration table
CREATE TABLE IF NOT EXISTS form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table for products
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Photo uploads table
CREATE TABLE IF NOT EXISTS photo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create 'studio' database
CREATE DATABASE IF NOT EXISTS studio;
USE studio;

-- Contact messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    phone_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Create a default admin user (password: admin123)
USE gallery;
INSERT INTO form (name, email, password, user_type) 
VALUES ('Admin', 'admin@studio.com', MD5('admin123'), 'admin')
ON DUPLICATE KEY UPDATE name=name;
