<?php
require_once __DIR__ . '/../../config/db.php';
header("Content-Type: application/json");

// Retrieve search parameters
$barcode    = isset($_GET['barcode']) ? trim($_GET['barcode']) : "";
$admissionNo = isset($_GET['admission_no']) ? trim($_GET['admission_no']) : "";
$venue       = isset($_GET['venue']) ? trim($_GET['venue']) : "";

if ((empty($barcode) && empty($admissionNo)) || empty($venue)) {
    echo json_encode(["success" => false, "error" => "Invalid search parameters"]);
    exit();
}

if (!empty($barcode)) {
    $query = "SELECT name, admission_no, nta_level, exam_no, program, venue, image_path 
              FROM students 
              WHERE barcode = ? AND venue = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $barcode, $venue);
} else {
    $query = "SELECT name, admission_no, nta_level, exam_no, program, venue, image_path 
              FROM students 
              WHERE admission_no = ? AND venue = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $admissionNo, $venue);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $student = $result->fetch_assoc();
    echo json_encode(["success" => true, "student" => $student]);
} else {
    echo json_encode(["success" => false, "error" => "Student not found"]);
}

$stmt->close();
$conn->close();
?>
