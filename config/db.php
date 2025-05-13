<?php
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root');
define('DB_PASS', ''); // Add password here if set
define('DB_NAME', 'exam_monitoring');

// Attempt connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if missing
$conn->query("CREATE DATABASE IF NOT EXISTS ".DB_NAME);
$conn->select_db(DB_NAME);
?>