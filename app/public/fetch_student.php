<?php
require_once __DIR__ . '/../../config/db.php';
header("Content-Type: application/json");

// Retrieve search parameters
$barcode = isset($_GET['barcode']) ? trim($_GET['barcode']) : "";
$admissionNo = isset($_GET['admission_no']) ? trim($_GET['admission_no']) : "";

if (empty($barcode) && empty($admissionNo)) {
    echo json_encode(["success" => false, "error" => "Invalid search parameters"]);
    exit();
}

if (!empty($barcode)) {
    $query = "SELECT name, admission_no, nta_level, exam_no, program, venue, image_path FROM students WHERE barcode = ?";
    $param = $barcode;
} else {
    $query = "SELECT name, admission_no, nta_level, exam_no, program, venue, image_path FROM students WHERE admission_no = ?";
    $param = $admissionNo;
}

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $param);
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
