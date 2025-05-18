# Examination Venue Monitoring System

Overview:
The Examination Venue Monitoring System is designed to efficiently manage student attendance, venue assignments, and invigilator oversight during exams. It provides real-time tracking and reporting to ensure smooth examination processes, maintain security with hashed password storage, and offer role-based access primarily for Admission Office and Invigilator users.

Features:
- Admin Roles: Two primary roles (“Admission Office” and “Invigilator”) with custom dashboards for each.
- Venue Management: Easy assignment of students to venues, real-time tracking of available seats, and full venue detection.
- Student Attendance: Invigilators can mark attendance via a user-friendly interface, filter by venue, and review student records.
- Incident Reporting: The system logs and manages reported incidents during examinations for accountability.
- Security: Secure authentication using hashed passwords along with proper session management.

Installation Guide:
1. Setup XAMPP:
   - Install and run XAMPP.
   - Start Apache and MySQL from the XAMPP Control Panel.
2. Database Configuration:
   - Open phpMyAdmin and create a new database named “exam_monitoring”.
   - Execute the following SQL commands to create the necessary tables:

   -- Admins Table (stores admin users)
   CREATE TABLE admins (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL, 
       password_hash VARCHAR(255) NOT NULL,
       role ENUM('admission_office', 'invigilator') NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   -- Students Table (stores student details and attendance status)
   CREATE TABLE students (
       id INT AUTO_INCREMENT PRIMARY KEY,
       admission_no VARCHAR(50) NOT NULL UNIQUE,
       name VARCHAR(100) NOT NULL,
       nta_level VARCHAR(20) NOT NULL,
       exam_no VARCHAR(50) NOT NULL UNIQUE,
       program VARCHAR(100) NOT NULL,
       venue VARCHAR(50) NOT NULL,
       attended BOOLEAN DEFAULT 0,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   -- Attendance Table (records attendance details per student)
   CREATE TABLE attendance (
       id INT AUTO_INCREMENT PRIMARY KEY,
       student_id INT NOT NULL,
       admission_no VARCHAR(50) NOT NULL,
       exam_no VARCHAR(50) NOT NULL,
       invigilator_id INT NOT NULL,
       invigilator_name VARCHAR(100) NOT NULL,
       venue VARCHAR(50) NOT NULL,
       attendance_status ENUM('present', 'absent') NOT NULL DEFAULT 'absent',
       recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (student_id) REFERENCES students(id)
   );

   -- Venues Table (manages examination venues)
   CREATE TABLE venues (
       id INT AUTO_INCREMENT PRIMARY KEY,
       venue_name VARCHAR(50) NOT NULL UNIQUE,
       capacity INT NOT NULL,
       assigned_students INT DEFAULT 0,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   -- Student_Venues Table (tracks student to venue assignments)
   CREATE TABLE student_venues (
       id INT AUTO_INCREMENT PRIMARY KEY,
       student_id INT NOT NULL,
       venue_id INT NOT NULL,
       assignment_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (student_id) REFERENCES students(id),
       FOREIGN KEY (venue_id) REFERENCES venues(id)
   );

   -- Incidents Table (logs reported examination incidents)
   CREATE TABLE incidents (
       id INT AUTO_INCREMENT PRIMARY KEY,
       incident_type VARCHAR(100) NOT NULL,
       reported_by VARCHAR(100) NOT NULL,
       admission_no VARCHAR(50) DEFAULT NULL,
       student_id INT DEFAULT NULL,
       details TEXT NOT NULL,
       reviewed_status ENUM('pending', 'reviewed') NOT NULL DEFAULT 'pending',
       reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

3. Configure Environment:
   - In the project’s config/db.php file, set up your database connection. For example:
     $conn = new mysqli("localhost", "root", "", "exam_monitoring");
   - Set the MySQL server timezone to Tanzania (Dar es Salaam) by running in the MySQL terminal:
     SET GLOBAL time_zone = 'Africa/Dar_es_Salaam';
4. Running the Application:
   - Place the project files in the htdocs/exam_monitoring folder.
   - Launch your web browser and navigate to:
     http://localhost/exam_monitoring/app/public/login.php

Usage Instructions:
- Admin Login: Users log in via the login page to be directed to their respective dashboards based on role.
- Venue Assignment: The Admission Office can assign venues to students using assign_venue.php.
- Attendance Marking: Invigilators mark student attendance via attendance.php using an interactive checkbox interface.
- Incident Reporting: Any examination irregularities are logged and managed via view_incidents.php.

Troubleshooting:
- If MySQL fails to start on Windows, open Command Prompt in Administrator mode and execute:
  net stop MySQL
  net start MySQL
- To reset the MySQL timezone setting, issue:
  SET GLOBAL time_zone = 'Africa/Dar_es_Salaam';

License:
This project is developed for academic and institutional use at Arusha Technical College. Unauthorized distribution or modifications must align with institutional guidelines.
