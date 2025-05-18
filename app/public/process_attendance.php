<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

file_put_contents(__DIR__ . "/debug_post.txt", "Received POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['attendance']) || empty($data['attendance'])) {
    echo json_encode(["success" => false, "message" => "No attendance data received"]);
    exit();
}

// Get invigilator ID from session
$invigilator_id = $_SESSION['admin_id'] ?? null;
$venue = $_SESSION['venue'] ?? "Unknown Venue"; // Ensure venue selection

if (!$invigilator_id) {
    echo json_encode(["success" => false, "message" => "Invigilator ID not found"]);
    exit();
}

// Fetch invigilator's name from the admins table
$stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
$stmt->bind_param("i", $invigilator_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invigilator not found in admins table"]);
    exit();
}

$admin_row = $result->fetch_assoc();
$invigilator_name = $admin_row['username'];

foreach ($data['attendance'] as $student) {
    $student_id = $student['student_id'];
    $attendance_status = $student['attendance_status'];
    $admission_no = $student['admission_no'];
    $exam_no = $student['exam_no'];
    $current_date = date("Y-m-d H:i:s");
    $venue = isset($student['venue']) && !empty($student['venue']) ? $student['venue'] : "Unknown Venue"; // Detect venue properly

    $stmt = $conn->prepare("INSERT INTO attendance (student_id, admission_no, exam_no, invigilator_id, invigilator_name, venue, attendance_status, recorded_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississss", $student_id, $admission_no, $exam_no, $invigilator_id, $invigilator_name, $venue, $attendance_status, $current_date);
    $stmt->execute();
}

echo json_encode(["success" => true, "message" => "Attendance saved successfully"]);
$conn->close();
?>
