-- Create the database if it doesn't exist and switch to it.
CREATE DATABASE IF NOT EXISTS exam_monitoring;
USE exam_monitoring;

-------------------------------------------------------------------
-- Table 1: admins
-- Stores administrator accounts.
-------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL, -- Storing plain text passwords is not secure. Use only hashes in production.
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-------------------------------------------------------------------
-- Table 2: students
-- Stores student details as per your phpMyAdmin screenshot.
-------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    admission_no VARCHAR(50) NOT NULL UNIQUE,
    nta_level VARCHAR(50),
    exam_no VARCHAR(50),
    program VARCHAR(255),
    venue VARCHAR(50),
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-------------------------------------------------------------------
-- Table 3: exams
-- Contains exam session details, including course, exam date, venue, and status.
-------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    course VARCHAR(255) NOT NULL,
    exam_date DATE NOT NULL,
    venue VARCHAR(50),
    status VARCHAR(50), -- e.g., Scheduled, Ongoing, Completed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-------------------------------------------------------------------
-- Table 4: settings
-- A simple key-value table for system preferences (e.g., theme and security level).
-------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL
);

-- Insert default settings (if they don't already exist)
INSERT INTO settings (setting_key, setting_value) VALUES
('theme', 'light'),
('security_level', 'medium')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-------------------------------------------------------------------
-- Table 5: reports
-- Stores exam-related reports or logs.
-------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_exam
        FOREIGN KEY (exam_id) 
        REFERENCES exams(exam_id)
        ON DELETE SET NULL
);

-------------------------------------------------------------------
-- Table 6: venues
-- Stores information about exam venues.
-------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_name VARCHAR(100) NOT NULL UNIQUE,
    capacity INT NOT NULL,
    assigned_students INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
