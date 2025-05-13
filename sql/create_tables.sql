-- Create the database and use it
CREATE DATABASE IF NOT EXISTS exam_monitoring;
USE exam_monitoring;

-- Table to store student records
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    admission_no VARCHAR(50) UNIQUE NOT NULL,
    exam_no VARCHAR(50) UNIQUE NOT NULL
);

-- Table to store venue information
CREATE TABLE IF NOT EXISTS venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_name VARCHAR(100) UNIQUE NOT NULL,
    capacity INT NOT NULL,
    assigned_students INT DEFAULT 0
);

-- Table for admin login (in production use password_hash instead of MD5)
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insert a sample admin (using MD5 here; in production use password_hash)
INSERT INTO admins (username, password) VALUES ('admin', MD5('admin123'));
