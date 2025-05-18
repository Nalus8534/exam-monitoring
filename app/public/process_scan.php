<?php
// process_scan.php
session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'invigilator') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}
require_once 'config/db.php';

// Get the scanned data; here we assume the scanner transmits the student's admission number.
$scannedStudent = trim($_POST['scanned'] ?? '');

if (empty($scannedStudent)) {
    echo "No student scanned.";
    exit();
}

// Find the student by admission number. Adjust the query if you use a different field.
$query = "SELECT id FROM students WHERE admission_no = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $scannedStudent);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Student not found.";
    exit();
}
$row = $result->fetch_assoc();
$studentId = $row['id'];
$stmt->close();

// Check if an attendance record for this student exists for today.
$today = date("Y-m-d");
$queryCheck = "SELECT id FROM attendance_records WHERE student_id = ? AND exam_date = ?";
$stmt = $conn->prepare($queryCheck);
$stmt->bind_param("is", $studentId, $today);
$stmt->execute();
$resultCheck = $stmt->get_result();
if ($resultCheck->num_rows > 0) {
    echo "Attendance already marked for today.";
    exit();
}
$stmt->close();

// Insert a new attendance record for today.
$queryInsert = "INSERT INTO attendance_records (student_id, exam_date, attended) VALUES (?, ?, 1)";
$stmt = $conn->prepare($queryInsert);
$stmt->bind_param("is", $studentId, $today);
if ($stmt->execute()) {
    echo "Attendance marked successfully for student {$scannedStudent}.";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
