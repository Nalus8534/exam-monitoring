<?php
session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'invigilator') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}
require_once 'config/db.php';

// Check that required fields are present
if (empty($_POST['title']) || empty($_POST['details'])) {
    echo "Please provide both the incident title and details.";
    exit();
}

$title = trim($_POST['title']);
$details = trim($_POST['details']);

// Optionally, record the current timestamp automatically
$reported_at = date("Y-m-d H:i:s");

// Insert the incident into the database (assumes a table named 'incidents' exists)
$stmt = $conn->prepare("INSERT INTO incidents (title, details, reported_at, reported_by) VALUES (?, ?, ?, ?)");
if ($stmt) {
    // Here, reported_by could be the username stored in the session.
    $reported_by = $_SESSION['username'];
    $stmt->bind_param("ssss", $title, $details, $reported_at, $reported_by);
    if ($stmt->execute()) {
        echo "Incident report submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}
$conn->close();
?>
